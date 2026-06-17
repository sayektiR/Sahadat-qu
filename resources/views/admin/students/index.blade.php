<x-layouts.dashboard title="Data Santri" description="Kelola data santri berdasarkan kelompok dan wali santri.">
    @if ($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="space-y-6">
        <div class="flex justify-end">
            <form method="GET" action="{{ route('admin.students') }}" class="grid w-full gap-2 sm:grid-cols-2 xl:w-auto xl:grid-cols-[200px_160px_auto]">
                <div class="relative">
                    <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    <input name="search" value="{{ request('search') }}" placeholder="Cari disini" class="h-9 w-full rounded-md border border-slate-300 bg-white pl-9 pr-3 text-xs outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <select name="group_id" class="h-9 rounded-md border border-slate-300 bg-white px-3 text-xs text-slate-600 outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    <option value="">Pilih Kelompok</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}" @selected((string) request('group_id') === (string) $group->id)>{{ $group->name }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="h-9 cursor-pointer rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-700 hover:border-blue-950 hover:text-blue-950">Filter</button>
                    <a href="{{ route('admin.students.create') }}" class="inline-flex h-9 cursor-pointer items-center gap-2 rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-800 hover:border-blue-950 hover:text-blue-950">
                        <x-icon name="plus" />
                        Tambah Santri
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
            <table class="w-full min-w-[960px] text-left">
                <thead>
                    <tr class="bg-white text-sm text-slate-950">
                        <th class="px-6 py-5 font-bold">No.</th>
                        <th class="px-6 py-5 font-bold">Nama Santri</th>
                        <th class="px-6 py-5 font-bold">NIS</th>
                        <th class="px-6 py-5 font-bold">Wali Santri</th>
                        <th class="px-6 py-5 font-bold">Kelompok</th>
                        <th class="px-6 py-5 font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $student)
                        <tr class="{{ $loop->odd ? 'bg-slate-100' : 'bg-white' }}">
                            <td class="px-6 py-5 text-sm">{{ $students->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-5 text-sm font-medium">{{ $student->name }}</td>
                            <td class="px-6 py-5 text-sm">{{ $student->nis ?: '-' }}</td>
                            <td class="px-6 py-5 text-sm">{{ $student->guardian?->name ?: '-' }}</td>
                            <td class="px-6 py-5 text-sm">{{ $student->group?->name ?: '-' }}</td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <button type="button" onclick="openDialog('view-student-{{ $student->id }}')" class="cursor-pointer text-slate-900 hover:text-blue-950" aria-label="Lihat santri"><x-icon name="eye" /></button>
                                    <a href="{{ route('admin.students.edit', $student) }}" class="cursor-pointer text-slate-900 hover:text-blue-950" aria-label="Edit santri"><x-icon name="pencil" /></a>
                                    <form method="POST" action="{{ route('admin.students.destroy', $student) }}" onsubmit="return confirm('Hapus santri ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="cursor-pointer text-slate-900 hover:text-red-600" aria-label="Hapus santri"><x-icon name="trash" /></button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <x-admin.student-dialogs :student="$student" :groups="$groups" :guardians="$guardiansForForm" />
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500">Data santri belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $students->links() }}
    </section>

    <script>
        function openDialog(id) {
            document.getElementById(id)?.showModal();
        }

        function closeDialog(button) {
            button.closest('dialog')?.close();
        }
    </script>
</x-layouts.dashboard>
