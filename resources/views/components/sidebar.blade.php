@php
    $role = auth()->user()->role;
    $menus = [
        'leader' => [
            ['Dashboard', 'dashboard', '/leader/dashboard'],
            ['Data Cabang', 'building', '/leader/branches'],
            ['Admin Cabang', 'shield-user', '/leader/admins'],
            ['Data Santri', 'graduation-cap', '/leader/students'],
            ['Data Guru', 'users', '/leader/teachers'],
            ['Presensi', 'calendar-check', '/leader/attendance'],
            ['Penilaian', 'clipboard-check', '/leader/assessments'],
            ['Rapor', 'file-text', '/leader/reports'],
        ],
        'admin' => [
            ['Dashboard', 'dashboard', '/admin/dashboard'],
            ['Santri', 'graduation-cap', '/admin/students'],
            ['Guru', 'users', '/admin/teachers'],
            ['Wali Santri', 'heart-hand', '/admin/guardians'],
            ['Kelompok', 'layers', '/admin/groups'],
            ['Jadwal', 'calendar-days', '/admin/schedules'],
            ['Presensi', 'calendar-check', '/admin/attendance'],
            ['Penilaian', 'clipboard-check', '/admin/assessments'],
            ['Rapor', 'file-text', '/admin/reports'],
            ['Periode', 'calendar-clock', '/admin/periods'],
            ['Pengaturan', 'settings', '/admin/settings'],
        ],
        'teacher' => [
            ['Dashboard', 'dashboard', '/teachers/dashboard'],
            ['Santri Saya', 'graduation-cap', '/teachers/students'],
            ['Jadwal', 'calendar-days', '/teachers/schedules'],
            ['Presensi', 'calendar-check', '/teachers/attendance'],
            ['Penilaian', 'clipboard-check', '/teachers/assessments'],
            ['Rapor', 'file-text', '/teachers/reports'],
        ],
        'guardian' => [
            ['Dashboard', 'dashboard', '/guardians/dashboard'],
            ['Data Santri', 'users', '/guardians/students'],
            ['Jadwal', 'calendar-days', '/guardians/schedules'],
            ['Presensi', 'calendar-check', '/guardians/attendance'],
            ['Nilai Mata Pelajaran', 'book-open', '/guardians/lesson-scores'],
            ['Rapor', 'file-text', '/guardians/reports'],
        ],
    ][$role] ?? [];
@endphp

<aside id="dashboard-sidebar" class="fixed inset-y-0 left-0 z-30 w-72 -translate-x-full border-r border-slate-200 bg-white shadow-xl transition-transform duration-200 ease-out lg:translate-x-0 lg:shadow-none">
    <div class="flex h-full flex-col">
        <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-4 lg:px-5 lg:py-5">
            <div class="flex min-w-0 items-center gap-3">
                <img src="{{ asset('logo-sahadat-qu.jpeg') }}" alt="Sahadat-Qu" class="h-10 w-10 rounded-full object-cover lg:h-11 lg:w-11">
                <div class="min-w-0">
                    <p class="font-semibold text-slate-950">Sahadat-Qu</p>
                    <p class="text-xs uppercase text-slate-500">{{
                        (str_replace('_', ' ', $role) === 'guardian' ? 'Wali Santri' :
                        (str_replace('_', ' ', $role) === 'leader' ? 'Ketua Lembaga' :
                        str_replace('_', ' ', $role)))
                    }}</p>
                </div>
            </div>
            <button id="sidebar-close" type="button" class="inline-flex h-9 w-9 shrink-0 cursor-pointer items-center justify-center rounded-md border border-slate-300 bg-white text-slate-600 hover:border-blue-950 hover:text-blue-950 lg:hidden" aria-label="Tutup menu">
                <x-icon name="x" />
            </button>
        </div>

        <nav class="flex flex-1 flex-col gap-2 overflow-y-auto px-4 py-4">
            @foreach ($menus as [$label, $icon, $url])
                @php
                    $path = ltrim($url, '/');
                    $isActive = request()->is($path) || request()->is($path . '/*');
                @endphp
                <a href="{{ url($url) }}" class="flex min-h-10 items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ $isActive ? 'bg-blue-950 text-white' : 'text-slate-700 hover:bg-slate-100 hover:text-blue-950' }}">
                    <x-icon :name="$icon" class="h-4 w-4 shrink-0" />
                    <span>{{ $label }}</span>
                </a>
            @endforeach
        </nav>

        <div class="border-t border-slate-200 p-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex min-h-10 w-full cursor-pointer items-center justify-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:border-blue-950 hover:text-blue-950">
                    Logout
                </button>
            </form>
        </div>
    </div>
</aside>
