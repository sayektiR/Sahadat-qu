@php
    $isEdit = $mode === 'edit';
    $pageTitle = $isEdit ? 'Edit Jadwal' : 'Tambah Jadwal';
    $action = $isEdit ? route('admin.schedules.update', $schedule) : route('admin.schedules.store');
    $selectedDays = old('active_days', $schedule->exists ? $schedule->details->pluck('day')->unique()->toArray(): []);
@endphp

<x-layouts.dashboard :title="$pageTitle" description="Atur periode, kelompok, jam belajar, dan materi setiap hari.">
    <form method="POST" action="{{ $action }}">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="mb-6">
            <nav class="flex items-center gap-2 text-sm text-slate-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-950">Dashboard</a>
                <span>/</span>
                <a href="{{ route('admin.schedules') }}" class="hover:text-blue-950">Jadwal</a>
                <span>/</span>
                <span class="font-medium text-slate-900">{{ $pageTitle }}</span>
            </nav>
        </div>

        @if ($errors->any())
            <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
        @endif

        <section class="w-full rounded-lg border border-slate-200 bg-white p-6 shadow-sm lg:p-8">
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                <div class="col-span-full">
                    <label class="block text-sm font-semibold text-slate-900">Periode</label>
                    <select id="period_id" name="period_id" required class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                        <option value="">Pilih periode</option>
                        @foreach ($periods as $period)
                            <option
                                value="{{ $period->id }}"
                                data-start="{{ $period->start_date->format('Y-m-d') }}"
                                data-end="{{ $period->end_date->format('Y-m-d') }}"
                                @selected((string) old('period_id', $schedule->period_id) === (string) $period->id)>
                                {{ $period->name }} - {{ $period->semester }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-900">Tanggal Mulai</label>
                    <input name="start_date" id="start_date" type="date" value="{{ old('start_date', $schedule->start_date?->format('Y-m-d')) }}" readonly class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-900">Tanggal Selesai</label>
                    <input name="end_date" id="end_date" type="date" value="{{ old('end_date', $schedule->end_date?->format('Y-m-d')) }}" readonly class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-900">Jumlah Pertemuan</label>
                    <input name="total_meetings" id="total_meetings" type="number" min="1" value="{{ old('total_meetings', $schedule->total_meetings) }}" readonly class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-900">Jam Mulai</label>
                    <input name="start_time" type="time" value="{{ old('start_time', $schedule->start_time ? substr($schedule->start_time, 0, 5) : '') }}" required class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-900">Jam Selesai</label>
                    <input name="end_time" type="time" value="{{ old('end_time', $schedule->end_time ? substr($schedule->end_time, 0, 5) : '') }}" required class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-900">Kelompok</label>
                    <select name="group_id" required class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                        <option value="">Pilih kelompok</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}" @selected((string) old('group_id', $schedule->group_id) === (string) $group->id)>{{ $group->name }}</option>
                        @endforeach
                    </select>
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="all_groups" value="1" {{ old('all_groups', $schedule->all_groups) ? 'checked' : '' }}>
                        <span class="text-sm text-slate-700">
                            Berlaku untuk semua kelompok
                        </span>
                    </label>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-sm font-semibold text-slate-900 mb-2">
                        Hari Belajar
                    </label>

                    @foreach ($days as $day)
                        <label class="inline-flex items-center mr-4">
                            <input
                                type="checkbox"
                                name="active_days[]"
                                value="{{ $day }}"
                                data-day="{{ $day }}"
                                onchange="calculateMeetings()"
                                @checked(in_array($day, $selectedDays))
                            >
                            <span class="ml-2">{{ $day }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="mt-8 grid gap-5 lg:grid-cols-2 xl:grid-cols-3">
                @foreach ($days as $day)
                    @php
                        $existingDetails = ($detailsByDay[$day] ?? collect())->sortBy('order_number')->values();
                        $oldDetails = old("details.$day");
                        $slotCount = is_array($oldDetails) ? count($oldDetails) : $existingDetails->count();
                        $slotCount = max(1, $slotCount);
                    @endphp
                    <section class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <h3 class="text-sm font-bold text-slate-950">{{ $day }}</h3>
                            <button type="button" onclick="addScheduleSlot(@js($day))" class="cursor-pointer rounded-md border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-blue-950 hover:bg-slate-100">
                                + Tambah Mapel
                            </button>
                        </div>
                        <div class="space-y-3" data-slots-for="{{ $day }}" data-next-index="{{ $slotCount }}">
                            @for ($slot = 0; $slot < $slotCount; $slot++)
                                @php
                                    $existing = $existingDetails->get($slot);
                                @endphp
                                <div class="grid gap-2 rounded-md border border-slate-200 bg-white p-3" data-schedule-slot>
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-xs font-semibold text-slate-500" data-slot-label>Mapel {{ $slot + 1 }}</span>
                                        <button type="button" onclick="removeScheduleSlot(this)" class="{{ $slot === 0 ? 'hidden' : '' }} cursor-pointer text-xs font-semibold text-red-600 hover:text-red-700">
                                            Hapus
                                        </button>
                                    </div>
                                    <select name="details[{{ $day }}][{{ $slot }}][subject_id]" data-day="{{ $day }}" class="subject-select h-10 rounded-md border border-slate-300 bg-white px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                                        <option value="">Pilih mapel</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}" @selected((string) old("details.$day.$slot.subject_id", $existing?->subject_id) === (string) $subject->id)>{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                    <input name="details[{{ $day }}][{{ $slot }}][material_name]" value="{{ old("details.$day.$slot.material_name", $existing?->material_name) }}" placeholder="Materi khusus / ujian" class="h-10 rounded-md border border-slate-300 bg-white px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                                </div>
                            @endfor
                        </div>
                    </section>
                @endforeach
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="cursor-pointer rounded-md bg-[#0B8C79]/[80%] px-5 py-3 text-sm font-semibold text-white hover:bg-[#0B8C79]">
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan' }}
                </button>
            </div>
        </section>
    </form>

    <template id="schedule-slot-template">
        <div class="grid gap-2 rounded-md border border-slate-200 bg-white p-3" data-schedule-slot>
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-semibold text-slate-500" data-slot-label>Mapel</span>
                <button type="button" onclick="removeScheduleSlot(this)" class="cursor-pointer text-xs font-semibold text-red-600 hover:text-red-700">
                    Hapus
                </button>
            </div>
            <select data-slot-subject class="subject-select h-10 rounded-md border border-slate-300 bg-white px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                <option value="">Pilih mapel</option>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                @endforeach
            </select>
            <input data-slot-material placeholder="Materi khusus / ujian" class="h-10 rounded-md border border-slate-300 bg-white px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
        </div>
    </template>

    <script>
        function calculateMeetings() {

            const startValue = document.querySelector('[name="start_date"]').value;
            const endValue = document.querySelector('[name="end_date"]').value;

            if (!startValue || !endValue) {
                document.getElementById('total_meetings').value = '';
                return;
            }

            const start = new Date(startValue);
            const end = new Date(endValue);

            if (start > end) {
                document.getElementById('total_meetings').value = '';
                return;
            }

            const selectedDays = [...document.querySelectorAll(
                'input[name="active_days[]"]:checked'
            )].map(cb => cb.value);

            const dayMap = {
                'Minggu': 0,
                'Senin': 1,
                'Selasa': 2,
                'Rabu': 3,
                'Kamis': 4,
                'Jumat': 5,
                'Sabtu': 6
            };

            let count = 0;

            for (
                let current = new Date(start);
                current <= end;
                current.setDate(current.getDate() + 1)
            ) {
                const dayNumber = current.getDay();

                selectedDays.forEach(day => {
                    if (dayMap[day] === dayNumber) {
                        count++;
                    }
                });
            }

            document.getElementById('total_meetings').value = count;
        }

        document.querySelector('[name="start_date"]')
            .addEventListener('change', calculateMeetings);

        document.querySelector('[name="end_date"]')
            .addEventListener('change', calculateMeetings);

        calculateMeetings();
    </script>

    <script>
        const checkbox = document.querySelector('[name="all_groups"]');
        const select = document.querySelector('[name="group_id"]');

        function toggleGroup() {
            select.disabled = checkbox.checked;

            if (checkbox.checked) {
                select.value = '';
            }
        }

        checkbox.addEventListener('change', toggleGroup);
        toggleGroup();
    </script>

    <script>
        function addScheduleSlot(day) {
            const container = document.querySelector(`[data-slots-for="${day}"]`);
            const template = document.getElementById('schedule-slot-template');

            if (!container || !template) {
                return;
            }

            const index = Number(container.dataset.nextIndex || 0);
            const slot = template.content.firstElementChild.cloneNode(true);
            const subject = slot.querySelector('[data-slot-subject]');
            const material = slot.querySelector('[data-slot-material]');

            subject.name = `details[${day}][${index}][subject_id]`;
            subject.dataset.day = day;
            material.name = `details[${day}][${index}][material_name]`;

            container.appendChild(slot);
            container.dataset.nextIndex = index + 1;
            refreshScheduleSlotLabels(container);
        }

        document.addEventListener('change', function (e) {

            if (!e.target.classList.contains('subject-select')) return;

            const day = e.target.dataset.day;

            const checkbox = document.querySelector(
                `input[name="active_days[]"][data-day="${day}"]`
            );

            const selects = document.querySelectorAll(
                `.subject-select[data-day="${day}"]`
            );

            const hasSubject = [...selects].some(select => select.value !== '');

            checkbox.checked = hasSubject;

            calculateMeetings();

        });
        

        function removeScheduleSlot(button) {
            const slot = button.closest('[data-schedule-slot]');
            const container = slot?.parentElement;

            if (!slot || !container || container.querySelectorAll('[data-schedule-slot]').length <= 1) {
                return;
            }

            slot.remove();
            refreshScheduleSlotLabels(container);
        }

        function refreshScheduleSlotLabels(container) {
            container.querySelectorAll('[data-schedule-slot]').forEach((slot, index) => {
                const label = slot.querySelector('[data-slot-label]');
                const removeButton = slot.querySelector('button');

                if (label) {
                    label.textContent = `Mapel ${index + 1}`;
                }

                if (removeButton) {
                    removeButton.classList.toggle('hidden', index === 0);
                }
            });
        }
    </script>

    <script>
        const periodSelect = document.getElementById('period_id');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        function updatePeriodDates() {

            const option = periodSelect.options[periodSelect.selectedIndex];

            if (!option || !option.dataset.start) {
                startDateInput.value = '';
                endDateInput.value = '';
                calculateMeetings();
                return;
            }

            startDateInput.value = option.dataset.start;
            endDateInput.value = option.dataset.end;

            calculateMeetings();
        }

        periodSelect.addEventListener('change', updatePeriodDates);

        // Saat halaman edit dibuka
        updatePeriodDates();
    </script>
</x-layouts.dashboard>
