<x-layouts.dashboard title="Mata Pelajaran" description="Kelola daftar mata pelajaran yang digunakan pada jadwal dan penilaian.">
    @if ($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="space-y-6">

        <div class="flex justify-end">
            <form method="GET" action="{{ route('admin.subjects.index') }}"
                class="grid w-full gap-2 sm:w-auto sm:grid-cols-[220px_auto_auto]">

                <div class="relative">
                    <x-icon name="search"
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />

                    <input
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari mata pelajaran"
                        class="h-9 w-full rounded-md border border-slate-300 bg-white pl-9 pr-3 text-xs outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>

                <button
                    type="submit"
                    class="h-9 cursor-pointer rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-700 hover:border-blue-950 hover:text-blue-950">
                    Filter
                </button>

                <button
                    type="button"
                    onclick="openDialog('create-subject')"
                    class="inline-flex h-9 cursor-pointer items-center gap-2 rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-800 hover:border-blue-950 hover:text-blue-950">
                    <x-icon name="plus" />
                    Tambah Mata Pelajaran
                </button>

            </form>
        </div>

        <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
            <table class="w-full min-w-[850px] text-left">

                <thead>
                    <tr class="bg-white text-sm text-slate-950">
                        <th class="px-6 py-5 font-bold">No.</th>
                        <th class="px-6 py-5 font-bold">Nama Mata Pelajaran</th>
                        <th class="px-6 py-5 font-bold">Deskripsi</th>
                        <th class="px-6 py-5 font-bold">Digunakan</th>
                        <th class="px-6 py-5 font-bold">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($subjects as $subject)

                        <tr class="{{ $loop->odd ? 'bg-slate-100' : 'bg-white' }}">

                            <td class="px-6 py-5 text-sm">
                                {{ $subjects->firstItem() + $loop->index }}
                            </td>

                            <td class="px-6 py-5 text-sm font-medium">
                                {{ $subject->name }}
                            </td>

                            <td class="px-6 py-5 text-sm">
                                {{ $subject->description ?: '-' }}
                            </td>

                            <td class="px-6 py-5 text-sm">
                                {{ $subject->schedule_details_count }} Jadwal
                            </td>

                            <td class="px-6 py-5">

                                <div class="flex items-center gap-4">

                                    <button
                                        type="button"
                                        onclick="openDialog('edit-subject-{{ $subject->id }}')"
                                        class="cursor-pointer text-yellow-500 hover:text-yellow-700">

                                        <x-icon name="pencil" />

                                    </button>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.subjects.destroy', $subject) }}"
                                        class="delete-form">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="button"
                                            class="delete-btn text-red-600 hover:text-red-700">

                                            <x-icon name="trash" />

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                        <dialog
                            id="edit-subject-{{ $subject->id }}"
                            class="management-dialog w-full max-w-xl rounded-lg border border-slate-200 p-0 shadow-xl">

                            @include('admin.subjects.partials.form',[
                                'subject'=>$subject,
                                'mode'=>'edit'
                            ])

                        </dialog>

                    @empty

                        <tr>
                            <td colspan="5"
                                class="px-6 py-10 text-center text-sm text-slate-500">
                                Belum ada data mata pelajaran.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>
        </div>

        {{ $subjects->links() }}

    </section>

    <dialog
        id="create-subject"
        class="management-dialog w-full max-w-xl rounded-lg border border-slate-200 p-0 shadow-xl">

        @include('admin.subjects.partials.form',[
            'subject'=>null,
            'mode'=>'create'
        ])

    </dialog>

    <script>

        function openDialog(id){
            document.getElementById(id)?.showModal();
        }

        function closeDialog(button){
            button.closest('dialog')?.close();
        }

        @if($errors->any() && old('_form')==='create')
            openDialog('create-subject');
        @endif

        @if($errors->any() && old('_form')==='edit' && old('_subject_id'))
            openDialog('edit-subject-{{ old('_subject_id') }}');
        @endif

    </script>

    <script>

        document.querySelectorAll('.delete-btn').forEach(button=>{

            button.addEventListener('click',function(){

                const form=this.closest('.delete-form');

                Swal.fire({
                    title:'Hapus Mata Pelajaran?',
                    text:'Data yang dihapus tidak dapat dikembalikan.',
                    icon:'warning',
                    showCancelButton:true,
                    confirmButtonText:'Ya, Hapus',
                    cancelButtonText:'Batal',
                    reverseButtons:true,
                    confirmButtonColor:'#dc2626',
                    cancelButtonColor:'#64748b'
                }).then((result)=>{

                    if(result.isConfirmed){
                        form.submit();
                    }

                });

            });

        });

    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</x-layouts.dashboard>