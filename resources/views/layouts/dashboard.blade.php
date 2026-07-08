<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>{{ $title ?? 'Dashboard' }} - Sahadat-Qu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        dialog.management-dialog {
            margin: auto;
            max-height: calc(100dvh - 2rem);
        }

        dialog.management-dialog::backdrop {
            background: rgba(15, 23, 42, .45);
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-950">
    <div class="min-h-screen lg:flex ">
        <button id="sidebar-backdrop" type="button" class="fixed inset-0 z-20 hidden bg-slate-950/40 lg:hidden" aria-label="Tutup menu"></button>
        <x-sidebar />

        <main class="min-h-screen min-w-0 flex-1 lg:ml-72 bg-[#F8FEFD]">
            <header class="sticky top-0 z-10 border-b border-slate-200 bg-white/95 px-4 py-3 backdrop-blur lg:px-8 lg:py-4">
                <div class="flex min-w-0 items-start gap-3">
                    <button id="sidebar-toggle" type="button" class="inline-flex h-10 w-10 shrink-0 cursor-pointer items-center justify-center rounded-md border border-slate-300 bg-white text-slate-700 hover:border-blue-950 hover:text-blue-950 lg:hidden" aria-controls="dashboard-sidebar" aria-expanded="false" aria-label="Buka menu">
                        <x-icon name="menu" />
                    </button>
                    <div class="min-w-0">
                        <h1 class="truncate text-xl font-semibold text-slate-950 sm:text-2xl">{{ $title ?? 'Dashboard' }}</h1>
                        {{-- <p class="mt-1 text-sm text-slate-500">{{ $description ?? 'Kelola data akademik Sahadat-Qu dengan rapi dan terpusat.' }}</p> --}}
                    </div>
                </div>
            </header>

            <div class="px-4 py-5 sm:px-5 lg:px-8">
                @if (session('status'))
                    <div class="mb-5 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
                @endif
                {{ $slot }}
            </div>
        </main>
    </div>
    <script>
        const sidebar = document.getElementById('dashboard-sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebarClose = document.getElementById('sidebar-close');
        const sidebarBackdrop = document.getElementById('sidebar-backdrop');

        function setSidebar(open) {
            sidebar?.classList.toggle('-translate-x-full', !open);
            sidebar?.classList.toggle('translate-x-0', open);
            sidebarBackdrop?.classList.toggle('hidden', !open);
            sidebarToggle?.setAttribute('aria-expanded', open ? 'true' : 'false');
        }

        sidebarToggle?.addEventListener('click', () => setSidebar(true));
        sidebarClose?.addEventListener('click', () => setSidebar(false));
        sidebarBackdrop?.addEventListener('click', () => setSidebar(false));

        window.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                setSidebar(false);
            }
        });
    </script>
</body>
</html>
