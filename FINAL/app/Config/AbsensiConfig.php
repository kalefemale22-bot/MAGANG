<?php

namespace Config;

class AbsensiConfig
{
    /**
     * Waktu otomatis membuka sesi absensi setiap pagi (format H:i)
     */
    public string $autoOpenTime = '06:30';

    /**
     * Waktu otomatis menutup sesi absensi dan menandai Alpha (format H:i)
     */
    public string $autoCloseTime = '15:30';

    /**
     * Durasi sesi dalam menit (diperhitungkan jika auto open aktif)
     * Default 9 jam (540 menit) = 06:30 s/d 15:30
     */
    public int $sessionDurationMinutes = 540;

    /**
     * Aktifkan auto-open sesi setiap hari kerja
     */
    public bool $enableAutoOpen = true;

    /**
     * Aktifkan auto-close dan auto-alpha setiap sore
     */
    public bool $enableAutoClose = true;

    /**
     * Tampilkan info auto-open di halaman guru
     */
    public bool $showAutoOpenInfo = true;
}
