@props(['student', 'groups', 'guardians'])

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
            <div><dt class="font-semibold text-slate-500">NIK</dt><dd class="mt-1">{{ $student->nik ?: '-' }}</dd></div>
            <div><dt class="font-semibold text-slate-500">Kelompok</dt><dd class="mt-1">{{ $student->group?->name ?: '-' }}</dd></div>
            <div><dt class="font-semibold text-slate-500">Wali</dt><dd class="mt-1">{{ $student->guardian?->name ?: '-' }}</dd></div>
            <div><dt class="font-semibold text-slate-500">Status</dt><dd class="mt-1">{{ $student->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}</dd></div>
            <div><dt class="font-semibold text-slate-500">Tempat Lahir</dt><dd class="mt-1">{{ $student->birth_place ?: '-' }}</dd></div>
            <div><dt class="font-semibold text-slate-500">Tanggal Lahir</dt><dd class="mt-1">{{ $student->birth_date?->format('d M Y') ?: '-' }}</dd></div>
            <div class="sm:col-span-2"><dt class="font-semibold text-slate-500">Alamat</dt><dd class="mt-1">{{ $student->address ?: '-' }}</dd></div>
        </dl>
        <div class="flex justify-end border-t border-slate-200 px-6 py-4">
            <button type="button" onclick="closeDialog(this)" class="cursor-pointer rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Tutup</button>
        </div>
    </div>
</dialog>
