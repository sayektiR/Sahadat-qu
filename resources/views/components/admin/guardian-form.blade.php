@props(['title', 'action', 'guardian' => null])

<form method="POST" action="{{ $action }}" class="bg-white">
    @csrf
    @if ($guardian)
        @method('PUT')
    @endif

    <div class="border-b border-slate-200 px-6 py-4">
        <h3 class="text-lg font-semibold text-slate-950">{{ $title }}</h3>
        <p class="mt-1 text-sm text-slate-500">Password default akun wali baru adalah password123.</p>
    </div>

    <div class="grid gap-4 p-6 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
            <input name="name" value="{{ old('name', $guardian?->name) }}" required class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Email Login</label>
            <input name="email" type="email" value="{{ old('email', $guardian?->user?->email) }}" required class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">No. Telepon</label>
            <input name="phone" value="{{ old('phone', $guardian?->phone) }}" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Hubungan</label>
            <input name="relation" value="{{ old('relation', $guardian?->relation) }}" placeholder="Ayah / Ibu / Wali" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-slate-700">Alamat</label>
            <textarea name="address" rows="3" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">{{ old('address', $guardian?->address) }}</textarea>
        </div>
    </div>

    <div class="flex justify-end gap-3 border-t border-slate-200 px-6 py-4">
        <button type="button" onclick="closeDialog(this)" class="cursor-pointer rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:border-slate-500">Batal</button>
        <button type="submit" class="cursor-pointer rounded-md bg-blue-950 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-900">Simpan</button>
    </div>
</form>
