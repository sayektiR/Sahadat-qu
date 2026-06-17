<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ($stats as $label => $value)
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
            <p class="mt-3 text-3xl font-semibold text-blue-950">{{ $value }}</p>
        </section>
    @endforeach
</div>
