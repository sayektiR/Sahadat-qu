<x-layouts.dashboard title="{{ $mode === 'create' ? 'Tambah Admin Cabang' : 'Edit Admin Cabang' }}">
    @if ($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('leader.admins') }}" class="cursor-pointer text-slate-600 hover:text-blue-950">
                <x-icon name="arrow-left" />
            </a>
            <h2 class="text-2xl font-bold text-slate-950">{{ $mode === 'create' ? 'Tambah Admin Cabang' : 'Edit Admin Cabang' }}</h2>
        </div>

        <form method="POST" action="{{ $mode === 'create' ? route('leader.admins.store') : route('leader.admins.update', $admin) }}" class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @if ($mode === 'edit')
                @method('PUT')
            @endif

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-950">Nama</label>
                    <input name="name" value="{{ old('name', $admin?->name) }}" required class="h-11 w-full rounded-md border border-slate-300 bg-white px-4 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-950">Email</label>
                    <input type="email" name="email" value="{{ old('email', $admin?->email) }}" required class="h-11 w-full rounded-md border border-slate-300 bg-white px-4 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-950">Telepon</label>
                    <input name="phone" value="{{ old('phone', $admin?->phone) }}" class="h-11 w-full rounded-md border border-slate-300 bg-white px-4 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-950">Cabang</label>
                    <select name="branch_id" required class="h-11 w-full rounded-md border border-slate-300 bg-white px-4 text-sm text-slate-600 outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                        <option value="">Pilih Cabang</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" @selected(old('branch_id', $admin?->branch_id) == $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-950">Password {{ $mode === 'edit' ? '(kosongkan jika tidak diubah)' : '' }}</label>
                    <input type="password" name="password" {{ $mode === 'create' ? 'required' : '' }} class="h-11 w-full rounded-md border border-slate-300 bg-white px-4 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-950">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" {{ $mode === 'create' ? 'required' : '' }} class="h-11 w-full rounded-md border border-slate-300 bg-white px-4 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('leader.admins') }}" class="cursor-pointer rounded-md border border-slate-300 bg-white px-6 py-2.5 text-sm font-semibold text-slate-700 hover:border-blue-950 hover:text-blue-950">Batal</a>
                <button type="submit" class="cursor-pointer rounded-md bg-[#0B8C79]/[80%] px-6 py-2.5 text-sm font-semibold text-white hover:bg-[#0B8C79]">{{ $mode === 'create' ? 'Simpan' : 'Perbarui' }}</button>
            </div>
        </form>
    </section>
</x-layouts.dashboard>