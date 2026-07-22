<x-layouts.dashboard title="Dashboard" description="Ringkasan data seluruh cabang Sahadat-Qu.">
    <section class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <a href="{{ route('leader.branches') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm hover:border-[#069688]">
                <p class="text-xs font-semibold uppercase text-slate-500">Cabang</p>
                <p class="mt-2 text-2xl font-bold text-blue-950">{{ $stats['branches'] }} Cabang</p>
            </a>
            <a href="{{ route('leader.admins') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm hover:border-[#069688]">
                <p class="text-xs font-semibold uppercase text-slate-500">Admin Cabang</p>
                <p class="mt-2 text-2xl font-bold text-blue-950">{{ $stats['admins'] }} Admin</p>
            </a>
            <a href="{{ route('leader.students') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm hover:border-[#069688]">
                <p class="text-xs font-semibold uppercase text-slate-500">Total Santri</p>
                <p class="mt-2 text-2xl font-bold text-blue-950">{{ $stats['students'] }} Santri</p>
            </a>
            <a href="{{ route('leader.teachers') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm hover:border-[#069688]">
                <p class="text-xs font-semibold uppercase text-slate-500">Total Guru</p>
                <p class="mt-2 text-2xl font-bold text-blue-950">{{ $stats['teachers'] }} Guru</p>
            </a>
        </div>

        <div class="grid items-start gap-5 xl:grid-cols-3">
            <article class="self-start rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-950">Cabang Terbaru</h2>
                    <a href="{{ route('leader.branches') }}" class="text-sm font-semibold text-blue-950 hover:underline">Lihat semua</a>
                </div>
                <div class="overflow-x-auto rounded-lg border border-slate-200">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-white text-sm text-slate-950">
                                <th class="px-4 py-3 font-bold">Nama Cabang</th>
                                <th class="px-4 py-3 font-bold">Santri</th>
                                <th class="px-4 py-3 font-bold">Guru</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentBranches as $branch)
                                <tr class="{{ $loop->odd ? 'bg-slate-100' : 'bg-white' }}">
                                    <td class="px-4 py-3 text-sm font-medium">{{ $branch->name }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $branch->students_count }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $branch->teachers_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada cabang.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="self-start rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-950">Penilaian Terbaru</h2>
                    <a href="{{ route('leader.assessments') }}" class="text-sm font-semibold text-blue-950 hover:underline">Lihat semua</a>
                </div>
                <div class="space-y-3">
                    @forelse ($recentAssessments as $assessment)
                        <a href="{{ route('leader.assessments.show', $assessment) }}" class="block rounded-lg border border-slate-200 bg-slate-50 p-4 hover:border-[#069688] hover:bg-[#069688] hover:text-white">
                            <div class="flex items-center justify-between gap-3">
                                <p class="font-semibold text-slate-950">{{ $assessment->student?->name ?: '-' }}</p>
                                <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">{{ ucfirst($assessment->assessment_type) }}</span>
                            </div>
                            <p class="mt-1 text-sm text-slate-500">{{ $assessment->branch?->name ?: '-' }} | {{ $assessment->teacher?->name ?: '-' }}</p>
                            <p class="mt-2 text-sm font-semibold text-blue-950">Nilai {{ number_format((float) $assessment->final_score, 1) }}</p>
                        </a>
                    @empty
                        <p class="rounded-lg border border-dashed border-slate-300 p-4 text-sm text-slate-500">Belum ada penilaian.</p>
                    @endforelse
                </div>
            </article>

            <article class="self-start rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-bold text-slate-950">Akses Cepat</h2>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <a href="{{ route('leader.attendance') }}" class="flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4 hover:border-[#069688] hover:bg-[#069688] hover:text-white">
                        <x-icon name="calendar-check" class="h-5 w-5" />
                        <span class="text-sm font-semibold">Presensi</span>
                    </a>
                    <a href="{{ route('leader.assessments') }}" class="flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4 hover:border-[#069688] hover:bg-[#069688] hover:text-white">
                        <x-icon name="clipboard-check" class="h-5 w-5" />
                        <span class="text-sm font-semibold">Penilaian</span>
                    </a>
                    <a href="{{ route('leader.reports') }}" class="flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4 hover:border-[#069688] hover:bg-[#069688] hover:text-white">
                        <x-icon name="file-text" class="h-5 w-5" />
                        <span class="text-sm font-semibold">Rapor</span>
                    </a>
                    <a href="{{ route('leader.admins.create') }}" class="flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4 hover:border-[#069688] hover:bg-[#069688] hover:text-white">
                        <x-icon name="shield-user" class="h-5 w-5" />
                        <span class="text-sm font-semibold">Tambah Admin</span>
                    </a>
                </div>
            </article>
        </div>
    </section>
</x-layouts.dashboard>
