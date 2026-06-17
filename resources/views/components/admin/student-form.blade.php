@props(['title', 'action', 'groups', 'guardians', 'student' => null])

<form method="POST" action="{{ $action }}" class="bg-white">
    @csrf
    @if ($student)
        @method('PUT')
    @endif

    <div class="border-b border-slate-200 px-6 py-4">
        <h3 class="text-lg font-semibold text-slate-950">{{ $title }}</h3>
    </div>

    <div class="grid gap-4 p-6 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-slate-700">Nama Santri</label>
            <input name="name" value="{{ old('name', $student?->name) }}" required class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Kelompok</label>
            <select name="group_id" required class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                <option value="">Pilih kelompok</option>
                @foreach ($groups as $group)
                    <option value="{{ $group->id }}" @selected((string) old('group_id', $student?->group_id) === (string) $group->id)>{{ $group->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Wali Santri</label>
            <select name="guardian_id" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                <option value="">Pilih wali santri</option>
                @foreach ($guardians as $guardian)
                    <option value="{{ $guardian->id }}" @selected((string) old('guardian_id', $student?->guardian_id) === (string) $guardian->id)>{{ $guardian->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Status</label>
            <select name="status" required class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                <option value="active" @selected(old('status', $student?->status ?? 'active') === 'active')>Aktif</option>
                <option value="inactive" @selected(old('status', $student?->status) === 'inactive')>Tidak Aktif</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">NIS</label>
            <input name="nis" value="{{ old('nis', $student?->nis) }}" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">NIK</label>
            <input name="nik" value="{{ old('nik', $student?->nik) }}" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Tempat Lahir</label>
            <input name="birth_place" value="{{ old('birth_place', $student?->birth_place) }}" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Tanggal Lahir</label>
            <input name="birth_date" type="date" value="{{ old('birth_date', $student?->birth_date?->format('Y-m-d')) }}" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Jenis Kelamin</label>
            <select name="gender" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                <option value="">Pilih jenis kelamin</option>
                <option value="male" @selected(old('gender', $student?->gender) === 'male')>Laki-laki</option>
                <option value="female" @selected(old('gender', $student?->gender) === 'female')>Perempuan</option>
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-slate-700">Alamat</label>
            <textarea name="address" rows="3" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">{{ old('address', $student?->address) }}</textarea>
        </div>
    </div>

    <div class="flex justify-end gap-3 border-t border-slate-200 px-6 py-4">
        <button type="button" onclick="closeDialog(this)" class="cursor-pointer rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:border-slate-500">Batal</button>
        <button type="submit" class="cursor-pointer rounded-md bg-blue-950 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-900">Simpan</button>
    </div>
</form>
