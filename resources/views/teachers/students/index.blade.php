<x-layouts.dashboard title="Santri Saya" description="Pantau santri berdasarkan kelompok yang diampu.">
    @if ($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <section class="space-y-6">
        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-slate-500">Guru</p>
                <p class="mt-2 text-2xl font-bold text-blue-950">{{ $teacher->name }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-slate-500">Kelompok</p>
                <p class="mt-2 text-2xl font-bold text-blue-950">{{ $groups->count() }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-slate-500">Santri Diampu</p>
                <p class="mt-2 text-2xl font-bold text-blue-950">{{ $totalStudents }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-slate-500">Rata-rata Nilai</p>
                <p class="mt-2 text-2xl font-bold text-blue-950">{{ $averageScore ? number_format($averageScore, 1) : '-' }}</p>
            </div>
        </div>

       <div class="flex justify-end">
            <form method="GET" id="filterForm" action="{{ route('teachers.students') }}" class="w-full md:w-72">
                <div class="relative">
                    <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    <input type="text" name="search" value="{{ request('search') }}" oninput="submitFilter()" placeholder="Cari santri" class="h-9 w-full rounded-md border border-slate-300 bg-white pl-9 pr-3 text-xs outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>

            </form>
        </div>

        <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
            <table class="w-full min-w-[980px] text-left">
                <thead>
                    <tr class="bg-white text-sm text-slate-950">
                        <th class="px-6 py-5 font-bold">No.</th>
                        <th class="px-6 py-5 font-bold">Nama Santri</th>
                        <th class="px-6 py-5 font-bold">NIS</th>
                        <th class="px-6 py-5 font-bold">Kelompok</th>
                        <th class="px-6 py-5 font-bold">Wali Santri</th>
                        <th class="px-6 py-5 font-bold">Presensi</th>
                        <th class="px-6 py-5 font-bold">Penilaian</th>
                        <th class="px-6 py-5 font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $student)
                        <tr class="{{ $loop->odd ? 'bg-slate-100' : 'bg-white' }}">
                            <td class="px-6 py-5 text-sm">{{ $students->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-5 text-sm font-medium">{{ $student->name }}</td>
                            <td class="px-6 py-5 text-sm">{{ $student->nis ?: '-' }}</td>
                            <td class="px-6 py-5 text-sm">{{ $student->group?->name ?: '-' }}</td>
                            <td class="px-6 py-5 text-sm">{{ $student->guardian?->name ?: '-' }}</td>
                            <td class="px-6 py-5 text-sm">{{ $student->attendance_count }} data</td>
                            <td class="px-6 py-5 text-sm">{{ $student->assessment_count }} nilai</td>
                            <td class="px-6 py-5">
                                <button type="button" onclick="openDialog('view-student-{{ $student->id }}')" class="cursor-pointer text-blue-500 hover:text-blue-700" aria-label="Lihat santri"><x-icon name="eye" /></button>
                            </td>
                        </tr>

                        <dialog id="view-student-{{ $student->id }}" class="management-dialog w-full max-w-xl rounded-lg border border-slate-200 p-0 shadow-xl">
                            <div class="bg-white">
                                <div class="border-b border-slate-200 px-6 py-4">
                                    <h3 class="text-lg font-semibold text-slate-950">Detail Santri</h3>
                                </div>
                                @if ($student->photo)
                                    <div class="border-b border-slate-200 px-6 py-5">
                                        <img src="{{ asset('storage/' . $student->photo) }}" alt="Foto {{ $student->name }}" class="h-28 w-28 rounded-md object-cover">
                                    </div>
                                @endif
                                <dl class="grid gap-4 p-6 text-sm sm:grid-cols-2">
                                    <div><dt class="font-semibold text-slate-500">Nama</dt><dd class="mt-1">{{ $student->name }}</dd></div>
                                    <div><dt class="font-semibold text-slate-500">NIS</dt><dd class="mt-1">{{ $student->nis ?: '-' }}</dd></div>
                                    <div><dt class="font-semibold text-slate-500">Kelompok</dt><dd class="mt-1">{{ $student->group?->name ?: '-' }}</dd></div>
                                    <div><dt class="font-semibold text-slate-500">Wali</dt><dd class="mt-1">{{ $student->guardian?->name ?: '-' }}</dd></div>
                                    <div><dt class="font-semibold text-slate-500">Status</dt><dd class="mt-1">{{ $student->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}</dd></div>
                                    <div><dt class="font-semibold text-slate-500">Jenis Kelamin</dt><dd class="mt-1">{{ $student->gender === 'female' ? 'Perempuan' : ($student->gender === 'male' ? 'Laki-laki' : '-') }}</dd></div>
                                    <div><dt class="font-semibold text-slate-500">Tempat Lahir</dt><dd class="mt-1">{{ $student->birth_place ?: '-' }}</dd></div>
                                    <div><dt class="font-semibold text-slate-500">Tanggal Lahir</dt><dd class="mt-1">{{ $student->birth_date?->format('d M Y') ?: '-' }}</dd></div>
                                    <div class="sm:col-span-2"><dt class="font-semibold text-slate-500">Alamat</dt><dd class="mt-1">{{ $student->address ?: '-' }}</dd></div>
                                </dl>
                                <div class="flex justify-end border-t border-slate-200 px-6 py-4">
                                    <button type="button" onclick="closeDialog(this)" class="cursor-pointer rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Tutup</button>
                                </div>
                            </div>
                        </dialog>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-sm text-slate-500">Belum ada santri pada kelompok yang diampu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $students->links() }}
    </section>

    <script>
        let timer;

        function submitFilter() {
            clearTimeout(timer);

            timer = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 500); 
        }
    </script>

    <script>
        function openDialog(id) {
            document.getElementById(id)?.showModal();
        }

        function closeDialog(button) {
            button.closest('dialog')?.close();
        }
    </script>
</x-layouts.dashboard>
