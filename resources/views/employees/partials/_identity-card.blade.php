<style>
.id-card-wrap{max-width:360px;width:100%;margin:0 auto;font-family:'Inter','Segoe UI',system-ui,sans-serif}
.id-card-wrap .id-card{position:relative;border-radius:16px;overflow:hidden;box-shadow:0 12px 48px rgba(0,0,0,.12);transition:transform .3s ease,box-shadow .3s ease}
.id-card-wrap .id-card:hover{transform:translateY(-4px);box-shadow:0 16px 64px rgba(0,0,0,.16)}
.id-card-wrap .id-photo{width:72px;height:72px;border-radius:12px;object-fit:cover;border:3px solid #fff;box-shadow:0 4px 12px rgba(0,0,0,.15);flex-shrink:0}
.id-card-wrap .id-avatar{width:72px;height:72px;border-radius:12px;background:linear-gradient(135deg,#6366f1,#a855f7,#ec4899);display:flex;align-items:center;justify-content:center;color:#fff;font-size:28px;font-weight:800;border:3px solid #fff;box-shadow:0 4px 12px rgba(0,0,0,.15);flex-shrink:0}
.id-card-wrap .id-r{display:flex;justify-content:space-between;align-items:center;padding:8px 0;gap:8px}
.id-card-wrap .id-r+.id-r{border-top:1px solid rgba(0,0,0,.08)}
.id-card-wrap .dark .id-r+.id-r{border-top-color:rgba(255,255,255,.08)}
.id-card-wrap .id-lb{font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#a1a5b7;flex-shrink:0}
.id-card-wrap .dark .id-lb{color:#6b7280}
.id-card-wrap .id-vl{font-size:12px;font-weight:600;color:#1f2937;text-align:right;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-left:auto;min-width:0;flex:1}
.id-card-wrap .dark .id-vl{color:#e5e7eb}
.id-card-wrap .id-badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;font-weight:600;padding:4px 8px;border-radius:10px;line-height:1.4;white-space:nowrap;transition:transform .2s,box-shadow .2s}
.id-card-wrap .id-badge:hover{transform:scale(1.05)}
.id-card-wrap .id-badge svg{width:10px;height:10px;flex-shrink:0}
.id-card-wrap .id-status{display:inline-flex;align-items:center;gap:5px;font-size:9px;font-weight:700;padding:4px 10px;border-radius:12px;letter-spacing:.05em;backdrop-filter:blur(8px)}
.id-card-wrap .id-divider{height:1px;background:linear-gradient(90deg,transparent,rgba(0,0,0,.08),transparent)}
.id-card-wrap .dark .id-divider{background:linear-gradient(90deg,transparent,rgba(255,255,255,.08),transparent)}
</style>

<div class="id-card-wrap">
<div class="id-card relative bg-white dark:bg-gray-900">
    {{-- Gradient border --}}
    <div class="absolute inset-0 rounded-[14px] p-[2px] bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 pointer-events-none z-10"></div>

    <div class="relative overflow-hidden rounded-[12px] bg-white dark:bg-gray-900">

        {{-- Header band --}}
        <div class="relative bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-500 px-5 py-3.5">
            <div class="absolute inset-0 opacity-[0.05]" style="background-image:url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cpath d=\"M30 30L5 5zM30 30L55 5zM30 30L5 55zM30 30L55 55z\" stroke=\"%23fff\" stroke-width=\"0.3\" fill=\"none\" opacity=\"0.4\"/%3E%3C/svg%3E')"></div>
            <div class="relative flex items-center justify-between gap-3">
                <div>
                    <p class="text-white/70 text-[8px] font-bold uppercase tracking-[0.12em] leading-none">Employee ID Card</p>
                    <p class="text-white text-[10px] font-bold leading-tight mt-1">{{ $employee->company?->name ?? 'Company' }}</p>
                </div>
                @if ($employee->status === 'active')
                    <span class="id-status bg-emerald-500/30 backdrop-blur-sm border border-emerald-400/50 text-emerald-100"><span class="w-1.5 h-1.5 rounded-full bg-emerald-300 animate-pulse"></span>ACTIVE</span>
                @else
                    <span class="id-status bg-red-500/30 backdrop-blur-sm border border-red-400/50 text-red-100"><span class="w-1.5 h-1.5 rounded-full bg-red-300"></span>INACTIVE</span>
                @endif
            </div>
        </div>

        {{-- Photo + Name --}}
        <div class="flex flex-col items-center text-center pt-4 pb-3 px-5">
            <div class="relative mb-3">
                @if ($employee->photo)
                    <img src="{{ Storage::url($employee->photo) }}" alt="{{ $employee->name }}" class="id-photo">
                @else
                    <div class="id-avatar">{{ strtoupper(substr($employee->name, 0, 1)) }}</div>
                @endif
                @if ($employee->status === 'active')
                    <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full border-2.5 border-white dark:border-gray-900 bg-emerald-500 flex items-center justify-center shadow-lg"><svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></div>
                @endif
            </div>
            <h3 class="text-[15px] font-black text-gray-900 dark:text-white leading-tight tracking-tight">{{ $employee->name }}</h3>
            <p class="text-[10px] font-semibold text-indigo-600 dark:text-indigo-400 mt-1.5 leading-tight">{{ $employee->jobPosition?->name ?? '-' }}</p>
            <!-- <p class="text-[9px] text-gray-500 dark:text-gray-400 font-mono mt-1 tracking-wide">{{ $employee->nip ?? '-' }}</p> -->
        </div>

        {{-- Divider --}}
        <!-- <div class="mx-5 my-2.5">
            <div class="id-divider"></div>
        </div> -->

        {{-- Info rows --}}
        <div class="px-5 py-3 space-y-1.5">
            <div class="id-r"><span class="id-lb">NIP</span><span class="id-vl font-mono">{{ $employee->nip ?? '-' }}</span></div>
            <div class="id-r"><span class="id-lb">Department</span><span class="id-vl">{{ $employee->department?->name ?? '-' }}</span></div>
            <div class="id-r"><span class="id-lb">Division</span><span class="id-vl">{{ $employee->division?->name ?? '-' }}</span></div>
            <div class="id-r"><span class="id-lb">Section</span><span class="id-vl">{{ $employee->section?->name ?? '-' }}</span></div>
            <div class="id-r"><span class="id-lb">Location</span><span class="id-vl">{{ $employee->workLocation?->name ?? '-' }}</span></div>
            <div class="id-r"><span class="id-lb">Join Date</span><span class="id-vl">{{ $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('d M Y') : '-' }}</span></div>
        </div>

        {{-- Divider --}}
        <div class="mx-5 my-2.5">
            <div class="id-divider"></div>
        </div>

        {{-- Badges Hide --}}
        <!-- <div class="px-5 py-3 flex flex-wrap items-center gap-2 justify-center">
            @if ($employee->religion)
                <span class="id-badge text-violet-600 dark:text-violet-300 bg-violet-100/70 dark:bg-violet-900/40 border border-violet-200/80 dark:border-violet-800/50 backdrop-blur-sm">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m-8-9H3m18 0h-1"/></svg>
                    {{ $employee->religion->name }}
                </span>
            @endif
            @if ($employee->blood_type)
                <span class="id-badge text-rose-600 dark:text-rose-300 bg-rose-100/70 dark:bg-rose-900/40 border border-rose-200/80 dark:border-rose-800/50 backdrop-blur-sm">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    {{ $employee->blood_type }}
                </span>
            @endif
            @if ($employee->marital_status)
                <span class="id-badge text-emerald-600 dark:text-emerald-300 bg-emerald-100/70 dark:bg-emerald-900/40 border border-emerald-200/80 dark:border-emerald-800/50 backdrop-blur-sm">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    {{ ucfirst($employee->marital_status) }}
                </span>
            @endif
            @if ($employee->npwp)
                <span class="id-badge text-amber-600 dark:text-amber-300 bg-amber-100/70 dark:bg-amber-900/40 border border-amber-200/80 dark:border-amber-800/50 backdrop-blur-sm">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    NPWP
                </span>
            @endif
            @if ($employee->email)
                <span class="id-badge text-blue-600 dark:text-blue-300 bg-blue-100/70 dark:bg-blue-900/40 border border-blue-200/80 dark:border-blue-800/50 backdrop-blur-sm">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Email
                </span>
            @endif
        </div> -->

        {{-- Footer Hide --}}
        <div class="bg-gradient-to-r from-gray-50 via-white to-indigo-50/40 dark:from-gray-800/60 dark:via-gray-800/50 dark:to-indigo-900/10 px-5 py-3.5 border-gray-100/80 dark:border-gray-800/60 mt-8">
            <div class="flex items-center justify-center">
                <p class="text-gray-500 dark:text-gray-400 text-[8px] tracking-[0.12em] leading-none">{{ $employee->company?->name ?? 'Company' }}</p>
            </div>
        </div>
    </div>
</div>
</div>