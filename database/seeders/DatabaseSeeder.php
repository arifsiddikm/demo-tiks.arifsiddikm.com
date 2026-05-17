<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =====================================================================
        // USERS
        // =====================================================================
        DB::table('users')->insert([
            'name'       => 'Admin TIKS',
            'phone'      => '08000000000',
            'password'   => Hash::make('admin123'),
            'role'       => 'admin',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name'       => 'Budi Santoso',
            'phone'      => '08112345678',
            'password'   => Hash::make('user123'),
            'role'       => 'user',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // =====================================================================
        // CITIES
        // =====================================================================
        $cities = [
            ['name' => 'Cilegon',   'slug' => 'cilegon'],
            ['name' => 'Jakarta',   'slug' => 'jakarta'],
            ['name' => 'Bandung',   'slug' => 'bandung'],
            ['name' => 'Surabaya',  'slug' => 'surabaya'],
            ['name' => 'Tangerang', 'slug' => 'tangerang'],
            ['name' => 'Bekasi',    'slug' => 'bekasi'],
        ];
        foreach ($cities as $city) {
            DB::table('cities')->insert(array_merge($city, [
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // =====================================================================
        // CINEMAS
        // =====================================================================
        $cinemas = [
            ['city_id' => 1, 'name' => 'XXI Cilegon Center Mall', 'slug' => 'xxi-cilegon-center-mall', 'address' => 'Cilegon Center Mall Lt. 3, Jl. Veteran No.1, Cilegon, Banten'],
            ['city_id' => 1, 'name' => 'XXI ICON Mall Cilegon',   'slug' => 'xxi-icon-mall-cilegon',   'address' => 'ICON Mall Cilegon Lt. 2, Jl. Semang Raya, Cilegon, Banten'],
            ['city_id' => 2, 'name' => 'XXI Grand Indonesia',     'slug' => 'xxi-grand-indonesia',     'address' => 'Grand Indonesia Shopping Town, Jl. M.H. Thamrin No.1, Jakarta Pusat'],
            ['city_id' => 2, 'name' => 'XXI Plaza Senayan',       'slug' => 'xxi-plaza-senayan',       'address' => 'Plaza Senayan Lt. 4, Jl. Asia Afrika No.8, Jakarta Selatan'],
            ['city_id' => 3, 'name' => 'XXI Paris Van Java',      'slug' => 'xxi-paris-van-java',      'address' => 'Paris Van Java Mall Lt. 4, Jl. Sukajadi No.137-139, Bandung'],
            ['city_id' => 3, 'name' => 'XXI Cihampelas Walk',     'slug' => 'xxi-cihampelas-walk',     'address' => 'Cihampelas Walk Lt. 3, Jl. Cihampelas No.160, Bandung'],
            ['city_id' => 4, 'name' => 'XXI Tunjungan Plaza',     'slug' => 'xxi-tunjungan-plaza',     'address' => 'Tunjungan Plaza 6 Lt. 5, Jl. Basuki Rahmat No.8-12, Surabaya'],
            ['city_id' => 5, 'name' => 'XXI Summarecon Serpong',  'slug' => 'xxi-summarecon-serpong',  'address' => 'Summarecon Mall Serpong Lt. 3, Jl. Boulevard Gading Serpong, Tangerang'],
            ['city_id' => 6, 'name' => 'XXI Bekasi Cyber Park',   'slug' => 'xxi-bekasi-cyber-park',   'address' => 'Bekasi Cyber Park Lt. 3, Jl. Jend. Ahmad Yani No.1, Bekasi'],
        ];
        foreach ($cinemas as $cinema) {
            DB::table('cinemas')->insert(array_merge($cinema, [
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // =====================================================================
        // GENRES
        // =====================================================================
        $genres = ['Drama', 'Horor', 'Aksi', 'Komedi', 'Thriller', 'Animasi', 'Sci-Fi', 'Romantis', 'Petualangan'];
        foreach ($genres as $g) {
            DB::table('genres')->insert([
                'name'       => $g,
                'slug'       => Str::slug($g),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // =====================================================================
        // FILMS — 23 film 2026 (poster Unsplash URL, relate tiap tema)
        // Model Film::getPosterUrlAttribute sudah diupdate untuk support URL eksternal
        // =====================================================================
        $films = [
            // 1
            [
                'title'        => 'Tunggu Aku Sukses Nanti Ya',
                'slug'         => 'tunggu-aku-sukses-nanti-ya',
                'synopsis'     => 'Kisah perjuangan seorang pemuda dari desa yang berani bermimpi besar di kota besar. Dengan segala keterbatasan, ia berusaha membuktikan bahwa kesuksesan bukan monopoli orang kaya.',
                'duration'     => '112 menit', 'rating' => 'SU', 'language' => 'Indonesia',
                'director'     => 'Fajar Nugros',
                'cast'         => 'Iqbaal Ramadhan, Aurelie Moeremans, Bucek Depp, Maudy Koesnaedi',
                'release_date' => '2026-03-05', 'status' => 'now_showing',
                'poster'       => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=600&fit=crop',
            ],
            // 2
            [
                'title'        => 'Senin Harga Naik',
                'slug'         => 'senin-harga-naik',
                'synopsis'     => 'Komedi satir tentang kehidupan sehari-hari di pasar tradisional yang kacau balau ketika harga sembako mendadak naik setiap hari Senin.',
                'duration'     => '98 menit', 'rating' => 'SU', 'language' => 'Indonesia',
                'director'     => 'Ernest Prakasa',
                'cast'         => 'Raditya Dika, Tika Panggabean, Asri Welas, Boris Bokir',
                'release_date' => '2026-03-12', 'status' => 'now_showing',
                'poster'       => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=400&h=600&fit=crop',
            ],
            // 3
            [
                'title'        => 'Na Willa',
                'slug'         => 'na-willa',
                'synopsis'     => 'Drama keluarga yang menyentuh tentang seorang anak perempuan bernama Na yang mencari sosok saudara perempuannya, Willa, yang menghilang secara misterius.',
                'duration'     => '105 menit', 'rating' => '13+', 'language' => 'Indonesia',
                'director'     => 'Dira Sugandi',
                'cast'         => 'Nirina Zubir, Prilly Latuconsina, Nicholas Saputra',
                'release_date' => '2026-03-05', 'status' => 'now_showing',
                'poster'       => 'https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?w=400&h=600&fit=crop',
            ],
            // 4
            [
                'title'        => 'Santet',
                'slug'         => 'santet',
                'synopsis'     => 'Horor mistis yang mengisahkan sebuah keluarga yang terjebak dalam kutukan santet turun-temurun. Ketika anggota keluarga mulai tewas satu per satu, sang ibu berusaha memutus kutukan sebelum terlambat.',
                'duration'     => '110 menit', 'rating' => '17+', 'language' => 'Indonesia',
                'director'     => 'Rizal Mantovani',
                'cast'         => 'Laura Basuki, Mike Lucock, Kinaryosih',
                'release_date' => '2026-03-19', 'status' => 'now_showing',
                'poster'       => 'https://images.unsplash.com/photo-1509248961158-e54f6934749c?w=400&h=600&fit=crop',
            ],
            // 5
            [
                'title'        => 'Pelangi Mars',
                'slug'         => 'pelangi-mars',
                'synopsis'     => 'Film sci-fi Indonesia pertama yang mengisahkan misi berani tim ilmuwan muda Indonesia ke Planet Mars. Di antara bintang-bintang, mereka menemukan kehidupan, cinta, dan arti sejati keberanian.',
                'duration'     => '128 menit', 'rating' => 'SU', 'language' => 'Indonesia',
                'director'     => 'Hanung Bramantyo',
                'cast'         => 'Chicco Jerikho, Adinia Wirasti, Reza Rahadian',
                'release_date' => '2026-03-26', 'status' => 'now_showing',
                'poster'       => 'https://images.unsplash.com/photo-1446776811953-b23d57bd21aa?w=400&h=600&fit=crop',
            ],
            // 6
            [
                'title'        => 'Danur: Asal Muasal',
                'slug'         => 'danur-asal-muasal',
                'synopsis'     => 'Prekuel dari franchise Danur. Kisah Risa Saraswati pertama kali menemukan kemampuannya melihat arwah dan pertemuannya dengan teman-teman tak kasat mata.',
                'duration'     => '115 menit', 'rating' => '13+', 'language' => 'Indonesia',
                'director'     => 'Awi Suryadi',
                'cast'         => 'Prilly Latuconsina, Adhisty Zara, Fatih Unru',
                'release_date' => '2026-03-12', 'status' => 'now_showing',
                'poster'       => 'https://images.unsplash.com/photo-1501084817091-a4f3d1d19e07?w=400&h=600&fit=crop',
            ],
            // 7
            [
                'title'        => 'Aku Harus Mati',
                'slug'         => 'aku-harus-mati',
                'synopsis'     => 'Thriller psikologis tentang seorang pria yang divonis mati oleh dokter namun justru menemukan semangat hidup yang belum pernah ia rasakan sebelumnya.',
                'duration'     => '103 menit', 'rating' => '13+', 'language' => 'Indonesia',
                'director'     => 'Anggy Umbara',
                'cast'         => 'Adipati Dolken, Sheila Dara Aisha, Ibnu Jamil',
                'release_date' => '2026-03-19', 'status' => 'now_showing',
                'poster'       => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=400&h=600&fit=crop',
            ],
            // 8
            [
                'title'        => 'They Will Kill You',
                'slug'         => 'they-will-kill-you',
                'synopsis'     => 'Thriller aksi tentang seorang mantan agen rahasia yang terpaksa kembali ke dunia gelap ketika keluarganya dijadikan sandera oleh sindikat internasional.',
                'duration'     => '120 menit', 'rating' => '17+', 'language' => 'Indonesia',
                'director'     => 'Timo Tjahjanto',
                'cast'         => 'Joe Taslim, Yayan Ruhian, Marthino Lio',
                'release_date' => '2026-03-26', 'status' => 'now_showing',
                'poster'       => 'https://images.unsplash.com/photo-1534278931827-8a259344abe7?w=400&h=600&fit=crop',
            ],
            // 9
            [
                'title'        => 'Warung Pocong',
                'slug'         => 'warung-pocong',
                'synopsis'     => 'Komedi horor tentang sebuah warung makan yang ternyata dikelola oleh para pocong. Ketika pelanggan mulai merasakan hal-hal aneh, pemilik warung harus memilih antara bisnis atau mengungkap rahasianya.',
                'duration'     => '95 menit', 'rating' => 'SU', 'language' => 'Indonesia',
                'director'     => 'Dimas Djayadiningrat',
                'cast'         => 'Cak Lontong, Sule, Parto Patrio, Azis Gagap',
                'release_date' => '2026-03-05', 'status' => 'now_showing',
                'poster'       => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400&h=600&fit=crop',
            ],
            // 10
            [
                'title'        => 'Ayah, Ini Arahnya Kemana Ya?',
                'slug'         => 'ayah-ini-arahnya-kemana-ya',
                'synopsis'     => 'Komedi keluarga hangat tentang seorang ayah yang mengajak keluarganya road trip keliling Jawa tanpa GPS. Petualangan kocak yang mempererat hubungan keluarga yang renggang.',
                'duration'     => '102 menit', 'rating' => 'SU', 'language' => 'Indonesia',
                'director'     => 'Monty Tiwa',
                'cast'         => 'Reza Rahadian, Bunga Citra Lestari, Bima Azriel',
                'release_date' => '2026-03-12', 'status' => 'now_showing',
                'poster'       => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=400&h=600&fit=crop',
            ],
            // 11
            [
                'title'        => 'IP Man: Kung Fu Legend',
                'slug'         => 'ip-man-kung-fu-legend',
                'synopsis'     => 'Film aksi biografi tentang Ip Man, sang maestro Wing Chun yang menjadi legenda dunia beladiri. Perjuangannya mempertahankan kehormatan kung fu di tengah gejolak zaman.',
                'duration'     => '118 menit', 'rating' => '13+', 'language' => 'Mandarin (Sub Indo)',
                'director'     => 'Wilson Yip',
                'cast'         => 'Donnie Yen, Danny Chan, Scott Adkins',
                'release_date' => '2026-03-19', 'status' => 'now_showing',
                'poster'       => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=400&h=600&fit=crop',
            ],
            // 12
            [
                'title'        => 'Project Hail Mary',
                'slug'         => 'project-hail-mary',
                'synopsis'     => 'Adaptasi novel bestseller Andy Weir. Seorang astronot terbangun sendirian di luar angkasa tanpa ingatan. Ia harus menemukan cara menyelamatkan umat manusia sambil mengungkap misinya.',
                'duration'     => '145 menit', 'rating' => 'SU', 'language' => 'Inggris (Sub Indo)',
                'director'     => 'Phil Lord, Christopher Miller',
                'cast'         => 'Ryan Gosling, Jack Black',
                'release_date' => '2026-03-26', 'status' => 'now_showing',
                'poster'       => 'https://images.unsplash.com/photo-1541185933-ef5d8ed016c2?w=400&h=600&fit=crop',
            ],
            // 13
            [
                'title'        => 'David',
                'slug'         => 'david',
                'synopsis'     => 'Drama aksi tentang seorang remaja berbakat yang terjebak dalam lingkaran kriminal di kota besar. Ia harus memilih antara kesetiaan kepada gang-nya atau keluarganya.',
                'duration'     => '108 menit', 'rating' => '17+', 'language' => 'Indonesia',
                'director'     => 'Joko Anwar',
                'cast'         => 'Jourdy Pranata, Ibnu Jamil, Putri Marino',
                'release_date' => '2026-03-05', 'status' => 'now_showing',
                'poster'       => 'https://images.unsplash.com/photo-1564564321837-a57b7070ac4f?w=400&h=600&fit=crop',
            ],
            // 14
            [
                'title'        => 'Gading Mardana: Si Raja Jalanan',
                'slug'         => 'gading-mardana-si-raja-jalanan',
                'synopsis'     => 'Seorang pembalap jalanan berbakat dari Cilegon bermimpi berlaga di ajang balap internasional. Dengan modal nekad, Gading harus menghadapi rival kelas dunia dan godaan dunia malam.',
                'duration'     => '116 menit', 'rating' => '13+', 'language' => 'Indonesia',
                'director'     => 'Herwin Novianto',
                'cast'         => 'Jefri Nichol, Amanda Rawles, Oka Antara',
                'release_date' => '2026-04-02', 'status' => 'now_showing',
                'poster'       => 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?w=400&h=600&fit=crop',
            ],
            // 15
            [
                'title'        => 'Rumah di Ujung Jalan',
                'slug'         => 'rumah-di-ujung-jalan',
                'synopsis'     => 'Sebuah keluarga pindah ke rumah tua di ujung jalan buntu. Satu per satu anggota keluarga mengalami teror yang tak bisa dijelaskan secara logika, hingga rahasia gelap sang rumah terungkap.',
                'duration'     => '107 menit', 'rating' => '17+', 'language' => 'Indonesia',
                'director'     => 'Rocky Soraya',
                'cast'         => 'Shandy Aulia, Derby Romero, Wulandary Herman',
                'release_date' => '2026-04-09', 'status' => 'now_showing',
                'poster'       => 'https://images.unsplash.com/photo-1518780664697-55e3ad937233?w=400&h=600&fit=crop',
            ],
            // 16
            [
                'title'        => 'Laut Bercerita',
                'slug'         => 'laut-bercerita',
                'synopsis'     => 'Adaptasi novel bestseller Leila S. Chudori. Kisah Biru Laut, mahasiswa aktivis yang hilang misterius di era 1998. Perjuangan keluarganya mencari kebenaran dan keadilan.',
                'duration'     => '132 menit', 'rating' => '17+', 'language' => 'Indonesia',
                'director'     => 'Benni Setiawan',
                'cast'         => 'Reza Rahadian, Adinia Wirasti, Chicco Jerikho, Maudy Koesnaedi',
                'release_date' => '2026-04-16', 'status' => 'now_showing',
                'poster'       => 'https://images.unsplash.com/photo-1505118380757-91f5f5632de0?w=400&h=600&fit=crop',
            ],
            // 17
            [
                'title'        => 'Cinta Itu Buta',
                'slug'         => 'cinta-itu-buta',
                'synopsis'     => 'Dua orang bertemu secara virtual dan jatuh cinta tanpa pernah bertemu langsung. Ketika akhirnya bertemu, kenyataan jauh berbeda dari ekspektasi masing-masing.',
                'duration'     => '93 menit', 'rating' => 'SU', 'language' => 'Indonesia',
                'director'     => 'Naya Anindita',
                'cast'         => 'Angga Yunanda, Zara JKT48, Arief Didu',
                'release_date' => '2026-04-23', 'status' => 'now_showing',
                'poster'       => 'https://images.unsplash.com/photo-1516589178581-6cd7833ae3b2?w=400&h=600&fit=crop',
            ],
            // 18
            [
                'title'        => 'Thunderbolts',
                'slug'         => 'thunderbolts',
                'synopsis'     => 'Tim pahlawan super antihero berkumpul untuk misi berbahaya yang tidak bisa dilakukan siapapun. Dengan kepribadian bertabrakan dan motivasi berbeda, bisakah mereka bekerja sama?',
                'duration'     => '127 menit', 'rating' => '13+', 'language' => 'Inggris (Sub Indo)',
                'director'     => 'Jake Schreier',
                'cast'         => 'Florence Pugh, Sebastian Stan, David Harbour, Wyatt Russell',
                'release_date' => '2026-05-01', 'status' => 'now_showing',
                'poster'       => 'https://images.unsplash.com/photo-1531259683007-016a7b628fc3?w=400&h=600&fit=crop',
            ],
            // 19
            [
                'title'        => 'Mission: Impossible — The Final Reckoning',
                'slug'         => 'mission-impossible-the-final-reckoning',
                'synopsis'     => 'Ethan Hunt kembali dalam petualangan paling berbahaya. Dengan ancaman nuklir yang mengancam seluruh dunia, IMF harus berpacu dengan waktu dan mengorbankan segalanya.',
                'duration'     => '169 menit', 'rating' => '13+', 'language' => 'Inggris (Sub Indo)',
                'director'     => 'Christopher McQuarrie',
                'cast'         => 'Tom Cruise, Hayley Atwell, Ving Rhames, Simon Pegg',
                'release_date' => '2026-05-21', 'status' => 'coming_soon',
                'poster'       => 'https://images.unsplash.com/photo-1547036967-23d11aacaee0?w=400&h=600&fit=crop',
            ],
            // 20
            [
                'title'        => 'Puspa dan Sang Penari',
                'slug'         => 'puspa-dan-sang-penari',
                'synopsis'     => 'Drama musikal tentang penari tradisional Jawa yang berjuang mempertahankan seni warisan leluhur di tengah modernisasi. Kisah cinta, pengorbanan, dan keindahan budaya Indonesia.',
                'duration'     => '119 menit', 'rating' => 'SU', 'language' => 'Indonesia',
                'director'     => 'Ifa Isfansyah',
                'cast'         => 'Raisa Andriana, Reza Rahadian, Tutie Kirana',
                'release_date' => '2026-05-07', 'status' => 'now_showing',
                'poster'       => 'https://images.unsplash.com/photo-1583795484071-3c453e3a7c71?w=400&h=600&fit=crop',
            ],
            // 21
            [
                'title'        => 'Ancika: Dia yang Bersamaku 1995',
                'slug'         => 'ancika-dia-yang-bersamaku-1995',
                'synopsis'     => 'Sekuel dari film Dilan. Kisah Ancika yang hadir mengisi kekosongan hati Dilan setelah berpisah dari Milea. Drama cinta berlatar era 90-an yang penuh nostalgia.',
                'duration'     => '111 menit', 'rating' => 'SU', 'language' => 'Indonesia',
                'director'     => 'Benni Setiawan',
                'cast'         => 'Iqbaal Ramadhan, Natasha Wilona, Vanesha Prescilla',
                'release_date' => '2026-04-30', 'status' => 'now_showing',
                'poster'       => 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?w=400&h=600&fit=crop',
            ],
            // 22
            [
                'title'        => 'Alien: Earth Awakening',
                'slug'         => 'alien-earth-awakening',
                'synopsis'     => 'Franchise Alien kembali hadir di Bumi. Ketika makhluk asing menyusup ke laboratorium rahasia di Jakarta, tim khusus harus menghentikan invasi sebelum seluruh kota musnah.',
                'duration'     => '138 menit', 'rating' => '17+', 'language' => 'Inggris (Sub Indo)',
                'director'     => 'Fede Alvarez',
                'cast'         => 'Cailee Spaeny, David Jonsson, Isabella Merced',
                'release_date' => '2026-06-05', 'status' => 'coming_soon',
                'poster'       => 'https://images.unsplash.com/photo-1614728263952-84ea256f9d4a?w=400&h=600&fit=crop',
            ],
            // 23
            [
                'title'        => 'Kartini: Api yang Tak Pernah Padam',
                'slug'         => 'kartini-api-yang-tak-pernah-padam',
                'synopsis'     => 'Kisah baru tentang perjuangan R.A. Kartini yang belum banyak diketahui. Film ini mengungkap sisi paling manusiawi dari sang pahlawan: keraguan, cinta terlarang, dan keberanian yang mengubah sejarah.',
                'duration'     => '124 menit', 'rating' => 'SU', 'language' => 'Indonesia',
                'director'     => 'Hanung Bramantyo',
                'cast'         => 'Adinia Wirasti, Reza Rahadian, Dian Sastrowardoyo',
                'release_date' => '2026-04-21', 'status' => 'now_showing',
                'poster'       => 'https://images.unsplash.com/photo-1526510747491-58f928ec870f?w=400&h=600&fit=crop',
            ],
        ];

        foreach ($films as $film) {
            DB::table('films')->insert(array_merge($film, [
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // =====================================================================
        // FILM GENRES
        // =====================================================================
        $filmGenres = [
            [1,  [1, 8]],    // Tunggu Aku Sukses — Drama, Romantis
            [2,  [4]],       // Senin Harga Naik — Komedi
            [3,  [1]],       // Na Willa — Drama
            [4,  [2]],       // Santet — Horor
            [5,  [7, 9]],    // Pelangi Mars — Sci-Fi, Petualangan
            [6,  [2]],       // Danur: Asal Muasal — Horor
            [7,  [5]],       // Aku Harus Mati — Thriller
            [8,  [3, 5]],    // They Will Kill You — Aksi, Thriller
            [9,  [2, 4]],    // Warung Pocong — Horor, Komedi
            [10, [4, 9]],    // Ayah, Ini Arahnya — Komedi, Petualangan
            [11, [3]],       // IP Man — Aksi
            [12, [7, 9]],    // Project Hail Mary — Sci-Fi, Petualangan
            [13, [3, 1]],    // David — Aksi, Drama
            [14, [3, 9]],    // Gading Mardana — Aksi, Petualangan
            [15, [2, 5]],    // Rumah di Ujung Jalan — Horor, Thriller
            [16, [1]],       // Laut Bercerita — Drama
            [17, [4, 8]],    // Cinta Itu Buta — Komedi, Romantis
            [18, [3, 9]],    // Thunderbolts — Aksi, Petualangan
            [19, [3, 9]],    // Mission: Impossible — Aksi, Petualangan
            [20, [1, 8]],    // Puspa dan Sang Penari — Drama, Romantis
            [21, [1, 8]],    // Ancika 1995 — Drama, Romantis
            [22, [2, 7]],    // Alien: Earth Awakening — Horor, Sci-Fi
            [23, [1]],       // Kartini — Drama
        ];
        foreach ($filmGenres as [$filmId, $genreIds]) {
            foreach ($genreIds as $gid) {
                DB::table('film_genres')->insert(['film_id' => $filmId, 'genre_id' => $gid]);
            }
        }

        // =====================================================================
        // SCHEDULES
        // =====================================================================
        $showTimes  = ['11:00', '13:30', '16:00', '18:30', '21:00'];
        $prices     = [50000, 55000, 60000, 65000];
        $studios    = ['Studio 1', 'Studio 2', 'Studio 3', 'Studio 4'];
        $filmTypes  = ['2D', '2D', '2D', '3D'];
        $today      = Carbon::today();
        $cinemaIds  = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        $totalFilms = count($films);

        foreach (range(1, $totalFilms) as $filmId) {
            $cinemaSample = array_slice($cinemaIds, 0, rand(4, 6));
            foreach ($cinemaSample as $cinemaId) {
                foreach (range(0, 13) as $dayOffset) {
                    $showDate   = $today->copy()->addDays($dayOffset)->format('Y-m-d');
                    $timeSample = array_slice($showTimes, 0, rand(3, 5));
                    foreach ($timeSample as $idx => $time) {
                        DB::table('film_schedules')->insert([
                            'film_id'         => $filmId,
                            'cinema_id'       => $cinemaId,
                            'show_date'       => $showDate,
                            'show_time'       => $time,
                            'studio'          => $studios[$idx % 4],
                            'film_type'       => $filmTypes[$idx % 4],
                            'total_seats'     => 96,
                            'available_seats' => rand(20, 96),
                            'price'           => $prices[$idx % 4],
                            'is_active'       => true,
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ]);
                    }
                }
            }
        }

        // =====================================================================
        // NEWS — 8 berita dengan thumbnail Unsplash URL
        // Model News::getThumbnailUrlAttribute sudah diupdate support URL eksternal
        // =====================================================================
        $newsItems = [
            [
                'title'        => 'Film Indonesia Rajai Box Office April–Mei 2026',
                'slug'         => 'film-indonesia-rajai-box-office-april-mei-2026',
                'excerpt'      => 'Deretan film Indonesia hadir dengan kualitas terbaik sepanjang April dan Mei 2026, membuktikan industri perfilman tanah air semakin matang dan berdaya saing global.',
                'content'      => '<p>Bulan April dan Mei 2026 menjadi momen bersejarah bagi industri perfilman Indonesia. Berbagai film karya sineas tanah air hadir dengan kualitas produksi yang tidak kalah dari film Hollywood.</p><p>Menurut data dari Badan Film Nasional, total penonton bioskop pada semester pertama 2026 meningkat 42% dibandingkan tahun sebelumnya. Film-film bertema horor, komedi, dan drama keluarga mendominasi penjualan tiket.</p><p>Film "Laut Bercerita" dan "Kartini: Api yang Tak Pernah Padam" menjadi dua film drama yang paling banyak diperbincangkan karena kedalaman cerita dan kualitas aktingnya yang memukau.</p>',
                'category'     => 'Box Office',
                'is_published' => true,
                'published_at' => now()->subDays(1),
                'thumbnail'    => 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=800&h=500&fit=crop',
            ],
            [
                'title'        => 'XXI Hadirkan Pengalaman Bioskop Premium di Cilegon',
                'slug'         => 'xxi-hadirkan-pengalaman-bioskop-premium-di-cilegon',
                'excerpt'      => 'Bioskop XXI di Cilegon kini hadir dengan kursi premium dan teknologi audio Dolby Atmos untuk memberikan pengalaman menonton terbaik bagi warga Banten.',
                'content'      => '<p>Cinema XXI terus berinovasi dengan menghadirkan pengalaman menonton yang semakin premium di seluruh jaringannya, termasuk di kota Cilegon yang kini memiliki dua gerai XXI dengan fasilitas lengkap.</p><p>Kursi premium berbahan kulit yang nyaman, layar IMAX, dan sistem audio Dolby Atmos menjadi daya tarik utama yang membuat penonton betah berlama-lama di dalam studio.</p>',
                'category'     => 'Cinema Update',
                'is_published' => true,
                'published_at' => now()->subDays(3),
                'thumbnail'    => 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=800&h=500&fit=crop',
            ],
            [
                'title'        => 'Project Hail Mary: Film Sci-Fi Paling Ditunggu 2026',
                'slug'         => 'project-hail-mary-film-sci-fi-paling-ditunggu-2026',
                'excerpt'      => 'Adaptasi novel Andy Weir akhirnya hadir di layar lebar dengan Ryan Gosling sebagai pemeran utama. Begini fakta menarik di balik produksinya.',
                'content'      => '<p>Novel bestseller karya Andy Weir, "Project Hail Mary", akhirnya diadaptasi ke layar lebar dengan Ryan Gosling sebagai sang astronot, Ryland Grace. Film ini menjadi salah satu yang paling ditunggu di tahun 2026.</p><p>Proses produksi film ini memakan waktu hampir tiga tahun dengan teknologi CGI tercanggih untuk menampilkan keindahan luar angkasa secara realistis. NASA pun turut membantu memastikan akurasi ilmiah dalam film ini.</p>',
                'category'     => 'Film Review',
                'is_published' => true,
                'published_at' => now()->subDays(5),
                'thumbnail'    => 'https://images.unsplash.com/photo-1462331940025-496dfbfc7564?w=800&h=500&fit=crop',
            ],
            [
                'title'        => 'Tips Mendapatkan Tiket Bioskop Terbaik Saat Akhir Pekan',
                'slug'         => 'tips-mendapatkan-tiket-bioskop-terbaik-saat-akhir-pekan',
                'excerpt'      => 'Pesan tiket lebih awal, pilih jam yang tepat, dan nikmati kursi terbaik. Tips lengkap dari TIKS untuk pengalaman bioskop yang sempurna.',
                'content'      => '<p>Akhir pekan selalu menjadi waktu tersibuk di bioskop. Untuk memastikan kamu mendapatkan pengalaman terbaik, TIKS hadir dengan beberapa tips yang bisa kamu ikuti.</p><p><strong>1. Pesan Tiket H-3</strong><br>Jangan tunggu hari H! Pesan tiketmu minimal 3 hari sebelumnya untuk mendapatkan pilihan kursi terbaik.</p><p><strong>2. Pilih Jam Strategis</strong><br>Jam 11:00 dan 13:30 biasanya lebih sepi. Kamu bisa menikmati film dengan lebih tenang.</p><p><strong>3. Kursi Tengah adalah Raja</strong><br>Baris F-H di kursi tengah memberikan sudut pandang terbaik untuk menonton film.</p>',
                'category'     => 'Tips & Trik',
                'is_published' => true,
                'published_at' => now()->subDays(7),
                'thumbnail'    => 'https://images.unsplash.com/photo-1512070679279-8988d32161be?w=800&h=500&fit=crop',
            ],
            [
                'title'        => 'Industri Film Indonesia Targetkan 100 Juta Penonton di 2026',
                'slug'         => 'industri-film-indonesia-targetkan-100-juta-penonton-di-2026',
                'excerpt'      => 'APFI optimis meraih 100 juta penonton bioskop di 2026, didukung kualitas produksi dan ekspansi bioskop ke kota-kota tier 2 dan tier 3.',
                'content'      => '<p>Asosiasi Produser Film Indonesia (APFI) menyatakan optimisme tinggi untuk mencapai target 100 juta penonton bioskop pada tahun 2026. Angka ini merupakan rekor tertinggi sepanjang sejarah perfilman Indonesia.</p><p>Faktor pendukung: meningkatnya jumlah bioskop di kota-kota tier 2 dan tier 3, kualitas produksi yang semakin baik, serta kolaborasi dengan sineas internasional yang semakin banyak.</p>',
                'category'     => 'Industri Film',
                'is_published' => true,
                'published_at' => now()->subDays(9),
                'thumbnail'    => 'https://images.unsplash.com/photo-1524712245354-2c4e5e7121c0?w=800&h=500&fit=crop',
            ],
            [
                'title'        => 'Joe Taslim & Yayan Ruhian: Duo Aksi Paling Ditakuti di 2026',
                'slug'         => 'joe-taslim-yayan-ruhian-duo-aksi-paling-ditakuti-2026',
                'excerpt'      => 'Joe Taslim dan Yayan Ruhian kembali dipertemukan dalam "They Will Kill You" — thriller aksi paling intens yang pernah dibuat sinema Indonesia.',
                'content'      => '<p>Dua ikon aksi Indonesia, Joe Taslim dan Yayan Ruhian, kembali dipertemukan dalam "They Will Kill You". Film yang disutradarai Timo Tjahjanto ini menjanjikan adegan aksi yang lebih intens dari sebelumnya.</p><p>Joe Taslim menyebut film ini sebagai proyek paling menantang dalam kariernya. "Saya harus latihan selama 6 bulan untuk menyiapkan diri bagi karakter ini," ujarnya. Film ini telah meraih lebih dari 2 juta penonton dalam dua minggu pertama.</p>',
                'category'     => 'Selebrita',
                'is_published' => true,
                'published_at' => now()->subDays(2),
                'thumbnail'    => 'https://images.unsplash.com/photo-1549476464-37392f717541?w=800&h=500&fit=crop',
            ],
            [
                'title'        => 'Ancika 1995 Pecahkan Rekor Penonton Hari Pertama',
                'slug'         => 'ancika-1995-pecahkan-rekor-penonton-hari-pertama',
                'excerpt'      => '"Ancika: Dia yang Bersamaku 1995" sukses menarik lebih dari 500 ribu penonton di hari pertama tayang, memecahkan rekor film Indonesia terlaris.',
                'content'      => '<p>Film "Ancika: Dia yang Bersamaku 1995" berhasil mencetak rekor baru dengan meraih lebih dari 500 ribu penonton hanya dalam hari pertama penayangannya.</p><p>Iqbaal Ramadhan yang kembali memerankan Dilan dan Natasha Wilona sebagai Ancika berhasil menciptakan chemistry yang memukau penonton dari berbagai generasi.</p>',
                'category'     => 'Box Office',
                'is_published' => true,
                'published_at' => now()->subDays(4),
                'thumbnail'    => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&h=500&fit=crop',
            ],
            [
                'title'        => 'Thunderbolts Tayang Perdana: Review Awal dari Penonton Indonesia',
                'slug'         => 'thunderbolts-tayang-perdana-review-awal-penonton-indonesia',
                'excerpt'      => 'Film superhero terbaru Marvel "Thunderbolts" resmi tayang 1 Mei 2026. Apa kata penonton Indonesia tentang film antihero yang satu ini?',
                'content'      => '<p>"Thunderbolts" resmi menggebrak layar bioskop Indonesia mulai 1 Mei 2026. Film superhero dari Marvel Studios ini menghadirkan tim antihero yang unik dengan Florence Pugh sebagai Yelena Belova dan Sebastian Stan sebagai Bucky Barnes.</p><p>Berbeda dari film Marvel kebanyakan, Thunderbolts lebih fokus pada karakter dan dilema moral. "Ini Marvel yang dewasa dan mau berpikir," tulis salah satu penonton di media sosial. Tiket sudah bisa dipesan sekarang di TIKS!</p>',
                'category'     => 'Film Review',
                'is_published' => true,
                'published_at' => now()->subDays(6),
                'thumbnail'    => 'https://images.unsplash.com/photo-1531259683007-016a7b628fc3?w=800&h=500&fit=crop',
            ],
        ];

        foreach ($newsItems as $news) {
            DB::table('news')->insert(array_merge($news, [
                'author_id'  => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
