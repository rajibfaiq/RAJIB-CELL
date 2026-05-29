<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Seed Poli (if empty)
        $poliData = [
            ['ID_POLI' => 'POL001', 'NAMA_POLI' => 'Poli Umum', 'ICON' => 'fa-stethoscope', 'KETERANGAN' => 'Pemeriksaan umum'],
            ['ID_POLI' => 'POL002', 'NAMA_POLI' => 'Poli Gigi', 'ICON' => 'fa-tooth', 'KETERANGAN' => 'Pemeriksaan dan perawatan gigi'],
            ['ID_POLI' => 'POL003', 'NAMA_POLI' => 'Poli Anak', 'ICON' => 'fa-baby', 'KETERANGAN' => 'Pemeriksaan anak-anak'],
            ['ID_POLI' => 'POL004', 'NAMA_POLI' => 'Poli Kandungan', 'ICON' => 'fa-female', 'KETERANGAN' => 'Pemeriksaan kandungan dan kebidanan'],
            ['ID_POLI' => 'POL005', 'NAMA_POLI' => 'Poli Mata', 'ICON' => 'fa-eye', 'KETERANGAN' => 'Pemeriksaan mata'],
            ['ID_POLI' => 'POL006', 'NAMA_POLI' => 'Poli THT', 'ICON' => 'fa-head-side-cough', 'KETERANGAN' => 'Telinga, Hidung, Tenggorokan'],
            ['ID_POLI' => 'POL007', 'NAMA_POLI' => 'Poli Bedah', 'ICON' => 'fa-procedures', 'KETERANGAN' => 'Konsultasi dan tindakan bedah'],
            ['ID_POLI' => 'POL008', 'NAMA_POLI' => 'Poli Penyakit Dalam', 'ICON' => 'fa-heartbeat', 'KETERANGAN' => 'Penyakit dalam / internis'],
        ];

        foreach ($poliData as $p) {
            $this->db->table('poli')->ignore()->insert($p);
        }

        // 2. Seed Dokter
        $dokterData = [
            [
                'ID_DOKTER' => 'D001',
                'NAMA_DOKTER' => 'Dr. Budi Santoso',
                'SPESIALIS' => '-',
                'NO_IZIN_PRAKTEK' => '123/SIP/2024',
                'JADWAL' => 'Senin - Jumat, 08:00 - 14:00',
                'STATUS' => 'Aktif',
                'ID_POLI' => 'POL001',
                'KUOTA_HARIAN' => 25
            ],
            [
                'ID_DOKTER' => 'D002',
                'NAMA_DOKTER' => 'Dr. Rina Wijaya',
                'SPESIALIS' => '-',
                'NO_IZIN_PRAKTEK' => '124/SIP/2024',
                'JADWAL' => 'Senin, Rabu, Jumat, 09:00 - 13:00',
                'STATUS' => 'Aktif',
                'ID_POLI' => 'POL003',
                'KUOTA_HARIAN' => 20
            ],
            [
                'ID_DOKTER' => 'D003',
                'NAMA_DOKTER' => 'Dr. Andi Pratama',
                'SPESIALIS' => '-',
                'NO_IZIN_PRAKTEK' => '125/SIP/2024',
                'JADWAL' => 'Selasa, Kamis, Sabtu, 10:00 - 15:00',
                'STATUS' => 'Aktif',
                'ID_POLI' => 'POL002',
                'KUOTA_HARIAN' => 15
            ],
            [
                'ID_DOKTER' => 'D004',
                'NAMA_DOKTER' => 'Dr. Siti Aminah',
                'SPESIALIS' => '-',
                'NO_IZIN_PRAKTEK' => '126/SIP/2024',
                'JADWAL' => 'Senin - Kamis, 08:00 - 12:00',
                'STATUS' => 'Aktif',
                'ID_POLI' => 'POL004',
                'KUOTA_HARIAN' => 20
            ],
            [
                'ID_DOKTER' => 'D005',
                'NAMA_DOKTER' => 'Dr. Hendra Wijaya',
                'SPESIALIS' => '-',
                'NO_IZIN_PRAKTEK' => '127/SIP/2024',
                'JADWAL' => 'Senin, Selasa, Kamis, 13:00 - 17:00',
                'STATUS' => 'Aktif',
                'ID_POLI' => 'POL008',
                'KUOTA_HARIAN' => 20
            ]
        ];

        foreach ($dokterData as $d) {
            $this->db->table('dokter')->ignore()->insert($d);
        }

        // 3. Seed Perawat
        $perawatData = [
            ['ID_PERAWAT' => 'P001', 'NAMA_PERAWAT' => 'Ns. Anita', 'SPESIALIS_PERAWAT' => 'Umum'],
            ['ID_PERAWAT' => 'P002', 'NAMA_PERAWAT' => 'Ns. Bambang', 'SPESIALIS_PERAWAT' => 'Anak'],
            ['ID_PERAWAT' => 'P003', 'NAMA_PERAWAT' => 'Ns. Citra', 'SPESIALIS_PERAWAT' => 'Bedah']
        ];

        foreach ($perawatData as $pr) {
            $this->db->table('perawat')->ignore()->insert($pr);
        }

        // 4. Seed Kamar
        $kamarData = [
            ['ID_KAMAR' => 'K001', 'NOMOR_KAMAR' => '101', 'TIPE_KAMAR' => 'VIP', 'STATUS' => 'Kosong', 'ID_PERIKSA' => null],
            ['ID_KAMAR' => 'K002', 'NOMOR_KAMAR' => '102', 'TIPE_KAMAR' => 'Kelas I', 'STATUS' => 'Kosong', 'ID_PERIKSA' => null],
            ['ID_KAMAR' => 'K003', 'NOMOR_KAMAR' => '103', 'TIPE_KAMAR' => 'Kelas II', 'STATUS' => 'Kosong', 'ID_PERIKSA' => null],
            ['ID_KAMAR' => 'K004', 'NOMOR_KAMAR' => '104', 'TIPE_KAMAR' => 'Kelas III', 'STATUS' => 'Kosong', 'ID_PERIKSA' => null]
        ];

        foreach ($kamarData as $k) {
            $this->db->table('kamar')->ignore()->insert($k);
        }

        // 5. Seed Pasien
        $pasienData = [
            [
                'ID_PASIEN' => 'RM001',
                'NAMA_PASIEN' => 'Ahmad Hidayat',
                'ALAMAT_PASIEN' => 'Jakarta Selatan',
                'TGL_LAHIR' => '1981-05-12 00:00:00',
                'JENIS_KELAMIN' => 'Laki-laki'
            ],
            [
                'ID_PASIEN' => 'RM002',
                'NAMA_PASIEN' => 'Sarah Amelia',
                'ALAMAT_PASIEN' => 'Depok Baru',
                'TGL_LAHIR' => '1998-09-24 00:00:00',
                'JENIS_KELAMIN' => 'Perempuan'
            ],
            [
                'ID_PASIEN' => 'RM003',
                'NAMA_PASIEN' => 'Budi Santoso',
                'ALAMAT_PASIEN' => 'Tangerang',
                'TGL_LAHIR' => '2016-11-03 00:00:00',
                'JENIS_KELAMIN' => 'Laki-laki'
            ]
        ];

        foreach ($pasienData as $ps) {
            $this->db->table('pasien')->ignore()->insert($ps);
        }

        // 6. Seed Users
        $usersData = [
            [
                'username' => 'admin',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'fullname' => 'Administrator',
                'role' => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'ID_DOKTER' => null
            ],
            [
                'username' => 'dr_budi',
                'password' => password_hash('dokter123', PASSWORD_DEFAULT),
                'fullname' => 'Dr. Budi Santoso',
                'role' => 'staff',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'ID_DOKTER' => 'D001'
            ],
            [
                'username' => 'dr_rina',
                'password' => password_hash('dokter123', PASSWORD_DEFAULT),
                'fullname' => 'Dr. Rina Wijaya',
                'role' => 'staff',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'ID_DOKTER' => 'D002'
            ],
            [
                'username' => 'dr_andi',
                'password' => password_hash('dokter123', PASSWORD_DEFAULT),
                'fullname' => 'Dr. Andi Pratama',
                'role' => 'staff',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'ID_DOKTER' => 'D003'
            ],
            [
                'username' => 'dr_siti',
                'password' => password_hash('dokter123', PASSWORD_DEFAULT),
                'fullname' => 'Dr. Siti Aminah',
                'role' => 'staff',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'ID_DOKTER' => 'D004'
            ],
            [
                'username' => 'dr_hendra',
                'password' => password_hash('dokter123', PASSWORD_DEFAULT),
                'fullname' => 'Dr. Hendra Wijaya',
                'role' => 'staff',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'ID_DOKTER' => 'D005'
            ]
        ];

        foreach ($usersData as $u) {
            // Check if user exists first to prevent duplicate username issues
            $exists = $this->db->table('users')->where('username', $u['username'])->countAllResults();
            if ($exists == 0) {
                $this->db->table('users')->insert($u);
            } else {
                $this->db->table('users')->where('username', $u['username'])->update($u);
            }
        }
    }
}
