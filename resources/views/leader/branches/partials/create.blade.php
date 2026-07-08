<dialog id="create-branch" class="management-dialog w-full max-w-lg rounded-lg border border-slate-200 p-0 shadow-xl">
    <form method="POST" action="{{ route('leader.branches.store') }}" class="bg-white">
        @csrf

        <div class="border-b border-slate-200 px-6 py-4">
            <h3 class="text-lg font-semibold text-slate-950">
                Tambah Cabang
            </h3>
        </div>

        <div class="space-y-5 p-6">

            <div>
                <label class="block text-sm font-semibold">
                    Nama Cabang
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3">
            </div>

            <div>
                <label class="block text-sm font-semibold">
                    Alamat
                </label>

                <textarea
                    name="address"
                    rows="3"
                    required
                    class="mt-2 w-full rounded-md border border-slate-300 bg-slate-100 px-3 py-2">{{ old('address') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold">
                    Nomor Telepon
                </label>

                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone') }}"
                    required
                    class="mt-2 h-11 w-full rounded-md border border-slate-300 bg-slate-100 px-3">
            </div>

        </div>

        <div class="flex justify-end gap-2 border-t border-slate-200 px-6 py-4">

            <button
                type="button"
                onclick="closeDialog(this)"
                class="rounded-md border border-slate-300 px-4 py-2">
                Batal
            </button>

            <button
                type="submit"
                class="rounded-md bg-[#0B8C79] px-4 py-2 text-white">
                Simpan
            </button>

        </div>

    </form>
</dialog>