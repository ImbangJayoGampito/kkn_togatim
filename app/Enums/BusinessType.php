<?php

namespace App\Enums;

enum BusinessType: string
{
    case PARIWISATA = 'pariwisata';
    case TOKO = 'toko';
    case WARUNG = 'warung';
    case RESTORAN = 'restoran';
    case KAFE = 'kafe';
    case JASA = 'jasa';
    case INDUSTRI = 'industri';
    case PERTANIAN = 'pertanian';
    case PETERNAKAN = 'peternakan';
    case PERIKANAN = 'perikanan';
    case KERAJINAN = 'kerajinan';
    case FASHION = 'fashion';
    case ELEKTRONIK = 'elektronik';
    case OTOMOTIF = 'otomotif';
    case KONSTRUKSI = 'konstruksi';
    case TRANSPORTASI = 'transportasi';
    case LAINNYA = 'lainnya';

    /**
     * Dapatkan label untuk nilai enum.
     */
    public function label(): string
    {
        return match ($this) {
            self::TOKO => 'Toko',
            self::WARUNG => 'Warung',
            self::RESTORAN => 'Restoran',
            self::KAFE => 'Kafe',
            self::JASA => 'Jasa',
            self::INDUSTRI => 'Industri',
            self::PERTANIAN => 'Pertanian',
            self::PETERNAKAN => 'Peternakan',
            self::PERIKANAN => 'Perikanan',
            self::KERAJINAN => 'Kerajinan',
            self::FASHION => 'Fashion',
            self::ELEKTRONIK => 'Elektronik',
            self::OTOMOTIF => 'Otomotif',
            self::KONSTRUKSI => 'Konstruksi',
            self::TRANSPORTASI => 'Transportasi',
            self::PARIWISATA => 'Pariwisata',
            self::LAINNYA => 'Lainnya',
        };
    }

    /**
     * Dapatkan deskripsi untuk jenis usaha.
     */
    public function description(): string
    {
        return match ($this) {
            self::TOKO => 'Usaha perdagangan eceran barang kebutuhan sehari-hari',
            self::WARUNG => 'Usaha kecil makanan, minuman, atau sembako',
            self::RESTORAN => 'Usaha penyediaan makanan dan minuman',
            self::KAFE => 'Usaha kedai kopi dan minuman ringan',
            self::JASA => 'Usaha penyediaan jasa tertentu',
            self::INDUSTRI => 'Usaha pengolahan bahan menjadi barang jadi',
            self::PERTANIAN => 'Usaha di bidang pertanian dan perkebunan',
            self::PETERNAKAN => 'Usaha pemeliharaan hewan ternak',
            self::PERIKANAN => 'Usaha di bidang perikanan dan budidaya ikan',
            self::KERAJINAN => 'Usaha pembuatan barang kerajinan tangan',
            self::FASHION => 'Usaha di bidang pakaian dan mode',
            self::ELEKTRONIK => 'Usaha penjualan atau perbaikan alat elektronik',
            self::OTOMOTIF => 'Usaha di bidang kendaraan bermotor',
            self::KONSTRUKSI => 'Usaha jasa konstruksi bangunan',
            self::TRANSPORTASI => 'Usaha jasa angkutan dan transportasi',
            self::PARIWISATA => 'Usaha pariwisata',
            self::LAINNYA => 'Usaha lainnya yang tidak termasuk dalam kategori di atas',
        };
    }

    /**
     * Dapatkan icon untuk jenis usaha (untuk UI).
     */
    public function icon(): string
    {
        return match ($this) {
            self::TOKO => '🏪',
            self::WARUNG => '🍜',
            self::RESTORAN => '🍽️',
            self::KAFE => '☕',
            self::JASA => '🛠️',
            self::INDUSTRI => '🏭',
            self::PERTANIAN => '🌾',
            self::PETERNAKAN => '🐄',
            self::PERIKANAN => '🐟',
            self::KERAJINAN => '🎨',
            self::FASHION => '👕',
            self::ELEKTRONIK => '📱',
            self::OTOMOTIF => '🚗',
            self::KONSTRUKSI => '🏗️',
            self::TRANSPORTASI => '🚚',
            self::PARIWISATA => '🎡',
            self::LAINNYA => '🏢',
        };
    }

    /**
     * Dapatkan semua kasus enum.
     */
    public static function all(): array
    {
        return self::cases();
    }

