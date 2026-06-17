<x-layouts.dashboard title="Data Santri" description="Daftar santri yang Anda wali.">
    <section class="space-y-6">
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-slate-950">Wali Santri</h2>
            <div class="mt-4 flex flex-col gap-3 text-sm">
                <div class="flex items-center gap-2">
                    <span class="w-20 text-slate-500">Nama</span>
                    <span class="font-medium text-slate-950">{{ $guardian->name }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-20 text-slate-500">Hubungan</span>
                    <span class="font-medium text-slate-950">{{ $guardian->relation ?: '-' }}</span>
                </div>
                @if ($guardian->phone)
                <div class="flex items-center gap-2">
                    <span class="w-20 text-slate-500">Telepon</span>
                    <span class="font-medium text-slate-950">{{ $guardian->phone }}</span>
                </div>
                @endif
                @if ($guardian->address)
                <div class="flex items-center gap-2">
                    <span class="w-20 text-slate-500">Alamat</span>
                    <span class="font-medium text-slate-950">{{ $guardian->address }}</span>
                </div>
                @endif
            </div>
        </div>

        <h2 class="text-xl font-bold text-slate-950">Data Santri</h2>

        <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
            <table class="w-full min-w-[600px] text-left">
                <thead>
                    <tr class="bg-slate-50 text-sm text-slate-950">
                        <th class="px-4 py-3 font-bold">Nama</th>
                        <th class="px-4 py-3 font-bold">NIS</th>
                        <th class="px-4 py-3 font-bold">Kelompok</th>
                        <th class="px-4 py-3 font-bold">Jenis Kelamin</th>
                        <th class="px-4 py-3 font-bold">Tanggal Lahir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $student)
                        <tr class="{{ $loop->odd ? 'bg-white' : 'bg-slate-50' }}">
                            <td class="px-4 py-3 text-sm font-medium text-slate-950">{{ $student->name }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $student->nis ?: '-' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $student->group?->name ?: '-' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $student->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $student->birth_date?->format('d M Y') ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada data santri.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-layouts.dashboard>