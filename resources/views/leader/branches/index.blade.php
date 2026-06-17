<x-layouts.dashboard title="Data Cabang" description="Lihat semua data cabang Sahadat-Qu.">
    @if ($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="space-y-6">
        <div class="flex justify-end">
            <form method="GET" action="{{ route('leader.branches') }}" class="grid w-full gap-2 sm:w-auto sm:grid-cols-[220px_auto]">
                <div class="relative">
                    <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    <input name="search" value="{{ request('search') }}" placeholder="Cari cabang" class="h-9 w-full rounded-md border border-slate-300 bg-white pl-9 pr-3 text-xs outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <button type="submit" class="h-9 cursor-pointer rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-700 hover:border-blue-950 hover:text-blue-950">Filter</button>
            </form>
        </div>

        <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
            <table class="w-full min-w-[800px] text-left">
                <thead>
                    <tr class="bg-white text-sm text-slate-950">
                        <th class="px-6 py-5 font-bold">No.</th>
                        <th class="px-6 py-5 font-bold">Nama Cabang</th>
                        <th class="px-6 py-5 font-bold">Alamat</th>
                        <th class="px-6 py-5 font-bold">Telepon</th>
                        <th class="px-6 py-5 font-bold">Admin</th>
                        <th class="px-6 py-5 font-bold">Santri</th>
                        <th class="px-6 py-5 font-bold">Guru</th>
                        <th class="px-6 py-5 font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($branches as $branch)
                        <tr class="{{ $loop->odd ? 'bg-slate-100' : 'bg-white' }}">
                            <td class="px-6 py-5 text-sm">{{ $branches->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-5 text-sm font-medium">{{ $branch->name }}</td>
                            <td class="px-6 py-5 text-sm">{{ $branch->address ?: '-' }}</td>
                            <td class="px-6 py-5 text-sm">{{ $branch->phone ?: '-' }}</td>
                            <td class="px-6 py-5 text-sm">{{ $branch->users_count }}</td>
                            <td class="px-6 py-5 text-sm">{{ $branch->students_count }}</td>
                            <td class="px-6 py-5 text-sm">{{ $branch->teachers_count }}</td>
                            <td class="px-6 py-5">
                                <a href="{{ route('leader.branches.show', $branch) }}" class="cursor-pointer text-slate-900 hover:text-blue-950" aria-label="Lihat detail"><x-icon name="eye" /></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada data cabang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $branches->links() }}
    </section>
</x-layouts.dashboard>
