<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TahunAjaranModel;
use App\Models\SemesterModel;

class AkademikController extends BaseController
{
    protected $tahunAjaranModel;
    protected $semesterModel;

    public function __construct()
    {
        $this->tahunAjaranModel = new TahunAjaranModel();
        $this->semesterModel = new SemesterModel();
    }

    // --- TAHUN AJARAN ---

    public function tahunAjaran()
    {
        $data = [
            'title' => 'Tahun Ajaran',
            'tahun_ajaran' => $this->tahunAjaranModel->orderBy('nama', 'DESC')->findAll()
        ];
        return view('admin/akademik/tahun_ajaran', $data);
    }

    public function storeTahunAjaran()
    {
        $rules = [
            'nama' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Nama Tahun Ajaran wajib diisi.')->withInput();
        }

        $this->tahunAjaranModel->save([
            'nama' => $this->request->getPost('nama'),
            'is_aktif' => 0
        ]);

        return redirect()->to('/admin/akademik/tahun-ajaran')->with('success', 'Tahun Ajaran berhasil ditambahkan.');
    }

    public function updateTahunAjaran($id)
    {
        $rules = [
            'nama' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Nama Tahun Ajaran wajib diisi.');
        }

        $this->tahunAjaranModel->update($id, [
            'nama' => $this->request->getPost('nama'),
        ]);

        return redirect()->to('/admin/akademik/tahun-ajaran')->with('success', 'Tahun Ajaran berhasil diupdate.');
    }

    public function deleteTahunAjaran($id)
    {
        try {
            $this->tahunAjaranModel->delete($id);
            return redirect()->to('/admin/akademik/tahun-ajaran')->with('success', 'Tahun Ajaran berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->to('/admin/akademik/tahun-ajaran')->with('error', 'Gagal menghapus! Data ini mungkin masih digunakan.');
        }
    }

    public function activateTahunAjaran($id)
    {
        // Nonaktifkan semua
        $this->tahunAjaranModel->set('is_aktif', 0)->where('id >', 0)->update();
        // Aktifkan yang dipilih
        $this->tahunAjaranModel->update($id, ['is_aktif' => 1]);

        return redirect()->to('/admin/akademik/tahun-ajaran')->with('success', 'Tahun Ajaran telah diaktifkan.');
    }

    // --- SEMESTER ---

    public function semester()
    {
        $data = [
            'title' => 'Semester Aktif',
            'semester' => $this->semesterModel
                               ->select('semester.*, tahun_ajaran.nama as nama_tahun')
                               ->join('tahun_ajaran', 'tahun_ajaran.id = semester.tahun_ajaran_id')
                               ->orderBy('tahun_ajaran.nama', 'DESC')
                               ->orderBy('semester.nama_semester', 'ASC')
                               ->findAll(),
            'tahun_ajaran' => $this->tahunAjaranModel->findAll()
        ];
        return view('admin/akademik/semester', $data);
    }

    public function storeSemester()
    {
        $rules = [
            'tahun_ajaran_id' => 'required',
            'nama_semester' => 'required|in_list[Ganjil,Genap]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Semua kolom wajib diisi dengan benar.')->withInput();
        }

        $this->semesterModel->save([
            'tahun_ajaran_id' => $this->request->getPost('tahun_ajaran_id'),
            'nama_semester' => $this->request->getPost('nama_semester'),
            'is_aktif' => 0
        ]);

        return redirect()->to('/admin/akademik/semester')->with('success', 'Semester berhasil ditambahkan.');
    }

