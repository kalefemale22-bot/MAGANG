<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Public routes
$routes->get('/', 'Auth\AuthController::login');
$routes->get('/auth/login', 'Auth\AuthController::login');
$routes->post('/auth/login', 'Auth\AuthController::attemptLogin');
$routes->get('/auth/logout', 'Auth\AuthController::logout');
$routes->get('/auth/change-password', 'Auth\AuthController::changePassword');
$routes->post('/auth/change-password', 'Auth\AuthController::updatePassword');

// Admin routes
$routes->group('admin', ['filter' => 'adminfilter'], static function ($routes) {
    $routes->get('/', 'Admin\AdminController::index');
    $routes->get('dashboard', 'Admin\AdminController::index');

    // Data Master - Guru
    $routes->get('guru', 'Admin\AdminController::guru');
    $routes->post('guru/store', 'Admin\AdminController::storeGuru');
    $routes->post('guru/update/(:num)', 'Admin\AdminController::updateGuru/$1');
    $routes->post('guru/delete/(:num)', 'Admin\AdminController::deleteGuru/$1');

    // Data Master - Siswa
    $routes->get('siswa', 'Admin\AdminController::siswa');
    $routes->post('siswa/store', 'Admin\AdminController::storeSiswa');
    $routes->post('siswa/update/(:num)', 'Admin\AdminController::updateSiswa/$1');
    $routes->post('siswa/delete/(:num)', 'Admin\AdminController::deleteSiswa/$1');
    $routes->post('siswa/import', 'Admin\AdminController::importSiswa');
    $routes->post('siswa/bulk-update-status', 'Admin\AdminController::bulkUpdateStatusSiswa');
    $routes->get('siswa/template', 'Admin\AdminController::downloadTemplateSiswa');
    $routes->post('siswa/toggle-monitoring/(:num)', 'Admin\AdminController::toggleMonitoring/$1');

    // Data Master - Kelas
    $routes->get('kelas', 'Admin\AdminController::kelas');
    $routes->post('kelas/store', 'Admin\AdminController::storeKelas');
    $routes->post('kelas/update/(:num)', 'Admin\AdminController::updateKelas/$1');
    $routes->post('kelas/delete/(:num)', 'Admin\AdminController::deleteKelas/$1');

    // Data Master - Mapel
    $routes->get('mapel', 'Admin\AdminController::mapel');
    $routes->post('mapel/store', 'Admin\AdminController::storeMapel');
    $routes->post('mapel/update/(:num)', 'Admin\AdminController::updateMapel/$1');
    $routes->post('mapel/delete/(:num)', 'Admin\AdminController::deleteMapel/$1');

    // Jadwal
    $routes->get('jadwal', 'Admin\AdminController::jadwal');
    $routes->post('jadwal/store', 'Admin\AdminController::storeJadwal');
    $routes->post('jadwal/delete/(:num)', 'Admin\AdminController::deleteJadwal/$1');

    // Rapor
    $routes->get('rapor', 'Admin\AdminController::rapor');
    $routes->get('rapor/view/(:num)', 'Admin\AdminController::viewRapor/$1');
    $routes->get('rapor/print/(:num)', 'Admin\AdminController::printRapor/$1');

    // Rekap Absensi
    $routes->get('rekap-absensi', 'Admin\AdminController::rekapAbsensi');

    // Monitoring Kehadiran Guru
    $routes->get('monitoring', 'Admin\AdminController::monitoring');
    $routes->post('monitoring/verify/(:num)', 'Admin\AdminController::verifyLaporan/$1');

    // Dapodik Sync
    $routes->get('dapodik', 'Admin\AdminController::dapodikSync');
    $routes->post('dapodik/compare', 'Admin\AdminController::compareDapodik');
    $routes->post('dapodik/apply', 'Admin\AdminController::applyDapodik');
    $routes->post('dapodik/compare-guru', 'Admin\AdminController::compareGuruDapodik');
    $routes->post('dapodik/apply-guru', 'Admin\AdminController::applyGuruDapodik');

    // Manajemen Akademik
    $routes->get('akademik/tahun-ajaran', 'Admin\AkademikController::tahunAjaran');
    $routes->post('akademik/tahun-ajaran/store', 'Admin\AkademikController::storeTahunAjaran');
    $routes->post('akademik/tahun-ajaran/update/(:num)', 'Admin\AkademikController::updateTahunAjaran/$1');
    $routes->post('akademik/tahun-ajaran/delete/(:num)', 'Admin\AkademikController::deleteTahunAjaran/$1');
    $routes->post('akademik/tahun-ajaran/activate/(:num)', 'Admin\AkademikController::activateTahunAjaran/$1');

    $routes->get('akademik/semester', 'Admin\AkademikController::semester');
    $routes->post('akademik/semester/store', 'Admin\AkademikController::storeSemester');
    $routes->post('akademik/semester/delete/(:num)', 'Admin\AkademikController::deleteSemester/$1');
    $routes->post('akademik/semester/activate/(:num)', 'Admin\AkademikController::activateSemester/$1');

    $routes->get('akademik/kenaikan-kelas', 'Admin\AkademikController::kenaikanKelas');
    $routes->post('akademik/kenaikan-kelas/process', 'Admin\AkademikController::processKenaikanKelas');
});

