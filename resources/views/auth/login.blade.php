@extends('auth.layouts.app')

@section('title', 'Login')

@section('content')
<link href="https://t3.ftcdn.net/jpg/03/54/29/64/360_F_354296454_0c5wDTLn3wsqXwru1fEekghkJaCkERL4.jpg" rel="stylesheet">

<style>
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        font-family: 'Mangal Pro', sans-serif;
    }

    .login-bg {
        position: relative;
        width: 100vw;
        height: 100vh;
        background-image: url('https://t3.ftcdn.net/jpg/03/54/29/64/360_F_354296454_0c5wDTLn3wsqXwru1fEekghkJaCkERL4.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        z-index: 1;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }

    .content-container {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        gap: 2rem;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        max-width: 1100px;
        padding: 2rem;
        color: white;
    }

    @media (min-width: 768px) {
        .content-container {
            flex-direction: row;
        }
    }
</style>

<div class="login-bg">
    <div class="overlay"></div>

    <div class="content-container">
        {{-- Bagian Kiri: Teks --}}
        <div class="text-white font-extrabold text-4xl md:text-5xl leading-tight max-w-lg drop-shadow-xl">
            <p>Selamat Datang di Adgrapusnas</p>
        </div>

        {{-- Bagian Kanan: Form Login --}}
        <div class="glass-card w-full max-w-sm p-8 text-white">
            <h2 class="text-3xl font-extrabold mb-6 text-center tracking-wide">LOGIN</h2>

            {{-- Pesan sukses --}}
            @if (session()->has('success'))
                <div class="mb-4 rounded-lg border border-green-400 bg-green-200/40 px-4 py-3 text-sm text-green-100">
                    {{ session()->get('success') }}
                </div>
            @endif

            {{-- Pesan error --}}
            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-rose-400 bg-rose-200/40 px-4 py-3 text-sm text-rose-100">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium mb-1">User Name</label>
                    <input id="email" name="email" type="email" required autocomplete="username"
                        class="w-full rounded-lg border border-white/40 bg-white/20 px-4 py-2.5 text-white placeholder:text-white/60 focus:ring-2 focus:ring-white outline-none"
                        placeholder="Enter your email" />
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium mb-1">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password"
                        class="w-full rounded-lg border border-white/40 bg-white/20 px-4 py-2.5 text-white placeholder:text-white/60 focus:ring-2 focus:ring-white outline-none"
                        placeholder="Enter your password" />
                </div>

                <button type="submit"
                    class="w-full rounded-lg bg-blue-600 hover:bg-blue-700 transition font-semibold py-2.5 shadow-lg tracking-wide">
                    LOGIN
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-white/70">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-blue-300 hover:underline font-medium">Daftar di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection