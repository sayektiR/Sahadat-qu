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
                    <label class="block text-sm font-medium text-slate-700" for="assessment_type">Jenis Penilaian</label>
                    <select id="assessment_type" name="assessment_type" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-900 focus:ring-blue-900">
                        <option value="materi">Penilaian Materi</option>
                        <option value="hafalan">Penilaian Hafalan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700" for="assessment_date">Tanggal Penilaian</label>
                    <input id="assessment_date" name="assessment_date" type="date" value="{{ old('assessment_date', now()->toDateString()) }}" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-900 focus:ring-blue-900">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700" for="student_id">Santri</label>
                    <select id="student_id" name="student_id" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-900 focus:ring-blue-900">
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700" for="group_id">Kelompok</label>
                    <select id="group_id" name="group_id" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-900 focus:ring-blue-900">
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div id="lesson-fields" class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700" for="subject_id">Materi</label>
                    <select id="subject_id" name="subject_id" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-900 focus:ring-blue-900">
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700" for="score">Nilai</label>
                    <input id="score" name="score" type="number" min="0" max="100" step="0.01" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-900 focus:ring-blue-900">
                </div>
            </div>

            <div id="memorization-fields" class="space-y-5">
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700" for="memorization_type">Jenis Hafalan</label>
                        <input id="memorization_type" name="memorization_type" type="text" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-900 focus:ring-blue-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700" for="surah">Surah</label>
                        @php
                            $selectedSurah = $surahs->firstWhere('id', (int) old('surah_id'));
                        @endphp
                        <div class="relative mt-2">
                            <input
                                id="surah"
                                type="search"
                                value="{{ $selectedSurah?->name }}"
                                placeholder="Cari surah, contoh: Al-Fatihah"
                                autocomplete="off"
                                class="w-full rounded-md border border-slate-300 px-3 py-2 pr-10 text-sm focus:border-blue-900 focus:ring-blue-900"
                            >
                            <button type="button" id="surah-toggle" class="absolute inset-y-0 right-0 flex w-10 cursor-pointer items-center justify-center text-slate-500 hover:text-blue-950" aria-label="Buka daftar surah">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div id="surah-dropdown" style="max-height: 14rem; overscroll-behavior: contain;" class="absolute z-30 mt-2 hidden w-full overflow-y-auto rounded-lg border border-slate-200 bg-white p-2 shadow-xl">
                                <div id="surah-options" class="space-y-1"></div>
                                <p id="surah-empty" class="hidden px-3 py-4 text-sm text-slate-500">Surah tidak ditemukan.</p>
                            </div>
                        </div>
                        <input id="surah_id" name="surah_id" type="hidden" value="{{ old('surah_id') }}">
                        <p id="surah-helper" class="mt-1 text-xs text-slate-500">Pilih surah untuk menampilkan pilihan ayat otomatis.</p>
                        @error('surah_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700" for="from_ayah">Dari Ayat</label>
                        <select
                            id="from_ayah"
                            name="from_ayah"
                            data-old-value="{{ old('from_ayah') }}"
                            disabled
                            class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-900 focus:ring-blue-900 disabled:bg-slate-100 disabled:text-slate-500"
                        >
                            <option value="">Pilih surah terlebih dahulu</option>
                        </select>
                        @error('from_ayah')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700" for="to_ayah">Sampai Ayat</label>
                        <select
                            id="to_ayah"
                            name="to_ayah"
                            data-old-value="{{ old('to_ayah') }}"
                            disabled
                            class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-900 focus:ring-blue-900 disabled:bg-slate-100 disabled:text-slate-500"
                        >
                            <option value="">Pilih surah terlebih dahulu</option>
                        </select>
                        @error('to_ayah')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700" for="movement_score">Nilai Gerakan</label>
                        <input id="movement_score" name="movement_score" type="number" min="0" max="100" step="0.01" value="{{ old('movement_score', 0) }}" class="score-input mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-900 focus:ring-blue-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700" for="fluency_score">Nilai Kelancaran</label>
                        <input id="fluency_score" name="fluency_score" type="number" min="0" max="100" step="0.01" value="{{ old('fluency_score', 0) }}" class="score-input mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-900 focus:ring-blue-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700" for="tajwid_score">Nilai Tajwid</label>
                        <input id="tajwid_score" name="tajwid_score" type="number" min="0" max="100" step="0.01" value="{{ old('tajwid_score', 0) }}" class="score-input mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-900 focus:ring-blue-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700" for="makhraj_score">Nilai Makhraj</label>
                        <input id="makhraj_score" name="makhraj_score" type="number" min="0" max="100" step="0.01" value="{{ old('makhraj_score', 0) }}" class="score-input mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-900 focus:ring-blue-900">
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div class="rounded-md border border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm text-slate-500">Total Nilai</p>
                        <p class="mt-1 text-2xl font-semibold text-blue-950" id="total-score">0.00</p>
                    </div>
                    <div class="rounded-md border border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm text-slate-500">Predikat</p>
                        <p class="mt-1 text-2xl font-semibold text-blue-950" id="predicate">Perlu Mengulang</p>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700" for="note">Catatan Guru</label>
                <textarea id="note" name="note" rows="4" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-blue-900 focus:ring-blue-900">{{ old('note') }}</textarea>
            </div>

            <button type="submit" class="rounded-md bg-blue-950 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-900">Simpan Penilaian</button>
        </form>
    </section>

    <script>
        const typeSelect = document.getElementById('assessment_type');
        const lessonFields = document.getElementById('lesson-fields');
        const memorizationFields = document.getElementById('memorization-fields');
        const scoreInputs = document.querySelectorAll('.score-input');
        const totalScore = document.getElementById('total-score');
        const predicate = document.getElementById('predicate');
        const surahInput = document.getElementById('surah');
        const surahIdInput = document.getElementById('surah_id');
        const surahToggle = document.getElementById('surah-toggle');
        const surahDropdown = document.getElementById('surah-dropdown');
        const surahOptions = document.getElementById('surah-options');
        const surahEmpty = document.getElementById('surah-empty');
        const fromAyahSelect = document.getElementById('from_ayah');
        const toAyahSelect = document.getElementById('to_ayah');
        const surahHelper = document.getElementById('surah-helper');
        const surahs = @js($surahs->map(fn ($surah) => [
            'id' => $surah->id,
            'number' => $surah->number,
            'name' => $surah->name,
            'ayah_count' => $surah->ayah_count,
        ])->values());

        typeSelect.value = @js(old('assessment_type', 'materi'));

        function predicateFor(score) {
            if (score >= 90) return 'Mumtaz';
            if (score >= 80) return 'Jayyid Jiddan';
            if (score >= 60) return 'Jayyid';
            return 'Perlu Mengulang';
        }

        function updateSections() {
            const isLesson = typeSelect.value === 'materi';
            lessonFields.classList.toggle('hidden', !isLesson);
            memorizationFields.classList.toggle('hidden', isLesson);
        }

        function updateScore() {
            const values = Array.from(scoreInputs).map((input) => Number(input.value || 0));
            const score = values.reduce((sum, value) => sum + value, 0) / values.length;
            totalScore.textContent = score.toFixed(2);
            predicate.textContent = predicateFor(score);
        }

        function fillAyahOptions(select, totalAyah, selectedValue, fallbackValue) {
            select.innerHTML = '';

            for (let ayah = 1; ayah <= totalAyah; ayah += 1) {
                const option = document.createElement('option');
                option.value = String(ayah);
                option.textContent = String(ayah);
                select.appendChild(option);
            }

            const desiredValue = selectedValue || fallbackValue;
            select.value = desiredValue && Number(desiredValue) <= totalAyah ? String(desiredValue) : fallbackValue;
            select.disabled = false;
        }

        function resetAyahOptions(message = 'Pilih surah untuk menampilkan pilihan ayat otomatis.') {
            [fromAyahSelect, toAyahSelect].forEach((select) => {
                select.innerHTML = '<option value="">Pilih surah terlebih dahulu</option>';
                select.disabled = true;
            });

            surahIdInput.value = '';
            surahHelper.textContent = message;
            surahHelper.classList.toggle('text-red-600', message.includes('tidak ada'));
        }

        function applySurah(surah) {
            surahInput.value = surah.name;
            surahIdInput.value = surah.id;
            fillAyahOptions(fromAyahSelect, surah.ayah_count, fromAyahSelect.dataset.oldValue, '1');
            fillAyahOptions(toAyahSelect, surah.ayah_count, toAyahSelect.dataset.oldValue, String(surah.ayah_count));
            surahHelper.textContent = `${surah.number}. ${surah.name} memiliki ${surah.ayah_count} ayat.`;
            surahHelper.classList.remove('text-red-600');
            hideSurahDropdown();
        }

        function filteredSurahs() {
            const query = surahInput.value.trim().toLowerCase();

            if (! query) return surahs;

            return surahs.filter((surah) => {
                return surah.name.toLowerCase().includes(query) || String(surah.number) === query;
            });
        }

        function renderSurahOptions() {
            const results = filteredSurahs();

            surahOptions.innerHTML = '';
            surahEmpty.classList.toggle('hidden', results.length > 0);

            results.forEach((surah) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'flex w-full cursor-pointer items-center justify-between gap-3 rounded-md px-3 py-2 text-left text-sm hover:bg-slate-100 focus:bg-slate-100 focus:outline-none';
                button.innerHTML = `
                    <span>
                        <span class="block font-semibold text-slate-950">${surah.number}. ${surah.name}</span>
                        <span class="block text-xs text-slate-500">${surah.ayah_count} ayat</span>
                    </span>
                    <span class="text-xs font-medium text-slate-400">Pilih</span>
                `;
                button.addEventListener('click', () => applySurah(surah));
                surahOptions.appendChild(button);
            });
        }

        function showSurahDropdown() {
            renderSurahOptions();
            surahDropdown.classList.remove('hidden');
        }

        function hideSurahDropdown() {
            surahDropdown.classList.add('hidden');
        }

        function updateSurahSearch() {
            surahIdInput.value = '';

            if (! surahInput.value.trim()) {
                resetAyahOptions();
            } else {
                resetAyahOptions('Pilih surah dari daftar hasil pencarian.');
            }

            showSurahDropdown();
        }

        function normalizeAyahRange() {
            if (Number(fromAyahSelect.value) > Number(toAyahSelect.value)) {
                toAyahSelect.value = fromAyahSelect.value;
            }
        }

        typeSelect.addEventListener('change', updateSections);
        surahInput.addEventListener('focus', showSurahDropdown);
        surahInput.addEventListener('input', updateSurahSearch);
        surahToggle.addEventListener('click', () => {
            surahInput.focus();
            showSurahDropdown();
        });
        surahDropdown.addEventListener('wheel', (event) => {
            const isScrollingUp = event.deltaY < 0;
            const isScrollingDown = event.deltaY > 0;
            const atTop = surahDropdown.scrollTop === 0;
            const atBottom = Math.ceil(surahDropdown.scrollTop + surahDropdown.clientHeight) >= surahDropdown.scrollHeight;

            if ((isScrollingUp && atTop) || (isScrollingDown && atBottom)) {
                event.preventDefault();
            }

            event.stopPropagation();
        }, { passive: false });
        document.addEventListener('click', (event) => {
            if (! surahInput.contains(event.target) && ! surahDropdown.contains(event.target) && ! surahToggle.contains(event.target)) {
                hideSurahDropdown();
            }
        });
        fromAyahSelect.addEventListener('change', normalizeAyahRange);
        toAyahSelect.addEventListener('change', normalizeAyahRange);
        scoreInputs.forEach((input) => input.addEventListener('input', updateScore));
        updateSections();
        if (surahIdInput.value) {
            const initialSurah = surahs.find((surah) => String(surah.id) === String(surahIdInput.value));

            if (initialSurah) {
                applySurah(initialSurah);
            }
        } else {
            resetAyahOptions();
        }
        updateScore();
    </script>
</x-layouts.dashboard>
