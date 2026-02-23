<?php
include "includes/functions.php";

// ambil input dengan nilai default
$tanggal_awal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
$jumlah = isset($_GET['jumlah']) ? (int) $_GET['jumlah'] : null;

// hanya hitung jika form sudah disubmit
$hasil = null;
$hasil_penambahan = null;

if (isset($_GET['tanggal']) && isset($_GET['jumlah'])) {
    $tanggal_baru = date('Y-m-d', strtotime($tanggal_awal . " +{$jumlah} days"));
    $hasil_penambahan = formatTanggalIndonesia($tanggal_baru);

    $tanggal_obj = new DateTime($tanggal_awal);
    $tanggal_obj->modify("$jumlah days");

    $hasil = [
        'tanggal_baru' => $tanggal_obj->format("Y-m-d"),
        'masehi' => formatTanggalIndonesia($tanggal_obj->format("Y-m-d")),
        'hijriyah' => masehiToHijriyah($tanggal_obj->format("Y-m-d")),
        'jawa' => masehiToJawa($tanggal_obj->format("Y-m-d"))
    ];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title>Kalkulator Hari • Taaho Kalender</title>

    <style>
        /* ======= GLOBAL STYLE ======= */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        a {
            color: #ed7230;
            text-decoration: none;
        }

        body {
            background-color: #0d1117;
            color: #e6edf3;
            font-family: "Poppins", sans-serif;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* ======= HEADER / NAVBAR ======= */
        header {
            background-color: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 2px solid #222;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 8%;
        }

        .logo a {
            font-size: 1.6em;
            font-weight: 600;
            color: #ed7230;
        }

        .dot {
            color: #fff;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 25px;
            transition: all 0.3s ease;
        }

        .nav-links a {
            color: #e6edf3;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: #ed7230;
        }

        /* ===== HAMBURGER ===== */
        .menu-toggle {
            display: none;
            flex-direction: column;
            cursor: pointer;
            transition: 0.3s;
        }

        .bar {
            width: 26px;
            height: 3px;
            background-color: #ffa500;
            margin: 4px 0;
            border-radius: 3px;
            transition: 0.4s;
        }

        /* ======= HERO SECTION ======= */
        main {
            max-width: 900px;
            margin: auto;
            padding: 0 20px;
        }

        .hero {
            text-align: center;
            padding: 60px 20px 40px;
        }

        .hero h1 {
            font-size: 2.2rem;
            color: #ff7b00;
            margin-bottom: 10px;
        }

        .hero p {
            color: #ccc;
            max-width: 600px;
            margin: 0 auto 30px;
        }

        .form-hero {
            background: #000;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 0 20px rgba(255, 123, 0, 0.1);
            max-width: 450px;
            margin: 0 auto;
            text-align: left;
        }

        .form-hero label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
            color: #ffb366;
        }

        .form-hero input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            background-color: #0e0e0e;
            border: 1px solid #333;
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
        }

        .form-hero button {
            margin-top: 25px;
            width: 100%;
            background: linear-gradient(90deg, #ff7b00, #ffae00);
            color: #fff;
            border: none;
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .form-hero button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 123, 0, 0.4);
        }

        /* ======= HASIL SECTION ======= */
        .result {
            background: #000;
            padding: 25px;
            margin-top: 30px;
            border-radius: 14px;
            box-shadow: 0 0 20px rgba(255, 123, 0, 0.08);
            text-align: left;
        }

        .result h3 {
            color: #ff7b00;
            margin-top: 0;
            text-align: center;
        }

        .result ul {
            list-style: none;
            padding-left: 0;
        }

        .result li {
            margin-bottom: 8px;
            color: #ddd;
        }

        /* ======= INFO SECTION ======= */
        .about,
        .how-it-works,
        .more-tools {
            background: #000;
            margin-top: 50px;
            padding: 40px 30px;
            border-radius: 14px;
            box-shadow: 0 0 20px rgba(255, 123, 0, 0.05);
        }

        h2.section-title {
            color: #ff7b00;
            text-align: center;
            margin-bottom: 20px;
        }

        .about p {
            text-align: center;
            color: #ccc;
        }

        .how-it-works ol {
            max-width: 600px;
            margin: 0 auto;
            padding-left: 20px;
            color: #ddd;
        }

        .more-tools .btn-secondary {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background: transparent;
            border: 1px solid #ff7b00;
            color: #ff7b00;
            border-radius: 8px;
            text-decoration: none;
            transition: 0.3s;
        }

        .more-tools .btn-secondary:hover {
            background: #ff7b00;
            color: #0e0e0e;
        }

        /* ======= FOOTER ======= */
        footer {
            background-color: #000;
            text-align: center;
            padding: 25px 0;
            margin-top: 50px;
            border-top: 2px solid #222;
            color: #888;
            font-size: 0.9rem;
        }

        /* ======= TOOLS CARD ======= */
        .tools-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .tool-card {
            background-color: #1e1e1e;
            border: 1px solid #2c2c2c;
            border-radius: 12px;
            padding: 20px;
            max-width: 350px;
            text-align: left;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .tool-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 15px rgba(255, 140, 0, 0.3);
        }

        .tool-card h3 {
            color: #ffa500;
            margin-bottom: 10px;
        }

        .tool-card p {
            color: #ccc;
            font-size: 0.95em;
            line-height: 1.5;
            margin-bottom: 15px;
        }

        .btn-secondary {
            background-color: #ffa500;
            color: #121212;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s;
        }

        .btn-secondary:hover {
            background-color: #ffb733;
        }

        /* ======= MEDIA QUERIES FOR MOBILE ======= */
        @media (max-width: 768px) {

            /* Navbar */
            .navbar {
                padding: 12px 5%;
            }

            .logo a {
                font-size: 1.4em;
            }

            .nav-links {
                position: fixed;
                top: 70px;
                right: -100%;
                width: 80%;
                height: calc(100vh - 70px);
                background-color: rgba(0, 0, 0, 0.95);
                flex-direction: column;
                align-items: center;
                justify-content: flex-start;
                padding-top: 40px;
                gap: 30px;
                transition: right 0.3s ease;
                z-index: 999;
                /* ✅ Tambahan berikut penting */
                display: none;
                opacity: 0;
                pointer-events: none;
            }

            .nav-links.active {
                right: 0;
                display: flex;
                opacity: 1;
                pointer-events: auto;
            }

            .menu-toggle {
                display: flex;
            }

            /* Hero Section */
            .hero {
                padding: 40px 15px 30px;
            }

            .hero h1 {
                font-size: 1.8rem;
            }

            .form-hero {
                padding: 20px;
            }

            /* Result Section */
            .result {
                padding: 20px;
                margin-top: 20px;
            }

            /* Info Sections */
            .about,
            .how-it-works,
            .more-tools {
                margin-top: 40px;
                padding: 30px 20px;
            }

            h2.section-title {
                font-size: 1.5rem;
            }

            /* Tools Container */
            .tools-container {
                flex-direction: column;
                align-items: center;
            }

            .tool-card {
                max-width: 100%;
                width: 100%;
            }

            /* Footer */
            footer {
                padding: 20px 0;
                margin-top: 40px;
            }
        }

        @media (max-width: 480px) {

            /* Navbar */
            .navbar {
                padding: 10px 4%;
            }

            .logo {
                font-size: 1.3em;
            }

            /* Hero Section */
            .hero {
                padding: 30px 10px 20px;
            }

            .hero h1 {
                font-size: 1.6rem;
            }

            .hero p {
                font-size: 0.95rem;
            }

            .form-hero {
                padding: 15px;
            }

            /* Result Section */
            .result {
                padding: 15px;
            }

            /* Info Sections */
            .about,
            .how-it-works,
            .more-tools {
                padding: 25px 15px;
            }

            h2.section-title {
                font-size: 1.4rem;
            }

            /* Footer */
            footer {
                font-size: 0.85rem;
            }
        }

        /* Animation for menu toggle */
        .menu-toggle.active .bar:nth-child(1) {
            transform: rotate(-45deg) translate(-5px, 6px);
        }

        .menu-toggle.active .bar:nth-child(2) {
            opacity: 0;
        }

        .menu-toggle.active .bar:nth-child(3) {
            transform: rotate(45deg) translate(-5px, -6px);
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <a href="index.php" target="_blank">
                    Taaho<span class="dot"></span> Kalender
                </a>
            </div>

            <!-- Tombol Menu Hamburger -->
            <div class="menu-toggle" id="menu-toggle">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>

            <!-- Link Navigasi -->
            <ul class="nav-links" id="nav-links">
                <li><a href="index.php" class="active">Kalkulator Hari</a></li>
                <li><a href="kalendar.php">Kalender Lengkap</a></li>
                <li><a href="konversi.php">Konversi Tanggal</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="hero">
            <h1>🌓 Kalkulator Hari</h1>
            <p>Hitung jumlah hari ke depan atau ke belakang berdasarkan kalender Masehi, Hijriyah, dan Jawa.</p>

            <form method="GET" class="form-hero">
                <label for="tanggal">Tanggal Awal:</label>
                <input type="date" id="tanggal" name="tanggal" value="<?= htmlspecialchars($tanggal_awal) ?>" required>

                <label for="jumlah">Tambah Hari (+/-):</label>
                <input type="number" id="jumlah" name="jumlah" value="<?= htmlspecialchars($_GET['jumlah'] ?? 10) ?>">

                <button type="submit">Hitung Sekarang</button>
            </form>

            <?php if ($hasil): ?>
                <div class="result">
                    <h3>📅 Hasil Perhitungan</h3>
                    <ul>
                        <!-- Bagian Input -->
                        <li><b>Tanggal Awal:</b> <?= htmlspecialchars($tanggal_awal) ?></li>
                        <li><b>Jumlah Hari Ditambahkan:</b> <?= htmlspecialchars($jumlah) ?></li>
                        <hr style="margin-bottom: 10px;">

                        <!-- Bagian Hasil -->
                        <li><b>Hasil (Masehi):</b> <?= $hasil_penambahan ?></li>
                        <li><b>Hasil (Hijriyah):</b> <?= htmlspecialchars($hasil['hijriyah']) ?></li>
                        <li><b>Hasil (Jawa):</b> <?= htmlspecialchars($hasil['jawa']['tanggal']) ?></li>
                        <hr style="margin-bottom: 10px;">

                        <!-- Bagian Kalender Jawa -->
                        <li><b>Hari (Jawa):</b> <?= htmlspecialchars($hasil['jawa']['hari']) ?></li>
                        <li><b>Pasaran (Jawa):</b> <?= htmlspecialchars($hasil['jawa']['pasaran']) ?></li>
                        <li><b>Weton (Jawa):</b> <?= htmlspecialchars($hasil['jawa']['weton']) ?></li>
                        <li><b>Tahun (Jawa):</b> <?= htmlspecialchars($hasil['jawa']['tahun_jawa']) ?>
                            (<?= htmlspecialchars($hasil['jawa']['tahun_jawa_name']) ?>)
                        </li>
                    </ul>

                </div>
            <?php endif; ?>
        </section>

        <section class="about">
            <h2 class="section-title">Tentang Taaho Kalender</h2>
            <p>Taaho Kalender adalah bagian dari ekosistem <b>taaho.id</b> yang dirancang untuk membantu kamu menghitung
                waktu lintas kalender — Masehi, Hijriyah, dan Jawa — secara cepat, akurat, dan modern.</p>
        </section>

        <section class="how-it-works">
            <h2 class="section-title">Cara Kerja</h2>
            <ol>
                <li>Pilih tanggal awal yang kamu inginkan.</li>
                <li>Masukkan jumlah hari untuk ditambah atau dikurangi (gunakan angka negatif untuk mundur).</li>
                <li>Klik <b>Hitung Sekarang</b> dan hasil lintas kalender akan muncul otomatis.</li>
            </ol>
        </section>

        <section class="more-tools">
            <h2 class="section-title">🧭 Fitur Tambahan</h2>

            <div class="tools-container">
                <!-- KALENDER -->
                <div class="tool-card">
                    <h3>📅 Kalender Tiga Sistem</h3>
                    <p>
                        Lihat kalender lengkap dengan tiga sistem penanggalan:
                        <b>Masehi</b>, <b>Hijriyah</b>, dan <b>Jawa</b>.
                        Kamu juga bisa memilih bulan dan tahun tertentu untuk melihat
                        konversinya secara langsung.
                    </p>
                    <a href="kalendar.php" class="btn-secondary">Buka Kalender</a>
                </div>

                <!-- KONVERSI -->
                <div class="tool-card">
                    <h3>🔄 Konversi Tanggal</h3>
                    <p>
                        Ubah tanggal dari satu sistem ke sistem lainnya, misalnya dari
                        <b>Masehi ke Hijriyah</b>, <b>Hijriyah ke Jawa</b>, dan sebaliknya.
                        Pilih jenis tanggal yang ingin dikonversi dan dapatkan hasilnya secara instan.
                    </p>
                    <a href="konversi.php" class="btn-secondary">Mulai Konversi</a>
                </div>
            </div>
        </section>
    </main>

    <footer>
        © <?= date('Y') ?> Taaho Kalender — Dibuat oleh <a href="https://taaho.id" target="_blank">Galista Haidir |
            Taaho.id</a>
    </footer>


    <script>
        // JavaScript untuk toggle menu hamburger
        const menuToggle = document.getElementById('menu-toggle');
        const navLinks = document.getElementById('nav-links');

        menuToggle.addEventListener('click', function () {
            navLinks.classList.toggle('active');
            menuToggle.classList.toggle('active');
        });

        // Menutup menu saat mengklik di luar menu
        document.addEventListener('click', function (event) {
            if (!menuToggle.contains(event.target) && !navLinks.contains(event.target)) {
                navLinks.classList.remove('active');
                menuToggle.classList.remove('active');
            }
        });

        // Menutup menu saat mengklik link
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('active');
                menuToggle.classList.remove('active');
            });
        });
    </script>
</body>

</html>