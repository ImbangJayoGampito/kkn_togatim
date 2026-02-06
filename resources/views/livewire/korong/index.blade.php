<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use App\Models\Business;
use App\Models\Korong;
use App\Models\KorongFacility;
use App\Enums\BusinessType;
use App\Enums\FacilityType;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Nagari;

new #[Layout('components.layouts.app')] #[Title('Welcome')] class extends Component {
    use WithPagination;

    public $korong_data = [];
    public $currentImageIndexes = []; // Store current image index for each Korong

    public function mount()
    {
        $nagari = Nagari::with('korongs.facilities', 'korongs.businesses', 'korongs.images')->first();
        $korong_data = [];

        foreach ($nagari->korongs as $korong) {
            $korong_data[$korong->id] = [
                // Basic Info
                'id' => $korong->id,
                'name' => $korong->name,
                'address' => $korong->address,
                'phone' => $korong->phone,
                'email' => $korong->email,
                'description' => $korong->description,

                // Location
                'latitude' => $korong->latitude,
                'longitude' => $korong->longitude,
                'coordinates' => [
                    'lat' => $korong->latitude,
                    'lng' => $korong->longitude,
                ],

                // Population Data
                'population' => [
                    'total' => $korong->male_population + $korong->female_population,
                    'male' => $korong->male_population,
                    'female' => $korong->female_population,
                    'households' => $korong->total_households,
                ],

                // Area
                'area_size_km2' => $korong->area_size_km2,

                // Images
                'images' => $korong->images
                    ->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'url' => Storage::url($image->path),
                            'path' => $image->path,
                        ];
                    })
                    ->toArray(),

                // Facilities
                'facilities' => $korong->facilities
                    ->map(function ($facility) {
                        return [
                            'id' => $facility->id,
                            'name' => $facility->name,
                            'type' => $facility->type,
                            'description' => $facility->description,
                            'status' => $facility->status,
                        ];
                    })
                    ->toArray(),

                // Businesses
                'businesses' => $korong->businesses
                    ->map(function ($business) {
                        return [
                            'id' => $business->id,
                            'name' => $business->name,
                            'type' => $business->type,
                            'address' => $business->address,
                            'phone' => $business->phone,
                            'latitude' => $business->latitude,
                            'longitude' => $business->longitude,
                            'product_count' => $business->products->count(),
                            'products' => $business->products
                                ->take(5)
                                ->map(function ($product) {
                                    return [
                                        'id' => $product->id,
                                        'name' => $product->name,
                                        'price' => $product->price,
                                        'stock' => $product->stock,
                                    ];
                                })
                                ->toArray(),
                        ];
                    })
                    ->toArray(),

                // Business Statistics
                'business_stats' => [
                    'total' => $korong->businesses->count(),
                    'by_type' => $korong->businesses->groupBy('type')->map->count(),
                    'product_count' => $korong->businesses->sum(function ($business) {
                        return $business->products->count();
                    }),
                ],

                // Wali Korong Info
                'wali_korong' => $korong->waliKorong
                    ? [
                        'id' => $korong->waliKorong->id,
                        'name' => $korong->waliKorong->name,
                        'phone' => $korong->waliKorong->phone,
                        'email' => $korong->waliKorong->email,
                    ]
                    : null,

                // Nagari Info
                'nagari' => $korong->nagari
                    ? [
                        'id' => $korong->nagari->id,
                        'name' => $korong->nagari->name,
                    ]
                    : null,

                // Metadata
                'created_at' => $korong->created_at,
                'updated_at' => $korong->updated_at,
            ];

            // Initialize current image index for this Korong
            $this->currentImageIndexes[$korong->id] = 0;
        }

        $this->korong_data = $korong_data;
    }

    // Carousel methods for specific Korong
    public function previousImage($korongId)
    {
        $images = $this->korong_data[$korongId]['images'] ?? [];
        $currentIndex = $this->currentImageIndexes[$korongId] ?? 0;

        if (count($images) > 0) {
            if ($currentIndex > 0) {
                $this->currentImageIndexes[$korongId] = $currentIndex - 1;
            } else {
                $this->currentImageIndexes[$korongId] = count($images) - 1;
            }
        }
    }

    public function nextImage($korongId)
    {
        $images = $this->korong_data[$korongId]['images'] ?? [];
        $currentIndex = $this->currentImageIndexes[$korongId] ?? 0;

        if (count($images) > 0) {
            if ($currentIndex < count($images) - 1) {
                $this->currentImageIndexes[$korongId] = $currentIndex + 1;
            } else {
                $this->currentImageIndexes[$korongId] = 0;
            }
        }
    }

    public function goToImage($korongId, $index)
    {
        $images = $this->korong_data[$korongId]['images'] ?? [];
        if ($index >= 0 && $index < count($images)) {
            $this->currentImageIndexes[$korongId] = $index;
        }
    }
};
?>