    /**
     * Dapatkan semua nilai enum sebagai array.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Dapatkan semua label sebagai array.
     */
    public static function labels(): array
    {
        $labels = [];
        foreach (self::cases() as $case) {
            $labels[$case->value] = $case->label();
        }
        return $labels;
    }

    /**
     * Dapatkan semua deskripsi sebagai array.
     */
    public static function descriptions(): array
    {
        $descriptions = [];
        foreach (self::cases() as $case) {
            $descriptions[$case->value] = $case->description();
        }
        return $descriptions;
    }

    /**
     * Dapatkan contoh untuk setiap jenis usaha.
     */
    public function examples(): array
    {
        return match ($this) {
            self::TOKO => ['Toko Kelontong', 'Minimarket', 'Toko Bangunan', 'Toko Buku'],
            self::WARUNG => ['Warung Makan', 'Warung Kopi', 'Warung Sembako', 'Warung Nasi'],
            self::RESTORAN => ['Restoran Padang', 'Rumah Makan', 'Kedai Makan', 'Warung Padang'],
            self::KAFE => ['Kedai Kopi', 'Coffee Shop', 'Tea House', 'Kafe Modern'],
            self::JASA => ['Bengkel', 'Salon', 'Laundry', 'Fotokopi', 'Tempat Cuci Mobil'],
            self::INDUSTRI => ['Industri Rumahan', 'UKM', 'Home Industry', 'Industri Kecil'],
            self::PERTANIAN => ['Kebun Sayur', 'Sawah', 'Perkebunan', 'Budidaya Tanaman'],
            self::PETERNAKAN => ['Peternakan Ayam', 'Ternak Sapi', 'Ternak Kambing', 'Peternakan Bebek'],
            self::PERIKANAN => ['Tambak Ikan', 'Budidaya Udang', 'Kolam Ikan', 'Budidaya Lele'],
            self::KERAJINAN => ['Anyaman Bambu', 'Tenun Songket', 'Ukiran Kayu', 'Kerajinan Tangan'],
            self::FASHION => ['Toko Baju', 'Konveksi', 'Butik', 'Tempat Jahit'],
            self::ELEKTRONIK => ['Toko Elektronik', 'Service HP', 'Toko Listrik', 'Toko Alat Rumah Tangga'],
            self::OTOMOTIF => ['Bengkel Motor', 'Bengkel Mobil', 'Cuci Mobil', 'Toko Sparepart'],
            self::KONSTRUKSI => ['Kontraktor', 'Toko Bangunan', 'Jasa Renovasi', 'Tukang Bangunan'],
            self::TRANSPORTASI => ['Angkutan Desa', 'Ojek Online', 'Rental Mobil', 'Jasa Pengiriman'],
            self::PARIWISATA => ['Roller coaster'],
            self::LAINNYA => ['Usaha Lainnya', 'Bidang Usaha Tidak Tercantum'],
        };
    }

    /**
     * Dapatkan kategori utama untuk jenis usaha.
     */
    public function category(): string
    {
        return match ($this) {
            self::TOKO, self::WARUNG, self::RESTORAN, self::KAFE => 'Perdagangan & Kuliner',
            self::JASA, self::KONSTRUKSI, self::TRANSPORTASI, self::PARIWISATA => 'Jasa',
            self::INDUSTRI, self::KERAJINAN, self::FASHION => 'Produksi & Manufaktur',
            self::PERTANIAN, self::PETERNAKAN, self::PERIKANAN => 'Agribisnis',
            self::ELEKTRONIK, self::OTOMOTIF => 'Teknologi & Otomotif',
            self::LAINNYA => 'Lainnya',
        };
    }

    /**
     * Dapatkan semua jenis usaha berdasarkan kategori.
     */
    public static function groupedByCategory(): array
    {
        $grouped = [];
        foreach (self::cases() as $case) {
            $category = $case->category();
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][] = [
                'value' => $case->value,
                'label' => $case->label(),
                'icon' => $case->icon(),
            ];
        }
        return $grouped;
    }

    /**
     * Validasi apakah nilai adalah jenis usaha yang valid.
     */
    public static function isValid(string $value): bool
    {
        return in_array($value, self::values());
    }

    /**
     * Dapatkan jenis usaha dari string dengan fallback.
     */
    public static function tryFromString(string $value): ?self
    {
        return self::tryFrom($value);
    }
}
