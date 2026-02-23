<?php
// includes/functions.php

// konversi Gregorian -> Julian Day Number (JDN)
function gregorian_to_jd_php($year, $month, $day)
{
    $a = (int)floor((14 - $month) / 12);
    $y = $year + 4800 - $a;
    $m = $month + 12 * $a - 3;
    $jd = $day + (int)floor((153 * $m + 2) / 5) + 365 * $y + (int)floor($y / 4) - (int)floor($y / 100) + (int)floor($y / 400) - 32045;
    return $jd;
}

// ----- Hijriyah (aritmetic/tabular) -----
// Mengembalikan array: ['day'=>, 'month_index'=>1..12, 'month_name'=>, 'year'=>]
function masehiToHijriyahArray($date)
{
    $ts = strtotime($date);
    $d = (int)date('d', $ts);
    $m = (int)date('m', $ts);
    $y = (int)date('Y', $ts);

    $jd = gregorian_to_jd_php($y, $m, $d);

    $arabic = $jd - 1948439 + 10632;
    $n = (int)floor(($arabic - 1) / 10631);
    $arabic = $arabic - 10631 * $n + 354;
    $j = (int)(
        (int)floor((10985 - $arabic) / 5316) * (int)floor((50 * $arabic) / 17719)
        + (int)floor($arabic / 5670) * (int)floor((43 * $arabic) / 15238)
    );
    $arabic = $arabic
        - (int)floor((30 - $j) / 15) * (int)floor((17719 * $j) / 50)
        - (int)floor($j / 16) * (int)floor((15238 * $j) / 43)
        + 29;
    $month_h = (int)floor((24 * $arabic) / 709);
    $day_h = (int)($arabic - (int)floor((709 * $month_h) / 24));
    $year_h = (int)(30 * $n + $j - 30);

    $months = [
        1 => "Muharram",
        2 => "Safar",
        3 => "Rabi'ul Awal",
        4 => "Rabi'ul Akhir",
        5 => "Jumadil Awal",
        6 => "Jumadil Akhir",
        7 => "Rajab",
        8 => "Sya'ban",
        9 => "Ramadan",
        10 => "Syawal",
        11 => "Dzulqa'dah",
        12 => "Dzulhijjah"
    ];
    $monthName = $months[$month_h] ?? ("Bulan$month_h");

    return [
        'day' => $day_h,
        'month_index' => $month_h,
        'month_name' => $monthName,
        'year' => $year_h
    ];
}

// helper: string Hijriyah
function masehiToHijriyah($date)
{
    $h = masehiToHijriyahArray($date);
    return $h['day'] . " " . $h['month_name'] . " " . $h['year'] . " H";
}

