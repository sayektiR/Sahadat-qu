<x-layouts.dashboard title="Data Cabang" description="Lihat semua data cabang Sahadat-Qu.">
    @if ($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="space-y-6">
        <div class="flex justify-end">
            <form method="GET" action="{{ route('leader.branches') }}" class="grid w-full gap-2 sm:grid-cols-2 xl:w-auto xl:grid-cols-[200px_160px_auto]" id="filterForm">
                <div class="relative">
                    <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    <input name="search" value="{{ request('search') }}" placeholder="Cari disini" oninput="submitFilter()" class="h-9 w-full rounded-md border border-slate-300 bg-white pl-9 pr-3 text-xs outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <div class="flex gap-2">
                    {{-- <button type="submit" class="h-9 cursor-pointer rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-700 hover:border-blue-950 hover:text-blue-950">Filter</button> --}}
                    <a onclick="openDialog('create-branch')" class="inline-flex h-9 cursor-pointer items-center gap-2 rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-800 hover:border-blue-950 hover:text-blue-950">
                        <x-icon name="plus" />
                        Tambah Cabang
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
            <table class="w-full min-w-[800px] text-left">
                <thead>
                    <tr class="bg-white text-sm text-slate-950">
                        <th class="px-6 py-5 font-bold">No.</th>
                        <th class="px-6 py-5 font-bold">Nama Cabang</th>
                        <th class="px-6 py-5 font-bold">Alamat</th>
                        <th class="px-6 py-5 font-bold">Telepon</th>
                        <th class="px-6 py-5 font-bold">Admin</th>
                        <th class="px-6 py-5 font-bold">Santri</th>
                        <th class="px-6 py-5 font-bold">Guru</th>
                        <th class="px-6 py-5 font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($branches as $branch)
                        <tr class="{{ $loop->odd ? 'bg-slate-100' : 'bg-white' }}">
                            <td class="px-6 py-5 text-sm">{{ $branches->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-5 text-sm font-medium">{{ $branch->name }}</td>
                            <td class="px-6 py-5 text-sm">{{ $branch->address ?: '-' }}</td>
                            <td class="px-6 py-5 text-sm">{{ $branch->phone ?: '-' }}</td>
                            <td class="px-6 py-5 text-sm">{{ $branch->users_count }}</td>
                            <td class="px-6 py-5 text-sm">{{ $branch->students_count }}</td>
                            <td class="px-6 py-5 text-sm">{{ $branch->teachers_count }}</td>
                            <td class="px-6 py-5">
                                <a href="{{ route('leader.branches.show', $branch) }}" class="cursor-pointer text-blue-500 hover:text-blue-700" aria-label="Lihat detail"><x-icon name="eye" /></a>
                                <button
                                    type="button"
                                    onclick="openDialog('edit-branch-{{ $branch->id }}')"
                                    class="text-yellow-500 hover:text-yellow-700">
                                    <x-icon name="pencil"/>
                                </button>
                                <form method="POST"
                                    action="{{ route('leader.branches.destroy', $branch) }}"
                                    class="delete-form inline">
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="button"
                                        class="delete-btn text-red-600 hover:text-red-700">
                                        <x-icon name="trash" />
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada data cabang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $branches->links() }}
    </section>
    @include('leader.branches.partials.create')
    @include('leader.branches.partials.edit')

    <script>
        function openDialog(id){
            document.getElementById(id)?.showModal();
        }

        function closeDialog(button){
            button.closest('dialog')?.close();
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.querySelectorAll('.delete-btn').forEach(button => {

            button.addEventListener('click', function () {

                const form = this.closest('.delete-form');

                Swal.fire({
                    title: 'Hapus Cabang?',
                    text: 'Data yang dihapus tidak dapat dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });

            });

        });
    </script>
</x-layouts.dashboard>
