@extends('layouts.app')

@section('title', 'Perpustakaan — Tambah Buku')
@section('header_title', 'Perpustakaan — Tambah Buku')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-lg font-semibold">Tambah Buku</h1>
        <a href="{{ route('books.index') }}" class="rounded-lg bg-white/10 px-3 py-2 hover:bg-white/20">← Kembali</a>
    </div>

    @if ($errors->any())
        <div id="alert-error"
            class="mb-4 rounded-lg border border-rose-400 bg-rose-200 px-4 py-3 text-sm flex justify-between items-start text-rose-700">
            <ul class="list-disc pl-5 flex-1">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
            <button type="button" class="ml-3 hover:opacity-70" onclick="document.getElementById('alert-error').remove()">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    @include('books._form', [
        'method' => 'POST',
        'action' => route('books.store'),
        'submitLabel' => 'Simpan',
        'defaultType' => $defaultType ?? 'pinjam',
    ])
@endsection
