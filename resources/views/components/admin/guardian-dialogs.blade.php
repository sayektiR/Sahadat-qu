@props(['guardian', 'studentNames', 'groupNames'])

<dialog id="view-guardian-{{ $guardian->id }}" class="management-dialog w-full max-w-xl rounded-lg border border-slate-200 p-0 shadow-xl">
    <div class="bg-white">
        <div class="border-b border-slate-200 px-6 py-4">
            <h3 class="text-lg font-semibold text-slate-950">Detail Wali Santri</h3>
        </div>
        @if ($guardian->photo)
            <div class="border-b border-slate-200 px-6 py-5">
                <img src="{{ asset('storage/' . $guardian->photo) }}" alt="Foto {{ $guardian->name }}" class="h-28 w-28 rounded-md object-cover">
            </div>
        @endif
        <dl class="grid gap-4 p-6 text-sm sm:grid-cols-2">
            <div><dt class="font-semibold text-slate-500">Nama</dt><dd class="mt-1">{{ $guardian->name }}</dd></div>
            <div><dt class="font-semibold text-slate-500">Email</dt><dd class="mt-1">{{ $guardian->user?->email }}</dd></div>
            <div><dt class="font-semibold text-slate-500">Telepon</dt><dd class="mt-1">{{ $guardian->phone ?: '-' }}</dd></div>
            <div><dt class="font-semibold text-slate-500">Hubungan</dt><dd class="mt-1">{{ $guardian->relation ?: '-' }}</dd></div>
            <div><dt class="font-semibold text-slate-500">Santri</dt><dd class="mt-1">{{ $studentNames ?: '-' }}</dd></div>
            <div><dt class="font-semibold text-slate-500">Kelompok</dt><dd class="mt-1">{{ $groupNames ?: '-' }}</dd></div>
            <div class="sm:col-span-2"><dt class="font-semibold text-slate-500">Alamat</dt><dd class="mt-1">{{ $guardian->address ?: '-' }}</dd></div>
        </dl>
        <div class="flex justify-end border-t border-slate-200 px-6 py-4">
            <button type="button" onclick="closeDialog(this)" class="cursor-pointer rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Tutup</button>
        </div>
    </div>
</dialog>
