<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportAttendanceRequest;
use App\Models\Attendance;
use App\Models\Company;
use App\Models\Department;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Section;
use App\Models\WorkLocation;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceController extends Controller
{
    private const COLUMNS = [
        'employee_id',
        'employee_nip',
        'employee_email',
        'employee_name',
        'clock_in',
        'clock_out',
        'work_hours',
        'status',
        'location_in_latitude',
        'location_in_longitude',
        'location_out_latitude',
        'location_out_longitude',
    ];

    public function index(Request $request): View
    {
        return view('attendances.index', [
            'attendances' => $this->attendanceQuery($request)->latest('clock_in')->paginate(10)->withQueryString(),
            'companies' => Company::orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
            'divisions' => Division::orderBy('name')->get(),
            'sections' => Section::orderBy('name')->get(),
            'workLocations' => WorkLocation::orderBy('name')->get(),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $filename = 'attendances-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($request): void {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, self::COLUMNS);

            $this->attendanceQuery($request)
                ->with('employee')
                ->latest('clock_in')
                ->chunk(200, function ($attendances) use ($handle): void {
                    foreach ($attendances as $attendance) {
                        fputcsv($handle, $this->attendanceRow($attendance));
                    }
                });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function template(): Response
    {
        $handle = fopen('php://temp', 'r+');
        fwrite($handle, "\xEF\xBB\xBF");
        fputcsv($handle, self::COLUMNS);
        fputcsv($handle, [
            '',
            'EMP001',
            'employee@example.com',
            '',
            '2026-06-09 08:00:00',
            '2026-06-09 17:00:00',
            '8',
            'present',
            '-6.2088',
            '106.8650',
            '-6.2088',
            '106.8650',
        ]);
        rewind($handle);
        $contents = stream_get_contents($handle);
        fclose($handle);

        return response($contents, 200, [
            'Content-Disposition' => 'attachment; filename="attendance-import-template.csv"',
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function import(ImportAttendanceRequest $request): RedirectResponse
    {
        $handle = fopen($request->file('file')->getRealPath(), 'r');
        $header = $this->normalizeHeader(fgetcsv($handle) ?: []);
        $requiredColumns = ['clock_in', 'status'];

        if (array_diff($requiredColumns, $header) !== []) {
            fclose($handle);

            return back()->withErrors(['file' => 'Template must include clock_in and status columns.']);
        }

        $created = 0;
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            if ($this->isEmptyRow($row)) {
                continue;
            }

            $data = array_combine($header, array_slice(array_pad($row, count($header), ''), 0, count($header)));
            $validator = Validator::make($data, [
                'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
                'employee_nip' => ['nullable', 'string', 'max:50'],
                'employee_email' => ['nullable', 'email', 'max:255'],
                'clock_in' => ['required', 'date'],
                'clock_out' => ['nullable', 'date', 'after_or_equal:clock_in'],
                'work_hours' => ['nullable', 'integer', 'min:0'],
                'status' => ['required', 'in:present,absent,sick,excuse'],
                'location_in_latitude' => ['nullable', 'numeric'],
                'location_in_longitude' => ['nullable', 'numeric'],
                'location_out_latitude' => ['nullable', 'numeric'],
                'location_out_longitude' => ['nullable', 'numeric'],
            ]);

            if ($validator->fails()) {
                $errors[] = 'Row '.$rowNumber.': '.$validator->errors()->first();

                continue;
            }

            $employee = $this->findEmployee($data);

            if (! $employee) {
                $errors[] = 'Row '.$rowNumber.': employee not found by employee_id, employee_nip, or employee_email.';

                continue;
            }

            $attendance = Attendance::create([
                'employee_id' => $employee->id,
                'clock_in' => Carbon::parse($data['clock_in']),
                'clock_out' => filled($data['clock_out'] ?? null) ? Carbon::parse($data['clock_out']) : null,
                'work_hours' => filled($data['work_hours'] ?? null) ? (int) $data['work_hours'] : null,
                'status' => $data['status'],
                'location_in' => $this->location($data, 'location_in'),
                'location_out' => $this->location($data, 'location_out'),
            ]);
            $this->logCreated($attendance);

            $created++;
        }

        fclose($handle);

        return back()
            ->with('success', $created.' attendance record(s) imported successfully.')
            ->with('import_errors', $errors);
    }

    private function attendanceQuery(Request $request)
    {
        return Attendance::with(['employee.company', 'employee.department', 'employee.division', 'employee.section', 'employee.workLocation'])
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = trim((string) $request->query('search'));

                $query->whereHas('employee', function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->query('status')))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('clock_in', '>=', $request->query('date_from')))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('clock_in', '<=', $request->query('date_to')))
            ->when($request->filled('company_id'), fn ($query) => $query->whereHas('employee', fn ($query) => $query->where('company_id', $request->query('company_id'))))
            ->when($request->filled('department_id'), fn ($query) => $query->whereHas('employee', fn ($query) => $query->where('department_id', $request->query('department_id'))))
            ->when($request->filled('division_id'), fn ($query) => $query->whereHas('employee', fn ($query) => $query->where('division_id', $request->query('division_id'))))
            ->when($request->filled('section_id'), fn ($query) => $query->whereHas('employee', fn ($query) => $query->where('section_id', $request->query('section_id'))))
            ->when($request->filled('work_location_id'), fn ($query) => $query->whereHas('employee', fn ($query) => $query->where('work_location_id', $request->query('work_location_id'))));
    }

    private function attendanceRow(Attendance $attendance): array
    {
        return [
            $attendance->employee_id,
            $attendance->employee?->nip,
            $attendance->employee?->email,
            $attendance->employee?->name,
            $attendance->clock_in?->format('Y-m-d H:i:s'),
            $attendance->clock_out?->format('Y-m-d H:i:s'),
            $attendance->work_hours,
            $attendance->status,
            $attendance->location_in['latitude'] ?? '',
            $attendance->location_in['longitude'] ?? '',
            $attendance->location_out['latitude'] ?? '',
            $attendance->location_out['longitude'] ?? '',
        ];
    }

    private function normalizeHeader(array $header): array
    {
        return array_map(fn ($column) => trim(strtolower((string) preg_replace('/^\xEF\xBB\xBF/', '', $column))), $header);
    }

    private function findEmployee(array $data): ?Employee
    {
        if (! filled($data['employee_id'] ?? null) && ! filled($data['employee_nip'] ?? null) && ! filled($data['employee_email'] ?? null)) {
            return null;
        }

        return Employee::query()
            ->when(filled($data['employee_id'] ?? null), fn ($query) => $query->orWhere('id', $data['employee_id']))
            ->when(filled($data['employee_nip'] ?? null), fn ($query) => $query->orWhere('nip', $data['employee_nip']))
            ->when(filled($data['employee_email'] ?? null), fn ($query) => $query->orWhere('email', $data['employee_email']))
            ->first();
    }

    private function location(array $data, string $prefix): ?array
    {
        $latitude = $data[$prefix.'_latitude'] ?? null;
        $longitude = $data[$prefix.'_longitude'] ?? null;

        if (! filled($latitude) && ! filled($longitude)) {
            return null;
        }

        return [
            'latitude' => filled($latitude) ? (float) $latitude : null,
            'longitude' => filled($longitude) ? (float) $longitude : null,
        ];
    }

    private function isEmptyRow(array $row): bool
    {
        return collect($row)->every(fn ($value) => trim((string) $value) === '');
    }
}
