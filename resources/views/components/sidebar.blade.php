@php
    $role = auth()->user()->role;

    $menus = [
        //ketua lemabaga
        'leader' => [
            ['title' => null,
                'items' => [
                    ['Dashboard', 'dashboard', '/leader/dashboard'],
                ],
            ],
            ['title' => 'Manajemen',
                'items' => [
                    ['Data Cabang', 'building', '/leader/branches'],
                    ['Admin Cabang', 'shield-user', '/leader/admins'],
                ],
            ],
            ['title' => 'Akademik',
                'items' => [
                    ['Data Santri', 'graduation-cap', '/leader/students'],
                    ['Data Guru', 'users', '/leader/teachers'],
                    ['Presensi', 'calendar-check', '/leader/attendance'],
                    ['Penilaian', 'clipboard-check', '/leader/assessments'],
                    ['Rapor', 'file-text', '/leader/reports'],
                ],
            ],
            ['title' => 'Sistem',
                'items' => [
                    ['Pengaturan', 'settings', '/leader/settings'],
                ],
            ],
        ],

        //admin
        'admin' => [
            ['title' => null,
                'items' => [
                    ['Dashboard', 'dashboard', '/admin/dashboard'],
                ],
            ],
            ['title' => 'Data Master',
                'items' => [
                    ['Wali Santri', 'heart-hand', '/admin/guardians'],
                    ['Santri', 'graduation-cap', '/admin/students'],
                    ['Guru', 'users', '/admin/teachers'],
                    ['Kelompok', 'layers', '/admin/groups'],
                    ['Mata Pelajaran', 'book-open', '/admin/subjects'],
                    ['Periode', 'calendar-clock', '/admin/periods'],
                ],
            ],
            ['title' => 'Akademik',
                'items' => [
                    ['Jadwal', 'calendar-days', '/admin/schedules'],
                    ['Presensi', 'calendar-check', '/admin/attendance'],
                    ['Penilaian', 'clipboard-check', '/admin/assessments'],
                    ['Rapor', 'file-text', '/admin/reports'],
                ],
            ],
            ['title' => 'Sistem',
                'items' => [
                    ['Pengaturan', 'settings', '/admin/settings'],
                ],
            ],
        ],

        //guru
        'teacher' => [
            ['title' => null,
                'items' => [
                    ['Dashboard', 'dashboard', '/teachers/dashboard'],
                ],
            ],
            ['title' => 'Akademik',
                'items' => [
                    ['Santri Saya', 'graduation-cap', '/teachers/students'],
                    ['Jadwal', 'calendar-days', '/teachers/schedules'],
                    ['Presensi', 'calendar-check', '/teachers/attendance'],
                    ['Penilaian', 'clipboard-check', '/teachers/assessments'],
                    ['Rapor', 'file-text', '/teachers/reports'],
                ],
            ],
            ['title' => 'Sistem',
                'items' => [
                    ['Pengaturan', 'settings', '/teachers/settings'],
                ],
            ],
        ],

        //wali santri
        'guardian' => [
            ['title' => null,
                'items' => [
                    ['Dashboard', 'dashboard', '/guardians/dashboard'],
                ],
            ],
            ['title' => 'Informasi Akademik',
                'items' => [
                    ['Data Santri', 'users', '/guardians/students'],
                    ['Jadwal', 'calendar-days', '/guardians/schedules'],
                    ['Presensi', 'calendar-check', '/guardians/attendance'],
                    ['Penilaian', 'book-open', '/guardians/assessments'],
                    ['Rapor', 'file-text', '/guardians/reports'],
                ],
            ],
            ['title' => 'Sistem',
                'items' => [
                    ['Pengaturan', 'settings', '/guardians/settings'],
                ],
            ],
        ],
    ]
    [$role] ?? [];
@endphp

<aside id="dashboard-sidebar" class="fixed inset-y-0 left-0 z-30 w-72 -translate-x-full border-r border-slate-200 bg-white shadow-xl transition-transform duration-200 ease-out lg:translate-x-0 lg:shadow-none">
    <div class="flex h-full flex-col">
        <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-4 lg:px-5 lg:py-5">
            <div class="flex min-w-0 items-center gap-3">
                <img src="{{ asset('logo-sahadat-qu.jpeg') }}" alt="Sahadat-Qu" class="h-10 w-10 rounded-full object-cover lg:h-11 lg:w-11">
                @php
                    $user = auth()->user();

                    $roleName = match ($user->role) {
                        'leader' => 'Ketua Lembaga',
                        'admin' => 'Admin',
                        'teacher' => 'Guru',
                        'guardian' => 'Wali Santri',
                        default => ucfirst($user->role),
                    };

                    $branchName = $user->branch?->name ?? '-';
                @endphp

                <div class="min-w-0">
                    <p class="truncate font-semibold text-slate-950">
                        {{ $user->name }}
                    </p>

                    <p class="truncate text-xs text-slate-500">
                        {{ $roleName }} - {{ $branchName }}
                    </p>
                </div>
            </div>
            <button id="sidebar-close" type="button" class="inline-flex h-9 w-9 shrink-0 cursor-pointer items-center justify-center rounded-md border border-slate-300 bg-white text-slate-600 hover:border-blue-950 hover:text-blue-950 lg:hidden" aria-label="Tutup menu">
                <x-icon name="x" />
            </button>
        </div>

        <nav class="flex flex-1 flex-col overflow-y-auto px-4 py-4">
            @foreach ($menus as $group)
                @if(!empty($group['title']))
                    <div class="mt-4 mb-2 border-t border-slate-200 pt-4 first:mt-0 first:border-t-0 first:pt-0">
                        <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            {{ $group['title'] }}
                        </p>
                    </div>
                @endif
                @foreach ($group['items'] as [$label, $icon, $url])
                    @php
                        $path = ltrim($url, '/');
                        $isActive = request()->is($path) || request()->is($path . '/*');
                    @endphp
                    <a href="{{ url($url) }}" class="mb-1 flex min-h-10 items-center gap-3 rounded-md px-3 py-2 text-sm font-medium
                        {{ $isActive
                                ? 'bg-[#069688] text-white'
                                : 'text-slate-700 hover:bg-slate-100 hover:text-blue-950' }}">
                        <x-icon :name="$icon" class="h-4 w-4 shrink-0" />
                        <span>{{ $label }}</span>
                    </a>
                @endforeach
            @endforeach
        </nav>

        <div class="border-t border-slate-200 p-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex min-h-10 w-full cursor-pointer items-center justify-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:border-[#069688] hover:text-blue-950">
                    Logout
                </button>
            </form>
        </div>
    </div>
</aside>
