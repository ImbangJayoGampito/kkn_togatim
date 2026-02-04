<?php

namespace App\Enums;

enum FacilityType: string
{
    case KESEHATAN = 'kesehatan';
    case PENDIDIKAN = 'pendidikan';
    case IBADAH = 'ibadah';
    case OLAHRAGA = 'olahraga';
    case PEMERINTAHAN = 'pemerintahan';
    case PASAR = 'pasar';
    case KEAMANAN = 'keamanan';
    case TRANSPORTASI = 'transportasi';
    case HIBURAN = 'hiburan';
    case UTILITAS = 'utilitas';
    case LAINNYA = 'lainnya';

    /**
     * Dapatkan semua nilai enum sebagai array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Dapatkan semua kasus enum sebagai array
     */
    public static function all(): array
    {
        return [
            self::KESEHATAN,
            self::PENDIDIKAN,
            self::IBADAH,
            self::OLAHRAGA,
            self::PEMERINTAHAN,
            self::PASAR,
            self::KEAMANAN,
            self::TRANSPORTASI,
            self::HIBURAN,
            self::UTILITAS,
            self::LAINNYA,
        ];
    }

    /**
     * Dapatkan label enum
     */
    public static function labels(): array
    {
        return [
            self::KESEHATAN->value => 'Kesehatan',
            self::PENDIDIKAN->value => 'Pendidikan',
            self::IBADAH->value => 'Ibadah',
            self::OLAHRAGA->value => 'Olahraga',
            self::PEMERINTAHAN->value => 'Pemerintahan',
            self::PASAR->value => 'Pasar',
            self::KEAMANAN->value => 'Keamanan',
            self::TRANSPORTASI->value => 'Transportasi',
            self::HIBURAN->value => 'Hiburan',
            self::UTILITAS->value => 'Utilitas',
            self::LAINNYA->value => 'Lainnya',
        ];
    }

    /**
     * Dapatkan label untuk jenis tertentu
     */
    public function label(): string
    {
        return self::labels()[$this->value] ?? $this->value;
    }

    /**
     * Dapatkan enum dengan contoh
     */
    public static function withExamples(): array
    {
        return [
            self::KESEHATAN->value => ['Puskesmas', 'Klinik', 'Posyandu', 'Apotek', 'Rumah Sakit'],
            self::PENDIDIKAN->value => ['SD', 'SMP', 'SMA', 'Madrasah', 'PAUD', 'TPA', 'Perguruan Tinggi'],
            self::IBADAH->value => ['Masjid', 'Musholla', 'Surau', 'Gereja', 'Pura', 'Vihara'],
            self::OLAHRAGA->value => ['Lapangan Sepak Bola', 'GOR', 'Lapangan Voli', 'Lapangan Bulutangkis', 'Kolam Renang'],
            self::PEMERINTAHAN->value => ['Kantor Korong', 'Balai Pertemuan', 'Kantor Nagari', 'Pos Pelayanan'],
            self::PASAR->value => ['Pasar Tradisional', 'Warung', 'Toko Kelontong', 'Minimarket'],
            self::KEAMANAN->value => ['Pos Kamling', 'Pos Polisi', 'Pos Keamanan'],
            self::TRANSPORTASI->value => ['Halte', 'Terminal', 'Stasiun', 'Jalan', 'Jembatan'],
            self::HIBURAN->value => ['Taman', 'Ruang Terbuka Hijau', 'Sanggar', 'Gedung Kesenian'],
            self::UTILITAS->value => ['Sumur', 'PDAM', 'Listrik', 'Drainase', 'Sampah'],
            self::LAINNYA->value => ['Fasilitas Lainnya'],
        ];
    }

    /**
     * Dapatkan contoh untuk jenis tertentu
     */
    public function examples(): array
    {
        return self::withExamples()[$this->value] ?? [];
    }

    /**
     * Validasi apakah nilai adalah jenis fasilitas yang valid
     */
    public static function isValid(string $value): bool
    {
        return in_array($value, self::values());
    }

    /**
     * Dapatkan deskripsi untuk jenis fasilitas
     */
    public function description(): string
    {
        return match ($this) {
            self::KESEHATAN => 'Fasilitas pelayanan kesehatan',
            self::PENDIDIKAN => 'Fasilitas pendidikan dan pembelajaran',
            self::IBADAH => 'Tempat ibadah dan keagamaan',
            self::OLAHRAGA => 'Fasilitas olahraga dan kebugaran',
            self::PEMERINTAHAN => 'Fasilitas pemerintahan dan pelayanan publik',
            self::PASAR => 'Fasilitas perdagangan dan perbelanjaan',
            self::KEAMANAN => 'Fasilitas keamanan dan ketertiban',
            self::TRANSPORTASI => 'Fasilitas transportasi dan perhubungan',
            self::HIBURAN => 'Fasilitas hiburan dan rekreasi',
            self::UTILITAS => 'Fasilitas utilitas dan pelayanan dasar',
            self::LAINNYA => 'Fasilitas lainnya',
        };
    }

    /**
     * Dapatkan icon untuk jenis fasilitas (untuk UI)
     */
    public function icon(): string
    {
        return match ($this) {
            self::KESEHATAN => '🏥',
            self::PENDIDIKAN => '🏫',
            self::IBADAH => '🕌',
            self::OLAHRAGA => '⚽',
            self::PEMERINTAHAN => '🏛️',
            self::PASAR => '🛒',
            self::KEAMANAN => '👮',
            self::TRANSPORTASI => '🚌',
            self::HIBURAN => '🎭',
            self::UTILITAS => '⚡',
            self::LAINNYA => '🏠',
        };
    }
}
