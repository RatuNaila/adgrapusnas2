@extends('auth.layouts.app')

@section('title', 'Register')

@section('content')
    {{-- Full Background --}}
    <div class="min-h-screen w-full flex items-center justify-center relative bg-no-repeat bg-cover bg-center"
        style="background-image: url('https://t3.ftcdn.net/jpg/03/54/29/64/360_F_354296454_0c5wDTLn3wsqXwru1fEekghkJaCkERL4.jpg');">
        
        {{-- Overlay --}}
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>

        {{-- Grid layout agar teks kiri dan form kanan --}}
        <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 w-full max-w-6xl mx-4 gap-8 items-center">
            {{-- Left Text --}}
            <div class="hidden md:block text-white">
                <h1 class="text-4xl font-bold leading-tight mb-2">Selamat Datang di Adgrapusnas</h1>
                <p class="text-lg opacity-90">Silakan daftar untuk membuat akun baru</p>
            </div>

            {{-- Register Box --}}
            <section
                class="backdrop-blur-md bg-white/20 border border-white/30 rounded-2xl p-6 md:p-8 shadow-2xl w-full max-w-md mx-auto">
                <h2 class="text-2xl font-bold text-center text-white mb-6">REGISTER</h2>

                @if ($errors->any())
                    <div id="alert-error"
                        class="mb-4 rounded-lg border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-700"
                        role="alert">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                    @csrf
                    {{-- Nama --}}
                    <div>
                        <label for="name" class="block text-sm text-white mb-1">Nama Lengkap</label>
                        <input id="name" name="name" type="text" required placeholder="Masukkan nama lengkap"
                            class="w-full rounded-lg border border-gray-300 bg-white/80 px-4 py-2.5 text-slate-900
                                   placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm text-white mb-1">Email</label>
                        <input id="email" name="email" type="email" required placeholder="Masukkan email"
                            class="w-full rounded-lg border border-gray-300 bg-white/80 px-4 py-2.5 text-slate-900
                                   placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm text-white mb-1">Kata Sandi</label>
                        <input id="password" name="password" type="password" required placeholder="Masukkan kata sandi"
                            class="w-full rounded-lg border border-gray-300 bg-white/80 px-4 py-2.5 text-slate-900
                                   placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm text-white mb-1">Konfirmasi Kata Sandi</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required placeholder="Ulangi kata sandi"
                            class="w-full rounded-lg border border-gray-300 bg-white/80 px-4 py-2.5 text-slate-900
                                   placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                    </div>

                    {{-- Tombol Daftar --}}
                    <button type="submit"
                        class="w-full rounded-lg bg-blue-600 text-white px-4 py-2.5 font-semibold
                               hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Daftar
                    </button>
                </form>

                <p class="mt-4 text-center text-sm text-white">
                    Sudah punya akun?
                    <a class="text-blue-400 hover:underline" href="{{ route('login') }}">Masuk di sini</a>
                </p>
            </section>
        </div>
    </div>
@endsection


