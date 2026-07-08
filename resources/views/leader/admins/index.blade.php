<x-layouts.dashboard title="Data Admin Cabang" description="Kelola admin untuk setiap cabang.">
    @if ($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="space-y-6">
        <div class="flex justify-end">
            <form method="GET" action="{{ route('leader.admins') }}" class="grid w-full gap-2 sm:w-auto sm:grid-cols-[200px_160px_auto]">
                <div class="relative">
                    <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    <input name="search" value="{{ request('search') }}" placeholder="Cari admin" class="h-9 w-full rounded-md border border-slate-300 bg-white pl-9 pr-3 text-xs outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <select name="branch_id" class="h-9 rounded-md border border-slate-300 bg-white px-3 text-xs text-slate-600 outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                    <option value="">Pilih Cabang</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" @selected((string) request('branch_id') === (string) $branch->id)>{{ $branch->name }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="h-9 cursor-pointer rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-700 hover:border-blue-950 hover:text-blue-950">Filter</button>
                    <a href="{{ route('leader.admins.create') }}" class="inline-flex h-9 cursor-pointer items-center gap-2 rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-800 hover:border-blue-950 hover:text-blue-950">
                        <x-icon name="plus" />
                        Tambah Admin
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
            <table class="w-full min-w-[800px] text-left">
                <thead>
                    <tr class="bg-white text-sm text-slate-950">
                        <th class="px-6 py-5 font-bold">No.</th>
                        <th class="px-6 py-5 font-bold">Nama</th>
                        <th class="px-6 py-5 font-bold">Email</th>
                        <th class="px-6 py-5 font-bold">Telepon</th>
                        <th class="px-6 py-5 font-bold">Cabang</th>
                        <th class="px-6 py-5 font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($admins as $admin)
                        <tr class="{{ $loop->odd ? 'bg-slate-100' : 'bg-white' }}">
                            <td class="px-6 py-5 text-sm">{{ $admins->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-5 text-sm font-medium">{{ $admin->name }}</td>
                            <td class="px-6 py-5 text-sm">{{ $admin->email }}</td>
                            <td class="px-6 py-5 text-sm">{{ $admin->phone ?: '-' }}</td>
                            <td class="px-6 py-5 text-sm">{{ $admin->branch?->name ?: '-' }}</td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <a href="{{ route('leader.admins.edit', $admin) }}" class="cursor-pointer text-yellow-500 hover:text-yellow-700" aria-label="Edit admin"><x-icon name="pencil" /></a>
                                    <form method="POST" action="{{ route('leader.admins.destroy', $admin) }}" onsubmit="return confirm('Hapus admin ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="cursor-pointer text-red-500 hover:text-red-700" aria-label="Hapus admin"><x-icon name="trash" /></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada data admin.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $admins->links() }}
    </section>
</x-layouts.dashboard>
