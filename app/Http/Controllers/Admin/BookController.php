<?php

namespace App\Http\Controllers\Admin;

use App\Models\Book;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $books = Book::query()
            ->when($q, fn($w) =>
                $w->where(fn($x) =>
                    $x->where('judul', 'like', "%{$q}%")
                      ->orWhere('penulis', 'like', "%{$q}%")
                      ->orWhere('isbn', 'like', "%{$q}%")
                )
            )
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('books.index', compact('books', 'q'));
    }

    public function indexAPI(Request $request)
    {
        $qRaw    = (string) $request->query('search', '');
        $q       = trim($qRaw);
        $perPage = (int) $request->integer('per_page', 12);

        // Escape wildcard untuk LIKE
        $needle = str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $q);

        $paginator = Book::query()
            ->when($q !== '', function ($w) use ($needle) {
                $w->where(function ($x) use ($needle) {
                    $x->where('judul',   'like', "%{$needle}%")
                      ->orWhere('penulis','like', "%{$needle}%")
                      ->orWhere('isbn',   'like', "%{$needle}%");
                });
            })
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->query());

        // Map data ke bentuk API
        $data = $paginator->getCollection()->transform(function (Book $b) {
            $foto = $b->foto ?: null;
            $fotoUrl = $foto
                ? (Str::startsWith($foto, ['http://','https://']) ? $foto : Storage::url($foto))
                : null;

            return [
                'id'        => (int) $b->id,
                'judul'     => $b->judul,
                'penulis'   => $b->penulis,
                'isbn'      => $b->isbn,
                'kategori'  => $b->kategori ?? null,  
                'stok'      => (int) $b->stok,
                'harga'     => (int) $b->harga,
                'deskripsi' => $b->deskripsi,
                'foto_url'  => $fotoUrl,
                'created_at'=> optional($b->created_at)->toIso8601String(),
                'updated_at'=> optional($b->updated_at)->toIso8601String(),
            ];
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'page'      => $paginator->currentPage(),
                'per_page'  => $paginator->perPage(),
                'total'     => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function showAPI($id)
    {
        $book = Book::findOrFail($id);

        if (!$book) {
            return response()->json([
                'message' => 'Book not found',
            ], 404);
        }

        return response()->json([
            'data' => [
                'id'        => $book->id,
                'judul'     => $book->judul,
                'penulis'   => $book->penulis,
                'kategori'  => $book->kategori,
                'isbn'      => $book->isbn,
                'harga'     => $book->harga ? (int) $book->harga : null,
                'foto'      => $book->foto ? $book->foto : null,
                'deskripsi' => $book->deskripsi,
                'created_at'=> optional($book->created_at)->toIso8601String(),
                'updated_at'=> optional($book->updated_at)->toIso8601String(),
            ],
        ]);
    }

    public function create()
    {
        return view('books.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'     => 'required|string|max:255',
            'penulis'   => 'required|string|max:255',
            'isbn'      => 'required|string|max:255',
            'harga'     => 'required|numeric',
            'stok'      => 'required|integer',
            'kategori'  => 'required|in:Fiksi,Nonfiksi',
            'deskripsi' => 'nullable|string',
            'foto'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $filePath = $request->file('foto')->store('book_images', 'public');
            $validated['foto'] = $filePath;
        }

        Book::create($validated);

        return redirect()->route('books.index')->with('success', 'Buku berhasil ditambahkan');
    }

    public function edit(Book $book)
    {
        return view('books.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'judul'     => 'required|string|max:255',
            'penulis'   => 'required|string|max:255',
            'isbn'      => 'required|string|max:255',
            'harga'     => 'required|numeric',
            'stok'      => 'required|integer',
            'kategori'  => 'required|in:Fiksi,Nonfiksi', 
            'deskripsi' => 'nullable|string',
            'foto'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($book->foto) {
                \Storage::disk('public')->delete($book->foto);
            }
            $filePath = $request->file('foto')->store('book_images', 'public');
            $validated['foto'] = $filePath;
        }

        $book->update($validated);

        return redirect()->route('books.index')->with('success', 'Buku berhasil diperbarui');
    }

    public function destroy(Book $book)
    {
        if ($book->foto) {
            \Storage::disk('public')->delete($book->foto);
        }

        $book->delete();

        return redirect()->route('books.index')->with('success', 'Buku berhasil dihapus');
    }
}
