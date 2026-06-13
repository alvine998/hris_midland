<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\UploadedFile;

class AttendanceService
{
    private const FAKE_GPS_ACCURACY_THRESHOLD = 5;

    private const SUSPICIOUS_COORDINATES = [
        ['latitude' => 0, 'longitude' => 0],
        ['latitude' => 90, 'longitude' => 0],
        ['latitude' => -90, 'longitude' => 0],
        ['latitude' => 0, 'longitude' => 180],
        ['latitude' => 0, 'longitude' => -180],
    ];

    public function checkIn(Employee $employee, array $data): Attendance
    {
        $locationData = $this->extractLocationData($data);

        $selfiePath = $this->storeSelfie($data['selfie'], $employee->id);

        $gpsAnalysis = $this->analyzeGps($locationData, $data['gps_accuracy'] ?? null);

        return Attendance::create([
            'employee_id' => $employee->id,
            'clock_in' => now(),
            'status' => 'present',
            'location_in' => $locationData,
            'selfie_in' => $selfiePath,
            'gps_accuracy_in' => $data['gps_accuracy'] ?? null,
            'is_mock_location_in' => $gpsAnalysis['is_suspicious'],
            'check_in_method' => 'selfie',
            'ip_address_in' => request()->ip(),
        ]);
    }

    public function checkOut(Attendance $attendance, array $data): Attendance
    {
        $locationData = $this->extractLocationData($data);

        $selfiePath = $this->storeSelfie($data['selfie'], $attendance->employee_id);

        $gpsAnalysis = $this->analyzeGps($locationData, $data['gps_accuracy'] ?? null);

        $clockOut = now();
        $clockIn = $attendance->clock_in;

        $workHours = $clockIn ? $clockIn->diffInHours($clockOut) : null;

        $attendance->update([
            'clock_out' => $clockOut,
            'work_hours' => $workHours,
            'location_out' => $locationData,
            'selfie_out' => $selfiePath,
            'gps_accuracy_out' => $data['gps_accuracy'] ?? null,
            'is_mock_location_out' => $gpsAnalysis['is_suspicious'],
            'ip_address_out' => request()->ip(),
        ]);

        return $attendance;
    }

    public function hasCheckedInToday(Employee $employee): ?Attendance
    {
        return Attendance::where('employee_id', $employee->id)
            ->whereDate('clock_in', today())
            ->whereNull('clock_out')
            ->first();
    }

    public function hasCheckedOutToday(Employee $employee): bool
    {
        return Attendance::where('employee_id', $employee->id)
            ->whereDate('clock_in', today())
            ->whereNotNull('clock_out')
            ->exists();
    }

    public function getTodayAttendance(Employee $employee): ?Attendance
    {
        return Attendance::where('employee_id', $employee->id)
            ->whereDate('clock_in', today())
            ->first();
    }

    public function analyzeGps(?array $location, ?float $accuracy): array
    {
        $flags = [];
        $isSuspicious = false;

        if ($location === null || ! isset($location['latitude'], $location['longitude'])) {
            return [
                'is_suspicious' => false,
                'flags' => ['no_location_data'],
                'score' => 0,
            ];
        }

        $lat = $location['latitude'];
        $lng = $location['longitude'];

        foreach (self::SUSPICIOUS_COORDINATES as $coord) {
            if (abs($lat - $coord['latitude']) < 0.001 && abs($lng - $coord['longitude']) < 0.001) {
                $flags[] = 'known_fake_coordinates';
                $isSuspicious = true;
            }
        }

        if ($accuracy !== null && $accuracy < self::FAKE_GPS_ACCURACY_THRESHOLD) {
            $flags[] = 'unrealistic_accuracy';
            $isSuspicious = true;
        }

        if ($lat === 0.0 && $lng === 0.0) {
            $flags[] = 'null_island';
            $isSuspicious = true;
        }

        $score = count($flags);

        return [
            'is_suspicious' => $isSuspicious,
            'flags' => $flags,
            'score' => $score,
        ];
    }

    public function isWithinWorkLocation(Employee $employee, float $latitude, float $longitude): array
    {
        $workLocation = $employee->workLocation;

        if (! $workLocation || ! $workLocation->latitude || ! $workLocation->longitude) {
            return [
                'within_range' => true,
                'distance' => null,
                'work_location' => null,
                'message' => 'No work location configured for this employee.',
            ];
        }

        $distance = $this->haversineDistance(
            $latitude,
            $longitude,
            (float) $workLocation->latitude,
            (float) $workLocation->longitude
        );

        $radius = $workLocation->radius ?? 100;

        $withinRange = $distance <= $radius;

        return [
            'within_range' => $withinRange,
            'distance' => round($distance, 2),
            'radius' => $radius,
            'work_location' => $workLocation->name,
            'message' => $withinRange
                ? 'You are within the allowed location range.'
                : "You are {$distance} meters away from your designated work location (allowed: {$radius}m).",
        ];
    }

    private function extractLocationData(array $data): ?array
    {
        if (! isset($data['latitude']) || ! isset($data['longitude'])) {
            return null;
        }

        return [
            'latitude' => (float) $data['latitude'],
            'longitude' => (float) $data['longitude'],
        ];
    }

    private function storeSelfie(UploadedFile $selfie, int $employeeId): string
    {
        $filename = $employeeId.'_'.now()->format('Ymd_His').'_'.uniqid().'.'.$selfie->extension();

        return $selfie->storeAs('attendances/selfies', $filename, 'public');
    }

    private function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
