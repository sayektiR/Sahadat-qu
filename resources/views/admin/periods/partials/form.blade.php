@php
    $isEdit = $mode === 'edit';
    $action = $isEdit ? route('admin.periods.update', $period) : route('admin.periods.store');
    $formMatches = old('_form') === $mode && (! $isEdit || (string) old('_period_id') === (string) $period?->id);
@endphp

<form method="POST" action="{{ $action }}" class="bg-white">
    @csrf
    @if ($isEdit)
        @method('PUT')
        <input type="hidden" name="_period_id" value="{{ $period->id }}">
    @endif
    <input type="hidden" name="_form" value="{{ $mode }}">

    <div class="border-b border-slate-200 px-6 py-4">
        <h3 class="text-lg font-semibold text-slate-950">{{ $isEdit ? 'Edit Periode' : 'Tambah Periode' }}</h3>
        <p class="mt-1 text-sm text-slate-500">Atur tahun ajaran, semester, dan periode aktif.</p>
    </div>

    <div class="grid gap-5 p-6 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label class="block text-sm font-semibold text-slate-900">Nama Periode</label>
            <input name="name" value="{{ $formMatches ? old('name') : $period?->name }}" required placeholder="Contoh: 2026/2027 Ganjil" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-900">Tahun Ajaran</label>
            <input name="academic_year" value="{{ $formMatches ? old('academic_year') : $period?->academic_year }}" required placeholder="Contoh: 2026/2027" class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-900">Semester</label>
            <select name="semester" required class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
                @php $semesterValue = $formMatches ? old('semester') : $period?->semester; @endphp
                <option value="">Pilih semester</option>
                <option value="Ganjil" @selected($semesterValue === 'Ganjil')>Ganjil</option>
                <option value="Genap" @selected($semesterValue === 'Genap')>Genap</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-900">Tanggal Mulai</label>
            <input name="start_date" type="date" value="{{ $formMatches ? old('start_date') : $period?->start_date?->format('Y-m-d') }}" required class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-900">Tanggal Selesai</label>
            <input name="end_date" type="date" value="{{ $formMatches ? old('end_date') : $period?->end_date?->format('Y-m-d') }}" required class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3 text-sm outline-none focus:border-blue-950 focus:ring-2 focus:ring-blue-950/10">
        </div>
        <label class="flex items-center gap-3 sm:col-span-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked((bool) ($formMatches ? old('is_active') : $period?->is_active)) class="h-4 w-4 rounded border-slate-300 text-blue-950 focus:ring-blue-950">
            <span class="text-sm font-semibold text-slate-900">Jadikan periode aktif</span>
        </label>
    </div>

    <div class="flex justify-end gap-2 border-t border-slate-200 px-6 py-4">
        <button type="button" onclick="closeDialog(this)" class="cursor-pointer rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Batal</button>
        <button type="submit" class="cursor-pointer rounded-md bg-[#0B8C79]/[80%] px-4 py-2 text-sm font-semibold text-white hover:bg-[#0B8C79]">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan' }}</button>
    </div>
</form>
