@php
    $isEdit = $mode === 'edit';
    $action = $isEdit
        ? route('admin.subjects.update', $subject)
        : route('admin.subjects.store');

    $formMatches = old('_form') === $mode &&
        (! $isEdit || (string) old('_subject_id') === (string) $subject?->id);
@endphp

<form method="POST" action="{{ $action }}" class="bg-white">
    @csrf

    @if ($isEdit)
        @method('PUT')
        <input type="hidden" name="_subject_id" value="{{ $subject->id }}">
    @endif

    <input type="hidden" name="_form" value="{{ $mode }}">

    <div class="border-b border-slate-200 px-6 py-4">
        <h3 class="text-lg font-semibold text-slate-950">
            {{ $isEdit ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran' }}
        </h3>
        <p class="mt-1 text-sm text-slate-500">
            Kelola daftar mata pelajaran yang digunakan pada jadwal dan penilaian.
        </p>
    </div>

    <div class="grid gap-5 p-6">

        <div>
            <label class="block text-sm font-semibold text-slate-900">
                Nama Mata Pelajaran
            </label>

            <input
                name="name"
                value="{{ $formMatches ? old('name') : $subject?->name }}"
                required
                placeholder="Contoh: Tahfidz"
                class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-900">
                Deskripsi
            </label>

            <textarea
                name="description"
                rows="4"
                placeholder="Deskripsi mata pelajaran (opsional)"
                class="mt-2 w-full rounded-md border border-slate-300 bg-slate-100 px-3 py-2 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">{{ $formMatches ? old('description') : $subject?->description }}</textarea>
        </div>

    </div>

    <div class="flex justify-end gap-2 border-t border-slate-200 px-6 py-4">
        <button
            type="button"
            onclick="closeDialog(this)"
            class="cursor-pointer rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">
            Batal
        </button>

        <button
            type="submit"
            class="cursor-pointer rounded-md bg-[#0B8C79]/[80%] px-4 py-2 text-sm font-semibold text-white hover:bg-[#0B8C79]">
            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan' }}
        </button>
    </div>
</form>