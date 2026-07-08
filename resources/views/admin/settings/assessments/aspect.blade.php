<x-layouts.dashboard title="Aspek Penilaian" description="Kelola aspek penilaian yang digunakan pada proses evaluasi santri.">
    @if ($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <nav class="flex items-center gap-2 text-sm">
        <a href="{{ route('admin.settings') }}">Pengaturan</a>

        <span>/</span>

        <a href="{{ route('admin.settings.assessments.assessment-template') }}">
            Template Penilaian
        </a>

        <span>/</span>

        <span class="font-semibold">
            Aspek Penilaian
        </span>
    </nav>

    <section class="space-y-6">
        <div class="flex justify-end">
            <form method="GET" action="#" class="grid w-full gap-2 sm:grid-cols-2 xl:w-auto xl:grid-cols-[200px_160px_auto]">
                <div class="relative">
                    <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    <input name="search" value="{{ request('search') }}" placeholder="Cari disini" class="h-9 w-full rounded-md border border-slate-300 bg-white pl-9 pr-3 text-xs outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="h-9 cursor-pointer rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-700 hover:border-blue-950 hover:text-blue-950">Filter</button>
                    <button type="button" onclick="openDialog('create-aspect')" class="inline-flex h-9 cursor-pointer items-center gap-2 rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-800 hover:border-blue-950 hover:text-blue-950">
                        <x-icon name="plus" />
                        Tambah Aspek
                    </button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
            <table class="w-full min-w-[900px] text-left">
                <thead>
                    <tr class="bg-white text-sm text-slate-950">
                        <th class="px-6 py-4 font-bold">No.</th>
                        <th class="px-6 py-4 font-bold">Nama Aspek</th>
                        <th class="px-6 py-4 font-bold">Bobot</th>
                        <th class="px-6 py-4 font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                    @forelse ($aspects as $index => $aspect)
                        <tr>
                            <td class="px-6 py-4">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">{{ $aspect->aspect_name }}</td>
                            <td class="px-6 py-4">{{ $aspect->weight }}</td>
                            <td class="px-6 py-4">
                                <button type="button" onclick="openDialog('edit-aspect-{{ $aspect->id }}')" class="cursor-pointer text-yellow-500 hover:text-yellow-700" aria-label="Edit aspek"><x-icon name="pencil" /></button>
                                <form method="POST" action="{{ route('admin.settings.assessments.assessment-template.aspects.destroy', [$assessmentTemplate, $aspect]) }}" class="delete-form inline">
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

                        @foreach($aspects as $aspect)
                        <dialog id="edit-aspect-{{ $aspect->id }}" class="management-dialog w-full max-w-lg rounded-lg border border-slate-200 p-0 shadow-xl">
                            <form method="POST" action="{{ route('admin.settings.assessments.assessment-template.aspects.update', [$assessmentTemplate, $aspect]) }}">
                                @csrf
                                @method('PUT')

                                <div class="space-y-5 p-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-900">Nama Aspek</label>
                                        <input name="aspect_name" value="{{ old('_form') === 'edit' && old('_aspect_id') == $aspect->id ? old('aspect_name') : $aspect->aspect_name }}" required placeholder="Masukkan nama aspek" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-900">Bobot</label>
                                        <input name="weight" type="number" value="{{ old('_form') === 'edit' && old('_aspect_id') == $aspect->id ? old('weight') : $aspect->weight }}" required placeholder="Masukkan bobot" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2 border-t border-slate-200 px-6 py-4">
                                    <button type="button" onclick="closeDialog(this)" class="cursor-pointer rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Batal</button>
                                    <button type="submit" class="cursor-pointer rounded-md bg-[#0B8C79]/[80%] px-4 py-2 text-sm font-semibold text-white hover:bg-[#0B8C79]">Simpan</button>
                                </div>
                            </form>
                        </dialog>
                        @endforeach

                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-slate-500">Tidak ada data atribut.</td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

    </section>

    <dialog id="create-aspect" class="management-dialog w-full max-w-lg rounded-lg border border-slate-200 p-0 shadow-xl">
        <form method="POST" action="{{ route('admin.settings.assessments.assessment-template.aspects.store', $assessmentTemplate) }}" class="bg-white">
            @csrf
            <input type="hidden" name="_form" value="create">

            <div class="border-b border-slate-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-slate-950">Tambah Aspek</h3>
                <p class="mt-1 text-sm text-slate-500">Tambahkan aspek baru.</p>
            </div>
            <div class="space-y-5 p-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-900">Nama Aspek</label>
                    <input name="aspect_name" value="{{ old('_form') === 'create' ? old('aspect_name') : '' }}" required placeholder="Masukkan nama aspek" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-900">Bobot</label>
                    <input name="weight" type="number" value="{{ old('_form') === 'create' ? old('weight') : '' }}" required placeholder="Masukkan bobot" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-slate-200 px-6 py-4">
                <button type="button" onclick="closeDialog(this)" class="cursor-pointer rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Batal</button>
                <button type="submit" class="cursor-pointer rounded-md bg-[#0B8C79]/[80%] px-4 py-2 text-sm font-semibold text-white hover:bg-[#0B8C79]">Simpan</button>
            </div>
        </form>
    </dialog>



    <script>
        function openDialog(id) {
            document.getElementById(id)?.showModal();
        }

        function closeDialog(button) {
            button.closest('dialog')?.close();
        }

        @if ($errors->any() && old('_form') === 'create')
            openDialog('create-aspect');
        @endif
        
    </script>

    <script>
        document.querySelectorAll('.delete-btn').forEach(button => {

            button.addEventListener('click', function () {

                const form = this.closest('.delete-form');

                Swal.fire({
                    title: 'Hapus Aspek?',
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</x-layouts.dashboard>
