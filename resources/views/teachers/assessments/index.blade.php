<x-layouts.dashboard title="Penilaian">
    <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <x-page-title title="Penilaian Guru" subtitle="Catat nilai materi atau hafalan santri." />

        @if ($errors->any())
            <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                Periksa kembali kolom yang ditandai, lalu kirim ulang.
            </div>
        @endif

        <form method="POST" action="{{ route('teachers.assessments.store') }}" class="space-y-6" id="assessment-form">
            @csrf
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700" for="assessment_template_id">Jenis Penilaian</label>
                    <select id="assessment_template_id" name="assessment_template_id" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                        <option value="">Pilih Jenis Penilaian</option>
                        @foreach ($assessmentTemplates as $template)
                            <option
                                value="{{ $template->id }}"
                                {{ old('assessment_template_id') == $template->id ? 'selected' : '' }}>
                                {{ $template->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700" for="assessment_date">Tanggal Penilaian</label>
                    <input id="assessment_date" name="assessment_date" type="date" value="{{ old('assessment_date', now()->toDateString()) }}" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-900 focus:ring-blue-900">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700" for="group_id">Kelompok</label>
                    <select id="group_id" name="group_id" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                        @foreach ($groups as $group)
                            <option value="{{ $groups->first()->id }}">
                                {{ $groups->first()->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div id="dynamic-attributes" class="mt-6 grid gap-4 md:grid-cols-3"></div>
            <div id="students-table-container"></div>
            <button type="submit" class="rounded-md bg-blue-950 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-900">Simpan Penilaian</button>
        </form>
    </section>

    <section>
        <div class="mt-10">
            <h2 class="mb-4 text-xl font-bold text-slate-900">
                Data Penilaian
            </h2>

            <form method="GET" class="mt-3 mb-5">
                <div class="flex items-center gap-3">
                    <input type="date" name="assessment_date" value="{{ request('assessment_date') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <button type="submit" class="rounded-lg bg-blue-900 px-4 py-2 text-sm font-medium text-white hover:bg-blue-900">
                        Filter
                    </button>

                    @if(request('assessment_date'))
                        <a href="{{ route('teachers.assessments.index') }}"
                            class="text-sm text-slate-500 hover:text-slate-700">
                            Reset
                        </a>
                    @endif

                </div>
            </form>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <table class="min-w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left font-semibold">
                                Tanggal
                            </th>

                            <th class="px-6 py-4 text-left font-semibold">
                                Santri
                            </th>

                            <th class="px-6 py-4 text-left font-semibold">
                                Kelompok
                            </th>

                            <th class="px-6 py-4 text-left font-semibold">
                                Jenis Penilaian
                            </th>

                            <th class="px-6 py-4 text-center font-semibold">
                                Nilai Akhir
                            </th>

                            <th class="px-6 py-4 text-center font-semibold">
                                Predikat
                            </th>

                            <th class="px-6 py-4 text-center font-semibold">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($assessments as $assessment)
                            <tr class="border-t hover:bg-slate-50">

                                <td class="px-6 py-4">
                                    {{ \Carbon\Carbon::parse($assessment->assessment_date)->translatedFormat('d M Y') }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $assessment->student->name }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $assessment->group->name }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $assessment->template->name }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    {{ number_format($assessment->final_score, 2) }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="rounded-full bg-blue-100 px-3 py-1 text-sm text-blue-900">
                                        {{ $assessment->predicate }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-3">

                                        <button
                                            type="button"
                                            onclick="openDetail({{ $assessment->id }})"
                                            class="text-blue-500 hover:text-blue-700">
                                            <x-icon name="eye" />
                                        </button>
                                        
                                        <form method="POST" action="{{ route('teachers.assessments.destroy', $assessment) }}" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="delete-btn cursor-pointer text-red-500 hover:text-red-700" aria-label="Hapus penilaian"><x-icon name="trash" /></button>
                                        </form>

                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7"
                                    class="px-6 py-10 text-center text-slate-500">
                                    Belum ada data penilaian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $assessments->links() }}
            </div>
        </div>

        @foreach($assessments as $assessment)

        <dialog id="detail-{{ $assessment->id }}" class="m-auto w-full max-w-4xl rounded-2xl p-0 backdrop:bg-black/40">

            <div class="bg-white">
                <div class="flex items-center justify-between border-b px-6 py-4">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">
                            {{ $assessment->student->name }}
                        </h3>

                        <p class="text-sm text-slate-500">
                            Detail Penilaian
                        </p>
                    </div>

                    <button onclick="document.getElementById('detail-{{ $assessment->id }}').close()" class="rounded-lg p-2 hover:bg-slate-100">
                        <x-icon name="x" />
                    </button>
                </div>

                <div class="grid gap-4 p-6 md:grid-cols-4">

                    <div class="rounded-xl border p-4">
                        <p class="text-xs uppercase text-slate-500">
                            Tanggal
                        </p>

                        <p class="mt-1 font-semibold">
                            {{ \Carbon\Carbon::parse($assessment->assessment_date)->translatedFormat('d F Y') }}
                        </p>
                    </div>

                    <div class="rounded-xl border p-4">
                        <p class="text-xs uppercase text-slate-500">
                            Kelompok
                        </p>

                        <p class="mt-1 font-semibold">
                            {{ $assessment->group->name }}
                        </p>
                    </div>

                    <div class="rounded-xl border p-4">
                        <p class="text-xs uppercase text-slate-500">
                            Nilai Akhir
                        </p>

                        <p class="mt-1 text-xl font-bold text-blue-900">
                            {{ number_format($assessment->final_score,2) }}
                        </p>
                    </div>

                    <div class="rounded-xl border p-4">
                        <p class="text-xs uppercase text-slate-500">
                            Predikat
                        </p>

                        <span class="mt-2 inline-flex rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-900">
                            {{ $assessment->predicate }}
                        </span>
                    </div>

                </div>

                <div class="px-6 pb-6">
                    <h4 class="mb-3 text-lg font-semibold">
                        Nilai Aspek
                    </h4>
                    <div class="overflow-hidden rounded-xl border">
                        <table class="min-w-full">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left">
                                        Aspek
                                    </th>

                                    <th class="px-4 py-3 text-center">
                                        Nilai
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($assessment->scorings as $score)
                                <tr class="border-t">
                                    <td class="px-4 py-3">
                                        {{ $score->aspect->aspect_name }}
                                    </td>

                                    <td class="px-4 py-3 text-center font-semibold">
                                        {{ $score->value }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                
                <div class="px-6 pb-6">
                    <h4 class="mb-3 text-lg font-semibold">
                        Detail Atribut
                    </h4>
                    <div class="grid gap-4 md:grid-cols-3">
                        @foreach($assessment->attributeValues as $attribute)
                            <div class="rounded-xl border p-4">
                                <p class="text-xs uppercase text-slate-500">
                                    {{ $attribute->attribute->attribute_name }}
                                </p>

                                <p class="mt-2 text-lg font-semibold text-slate-900">
                                    {{ $attribute->value }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end border-t px-6 py-4">
                    <button onclick="document.getElementById('detail-{{ $assessment->id }}').close()" class="rounded-lg bg-[#0B8C79]/[80%] px-5 py-2 text-white hover:bg-[#0B8C79]">
                        Tutup
                    </button>
                </div>
            </div>
        </dialog>
        @endforeach
    </section>

    <script>
        function openDetail(id)
        {
            const dialog = document.getElementById(`detail-${id}`);

            if (dialog) {
                dialog.showModal();
            }
        }
    </script>

    <script>
        const templates = @json($assessmentTemplates);

        const templateSelect = document.getElementById('assessment_template_id');
        const attributeContainer = document.getElementById('dynamic-attributes');
        const aspectContainer = document.getElementById('dynamic-aspects');
        const studentsTableContainer = document.getElementById('students-table-container');
        
        function renderTemplate(templateId)
        {
            const selectedTemplate =
                templates.find(t => t.id == templateId);

            attributeContainer.innerHTML = '';

            if (!selectedTemplate) return;

            selectedTemplate.attributes.forEach(attribute => {

                const inputType =
                    attribute.attribute_type === 'number'
                    ? 'number'
                    : 'text';

                attributeContainer.innerHTML += `
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700">
                            ${attribute.attribute_name}
                        </label>

                        <input
                            type="${inputType}"
                            name="attributes[${attribute.id}]"
                            class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2">
                    </div>
                `;
            });
        }

        function renderStudentTable(students, aspects)
        {
            let headers = '';

            aspects.forEach(aspect => {
                headers += `
                    <th class="border-b border-slate-200 px-4 py-3 text-center font-semibold">
                        ${aspect.aspect_name}
                        <span class="block text-xs text-slate-500">
                            (${aspect.weight}%)
                        </span>
                    </th>
                `;
            });

            let rows = '';

            students.forEach(student => {

                let aspectInputs = '';

                aspects.forEach(aspect => {

                    aspectInputs += `
                        <td class="border-b border-slate-200 px-4 py-3 text-center">
                            <input
                                type="number"
                                min="0"
                                max="100"
                                name="scores[${student.id}][${aspect.id}]"
                                class="h-10 w-20 rounded-lg border border-slate-300 text-center outline-none transition focus:border-blue-900 focus:ring-2 focus:ring-blue-900/10">
                        </td>
                    `;
                });

                rows += `
                    <tr>
                        <td class="border-b border-slate-200 px-4 py-3 font-medium">
                            ${student.name}
                        </td>

                        ${aspectInputs}

                        <td class="total-cell border-b border-slate-200 px-4 py-3 text-center font-bold text-blue-900">
                            0
                        </td>

                        <td class="predicate-cell border-b border-slate-200 px-4 py-3 text-center font-semibold text-emerald-700">
                            <input
                                type="hidden"
                                name="predicates[${student.id}]"
                                class="predicate-input">
                        </td>
                    </tr>
                `;
            });

            studentsTableContainer.innerHTML = `
                <div class="overflow-x-auto rounded-2xl border border-slate-200 shadow-sm">
                    <table class="w-full overflow-hidden rounded-2xl border-separate border-spacing-0 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="border-b border-slate-200 px-4 py-3 text-left font-semibold first:rounded-tl-2xl">
                                    Santri
                                </th>

                                ${headers}

                                <th class="border-b border-slate-200 px-4 py-3 text-center font-semibold">
                                    Total
                                </th>

                                <th class="border-b border-slate-200 px-4 py-3 text-center font-semibold last:rounded-tr-2xl">
                                    Predikat
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            ${rows}
                        </tbody>
                    </table>
                </div>
            `;

            attachScoreEvents();
        }

        function attachScoreEvents()
        {
            document
                .querySelectorAll(
                    '#students-table-container input[type="number"]'
                )
                .forEach(input => {

                    input.addEventListener('input', function () {

                        const row = this.closest('tr');

                        const scoreInputs =
                            row.querySelectorAll(
                                'input[type="number"]'
                            );

                        let total = 0;

                        scoreInputs.forEach(i => {
                            total += Number(i.value || 0);
                        });

                        const average =
                            total / scoreInputs.length;

                        row.querySelector('.total-cell')
                            .innerText =
                            average.toFixed(2);

                        const predicate =
                            getPredicate(average);

                        row.querySelector('.predicate-cell')
                            .innerText =
                            predicate;

                        row.querySelector('.predicate-input')
                            .value =
                            predicate;
                    });

                });
        }

        const groupSelect =
            document.getElementById('group_id');

        groupSelect.addEventListener('change', async function () {

            const groupId = this.value;

            const templateId =
                templateSelect.value;

            if (!groupId || !templateId)
                return;

            const template =
                templates.find(
                    t => t.id == templateId
                );

            console.log(template);
            console.log(template.aspects);
            
            const response =
                await fetch(
                    `/teachers/groups/${groupId}/students`
                );

            const students =
                await response.json();

            renderStudentTable(
                students,
                template.aspects
            );
        });

        function getPredicate(score)
        {
            if (score >= 90)
                return 'Mumtaz';

            if (score >= 80)
                return 'Jayyid Jiddan';

            if (score >= 70)
                return 'Jayyid';

            return 'Perlu Mengulang';
        }

        templateSelect.addEventListener('change', function () {
            renderTemplate(this.value);
        });

        // tampilkan otomatis saat halaman dibuka
        async function loadStudents()
        {
            const groupId = groupSelect.value;
            const templateId = templateSelect.value;

            if (!groupId || !templateId) {
                studentsTableContainer.innerHTML = '';
                return;
            }

            const template = templates.find(
                t => t.id == templateId
            );

            const response = await fetch(
                `/teachers/groups/${groupId}/students`
            );

            const students = await response.json();

            renderStudentTable(
                students,
                template.aspects
            );
        }

        templateSelect.addEventListener('change', function () {
            renderTemplate(this.value);
            loadStudents();
        });

        // otomatis saat halaman pertama dibuka
        window.addEventListener('DOMContentLoaded', () => {
            renderTemplate(templateSelect.value);
            loadStudents();
        });
    </script>

    <script>
        document.querySelectorAll('.delete-btn').forEach(button => {

            button.addEventListener('click', function () {

                const form = this.closest('.delete-form');

                Swal.fire({
                    title: 'Hapus Penilaian?',
                    text: 'Data yang dihapus tidak dapat dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b'
                }).then((result) => {

                    if (result.isConfirmed) {
                        form.submit();
                    }

                });

            });

        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</x-layouts.dashboard>