    public function deleteSemester($id)
    {
        try {
            $this->semesterModel->delete($id);
            return redirect()->to('/admin/akademik/semester')->with('success', 'Semester berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->to('/admin/akademik/semester')->with('error', 'Gagal menghapus! Data ini mungkin masih digunakan.');
        }
    }

    public function activateSemester($id)
    {
        // Nonaktifkan semua
        $this->semesterModel->set('is_aktif', 0)->where('id >', 0)->update();
        // Aktifkan yang dipilih
        $this->semesterModel->update($id, ['is_aktif' => 1]);

        return redirect()->to('/admin/akademik/semester')->with('success', 'Semester telah diaktifkan.');
    }

    // --- KENAIKAN KELAS & KELULUSAN ---

    public function kenaikanKelas()
    {
        $kelasModel = new \App\Models\KelasModel();
        $siswaModel = new \App\Models\SiswaModel();
        
        $kelasAsalId = $this->request->getGet('kelas_asal_id');
        
        $data = [
            'title' => 'Kenaikan Kelas & Kelulusan',
            'tahun_ajaran' => $this->tahunAjaranModel->orderBy('nama', 'DESC')->findAll(),
            'semua_kelas' => $kelasModel->select('kelas.*, tahun_ajaran.nama as nama_tahun')
                                        ->join('tahun_ajaran', 'tahun_ajaran.id = kelas.tahun_ajaran_id')
                                        ->orderBy('tahun_ajaran.nama', 'DESC')
                                        ->orderBy('kelas.nama_kelas', 'ASC')
                                        ->findAll(),
            'kelas_asal_id' => $kelasAsalId,
            'siswa' => [],
            'kelas_asal' => null
        ];

        if ($kelasAsalId) {
            $data['siswa'] = $siswaModel->where('kelas_id', $kelasAsalId)->findAll();
            $data['kelas_asal'] = $kelasModel->find($kelasAsalId);
        }

        return view('admin/akademik/kenaikan_kelas', $data);
    }

    public function processKenaikanKelas()
    {
        $siswaIds = $this->request->getPost('siswa_ids');
        $kelasTujuanId = $this->request->getPost('kelas_tujuan_id'); 
        $kelasAsalId = $this->request->getPost('kelas_asal_id');
        
        if (empty($siswaIds) || !$kelasTujuanId || !$kelasAsalId) {
            return redirect()->back()->with('error', 'Pilih minimal satu siswa dan tujuan kenaikan.');
        }

        $siswaModel = new \App\Models\SiswaModel();
        $kelasModel = new \App\Models\KelasModel();
        $db = \Config\Database::connect();

        $kelasAsal = $kelasModel->find($kelasAsalId);
        $tahunAjaranLama = $kelasAsal['tahun_ajaran_id'];
        
        $statusAkhir = 'naik_kelas';
        $newKelasId = null;
        if (is_numeric($kelasTujuanId)) {
            $newKelasId = $kelasTujuanId;
            // jika tahun ajaran tujuan sama dengan lama, mungkin tinggal kelas
            $kelasTujuan = $kelasModel->find($newKelasId);
            if ($kelasTujuan['tahun_ajaran_id'] == $tahunAjaranLama) {
                $statusAkhir = 'tinggal_kelas';
            }
        } else {
            // lulus atau keluar
            $statusAkhir = $kelasTujuanId; 
        }

        $db->transStart();

        foreach ($siswaIds as $sId) {
            // Insert ke tabel riwayat_kelas
            $db->table('riwayat_kelas')->insert([
                'siswa_id' => $sId,
                'kelas_id' => $kelasAsalId,
                'tahun_ajaran_id' => $tahunAjaranLama,
                'semester_id' => session()->get('semester_id'), // nullable
                'status_akhir' => $statusAkhir,
                'catatan' => 'Diproses massal oleh admin',
                'created_at' => date('Y-m-d H:i:s')
            ]);

            // Update siswa
            if ($newKelasId) {
                $siswaModel->update($sId, ['kelas_id' => $newKelasId]);
            } else {
                // jika lulus/keluar, kelas dikosongkan agar tidak muncul di jadwal aktif
                $siswaModel->update($sId, ['kelas_id' => null]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memproses kenaikan kelas.');
        }

        return redirect()->to('/admin/akademik/kenaikan-kelas?kelas_asal_id='.$kelasAsalId)->with('success', count($siswaIds).' siswa berhasil diproses.');
    }
}
