@extends('layouts.app')

@section('title', 'Edit Employee - ' . config('app.name'))

@section('content')
<div class="mb-6">
    <a href="{{ route('employees.index') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors">&larr; Back to Employees</a>
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mt-2">Edit Employee</h2>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update the employee details below.</p>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 sm:p-8">
    <form method="POST" action="{{ route('employees.update', $employee) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">NIP</label>
                <input type="text" name="nip" value="{{ old('nip', $employee->nip) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm @error('nip') border-red-500 @enderror" placeholder="Employee NIP">
                @error('nip')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">NIK</label>
                <input type="text" name="nik" value="{{ old('nik', $employee->nik) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="National ID number">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">NPWP</label>
                <input type="text" name="npwp" value="{{ old('npwp', $employee->npwp) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="Tax ID number">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $employee->name) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm @error('name') border-red-500 @enderror" placeholder="Full name">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $employee->email) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm @error('email') border-red-500 @enderror" placeholder="employee@company.com">
                @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="Phone number">
            </div>
            <div class="md:col-span-2 lg:col-span-3">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Address</label>
                <textarea name="address" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="Complete address">{{ old('address', $employee->address) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Birth Place</label>
                <input type="text" name="birth_place" value="{{ old('birth_place', $employee->birth_place) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="City of birth">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Birth Date</label>
                <input type="date" name="birth_date" value="{{ old('birth_date', $employee->birth_date) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Join Date</label>
                <input type="date" name="join_date" value="{{ old('join_date', $employee->join_date) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">BPJS Kes</label>
                <input type="text" name="bpjs_kes" value="{{ old('bpjs_kes', $employee->bpjs_kes) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="BPJS Kesehatan number">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">BPJS TK</label>
                <input type="text" name="bpjs_tk" value="{{ old('bpjs_tk', $employee->bpjs_tk) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="BPJS Ketenagakerjaan number">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Marital Status</label>
                <select name="marital_status" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="">Select...</option>
                    <option value="belum kawin" @selected(old('marital_status', $employee->marital_status) === 'belum kawin')>Belum Kawin</option>
                    <option value="kawin" @selected(old('marital_status', $employee->marital_status) === 'kawin')>Kawin</option>
                    <option value="pisah" @selected(old('marital_status', $employee->marital_status) === 'pisah')>Pisah</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Religion</label>
                <select name="religion_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="">Select...</option>
                    @foreach($religions as $r)
                    <option value="{{ $r->id }}" @selected(old('religion_id', $employee->religion_id) == $r->id)>{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Company</label>
                <select name="company_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="">Select...</option>
                    @foreach($companies as $c)
                    <option value="{{ $c->id }}" @selected(old('company_id', $employee->company_id) == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Department</label>
                <select name="department_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="">Select...</option>
                    @foreach($departments as $d)
                    <option value="{{ $d->id }}" @selected(old('department_id', $employee->department_id) == $d->id)>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Division</label>
                <select name="division_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="">Select...</option>
                    @foreach($divisions as $d)
                    <option value="{{ $d->id }}" @selected(old('division_id', $employee->division_id) == $d->id)>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Section</label>
                <select name="section_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="">Select...</option>
                    @foreach($sections as $s)
                    <option value="{{ $s->id }}" @selected(old('section_id', $employee->section_id) == $s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Job Position</label>
                <select name="job_position_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="">Select...</option>
                    @foreach($jobPositions as $j)
                    <option value="{{ $j->id }}" @selected(old('job_position_id', $employee->job_position_id) == $j->id)>{{ $j->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Work Location</label>
                <select name="work_location_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="">Select...</option>
                    @foreach($workLocations as $wl)
                    <option value="{{ $wl->id }}" @selected(old('work_location_id', $employee->work_location_id) == $wl->id)>{{ $wl->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status</label>
                <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="active" @selected(old('status', $employee->status) === 'active')>Active</option>
                    <option value="inactive" @selected(old('status', $employee->status) === 'inactive')>Inactive</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Blood Type</label>
                <input type="text" name="blood_type" value="{{ old('blood_type', $employee->blood_type) }}" placeholder="A, B, AB, O" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Facilities</label>
                <select name="facility_ids[]" multiple class="w-full min-h-32 px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm">
                    @foreach($facilities as $facility)
                    <option value="{{ $facility->id }}" @selected(in_array($facility->id, old('facility_ids', $employee->facility_ids ?? [])))>{{ $facility->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('employees.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-sm text-sm transition-colors">Update Employee</button>
        </div>
    </form>
</div>
@endsection
