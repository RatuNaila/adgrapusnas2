@extends('layouts.app')

@section('title', 'Perpustakaan — Dashboard')
@section('header_title', 'Perpustakaan — Dashboard')

@section('content')
    <div x-data="dashboardApp()"
        class="relative min-h-screen bg-gradient-to-br from-indigo-100 via-purple-50 to-blue-100 p-6 overflow-hidden">

        {{-- Decorative blurred shapes --}}
        <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-gradient-to-br from-indigo-400 via-blue-400 to-purple-400 blur-3xl opacity-30 animate-pulse"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 rounded-full bg-gradient-to-tr from-pink-400 via-purple-300 to-indigo-400 blur-3xl opacity-25 animate-pulse"></div>

        {{-- Main content container with glass effect --}}
        <div class="relative backdrop-blur-lg bg-white/60 border border-white/30 rounded-3xl shadow-2xl p-6">

            {{-- Header --}}
            <div class="mb-6 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
                <div class="text-2xl font-bold text-indigo-900 drop-shadow-sm tracking-wide flex items-center gap-2">
                    📚 <span>Daftar Buku</span>
                </div>
                <a href="{{ route('books.create') }}"
                    class="rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-2.5 text-sm font-semibold shadow-md hover:shadow-lg hover:scale-[1.02] transition-all">
                    + Tambah Buku
                </a>
            </div>

            {{-- Search --}}
            <form method="GET" action="{{ route('books.index') }}" class="flex justify-end">
                <input x-model="q" name="q" value="{{ $q ?? '' }}" type="search" placeholder="Cari judul / penulis..."
                    class="mb-6 w-full md:w-80 rounded-xl border border-slate-300 bg-white/70 backdrop-blur-md px-4 py-2.5 text-sm
                          placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 shadow-sm" />
            </form>

            {{-- Alerts --}}
            @if (session('success'))
                <div id="alert-success"
                    class="mb-4 rounded-lg border border-green-400 bg-green-200/70 px-4 py-3 text-sm flex justify-between items-start text-green-800 shadow-sm">
                    <div class="font-medium">{{ session('success') }}</div>
                    <button type="button" class="ml-3 hover:opacity-70 transition"
                        onclick="document.getElementById('alert-success').remove()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            @if ($errors->any())
                <div id="alert-error"
                    class="mb-4 rounded-lg border border-rose-400 bg-rose-200/70 px-4 py-3 text-sm flex justify-between items-start text-rose-800 shadow-sm">
                    <ul class="list-disc pl-5 flex-1">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="ml-3 hover:opacity-70 transition"
                        onclick="document.getElementById('alert-error').remove()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            {{-- Table --}}
            <div class="overflow-x-auto rounded-2xl border border-white/50 bg-white/80 backdrop-blur-lg shadow-lg hover:shadow-xl transition-all duration-300">
                <table class="min-w-full text-sm">
                    <thead class="bg-gradient-to-r from-indigo-50 to-blue-50 border-b border-slate-200 text-indigo-900">
                        <tr class="text-slate-700">
                            <th class="px-4 py-3 text-left">No</th>
                            <th class="px-4 py-3 text-left">Cover</th>
                            <th class="px-4 py-3 text-left">Judul</th>
                            <th class="px-4 py-3 text-left">Penulis</th>
                            <th class="px-4 py-3 text-left">ISBN</th>
                            <th class="px-4 py-3 text-left">Kategori</th>
                            <th class="px-4 py-3 text-left">Stok</th>
                            <th class="px-4 py-3 text-left">Harga</th>
                            <th class="px-4 py-3 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($books as $b)
                            <tr class="border-t border-slate-200 hover:bg-indigo-50/40 transition">
                                <td class="px-4 py-3 text-slate-700">{{ ($books->firstItem() ?? 0) + $loop->index }}</td>
                                <td class="px-4 py-3">
                                    @if ($b->foto)
                                        <img src="{{ Str::startsWith($b->foto, ['http://', 'https://']) ? $b->foto : Storage::url($b->foto) }}"
                                            alt="cover"
                                            class="h-12 w-9 object-cover rounded-md border border-slate-200 bg-slate-100 shadow-sm">
                                    @else
                                        <div
                                            class="h-12 w-9 rounded-md border border-slate-200 bg-slate-100 grid place-items-center text-xl">
                                            📘
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $b->judul }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $b->penulis }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $b->isbn }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center rounded-md bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700 border border-blue-200">
                                        {{ strtoupper($b->kategori) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-700">{{ $b->stok }}</td>
                                <td class="px-4 py-3 text-slate-900">Rp {{ number_format($b->harga, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('books.edit', $b->id) }}"
                                            class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-500 text-white px-3 py-1.5 text-sm font-medium hover:scale-[1.03] transition">
                                            ✏ Edit
                                        </a>

                                        <button @click="openConfirm({{ $b->id }}, '{{ addslashes($b->judul) }}')"
                                            class="rounded-lg bg-gradient-to-r from-rose-500 to-red-600 text-white px-3 py-1.5 hover:scale-[1.03] transition">
                                            🗑 Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-6 text-center text-slate-500">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $books->withQueryString()->links('components.pagination') }}
            </div>
        </div>

        {{-- Modal Konfirmasi Hapus --}}
        <div x-show="confirmOpen" x-cloak x-transition.opacity
            class="fixed inset-0 z-50 bg-black/60 grid place-items-center p-4 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white/90 backdrop-blur-md p-6 shadow-2xl animate-fade-in"
                @click.outside="confirmOpen=false">
                <div class="flex items-center gap-3 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-rose-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M6 7h12M9 7V4h6v3m2 0v13a2 2 0 01-2 2H8a2 2 0 01-2-2V7z" />
                    </svg>
                    <h2 class="text-lg font-semibold text-slate-900">Hapus Buku</h2>
                </div>
                <p class="text-sm text-slate-600 mb-6">
                    Apakah Anda yakin ingin menghapus <span class="font-semibold text-slate-900"
                        x-text="deleteTitle"></span>?
                </p>
                <div class="flex items-center justify-end gap-3">
                    <button @click="confirmOpen=false"
                        class="rounded-lg border border-slate-300 bg-white px-4 py-2 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <form :action="deleteAction" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="rounded-lg bg-gradient-to-r from-rose-500 to-red-600 text-white px-4 py-2 font-medium hover:scale-[1.02] transition">
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function dashboardApp() {
            return {
                q: @json($q ?? ''),
                delUrl: @json(route('books.destroy', '_ID_')),
                confirmOpen: false,
                currentId: null,
                deleteTitle: '',
                openConfirm(id, title) {
                    this.currentId = id;
                    this.deleteTitle = title || '';
                    this.confirmOpen = true;
                },
                get deleteAction() {
                    return this.delUrl.replace('_ID_', this.currentId);
                }
            }
        }
    </script>
@endpush