<div class="min-h-screen flex flex-col items-center justify-center p-4">
    <h2 class="text-2xl font-semibold text-center mt-4">
        Akses Cepat:
    </h2>
    <div class="flex flex-col md:flex-row gap-4 mt-2">
        @auth
            <x-button label="Ke Dasbor" link="/dashboard" icon="o-chart-bar" class="btn-primary" />
            <x-button label="Logout" link="/logout" icon="o-arrow-right-on-rectangle" class="btn-ghost" />
        @else
            <x-button label="Login" link="/login" icon="o-arrow-right-on-rectangle" class="btn-primary" />
            <x-button label="Daftar" link="/register" icon="o-user-plus" class="btn-secondary" />
        @endauth
    </div>

    <div class="space-y-6 mt-6">
        @foreach ($korong_data as $korong)
            <x-card shadow class="overflow-hidden">
                <!-- Korong Header -->
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-xl font-bold">{{ $korong['name'] }}</h3>
                            <p class="mt-1">{{ $korong['address'] ?? '' }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold">
                                {{ number_format($korong['population']['total'] ?? 0) }} penduduk
                            </div>
                            <div class="text-sm">{{ $korong['population']['households'] ?? 0 }} KK</div>
                        </div>
                    </div>

                    <!-- Basic Info -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                        <div class="space-y-1">
                            <p class="text-sm">Koordinat</p>
                            <p class="font-medium">
                                {{ $korong['latitude'] ?? '' }}, {{ $korong['longitude'] ?? '' }}
                            </p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm">Luas Area</p>
                            <p class="font-medium">{{ $korong['area_size_km2'] ?? 0 }} km²</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm">Email</p>
                            <p class="font-medium">{{ $korong['email'] ?? '' }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm">Telepon</p>
                            <p class="font-medium">{{ $korong['phone'] ?? '' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Korong Images Carousel -->
                @if (!empty($korong['images']) && count($korong['images']) > 0)
                    <div class="border-t p-6">
                        <h4 class="font-semibold mb-4 flex items-center gap-2">
                            <x-icon name="o-photo" class="w-5 h-5" />
                            Gambar Korong
                        </h4>

                        <!-- Simple Full Image Carousel -->
                        <div class="relative">
                            <!-- Image Display -->
                            <div class="flex items-center justify-center w-full h-96 rounded-lg overflow-hidden ">
                                <img src="{{ asset($korong['images'][$currentImageIndexes[$korong['id']]]['path']) }}"
                                    alt="Korong Image {{ $currentImageIndexes[$korong['id']] + 1 }}"
                                    class="max-w-full max-h-full object-contain">
                            </div>

                            <!-- Navigation -->
                            @if (count($korong['images']) > 1)
                                <div class="flex justify-between items-center mt-4">
                                    <!-- Previous Button -->
                                    <x-button icon="o-chevron-left" class="btn-circle btn-sm"
                                        wire:click="previousImage({{ $korong['id'] }})" />

                                    <!-- Image Counter -->
                                    <div class="text-center">
                                        <x-badge class="px-4 py-1">
                                            {{ $currentImageIndexes[$korong['id']] + 1 }} /
                                            {{ count($korong['images']) }}
                                        </x-badge>
                                    </div>

                                    <!-- Next Button -->
                                    <x-button icon="o-chevron-right" class="btn-circle btn-sm"
                                        wire:click="nextImage({{ $korong['id'] }})" />
                                </div>

                                <!-- Dots Indicator -->
                                <div class="flex justify-center gap-2 mt-2">
                                    @foreach ($korong['images'] as $index => $image)
                                        <button wire:click="goToImage({{ $korong['id'] }}, {{ $index }})"
                                            class="w-3 h-3 rounded-full transition-all duration-300 {{ $index === $currentImageIndexes[$korong['id']] ? 'bg-primary' : 'bg-gray-300' }}">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- ACCORDION 1: Businesses -->
                <details class="border-t">
                    <summary class="cursor-pointer p-4  flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <x-icon name="o-building-storefront" class="w-5 h-5" />
                            <span class="font-semibold">UMKM & Bisnis</span>
                            <span class="badge">{{ count($korong['businesses'] ?? []) }}</span>
                        </div>
                        <x-icon name="o-chevron-down" class="w-5 h-5" />
                    </summary>

                    <div class="px-4 pb-4 pt-2">
                        @if (!empty($korong['businesses']) && count($korong['businesses']) > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach ($korong['businesses'] as $business)
                                    <div class="border rounded-lg p-4 ">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <h5 class="font-semibold">{{ $business['name'] ?? 'N/A' }}</h5>
                                                @if (isset($business['type']))
                                                    <p class="text-sm">
                                                        {{ is_object($business['type']) ? $business['type']->value ?? '' : $business['type'] }}
                                                    </p>
                                                @endif
                                            </div>
                                            <span class="badge">{{ $business['product_count'] ?? 0 }} produk</span>
                                        </div>
                                        <p class="text-sm mb-2">{{ $business['address'] ?? '' }}</p>
                                        <div class="flex justify-between items-center text-sm">
                                            <span>{{ $business['phone'] ?? '' }}</span>
                                            <x-button label="Lihat"
                                                link="{{ route('business.show', [$business['id']]) }}"
                                                class="btn-ghost btn-sm" />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <x-icon name="o-building-storefront" class="w-12 h-12 mx-auto mb-3" />
                                <p>Belum ada UMKM terdaftar</p>
                            </div>
                        @endif
                    </div>
                </details>

                <!-- ACCORDION 2: Facilities -->
                <details class="border-t">
                    <summary class="cursor-pointer p-4  flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <x-icon name="o-building-office" class="w-5 h-5" />
                            <span class="font-semibold">Fasilitas Publik</span>
                            <span class="badge">{{ count($korong['facilities'] ?? []) }}</span>
                        </div>
                        <x-icon name="o-chevron-down" class="w-5 h-5" />
                    </summary>

                    <div class="px-4 pb-4 pt-2">
                        @if (!empty($korong['facilities']) && count($korong['facilities']) > 0)
                            <div class="space-y-3">
                                @foreach ($korong['facilities'] as $facility)
                                    <div class="flex items-center justify-between p-3 border rounded-lg ">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10  rounded-lg flex items-center justify-center">
                                                @php
                                                    $type = is_object($facility['type'] ?? null)
                                                        ? $facility['type']->value ?? ''
                                                        : $facility['type'] ?? '';
                                                    $icon = match (strtolower($type)) {
                                                        'masjid' => 'o-building-library',
                                                        'sekolah', 'sd' => 'o-academic-cap',
                                                        'puskesmas', 'kesehatan' => 'o-heart',
                                                        'posyandu' => 'o-user-group',
                                                        default => 'o-building-office',
                                                    };
                                                @endphp
                                                <x-icon name="{{ $icon }}" class="w-5 h-5 text-gray-600" />
                                            </div>
                                            <div>
                                                <h6 class="font-medium">{{ $facility['name'] ?? 'N/A' }}</h6>
                                                <p class="text-sm">{{ $type }}</p>
                                            </div>
                                        </div>
                                        <span
                                            class="badge {{ $facility['status'] === 'aktif' ? 'badge-success' : 'badge-warning' }}">
                                            {{ $facility['status'] ?? 'tidak aktif' }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <x-icon name="o-building-office" class="w-12 h-12 mx-auto mb-3" />
                                <p>Belum ada fasilitas terdaftar</p>
                            </div>
                        @endif
                    </div>
                </details>

                <!-- Wali Korong Info -->
                @if (!empty($korong['wali_korong']) || !empty($korong['nagari']))
                    <div class="border-t p-4 ">
                        <div class="flex flex-wrap gap-6">
                            @if (!empty($korong['wali_korong']))
                                <div>
                                    <p class="text-sm">Wali Korong</p>
                                    <p class="font-medium">{{ $korong['wali_korong']['name'] ?? '' }}</p>
                                    <p class="text-sm">{{ $korong['wali_korong']['phone'] ?? '' }}</p>
                                </div>
                            @endif
                            @if (!empty($korong['nagari']))
                                <div>
                                    <p class="text-sm">Nagari</p>
                                    <p class="font-medium">{{ $korong['nagari']['name'] ?? '' }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Edit Button for Admin -->
                @if (auth()->check() && auth()->user()->hasRole('admin'))
                    <div class="border-t p-4">
                        <x-button label="Edit Korong" link="{{ route('korong.edit', [$korong['id']]) }}"
                            class="btn-primary w-full" icon="o-map-pin" />
                    </div>
                @endif
            </x-card>
        @endforeach
    </div>
</div>
