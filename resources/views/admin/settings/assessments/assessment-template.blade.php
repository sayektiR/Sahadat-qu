<x-layouts.dashboard title="Template Penilaian" description="Kelola template penilaian yang digunakan pada proses evaluasi santri.">
    @if ($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <nav class="flex items-center gap-2 text-sm">
        <a href="{{ route('admin.settings') }}">Pengaturan</a>
        <span>/</span>
        <span class="font-semibold">Template Penilaian</span>
    </nav>

    <section class="space-y-6">
        <div class="flex justify-end">
            <form method="GET" action="#" class="flex items-center gap-3">
                <div class="relative w-64">
                    <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />

                    <input
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari disini"
                        class="h-9 w-full rounded-md border border-slate-300 bg-white pl-9 pr-3 text-xs outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>

                <button
                    type="submit"
                    class="h-9 rounded-md border border-slate-300 bg-white px-4 text-xs font-semibold">
                    Filter
                </button>

                <button
                    type="button"
                    onclick="openDialog('create-assessment')"
                    class="inline-flex h-9 items-center gap-2 rounded-md border border-slate-300 bg-white px-4 whitespace-nowrap text-xs font-semibold">
                    <x-icon name="plus" />
                    Tambah Penilaian
                </button>
            </form>
        </div>

        <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
            <table class="w-full min-w-[900px] text-left">
                <thead>
                    <tr class="bg-white text-sm text-slate-950">
                        <th class="px-6 py-4 font-bold">No.</th>
                        <th class="px-6 py-4 font-bold">Nama Penilaian</th>
                        <th class="px-6 py-4 font-bold">Detail</th>
                        <th class="px-6 py-4 font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                    @forelse ($assessmentTemplates as $index => $assessmentTemplate)
                        <tr>
                            <td class="px-6 py-4">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">{{ $assessmentTemplate->name }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.settings.assessments.assessment-template.attributes', $assessmentTemplate) }}" class="text-blue-950 hover:underline">Atribut Penilaian</a> | <a href="{{ route('admin.settings.assessments.assessment-template.aspects', $assessmentTemplate) }}" class="text-blue-950 hover:underline">Aspek Penilaian</a>
                            </td>
                            <td class="px-6 py-4">
                                <button type="button" onclick="openDialog('edit-assessment-template-{{ $assessmentTemplate->id }}')" class="cursor-pointer text-yellow-500 hover:text-yellow-700"aria-label="Edit template penilaian"><x-icon name="pencil" /></button>
                                <form method="POST" action="{{ route('admin.settings.assessments.assessment-template.destroy', $assessmentTemplate) }}" class="delete-form inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="delete-btn text-red-600 hover:text-red-700">
                                        <x-icon name="trash" />
                                    </button>
                                </form>
                            </td>
                        </tr>

                        @foreach($assessmentTemplates as $index => $assessmentTemplate)
                        <dialog id="edit-assessment-template-{{ $assessmentTemplate->id }}" class="management-dialog w-full max-w-lg rounded-lg border border-slate-200 p-0 shadow-xl">
                            <form method="POST" action="{{ route('admin.settings.assessments.assessment-template.update', $assessmentTemplate) }}" class="bg-white">
                            @csrf
                            @method('PUT')

                                <div class="border-b border-slate-200 px-6 py-4">
                                    <h3 class="text-lg font-semibold text-slate-950">Edit Penilaian</h3>
                                </div>

                                <div class="p-6">
                                    <label class="block text-sm font-semibold text-slate-900">
                                        Nama Penilaian
                                    </label>

                                    <input
                                        type="text"
                                        name="name"
                                        value="{{ $assessmentTemplate->name }}"
                                        required
                                        class="mt-2 h-11 w-full rounded-md border border-slate-300 px-3 text-sm">
                                </div>

                                <div class="flex justify-end gap-2 border-t border-slate-200 px-6 py-4">
                                    <button type="button"
                                        onclick="closeDialog(this)"
                                        class="rounded-md border border-slate-300 px-4 py-2 text-sm">
                                        Batal
                                    </button>

                                    <button type="submit"
                                        class="rounded-md bg-[#0B8C79]/[80%] px-4 py-2 text-sm text-white hover:bg-[#0B8C79]">
                                        Simpan
                                    </button>
                                </div>
                            </form>
                    </dialog>
                    @endforeach

                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-slate-500">Tidak ada data penilaian.</td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

    </section>

    <dialog id="create-assessment" class="management-dialog w-full max-w-lg rounded-lg border border-slate-200 p-0 shadow-xl">
        <form method="POST" action="{{ route('admin.settings.assessments.assessment-template.store') }}" class="bg-white">
            @csrf
            <input type="hidden" name="_form" value="create">

            <div class="border-b border-slate-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-slate-950">Tambah Penilaian</h3>
                <p class="mt-1 text-sm text-slate-500">Tambahkan penilaian baru.</p>
            </div>
            <div class="space-y-5 p-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-900">Nama Penilaian</label>
                    <input name="name" value="{{ old('_form') === 'create' ? old('name') : '' }}" required placeholder="Masukkan nama penilaian" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-slate-200 px-6 py-4">
                <button type="button" onclick="closeDialog(this)" class="cursor-pointer rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Batal</button>
                <button type="submit" class="cursor-pointer rounded-md bg-[#0B8C79] px-4 py-2 text-sm font-semibold text-white hover:bg-[#0B8C79]/[80%]">Simpan</button>
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
            openDialog('create-assessment');
        @endif

        @if ($errors->any() && old('_form') === 'edit' && old('_assessment_id'))
            openDialog('edit-assessment-{{ old('_assessment_id') }}');
        @endif
    </script>

    <script>
        document.querySelectorAll('.delete-btn').forEach(button => {

            button.addEventListener('click', function () {

                const form = this.closest('.delete-form');

                Swal.fire({
                    title: 'Hapus Penilaian?',
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
