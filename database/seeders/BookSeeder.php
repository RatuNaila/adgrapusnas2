<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            [
                'judul' => 'Pemrograman Laravel untuk Pemula',
                'penulis' => 'Dikha Adi Nugraha',
                'isbn' => '978-623-001-1001',
                'kategori' => 'Non-fiksi',
                'stok' => 7,
                'harga' => 95000,
                'deskripsi' => 'Panduan langkah demi langkah membangun aplikasi Laravel dari nol.',
                'foto' => 'book_images/YrMSm0iLKPxcQhlRyZqRAKKmsAxRGi2skY2OBzzs.jpg',
            ],
            [
                'judul' => 'Dasar-Dasar Flutter',
                'penulis' => 'Aulia Rahma',
                'isbn' => '978-623-001-1002',
                'kategori' => 'Non-fiksi',
                'stok' => 10,
                'harga' => 120000,
                'deskripsi' => 'Mengenal widget, state management, dan best practice Flutter.',
                'foto' => 'book_images/YrMSm0iLKPxcQhlRyZqRAKKmsAxRGi2skY2OBzzs.jpg',
            ],
            [
                'judul' => 'Clean Code (Edisi Indonesia)',
                'penulis' => 'Robert C. Martin',
                'isbn' => '978-623-001-1003',
                'kategori' => 'Non-fiksi',
                'stok' => 4,
                'harga' => 135000,
                'deskripsi' => 'Prinsip menulis kode bersih, mudah dibaca, dan mudah dirawat.',
                'foto' => 'book_images/YrMSm0iLKPxcQhlRyZqRAKKmsAxRGi2skY2OBzzs.jpg',
            ],
            [
                'judul' => 'Database Relasional dengan MySQL',
                'penulis' => 'Nanda Putra',
                'isbn' => '978-623-001-1004',
                'kategori' => 'Non-fiksi',
                'stok' => 8,
                'harga' => 99000,
                'deskripsi' => 'Desain skema, normalisasi, indexing, dan query optimasi.',
                'foto' => 'book_images/YrMSm0iLKPxcQhlRyZqRAKKmsAxRGi2skY2OBzzs.jpg',
            ],
            [
                'judul' => 'Design Pattern untuk PHP',
                'penulis' => 'Rama Wijaya',
                'isbn' => '978-623-001-1005',
                'kategori' => 'Non-fiksi',
                'stok' => 5,
                'harga' => 110000,
                'deskripsi' => 'Implementasi pattern populer di ekosistem PHP modern.',
                'foto' => 'book_images/YrMSm0iLKPxcQhlRyZqRAKKmsAxRGi2skY2OBzzs.jpg',
            ],
            [
                'judul' => 'Tailwind CSS: Utility-First Design',
                'penulis' => 'Mega Larasati',
                'isbn' => '978-623-001-1006',
                'kategori' => 'Non-fiksi',
                'stok' => 12,
                'harga' => 88000,
                'deskripsi' => 'Membangun UI cepat dan konsisten menggunakan Tailwind CSS.',
                'foto' => 'book_images/YrMSm0iLKPxcQhlRyZqRAKKmsAxRGi2skY2OBzzs.jpg',
            ],
            [
                'judul' => 'Membangun REST API dengan Laravel',
                'penulis' => 'Indra Saputra',
                'isbn' => '978-623-001-1007',
                'kategori' => 'Non-fiksi',
                'stok' => 6,
                'harga' => 105000,
                'deskripsi' => 'Autentikasi, validasi, resource, dan best practices API.',
                'foto' => 'book_images/YrMSm0iLKPxcQhlRyZqRAKKmsAxRGi2skY2OBzzs.jpg',
            ],
            [
                'judul' => 'Git & GitHub untuk Kolaborasi',
                'penulis' => 'Yuni Kartika',
                'isbn' => '978-623-001-1008',
                'kategori' => 'Non-fiksi',
                'stok' => 9,
                'harga' => 75000,
                'deskripsi' => 'Workflow branching, PR, code review, dan release management.',
                'foto' => 'book_images/YrMSm0iLKPxcQhlRyZqRAKKmsAxRGi2skY2OBzzs.jpg',
            ],
            [
                'judul' => 'Testing Otomatis dengan PHPUnit',
                'penulis' => 'Dwi Saputro',
                'isbn' => '978-623-001-1009',
                'kategori' => 'Non-fiksi',
                'stok' => 3,
                'harga' => 99000,
                'deskripsi' => 'Unit testing, mocking, dan integrasi CI/CD.',
                'foto' => 'book_images/YrMSm0iLKPxcQhlRyZqRAKKmsAxRGi2skY2OBzzs.jpg',
            ],
            [
                'judul' => 'Dasar-Dasar Algoritma',
                'penulis' => 'Sinta Ayu',
                'isbn' => '978-623-001-1010',
                'kategori' => 'Non-fiksi',
                'stok' => 11,
                'harga' => 89000,
                'deskripsi' => 'Struktur data, kompleksitas waktu, dan pola pemecahan masalah.',
                'foto' => 'book_images/YrMSm0iLKPxcQhlRyZqRAKKmsAxRGi2skY2OBzzs.jpg',
            ],
            [
                'judul' => 'JavaScript Modern (ES6+)',
                'penulis' => 'Bima Pradana',
                'isbn' => '978-623-001-1011',
                'kategori' => 'Non-fiksi',
                'stok' => 5,
                'harga' => 99000,
                'deskripsi' => 'Fitur ES6+, async/await, modul, dan tooling modern.',
                'foto' => 'book_images/YrMSm0iLKPxcQhlRyZqRAKKmsAxRGi2skY2OBzzs.jpg',
            ],
            [
                'judul' => 'Refactoring UI',
                'penulis' => 'Adam Wathan',
                'isbn' => '978-623-001-1012',
                'kategori' => 'Non-fiksi',
                'stok' => 4,
                'harga' => 150000,
                'deskripsi' => 'Tip praktis meningkatkan tampilan UI tanpa desain ulang total.',
                'foto' => 'book_images/YrMSm0iLKPxcQhlRyZqRAKKmsAxRGi2skY2OBzzs.jpg',
            ],
            [
                'judul' => 'Kotlin untuk Android',
                'penulis' => 'Fajar Nugroho',
                'isbn' => '978-623-001-1013',
                'kategori' => 'Non-fiksi',
                'stok' => 7,
                'harga' => 115000,
                'deskripsi' => 'Dasar Kotlin dan praktik membangun aplikasi Android.',
                'foto' => 'book_images/YrMSm0iLKPxcQhlRyZqRAKKmsAxRGi2skY2OBzzs.jpg',
            ],
            [
                'judul' => 'Arsitektur Mikroservis',
                'penulis' => 'Laras Puspita',
                'isbn' => '978-623-001-1014',
                'kategori' => 'Non-fiksi',
                'stok' => 6,
                'harga' => 140000,
                'deskripsi' => 'Service decomposition, komunikasi, observability, dan deployment.',
                'foto' => 'book_images/YrMSm0iLKPxcQhlRyZqRAKKmsAxRGi2skY2OBzzs.jpg',
            ],
            [
                'judul' => 'Docker & Containerization',
                'penulis' => 'Rizky Maulana',
                'isbn' => '978-623-001-1015',
                'kategori' => 'Non-fiksi',
                'stok' => 5,
                'harga' => 130000,
                'deskripsi' => 'Membungkus aplikasi, image, container, dan orkestrasi dasar.',
                'foto' => 'book_images/YrMSm0iLKPxcQhlRyZqRAKKmsAxRGi2skY2OBzzs.jpg',
            ],
        ];

        foreach ($books as $b) {
            Book::create($b);
        }
    }
}
