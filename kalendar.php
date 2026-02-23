<?php
include "includes/functions.php";

// ambil input bulan & tahun dari GET
$bulan = isset($_GET['bulan']) ? (int) $_GET['bulan'] : date("n");
$tahun = isset($_GET['tahun']) ? (int) $_GET['tahun'] : date("Y");

// nama bulan masehi
$bulan_nama = [
    1 => "Januari",
    2 => "Februari",
    3 => "Maret",
    4 => "April",
    5 => "Mei",
    6 => "Juni",
    7 => "Juli",
    8 => "Agustus",
    9 => "September",
    10 => "Oktober",
    11 => "November",
    12 => "Desember"
];

// jumlah hari dalam bulan masehi
$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

// hari pertama bulan ini jatuh di hari apa (0=Min, 6=Sabtu)
$start_day = (int) date("w", strtotime("$tahun-$bulan-01"));
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <!-- 🌐 Meta Dasar -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- 🏷️ Judul & Deskripsi -->
    <title>Kalender Lengkap <?= $bulan_nama[$bulan] . " " . $tahun ?> • Taaho Kalender</title>

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

        /* ======= MAIN ======= */
        .main {
            max-width: 900px;
            margin: 30px auto 0 auto;
            padding: 0 20px;
        }

        .calendar-container {
            text-align: center;
        }

        .page-title {
            color: #ffa500;
            font-weight: 700;
            font-size: 1.8em;
            margin-bottom: 20px;
        }

        /* ======= FORM BULAN & TAHUN ======= */
        .month-nav {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            background-color: #1e1e1e;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 25px;
        }

        .form-group label {
            margin-right: 8px;
            color: #ccc;
            font-size: 0.9em;
        }

        select,
        input[type="number"] {
            background-color: #2a2a2a;
            border: 1px solid #333;
            color: #fff;
            padding: 8px 12px;
            border-radius: 8px;
            outline: none;
            font-size: 0.9em;
        }

        select:focus,
        input[type="number"]:focus {
            border-color: #ffa500;
        }

        .btn-primary {
            background-color: #ffa500;
            color: #121212;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-primary:hover {
            background-color: #ffb733;
        }

        /* ======= KALENDER ======= */
        .calendar-wrapper {
            overflow-x: auto;
            margin-bottom: 20px;
            -webkit-overflow-scrolling: touch;
        }

        .calendar-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #1a1a1a;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(255, 140, 0, 0.2);
            min-width: 600px;
        }

        th,
        td {
            border: 1px solid #2a2a2a;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #2c2c2c;
            color: #ffa500;
            font-weight: 600;
        }

        td {
            color: #fff;
            vertical-align: top;
            min-width: 100px;
            height: 80px;
        }

        td.empty {
            background-color: #191919;
        }

        .date-number {
            font-size: 1.1em;
            font-weight: 700;
            color: #ffa500;
        }

        .date-info {
            font-size: 0.8em;
            line-height: 1.2;
            color: #ccc;
        }

        .date-info.hijri {
            color: #80ffb3;
        }

        .date-info.jawa {
            color: #9fc5ff;
        }

        /* ======= SECTION INFO ======= */
        .calendar-info {
            margin-top: 40px;
            background-color: #1a1a1a;
            border-left: 3px solid #ffa500;
            border-radius: 10px;
            padding: 20px;
            text-align: left;
        }

        .calendar-info h3 {
            color: #ffa500;
            margin-bottom: 10px;
            font-size: 1.3em;
        }

        .calendar-info p {
            font-size: 0.95em;
            color: #ccc;
        }

        /* ======= FOOTER ======= */
        footer {
            text-align: center;
            padding: 25px 0;
            margin-top: 50px;
            border-top: 1px solid #222;
            color: #888;
            font-size: 0.9rem;
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
            .main {
                margin: 20px auto 0 auto;
                padding: 0 15px;
            }

            .page-title {
                font-size: 1.5em;
                margin-bottom: 15px;
            }

            /* Month Navigation */
            .month-nav {
                flex-direction: column;
                gap: 12px;
                padding: 15px 10px;
            }

            .form-group {
                width: 100%;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .form-group label {
                flex: 1;
                text-align: left;
            }

            select,
            input[type="number"] {
                flex: 2;
                width: 100%;
            }

            .btn-primary {
                width: 100%;
                margin-top: 10px;
            }

            /* Calendar Table */
            .calendar-table {
                min-width: 650px;
            }

            th,
            td {
                padding: 8px 5px;
                min-width: 90px;
                height: 70px;
            }

            .date-number {
                font-size: 1em;
            }

            .date-info {
                font-size: 0.7em;
            }

            /* Calendar Info */
            .calendar-info {
                margin-top: 30px;
                padding: 15px;
            }

            .calendar-info h3 {
                font-size: 1.2em;
            }

            .calendar-info p {
                font-size: 0.9em;
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

            /* Main Content */
            .main {
                padding: 0 10px;
            }

            .page-title {
                font-size: 1.3em;
            }

            /* Month Navigation */
            .month-nav {
                padding: 12px 8px;
                gap: 10px;
            }

            .form-group {
                flex-direction: column;
                align-items: flex-start;
            }

            .form-group label {
                margin-bottom: 5px;
                width: 100%;
            }

            select,
            input[type="number"] {
                width: 100%;
            }

            /* Calendar Table */
            .calendar-table {
                min-width: 700px;
            }

            th,
            td {
                padding: 6px 3px;
                min-width: 85px;
                height: 65px;
            }

            .date-number {
                font-size: 0.95em;
            }

            .date-info {
                font-size: 0.65em;
            }

            /* Calendar Info */
            .calendar-info {
                padding: 12px;
            }

            .calendar-info h3 {
                font-size: 1.1em;
            }

            .calendar-info p {
                font-size: 0.85em;
            }

            /* Footer */
            footer {
                font-size: 0.85rem;
            }
        }

        /* Additional Mobile Optimizations */
        @media (max-width: 360px) {
            .calendar-table {
                min-width: 750px;
            }

            th,
            td {
                padding: 5px 2px;
                min-width: 80px;
                height: 60px;
            }

            .date-info {
                font-size: 0.6em;
            }
        }

        /* Touch-friendly improvements */
        .btn-primary,
        select,
        input[type="number"] {
            min-height: 44px;
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
                <li><a href="kalendar.php" class="active">Kalender Lengkap</a></li>
                <li><a href="konversi.php">Konversi Tanggal</a></li>
            </ul>
        </nav>
    </header>

    <div class="main">
        <div class="calendar-container">
            <h2 class="page-title">📅 Kalender <?= $bulan_nama[$bulan] . " " . $tahun ?></h2>

            <form method="GET" class="month-nav">
                <div class="form-group">
                    <label for="bulan">Bulan</label>
                    <select name="bulan" id="bulan">
                        <?php foreach ($bulan_nama as $i => $n): ?>
                            <option value="<?= $i ?>" <?= $i == $bulan ? "selected" : "" ?>><?= $n ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tahun">Tahun</label>
                    <input type="number" name="tahun" id="tahun" value="<?= $tahun ?>">
                </div>

                <button type="submit" class="btn-primary">Tampilkan</button>
            </form>

            <div class="calendar-wrapper">
                <table class="calendar-table">
                    <thead>
                        <tr>
                            <th>Minggu</th>
                            <th>Senin</th>
                            <th>Selasa</th>
                            <th>Rabu</th>
                            <th>Kamis</th>
                            <th>Jumat</th>
                            <th>Sabtu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                            for ($i = 0; $i < $start_day; $i++) {
                                echo "<td class='empty'></td>";
                            }

                            for ($tanggal = 1; $tanggal <= $jumlah_hari; $tanggal++) {
                                $currentDate = sprintf("%04d-%02d-%02d", $tahun, $bulan, $tanggal);
                                $h = masehiToHijriyahArray($currentDate);
                                $j = masehiToJawa($currentDate);

                                echo "<td>";
                                echo "<div class='date-number'>$tanggal</div>";
                                echo "<div class='date-info hijri'>H: {$h['day']} {$h['month_name']}</div>";
                                echo "<div class='date-info jawa'>J: {$j['tanggal']} {$j['pasaran']}</div>";
                                echo "</td>";

                                if (date('w', strtotime($currentDate)) == 6) {
                                    echo "</tr><tr>";
                                }
                            }
                            echo "</tr>";
                            ?>
                    </tbody>
                </table>
            </div>

            <section class="calendar-info">
                <h3>🗓 Tentang Kalender <?= $bulan_nama[$bulan] . " " . $tahun ?></h3>
                <p>
                    Kalender ini menampilkan tanggal <strong>Masehi</strong> lengkap dengan padanan
                    <strong>Hijriyah</strong> dan <strong>Kalender Jawa</strong>. Dengan tampilan interaktif,
                    kamu bisa melihat setiap hari secara detail beserta tanggal penting dalam sistem penanggalan Islam
                    dan Jawa.
                </p>
                <p>
                    Gunakan fitur navigasi di atas untuk mengganti bulan dan tahun sesuai kebutuhan.
                    Fitur ini berguna untuk melihat <em>hari besar keagamaan</em>, <em>weton Jawa</em>, atau
                    sekadar perencanaan kegiatan harian kamu di bulan <strong><?= $bulan_nama[$bulan] ?></strong> tahun
                    <strong><?= $tahun ?></strong>.
                </p>
            </section>
        </div>
    </div>

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