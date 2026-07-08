<x-layouts.dashboard title="Penilaian" description="Pantau hasil penilaian materi dan hafalan santri.">
    @if ($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <section class="space-y-6">
        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-slate-500">Total Penilaian</p>
                <p class="mt-2 text-3xl font-bold text-blue-950">{{ $totalCount }}</p>
            </div>
            
            
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase text-slate-500">Rata-rata Nilai</p>
                <p class="mt-2 text-3xl font-bold text-blue-950">{{ $averageScore ? number_format($averageScore, 1) : '-' }}</p>
            </div>
        </div>

        <div class="flex flex-col gap-5 2xl:flex-row 2xl:items-end 2xl:justify-between">

            <form method="GET" action="{{ route('admin.assessments') }}" class="grid w-full gap-2 sm:grid-cols-2 xl:w-auto xl:grid-cols-[200px_160px_160px_auto]">

                <div class="relative">
                    <x-icon name="search"
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />

                    <input name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari santri/guru"
                        class="h-9 w-full rounded-md border border-slate-300 bg-white pl-9 pr-3 text-xs">
                </div>

                <select name="group_id"
                    class="h-9 rounded-md border border-slate-300 bg-white px-3 text-xs">

                    <option value="">Pilih Kelompok</option>

                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}"
                            @selected(request('group_id') == $group->id)>
                            {{ $group->name }}
                        </option>
                    @endforeach
                </select>

                <select name="period_id"
                    class="h-9 rounded-md border border-slate-300 bg-white px-3 text-xs">

                    <option value="">Pilih Periode</option>

                    @foreach ($periods as $period)
                        <option value="{{ $period->id }}"
                            @selected(request('period_id') == $period->id)>
                            {{ $period->name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit"
                    class="h-9 rounded-md border border-slate-300 bg-white px-3 text-xs font-semibold">
                    Filter
                </button>
            </form>
        </div>

        <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
            <table class="w-full min-w-[1120px] text-left">
                <thead>
                    <tr class="bg-white text-sm text-slate-950">
                        <th class="px-6 py-5 font-bold">No.</th>
                        <th class="px-6 py-5 font-bold">Tanggal</th>
                        <th class="px-6 py-5 font-bold">Nama Santri</th>
                        <th class="px-6 py-5 font-bold">Kelompok</th>
                        <th class="px-6 py-5 font-bold">Nilai</th>
                        <th class="px-6 py-5 font-bold">Predikat</th>
                        <th class="px-6 py-5 font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assessments as $assessment)
                       
                        <tr class="{{ $loop->odd ? 'bg-slate-100' : 'bg-white' }}">
                            <td class="px-6 py-5 text-sm">{{ $assessments->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-5 text-sm">{{ $assessment->assessment_date?->format('d M Y') }}</td>
                            <td class="px-6 py-5 text-sm font-medium">{{ $assessment->student?->name }}</td>
                            <td class="px-6 py-5 text-sm">{{ $assessment->group?->name ?: '-' }}</td>
                            <td class="px-6 py-5 text-sm font-semibold text-blue-950">{{ number_format((float) $assessment->final_score, 1) }}</td>
                            <td class="px-6 py-5 text-sm">{{ $assessment->predicate ?: '-' }}</td>
                            <td class="px-6 py-5">
                                <button
                                    type="button"
                                    onclick="openDialog('view-assessment-{{ $assessment->id }}')"
                                    class="cursor-pointer text-slate-900 hover:text-blue-950">

                                    <x-icon name="eye" />
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-10 text-center text-sm text-slate-500">Data penilaian belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            @foreach ($assessments as $assessment)
                <dialog id="view-assessment-{{ $assessment->id }}" class="m-auto w-full max-w-4xl rounded-xl border border-slate-200 p-0 shadow-xl backdrop:bg-black/50">

                    <div class="bg-white">

                        <div class="border-b border-slate-200 px-6 py-4">
                            <h3 class="text-xl font-bold text-slate-900">
                                {{ $assessment->student->name }}
                            </h3>

                            <p class="text-sm text-slate-500">
                                {{ $assessment->template->name }}
                            </p>
                        </div>

                        <div class="p-6">

                            <div class="mb-6 grid gap-4 md:grid-cols-2">

                                <div>
                                    <span class="font-semibold text-slate-600">
                                        Tanggal:
                                    </span>
                                    {{ $assessment->assessment_date?->format('d M Y') }}
                                </div>

                                <div>
                                    <span class="font-semibold text-slate-600">
                                        Kelompok:
                                    </span>
                                    {{ $assessment->group->name }}
                                </div>

                                <div>
                                    <span class="font-semibold text-slate-600">
                                        Guru:
                                    </span>
                                    {{ $assessment->teacher->name }}
                                </div>

                                <div>
                                    <span class="font-semibold text-slate-600">
                                        Template:
                                    </span>
                                    {{ $assessment->template->name }}
                                </div>

                            </div>

                            {{-- NILAI ASPEK --}}
                            <h4 class="mb-3 text-lg font-semibold">
                                Nilai Aspek
                            </h4>

                            <div class="overflow-hidden rounded-lg border border-slate-200">
                                <table class="w-full">
                                    <thead class="bg-slate-100">
                                        <tr>
                                            <th class="border px-4 py-2 text-left">
                                                Aspek
                                            </th>

                                            <th class="border px-4 py-2 text-center">
                                                Nilai
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach($assessment->scorings as $score)
                                            <tr>
                                                <td class="border px-4 py-2">
                                                    {{ $score->aspect->aspect_name }}
                                                </td>

                                                <td class="border px-4 py-2 text-center">
                                                    {{ $score->value }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- ATRIBUT --}}
                            <h4 class="mt-6 mb-3 text-lg font-semibold">
                                Atribut Penilaian
                            </h4>

                            <div class="overflow-hidden rounded-lg border border-slate-200">
                                <table class="w-full">
                                    <thead class="bg-slate-100">
                                        <tr>
                                            <th class="border px-4 py-2 text-left">
                                                Atribut
                                            </th>

                                            <th class="border px-4 py-2 text-center">
                                                Nilai
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse($assessment->attributeValues as $attribute)
                                            <tr>
                                                <td class="border px-4 py-2">
                                                    {{ $attribute->attribute->attribute_name }}
                                                </td>

                                                <td class="border px-4 py-2 text-center">
                                                    {{ $attribute->value }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2"
                                                    class="border px-4 py-3 text-center text-slate-500">
                                                    Tidak ada atribut.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- HASIL AKHIR --}}
                            <div class="mt-6 rounded-lg bg-slate-50 p-4">

                                <div class="mb-2">
                                    <span class="font-semibold">
                                        Nilai Akhir :
                                    </span>

                                    <span class="text-lg font-bold text-blue-950">
                                        {{ number_format($assessment->final_score, 2) }}
                                    </span>
                                </div>

                                <div>
                                    <span class="font-semibold">
                                        Predikat :
                                    </span>

                                    <span class="rounded-full bg-blue-100 px-3 py-1 text-sm text-blue-900">
                                        {{ $assessment->predicate }}
                                    </span>
                                </div>

                            </div>

                        </div>

                        <div class="flex justify-end border-t border-slate-200 px-6 py-4">
                            <button type="button"
                                onclick="closeDialog(this)"
                                class="rounded-md bg-slate-800 px-4 py-2 text-white hover:bg-slate-700">
                                Tutup
                            </button>
                        </div>

                    </div>

                </dialog>
            @endforeach

        {{ $assessments->links() }}
    </section>

    <script>
        document.querySelectorAll('.btn-detail').forEach(button => {
            button.addEventListener('click', () => {

                const id = button.dataset.assessment;

                // ajax fetch detail
                // isi modal
                // tampilkan modal
            });
        });
    </script>
        

    <script>
        function openDialog(id) {
            document.getElementById(id)?.showModal();
        }

        function closeDialog(button) {
            button.closest('dialog')?.close();
        }
    </script>
    
</x-layouts.dashboard>
