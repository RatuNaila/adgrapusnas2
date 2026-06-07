<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard Admin — Buku</title>

    <!-- Tailwind (dev only). Production: gunakan Vite/PostCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            /* theme tokens light */
            --bg: #f7fafc;
            /* body background (slate-50-ish) */
            --card: #ffffff;
            /* card/section background */
            --muted: #64748b;
            /* slate-500 */
            --text: #0f172a;
            /* slate-900 */
            --border: #e2e8f0;
            /* slate-200 */
            --ring: #2563eb;
            /* blue-600 */
            --dot: rgba(15, 23, 42, .06);
        }

        html,
        body {
            height: 100%;
            font-family: Poppins, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial;
            color: var(--text);
        }

        .glass {
            /* soft glass for light */
            background: linear-gradient(180deg, rgba(255, 255, 255, .96), rgba(255, 255, 255, .92));
            backdrop-filter: blur(8px);
        }

        /* Collapse (desktop) */
        #sidebar[data-collapsed="true"] .label {
            display: none;
        }

        #sidebar[data-collapsed="true"] .brand-text {
            display: none;
        }

        #sidebar a {
            display: flex;
            align-items: center;
            gap: .6rem;
        }

        /* #sidebar svg.icon,
        #sidebar img.icon {
            width: 1.25rem;
            height: 1.25rem;
            opacity: .9;
        } */

        #btn-toggle svg,
        #btn-toggle img {
            width: 1.25rem;
            height: 1.25rem;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="min-h-screen bg-[var(--bg)] text-slate-900 flex" x-data="{
    mq: null,
    collapsed: false, // desktop collapse state
    mobileOpen: false, // drawer mobile state
    logoutOpen: false,
    doLogout() {
        document.getElementById('logout-form')?.submit();
    },
    init() {
        this.mq = window.matchMedia('(min-width: 768px)');
        if (!this.mq.matches) this.mobileOpen = false;
        this.mq.addEventListener?.('change', e => {
            if (e.matches) { // masuk desktop
                this.mobileOpen = false;
                document.body.style.overflow = '';
            } else { // masuk mobile
                this.collapsed = false;
            }
        });
    },
    toggleSidebar() {
        if (this.mq?.matches) {
            this.collapsed = !this.collapsed;
        } else {
            this.mobileOpen = !this.mobileOpen;
            document.body.style.overflow = this.mobileOpen ? 'hidden' : '';
        }
    },
    closeMobile() {
        if (!this.mq?.matches) {
            this.mobileOpen = false;
            document.body.style.overflow = '';
        }
    }
}">

    <!-- Mobile backdrop -->
    <div id="backdrop" x-show="mobileOpen" x-transition.opacity class="fixed inset-0 bg-black/50 z-30 md:hidden"
        @click="closeMobile()"></div>

    <!-- Sidebar -->
    <aside id="sidebar" :data-collapsed="collapsed"
        class="fixed md:sticky md:top-0 z-40 top-0 left-0 h-full md:h-[100dvh]
                w-72 md:w-60 md:translate-x-0 md:flex flex-col
                border-r border-[var(--border)] bg-[var(--card)] p-4 space-y-2
                transition-all duration-300 shadow-sm"
        :class="{
            '-translate-x-full': !mobileOpen,
            'translate-x-0': mobileOpen,
            'md:w-20': collapsed,
            'md:w-60': !collapsed
        }">

        <div class="text-xl font-semibold mb-3 px-3 text-center">
            <!-- Icon brand -->
            <center>

                <svg xmlns="http://www.w3.org/2000/svg" class="size-6 icon text-blue-600" viewBox="0 0 24 24"
                    fill="currentColor">
                    <path
                        d="M11.584 2.376a.75.75 0 0 1 .832 0l9 6a.75.75 0 1 1-.832 1.248L12 3.901 3.416 9.624a.75.75 0 0 1-.832-1.248l9-6Z" />
                    <path fill-rule="evenodd"
                        d="M20.25 10.332v9.918H21a.75.75 0 0 1 0 1.5H3a.75.75 0 0 1 0-1.5h.75v-9.918a.75.75 0 0 1 .634-.74A49.109 49.109 0 0 1 12 9c2.59 0 5.134.202 7.616.592a.75.75 0 0 1 .634.74Zm-7.5 2.418a.75.75 0 0 0-1.5 0v6.75a.75.75 0 0 0 1.5 0v-6.75Zm3-.75a.75.75 0 0 1 .75.75v6.75a.75.75 0 0 1-1.5 0v-6.75a.75.75 0 0 1 .75-.75ZM9 12.75a.75.75 0 0 0-1.5 0v6.75a.75.75 0 0 0 1.5 0v-6.75Z"
                        clip-rule="evenodd" />
                    <path d="M12 7.875a1.125 1.125 0 1 0 0-2.25 1.125 1.125 0 0 0 0 2.25Z" />
                </svg>
            </center>
            <span class="brand-text text-base">Pusmedia</span>
        </div>

        <a href="#" class="rounded-lg px-3 py-2 bg-blue-600 text-white font-medium hover:bg-blue-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 icon" viewBox="0 0 24 24" fill="currentColor">
                <path
                    d="M11.25 4.533A9.707 9.707 0 0 0 6 3a9.735 9.735 0 0 0-3.25.555.75.75 0 0 0-.5.707v14.25a.75.75 0 0 0 1 .707A8.237 8.237 0 0 1 6 18.75c1.995 0 3.823.707 5.25 1.886V4.533ZM12.75 20.636A8.214 8.214 0 0 1 18 18.75c.966 0 1.89.166 2.75.47a.75.75 0 0 0 1-.708V4.262a.75.75 0 0 0-.5-.707A9.735 9.735 0 0 0 18 3a9.707 9.707 0 0 0-5.25 1.533v16.103Z" />
            </svg>
            <span class="label">Buku</span>
        </a>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">
        <!-- Topbar -->
        <header class="border-b border-[var(--border)] bg-[var(--card)]">
            <div class="px-4 py-3 flex items-center gap-3">
                <button id="btn-toggle" @click="toggleSidebar()" :aria-expanded="mobileOpen ? 'true' : 'false'"
                    aria-label="Toggle sidebar"
                    class="rounded-lg border border-[var(--border)] bg-white px-3 py-2 hover:bg-slate-50 md:px-2 md:py-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-slate-700" viewBox="0 0 24 24"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M3 6.75A.75.75 0 0 1 3.75 6h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 6.75ZM3 12a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 12Zm0 5.25a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <div class="font-semibold truncate">Dashboard Buku</div>

                <div class="ms-auto flex items-center gap-3 text-sm">
                    <span class="opacity-80 hidden sm:inline text-slate-700">Admin</span>
                    <button @click="logoutOpen = true"
                        class="rounded-lg border border-[var(--border)] bg-white px-3 py-1.5 hover:bg-slate-50">
                        Keluar
                    </button>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="px-4 py-6 w-full">
            @yield('content')
        </main>

        <!-- Logout Modal -->
        <div x-show="logoutOpen" x-cloak x-transition.opacity
            class="fixed inset-0 z-50 bg-black/60 grid place-items-center p-4">
            <div class="w-full max-w-md rounded-2xl border border-[var(--border)] bg-[var(--card)] p-6 shadow-xl"
                @click.outside="logoutOpen=false">
                <div class="flex items-center gap-3 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3H6A2.25 2.25 0 003.75 5.25v13.5A2.25 2.25 0 006 21h7.5a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                    <h2 class="text-lg font-semibold">Keluar Akun</h2>
                </div>
                <p class="text-sm text-slate-600 mb-6">Apakah Anda yakin ingin logout dari akun ini?</p>
                <div class="flex items-center justify-end gap-3">
                    <button @click="logoutOpen=false"
                        class="rounded-lg border border-[var(--border)] bg-white px-4 py-2 hover:bg-slate-50">
                        Batal
                    </button>
                    <button @click="doLogout()"
                        class="rounded-lg bg-rose-600 text-white px-4 py-2 hover:bg-rose-700 font-medium">
                        Ya, Logout
                    </button>
                </div>

                {{-- Laravel session logout --}}
                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>

</html>
