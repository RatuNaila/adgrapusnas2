<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Perpustakaan — @yield('title')</title>

    <!-- Tailwind (dev). Untuk production, pakai Vite/PostCSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #f7fafc;
            /* body background */
            --card: #ffffff;
            /* card background */
            --muted: #64748b;
            /* text muted slate-500 */
            --ring: #2563eb;
            /* blue-600 */
            --border: #e2e8f0;
            /* slate-200 */
            --dot: rgba(15, 23, 42, .05);
            /* soft navy dots */
        }

        html,
        body {
            height: 100%;
            font-family: Poppins, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial;
            color: #0f172a;
            /* slate-900 */
        }

        .glass {
            background: linear-gradient(180deg, rgba(255, 255, 255, .9), rgba(255, 255, 255, .86));
            backdrop-filter: blur(6px);
        }
    </style>
</head>

<body class="min-h-screen bg-[var(--bg)] text-slate-900">
    <!-- Background decor (versi light) -->
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full blur-3xl opacity-40 bg-blue-300/60"></div>
        <div class="absolute -bottom-24 -right-24 h-80 w-80 rounded-full blur-3xl opacity-40 bg-cyan-300/60"></div>
        <div class="absolute inset-0"
            style="background-image: radial-gradient(var(--dot) 1px, transparent 1px);
                    background-size: 22px 22px;">
        </div>
    </div>

    <main class="relative z-10 flex min-h-screen items-center justify-center px-4">
        @yield('content')
    </main>

    <script>
        // Toggle password (tetap jalan)
        document.querySelectorAll('[data-toggle-password]')?.forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-toggle-password');
                const input = document.getElementById(id);
                if (!input) return;
                const isPass = input.type === 'password';
                input.type = isPass ? 'text' : 'password';
                btn.textContent = isPass ? 'Sembunyikan' : 'Tampilkan';
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