// ----- Kalender Jawa -----
// Mengembalikan array: tanggal Jawa, hari (ID), pasaran, weton, tahun_jawa, nama tahun jawa
function masehiToJawa($date)
{
    $ts = strtotime($date);
    $d = (int)date('d', $ts);
    $m = (int)date('m', $ts);
    $y = (int)date('Y', $ts);
    $jd = gregorian_to_jd_php($y, $m, $d);

    // Ambil data Hijriyah terstruktur
    $h = masehiToHijriyahArray($date);
    $h_day = $h['day'];
    $h_month_index = $h['month_index'];
    $h_year = $h['year'];

    // Tahun Jawa = Hijriyah + 512
    $java_year = $h_year + 512;

    // Map bulan Hijriyah -> nama bulan Jawa
    $bulan_map = [
        1 => "Sura",
        2 => "Sapar",
        3 => "Mulud",
        4 => "Bakda Mulud",
        5 => "Jumadil Awal",
        6 => "Jumadil Akhir",
        7 => "Rejeb",
        8 => "Ruwah",
        9 => "Pasa",
        10 => "Sawal",
        11 => "Dulkangidah",
        12 => "Besar"
    ];
    $month_j = $bulan_map[$h_month_index] ?? ("BulanJawa$h_month_index");

    // Hari Masehi (Indonesian)
    $hari_eng = date("l", $ts);
    $hari_id = [
        "Sunday" => "Minggu",
        "Monday" => "Senin",
        "Tuesday" => "Selasa",
        "Wednesday" => "Rabu",
        "Thursday" => "Kamis",
        "Friday" => "Jumat",
        "Saturday" => "Sabtu"
    ];
    $hari_idn = $hari_id[$hari_eng] ?? $hari_eng;

    // Pasaran: gunakan patokan 8 Juli 1633 = JDN_ref = Legi
    $jd_ref = gregorian_to_jd_php(1633, 7, 8); // patokan historis
    $pasaran_list = ["Legi", "Pahing", "Pon", "Wage", "Kliwon"];
    $index = ($jd - $jd_ref) % 5;
    if ($index < 0) $index += 5;
    $pasaran = $pasaran_list[$index];

    $weton = $hari_idn . " " . $pasaran;

    // Nama Tahun Jawa (windu) - 8-tahun cycle
    $windu = ["Alip", "Ehe", "Jimawal", "Je", "Dal", "Be", "Wawu", "Jimakir"];
    // offset dipilih agar contoh 1959 -> "Dal" sesuai patokanmu
    $windu_name = $windu[(($java_year + 5) % 8)];

    return [
        'tanggal' => $h_day . " " . $month_j . " " . $java_year,
        'hari' => $hari_idn,
        'pasaran' => $pasaran,
        'weton' => $weton,
        'tahun_jawa' => $java_year,
        'tahun_jawa_name' => $windu_name
    ];
}


// Hijriyah → Masehi
// =====================================
function islamic_to_jd_custom($month, $day, $year)
{
    // Rumus astronomi konversi Hijriyah ke Julian Day
    return (int)($day
        + ceil(29.5 * ($month - 1))
        + ($year - 1) * 354
        + floor((3 + (11 * $year)) / 30)
        + 1948439.5) - 0.5;
}

// =====================================
// Hijriyah → Masehi
// =====================================
function hijriyahToMasehi($day, $month, $year)
{
    $jd = islamic_to_jd_custom($month, $day, $year);
    $greg = jdtogregorian($jd); // format mm/dd/yyyy
    $parts = explode("/", $greg);
    return sprintf("%04d-%02d-%02d", $parts[2], $parts[0], $parts[1]);
}


// =====================================
// Jawa → Masehi
// =====================================
function jawaToMasehi($day, $month, $year)
{
    $bulanJawa = [
        "Sura" => 1,
        "Sapar" => 2,
        "Mulud" => 3,
        "Bakda Mulud" => 4,
        "Jumadilawal" => 5,
        "Jumadilakir" => 6,
        "Rejeb" => 7,
        "Ruwah" => 8,
        "Pasa" => 9,
        "Sawal" => 10,
        "Dulkangidah" => 11,
        "Besar" => 12
    ];

    if (!isset($bulanJawa[$month])) {
        return null;
    }

    $tahunHijriyah = $year - 512;
    $bulanHijriyah = $bulanJawa[$month];

    return hijriyahToMasehi($day, $bulanHijriyah, $tahunHijriyah);
}


// =====================================
// Format tanggal Masehi ke Bahasa Indonesia
// =====================================
function formatTanggalIndonesia($date)
{
    $timestamp = strtotime($date);

    $hariInggris = date('l', $timestamp);
    $bulanInggris = date('F', $timestamp);

    $hari = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];

    $bulan = [
        'January' => 'Januari',
        'February' => 'Februari',
        'March' => 'Maret',
        'April' => 'April',
        'May' => 'Mei',
        'June' => 'Juni',
        'July' => 'Juli',
        'August' => 'Agustus',
        'September' => 'September',
        'October' => 'Oktober',
        'November' => 'November',
        'December' => 'Desember'
    ];

    $hariID = $hari[$hariInggris] ?? $hariInggris;
    $bulanID = $bulan[$bulanInggris] ?? $bulanInggris;

    return $hariID . ', ' . date('d', $timestamp) . ' ' . $bulanID . ' ' . date('Y', $timestamp);
}
