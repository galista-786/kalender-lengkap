<?php
include "includes/functions.php";

// ambil input
$jenis = $_GET['jenis'] ?? "masehi";
$submit = isset($_GET['submit']); // hanya true kalau tombol ditekan

$hasil = [];

if ($submit) {
    if ($jenis == "masehi" && !empty($_GET['tanggal_masehi'])) {
        $tanggal = $_GET['tanggal_masehi'];
        $masehi = date("l, d F Y", strtotime($tanggal));
        $h = masehiToHijriyahArray($tanggal);
        $hijriyah = $h['day'] . " " . $h['month_name'] . " " . $h['year'] . " H";
        $j = masehiToJawa($tanggal);
        $jawa = $j['tanggal'] . " (" . $j['weton'] . ", " . $j['tahun_jawa_name'] . ")";

        $hasil = [
            "Masehi" => $masehi,
            "Hijriyah" => $hijriyah,
            "Jawa" => $jawa
        ];
    } elseif ($jenis == "hijriyah") {
        $d = $_GET['hijri_day'] ?? null;
        $m = $_GET['hijri_month'] ?? null;
        $y = $_GET['hijri_year'] ?? null;
        if ($d && $m && $y) {
            $masehi = hijriyahToMasehi((int) $d, (int) $m, (int) $y);
            $hasil = [
                "Hijriyah Input" => "$d $m $y H",
                "Masehi" => date("l, d F Y", strtotime($masehi)),
                "Hijriyah" => "$d $m $y H",
                "Jawa" => masehiToJawa($masehi)['tanggal']
            ];
        }
    } elseif ($jenis == "jawa") {
        $d = $_GET['jawa_day'] ?? null;
        $m = $_GET['jawa_month'] ?? null;
        $y = $_GET['jawa_year'] ?? null;
        if ($d && $m && $y) {
            $bulanJawa = [
                1 => "Sura",
                2 => "Sapar",
                3 => "Mulud",
                4 => "Bakda Mulud",
                5 => "Jumadilawal",
                6 => "Jumadilakir",
                7 => "Rejeb",
                8 => "Ruwah",
                9 => "Pasa",
                10 => "Sawal",
                11 => "Dulkaidah",
                12 => "Besar"
            ];
            $namaBulan = $bulanJawa[(int) $m] ?? "Sura";

            $masehi = jawaToMasehi((int) $d, $namaBulan, (int) $y);
            $hasil = [
                "Jawa Input" => "$d $namaBulan $y Jawa",
                "Masehi" => date("l, d F Y", strtotime($masehi)),
                "Hijriyah" => masehiToHijriyah($masehi),
                "Jawa" => masehiToJawa($masehi)['tanggal']
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <!-- 🌐 META DASAR -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konversi Tanggal Masehi, Hijriyah, dan Jawa | Taaho Kalender</title>

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
        .header {
            background-color: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 2px solid #222;
            position: sticky;
            top: 0;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 8%;
            z-index: 1000;
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

        /* ======= MAIN ======= */
        main {
            max-width: 900px;
            margin: 30px auto 0 auto;
            padding: 0 20px;
        }

        /* ======= INTRO SECTION ======= */
        .intro-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-title {
            color: #ffa500;
            font-size: 2.2em;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .subtitle {
            color: #bbb;
            margin-bottom: 25px;
            font-size: 1.1em;
            line-height: 1.7;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        /* ======= FORM SECTION ======= */
        .form-section {
            background-color: #000000;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(255, 140, 0, 0.15);
            margin-bottom: 30px;
        }

        .form-section h2 {
            color: #ffa500;
            font-size: 1.6em;
            margin-bottom: 10px;
            text-align: center;
        }

        .form-section>p {
            color: #ccc;
            text-align: center;
            margin-bottom: 25px;
            font-size: 1em;
        }

        .konversi-form {
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            color: #ffb366;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 0.95em;
        }

        select,
        input[type="number"],
        input[type="date"] {
            background-color: #2a2a2a;
            border: 1px solid #444;
            color: #fff;
            padding: 12px 15px;
            border-radius: 8px;
            width: 100%;
            font-size: 1em;
            transition: all 0.3s ease;
        }

        select:focus,
        input:focus {
            border-color: #ffa500;
            box-shadow: 0 0 0 2px rgba(255, 165, 0, 0.2);
            outline: none;
        }

        .input-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .input-row>* {
            flex: 1;
            min-width: 120px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ff7b00, #ffae00);
            color: #121212;
            border: none;
            padding: 14px 25px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            font-size: 1.1em;
            margin-top: 10px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 123, 0, 0.4);
        }

        /* ======= RESULT SECTION ======= */
        .result-section {
            background: linear-gradient(135deg, #000000, #000000);
            border-left: 4px solid #ffa500;
            padding: 25px;
            margin: 30px 0;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(255, 140, 0, 0.1);
        }

        .result-section h2 {
            color: #ffa500;
            font-size: 1.6em;
            margin-bottom: 15px;
            text-align: center;
        }

        .result-section>p {
            color: #ccc;
            text-align: center;
            margin-bottom: 20px;
        }

        .result-section ul {
            list-style: none;
            padding-left: 0;
            background-color: #000000;
            border-radius: 8px;
            padding: 20px;
        }

        .result-section li {
            margin: 12px 0;
            padding: 10px 15px;
            background-color: #333;
            border-radius: 6px;
            border-left: 3px solid #ffa500;
        }

        .result-section li b {
            color: #ffb366;
        }

        /* ======= INFO SECTION ======= */
        .info-section {
            background-color: #000000;
            padding: 30px;
            border-radius: 12px;
            margin: 40px 0;
            border-top: 3px solid #ffa500;
        }

        .info-section h2 {
            color: #ffa500;
            font-size: 1.6em;
            margin-bottom: 15px;
            text-align: center;
        }

        .info-section p {
            color: #ccc;
            margin-bottom: 15px;
            line-height: 1.7;
            text-align: justify;
        }

        /* ======= FOOTER ======= */
        footer {
            text-align: center;
            padding: 30px 0;
            margin-top: 50px;
            border-top: 1px solid #333;
            color: #888;
            font-size: 0.95rem;
            background-color: #111;
        }

        footer a {
            color: #ffa500;
            font-weight: 600;
        }

        footer a:hover {
            text-decoration: underline;
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

            /* Main Content */
            main {
                margin: 20px auto 0 auto;
                padding: 0 15px;
            }

            /* Intro Section */
            .page-title {
                font-size: 1.8em;
            }

            .subtitle {
                font-size: 1em;
                padding: 0 10px;
            }

            /* Form Section */
            .form-section {
                padding: 20px;
            }

            .form-section h2 {
                font-size: 1.4em;
            }

            .input-row {
                flex-direction: column;
                gap: 10px;
            }

            .input-row>* {
                min-width: 100%;
            }

            /* Result Section */
            .result-section {
                padding: 20px;
                margin: 25px 0;
            }

            .result-section h2 {
                font-size: 1.4em;
            }

            .result-section ul {
                padding: 15px;
            }

            .result-section li {
                padding: 8px 12px;
                margin: 10px 0;
            }

            /* Info Section */
            .info-section {
                padding: 20px;
                margin: 30px 0;
            }

            .info-section h2 {
                font-size: 1.4em;
            }

            /* Footer */
            footer {
                padding: 25px 0;
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

            /* Main Content */
            main {
                padding: 0 10px;
            }

            /* Intro Section */
            .page-title {
                font-size: 1.6em;
            }

            .subtitle {
                font-size: 0.95em;
            }

            /* Form Section */
            .form-section {
                padding: 15px;
            }

            .form-section h2 {
                font-size: 1.3em;
            }

            .konversi-form {
                max-width: 100%;
            }

            .form-group {
                margin-bottom: 15px;
            }

            select,
            input[type="number"],
            input[type="date"] {
                padding: 10px 12px;
                font-size: 0.95em;
            }

            .btn-primary {
                padding: 12px 20px;
                font-size: 1em;
            }

            /* Result Section */
            .result-section {
                padding: 15px;
            }

            .result-section h2 {
                font-size: 1.3em;
            }

            .result-section ul {
                padding: 12px;
            }

            .result-section li {
                padding: 6px 10px;
                margin: 8px 0;
                font-size: 0.95em;
            }

            /* Info Section */
            .info-section {
                padding: 15px;
            }

            .info-section h2 {
                font-size: 1.3em;
            }

            .info-section p {
                font-size: 0.95em;
                text-align: left;
            }

            /* Footer */
            footer {
                font-size: 0.9rem;
                padding: 20px 10px;
            }
        }

        /* Additional Mobile Optimizations */
        @media (max-width: 360px) {
            .page-title {
                font-size: 1.5em;
            }

            .form-section,
            .result-section,
            .info-section {
                padding: 12px;
            }

            select,
            input[type="number"],
            input[type="date"] {
                padding: 8px 10px;
            }
        }

        /* Touch-friendly improvements */
        .btn-primary,
        select,
        input[type="number"],
        input[type="date"] {
            min-height: 44px;
        }

        .nav-links a {
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
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
    <header class="header">
        <nav class="navbar">
            <div class="logo">
                <a href="https://taaho.id" target="_blank">
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
                <li><a href="index.php">Kalkulator Hari</a></li>
                <li><a href="kalendar.php">Kalender Lengkap</a></li>
                <li><a href="konversi.php" class="active">Konversi Tanggal</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <!-- 🔹 HERO / INTRO SECTION -->
        <section class="intro-section">
            <header>
                <h1 class="page-title">🔄 Konversi Tanggal</h1>
                <p class="subtitle">
                    Ubah tanggal dari sistem <strong>Masehi</strong>, <strong>Hijriyah</strong>, atau
                    <strong>Jawa</strong> ke bentuk lainnya secara otomatis.
                    <br>
                    Fitur ini membantu kamu mengetahui kesetaraan antara kalender berbeda secara cepat dan akurat.
                </p>
            </header>
        </section>

        <!-- 🔹 FORM SECTION -->
        <section class="form-section" aria-labelledby="konversiForm">
            <h2 id="konversiForm">🧮 Formulir Konversi</h2>
            <p>Masukkan tanggal berdasarkan jenis kalender yang kamu pilih untuk melihat hasil konversinya secara
                instan.</p>

            <form method="GET" class="konversi-form">
                <div class="form-group">
                    <label for="jenis">Pilih jenis input:</label>
                    <select name="jenis" id="jenis">
                        <option value="masehi" <?= $jenis == "masehi" ? "selected" : "" ?>>Masehi</option>
                        <option value="hijriyah" <?= $jenis == "hijriyah" ? "selected" : "" ?>>Hijriyah</option>
                        <option value="jawa" <?= $jenis == "jawa" ? "selected" : "" ?>>Jawa</option>
                    </select>
                </div>

                <!-- Form Masehi -->
                <div id="form-masehi" class="form-group">
                    <label>Tanggal Masehi:</label>
                    <input type="date" name="tanggal_masehi" value="<?= $_GET['tanggal_masehi'] ?? '' ?>">
                </div>

                <!-- Form Hijriyah -->
                <div id="form-hijriyah" class="form-group" style="display:none;">
                    <label>Tanggal Hijriyah:</label>
                    <div class="input-row">
                        <input type="number" name="hijri_day" min="1" max="30" placeholder="Hari"
                            value="<?= $_GET['hijri_day'] ?? '' ?>">
                        <select name="hijri_month">
                            <?php
                            $bulanHijriyah = [
                                1 => "Muharram",
                                2 => "Safar",
                                3 => "Rabi'ul Awal",
                                4 => "Rabi'ul Akhir",
                                5 => "Jumadil Ula",
                                6 => "Jumadil Akhir",
                                7 => "Rajab",
                                8 => "Sya'ban",
                                9 => "Ramadhan",
                                10 => "Syawwal",
                                11 => "Dzulqa'dah",
                                12 => "Dzulhijjah"
                            ];
                            foreach ($bulanHijriyah as $k => $v) {
                                $sel = (($_GET['hijri_month'] ?? '') == $k) ? "selected" : "";
                                echo "<option value='$k' $sel>$v</option>";
                            }
                            ?>
                        </select>
                        <input type="number" name="hijri_year" placeholder="Tahun"
                            value="<?= $_GET['hijri_year'] ?? '' ?>">
                    </div>
                </div>

                <!-- Form Jawa -->
                <div id="form-jawa" class="form-group" style="display:none;">
                    <label>Tanggal Jawa:</label>
                    <div class="input-row">
                        <input type="number" name="jawa_day" min="1" max="30" placeholder="Hari"
                            value="<?= $_GET['jawa_day'] ?? '' ?>">
                        <select name="jawa_month">
                            <?php
                            $bulanJawa = [
                                1 => "Sura",
                                2 => "Sapar",
                                3 => "Mulud",
                                4 => "Bakda Mulud",
                                5 => "Jumadilawal",
                                6 => "Jumadilakir",
                                7 => "Rejeb",
                                8 => "Ruwah",
                                9 => "Pasa",
                                10 => "Sawal",
                                11 => "Dulkaidah",
                                12 => "Besar"
                            ];
                            foreach ($bulanJawa as $k => $v) {
                                $sel = (($_GET['jawa_month'] ?? '') == $k) ? "selected" : "";
                                echo "<option value='$k' $sel>$v</option>";
                            }
                            ?>
                        </select>
                        <input type="number" name="jawa_year" placeholder="Tahun"
                            value="<?= $_GET['jawa_year'] ?? '' ?>">
                    </div>
                </div>

                <button type="submit" name="submit" value="1" class="btn-primary">Konversi</button>
            </form>
        </section>

        <!-- 🔹 RESULT SECTION -->
        <?php if ($submit && !empty($hasil)): ?>
            <section class="result-section" aria-labelledby="hasilKonversi">
                <h2 id="hasilKonversi">🧭 Hasil Konversi</h2>
                <p>Berikut hasil konversi tanggal dari sistem kalender yang kamu masukkan:</p>
                <ul>
                    <?php foreach ($hasil as $k => $v): ?>
                        <li><b><?= ucfirst($k) ?>:</b> <?= htmlspecialchars($v) ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

        <!-- 🔹 INFORMASI TAMBAHAN -->
        <aside class="info-section">
            <h2>ℹ️ Tentang Konversi Kalender</h2>
            <p>
                Konversi kalender membantu memahami perbedaan sistem waktu antara berbagai budaya. Kalender Masehi
                berbasis matahari,
                sementara Hijriyah mengikuti peredaran bulan, dan Kalender Jawa merupakan perpaduan keduanya.
            </p>
            <p>
                Alat ini dibuat oleh <strong>Taaho.id</strong> untuk mempermudah pengguna dalam menyesuaikan tanggal
                lintas sistem kalender tradisional dan modern.
            </p>
        </aside>

    </main>

    <!-- 🔹 FOOTER -->
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

        // JavaScript untuk form switching
        const jenis = document.getElementById('jenis');
        const formMasehi = document.getElementById('form-masehi');
        const formHijriyah = document.getElementById('form-hijriyah');
        const formJawa = document.getElementById('form-jawa');

        function updateForm() {
            formMasehi.style.display = "none";
            formHijriyah.style.display = "none";
            formJawa.style.display = "none";
            if (jenis.value === "masehi") formMasehi.style.display = "block";
            if (jenis.value === "hijriyah") formHijriyah.style.display = "block";
            if (jenis.value === "jawa") formJawa.style.display = "block";
        }
        jenis.addEventListener("change", updateForm);
        updateForm();
    </script>

</body>

</html>