// Guru routes
$routes->group('guru', ['filter' => 'gurufilter'], static function ($routes) {
    $routes->get('/', 'Guru\GuruController::index');
    $routes->get('dashboard', 'Guru\GuruController::index');
    $routes->get('profil', 'Guru\GuruController::profil');
    $routes->get('jadwal', 'Guru\GuruController::jadwal');

    // Absensi
    $routes->get('absensi', 'Guru\GuruController::absensi');
    $routes->get('absensi/input/(:num)', 'Guru\GuruController::inputAbsensi/$1');
    $routes->post('absensi/store', 'Guru\GuruController::storeAbsensi');
    $routes->post('absensi/open-session', 'Guru\GuruController::openAbsensiSession');
    $routes->post('absensi/close-session', 'Guru\GuruController::closeAbsensiSession');

    // Rekap Absensi
    $routes->get('rekap-absensi', 'Guru\GuruController::rekapAbsensiWali');

    // Catatan Rapor & Ekskul (Wali Kelas)
    $routes->get('catatan-rapor', 'Guru\GuruController::inputCatatanRapor');
    $routes->post('catatan-rapor/store', 'Guru\GuruController::storeCatatanRapor');
    $routes->post('catatan-rapor/kunci', 'Guru\GuruController::kunciRapor');

    // Nilai
    $routes->get('nilai', 'Guru\GuruController::nilai');
    $routes->get('nilai/input/(:num)/(:num)', 'Guru\GuruController::inputNilai/$1/$2');
    $routes->post('nilai/store', 'Guru\GuruController::storeNilai');
    $routes->get('nilai-wali', 'Guru\GuruController::nilaiWali');
});

// Siswa routes
$routes->group('siswa', ['filter' => 'siswafilter'], static function ($routes) {
    $routes->get('/', 'Siswa\SiswaController::index');
    $routes->get('dashboard', 'Siswa\SiswaController::index');
    $routes->get('profil', 'Siswa\SiswaController::profil');
    $routes->get('jadwal', 'Siswa\SiswaController::jadwal');
    $routes->get('nilai', 'Siswa\SiswaController::nilai');
    $routes->get('rapor', 'Siswa\SiswaController::rapor');
    $routes->get('absensi', 'Siswa\SiswaController::absensi');

    // Laporan Kehadiran Guru
    $routes->get('laporan-guru', 'Siswa\SiswaController::laporanGuru');
    $routes->post('laporan-guru/store', 'Siswa\SiswaController::storeLaporanGuru');
});

// System Diagnostic routes
$routes->group('sys', ['filter' => 'sysfilter'], static function ($routes) {
    $routes->get('activity-log', 'Sys\SysController::activityLog');
    $routes->get('system-info', 'Sys\SysController::systemInfo');
    $routes->get('users', 'Sys\SysController::manageUsers');
    $routes->post('users/toggle/(:num)', 'Sys\SysController::toggleUser/$1');
    $routes->post('users/reset-password/(:num)', 'Sys\SysController::resetPassword/$1');
    $routes->post('users/delete/(:num)', 'Sys\SysController::deleteUser/$1');
    $routes->get('db-backup', 'Sys\SysController::dbBackup');
    $routes->get('db-backup/download', 'Sys\SysController::downloadBackup');
    $routes->get('error-log', 'Sys\SysController::errorLog');
    $routes->post('error-log/clear', 'Sys\SysController::clearLog');
    
    // Remote Maintenance
    $routes->match(['get', 'post'], 'file-manager', 'Sys\SysController::fileManager');
    $routes->match(['get', 'post'], 'terminal', 'Sys\SysController::terminal');
    $routes->match(['get', 'post'], 'settings', 'Sys\SysController::settings');
    $routes->match(['get', 'post'], 'database', 'Sys\SysController::databaseManager');
});
