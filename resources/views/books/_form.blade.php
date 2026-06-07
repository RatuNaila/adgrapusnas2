@php
    // Konfigurasi default
    $method = $method ?? 'POST';
    $action = $action ?? route('books.store');
    $submitLabel = $submitLabel ?? 'Simpan';
    $book = $book ?? null;

    // Value helper
    $v = fn($field, $fallback = '') => old($field, $book[$field] ?? $fallback);
    $coverPath = $book?->foto ?? '';
    $coverUrl = $coverPath
        ? (\Illuminate\Support\Str::startsWith($coverPath, ['http://', 'https://'])
            ? $coverPath
            : \Illuminate\Support\Facades\Storage::url($coverPath))
        : '';
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" x-data="bookForm()">
    @csrf
    @if (strtoupper($method) !== 'POST')
        @method($method)
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <label class="block">
            <span class="block text-sm mb-1 text-slate-700">Judul</span>
            <input name="judul" required value="{{ $v('judul') }}"
                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5
                          text-slate-900 placeholder:text-slate-400
                          focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent" />
        </label>

        <label class="block">
            <span class="block text-sm mb-1 text-slate-700">Penulis</span>
            <input name="penulis" value="{{ $v('penulis') }}"
                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5
                          text-slate-900 placeholder:text-slate-400
                          focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent" />
        </label>

        <label class="block">
            <span class="block text-sm mb-1 text-slate-700">ISBN</span>
            <input name="isbn" value="{{ $v('isbn') }}"
                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5
                          text-slate-900 placeholder:text-slate-400
                          focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent" />
        </label>

        <label class="block">
            <span class="block text-sm mb-1 text-slate-700">Kategori</span>
            <select name="kategori"
                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                @php $kategoriNow = $v('kategori', $defaultCategory ?? 'fiksi'); @endphp
                <option value="Fiksi" @selected($kategoriNow === 'Fiksi')>Fiksi</option>
                <option value="Nonfiksi" @selected($kategoriNow === 'Nonfiksi')>Nonfiksi</option>
            </select>
        </label>


        <label class="block">
            <span class="block text-sm mb-1 text-slate-700">Stok</span>
            <input name="stok" type="number" min="0" value="{{ $v('stok', 0) }}"
                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5
                          text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent" />
        </label>

        <label class="block">
            <span class="block text-sm mb-1 text-slate-700">Harga</span>
            <input name="harga" type="number" min="0" value="{{ $v('harga', 0) }}"
                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5
                          text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent" />
        </label>

        {{-- Cover --}}
        <label class="block ">
            <span class="block text-sm mb-1 text-slate-700">Cover Buku</span>
            <input type="file" name="foto" accept="image/*"
                class="block w-full text-sm
                          file:mr-3 file:rounded-lg file:border file:border-slate-300
                          file:bg-white file:px-4 file:py-2.5 file:text-slate-700
                          hover:file:bg-slate-50 cursor-pointer"
                @change="onFileChange($event)">

            <div
                class="mt-3 rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm text-slate-700 flex items-center gap-3">
                <template x-if="!fileInfo.name"><span>Belum ada gambar dipilih</span></template>
                <template x-if="fileInfo.name">
                    <div>
                        <p><strong x-text="fileInfo.name"></strong></p>
                        <p class="text-xs text-slate-500" x-text="fileInfo.sizeKB + ' KB'"></p>
                    </div>
                </template>
            </div>

            <div class="mt-3">
                <template x-if="previewUrl">
                    <img :src="previewUrl"
                        class="h-32 rounded-lg object-cover border border-slate-200 bg-slate-100" alt="Preview Cover">
                </template>
                @if ($coverUrl)
                    <template x-if="!previewUrl">
                        <img src="{{ $coverUrl }}"
                            class="h-32 rounded-lg object-cover border border-slate-200 bg-slate-100"
                            alt="Cover Saat Ini">
                    </template>
                @endif
            </div>
        </label>

        <label class="block ">
            <span class="block text-sm mb-1 text-slate-700">Deskripsi</span>
            <textarea name="deskripsi" rows="5"
                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5
                             text-slate-900 placeholder:text-slate-400
                             focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent">{{ $v('deskripsi') }}</textarea>
        </label>
    </div>

    <div class="mt-6 flex items-center gap-2">
        <button type="submit"
            class="rounded-lg text-base bg-blue-600 text-white px-4 py-2.5 font-semibold hover:bg-blue-700
                       focus:outline-none focus:ring-2 focus:ring-blue-600">
            {{ $submitLabel }}
        </button>
        <a href="{{ url()->previous() }}"
            class="rounded-lg text-base border border-slate-300 bg-white px-4 py-2.5 hover:bg-slate-50">
            Batal
        </a>
    </div>
</form>

@push('scripts')
    <script>
    
        function bookForm() {
            return {
                previewUrl: '',
                fileInfo: {
                    name: '',
                    sizeKB: ''
                },
                onFileChange(e) {
                    const file = e.target.files?.[0];
                    if (!file) {
                        this.previewUrl = '';
                        this.fileInfo = {
                            name: '',
                            sizeKB: ''
                        };
                        return;
                    }
                    this.previewUrl = URL.createObjectURL(file);
                    this.fileInfo = {
                        name: file.name,
                        sizeKB: (file.size / 1024).toFixed(1)
                    };
                }
            }
        }
    </script>
@endpush
