<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use App\Models\Nagari;
use App\Models\Product;
use Livewire\WithPagination;

new #[Layout('components.layouts.app')] #[Title('Welcome')] class extends Component {
    use WithPagination;

    public $errorMsg = '';
    public $isLoading = true;

    public $statistics = [];
    public $facilities = [];
    public $korongs = [];
    public $umkm = [];
    public $nagariInfo = [];
    public $products = [];

    public function mount()
    {
        $nagari = Nagari::with('korongs.facilities', 'korongs.businesses')->first();

        if (!$nagari) {
            $this->errorMsg = 'No Nagari Found';
            $this->isLoading = false;
            return;
        }

        $this->nagariInfo = [
            'name' => $nagari->name,
            'address' => $nagari->address,
            'description' => $nagari->description,
            'established' => optional($nagari->established_date)->format('Y') ?? 'N/A',
        ];

        $totalPopulation = 0;
        $facilitiesGrouped = [];

        $this->products = Product::with('images')->paginate(5)->toArray()['data'];
        $businessCount = 0;
        foreach ($nagari->korongs as $korong) {
            $population = $korong->male_population + $korong->female_population;
            $totalPopulation += $population;

            $this->korongs[] = [
                'name' => $korong->name,
                'population' => $population,
                'wali_korong' => $korong->waliKorong->name ?? '-',
            ];

            foreach ($korong->facilities as $facility) {
                $type = $facility->type->value;

                if (!isset($facilitiesGrouped[$type])) {
                    $facilitiesGrouped[$type] = [
                        'name' => $facility->type_label,
                        'count' => 0,
                        'icon' => $facility->type->icon(),
                    ];
                }

                $facilitiesGrouped[$type]['count']++;
            }
            $businessCount += $korong->businesses->count();
            if ($korong->businesses->isNotEmpty()) {
                $business = $korong->businesses->random();
                $this->umkm[] = [
                    'name' => $business->name,
                    'category' => $business->business_type_label ?? $business->business_type,
                ];
            }
        }

        $this->facilities = array_values($facilitiesGrouped);

        $this->statistics = [['label' => 'Jumlah Populasi', 'value' => $totalPopulation, 'unit' => 'jiwa'], ['label' => 'Jumlah Korong', 'value' => count($this->korongs), 'unit' => 'korong'], ['label' => 'Jumlah UMKM', 'value' => $businessCount, 'unit' => 'usaha'], ['label' => 'Luas Wilayah', 'value' => $nagari->area_size ?? 0, 'unit' => 'km²']];

        $this->isLoading = false;
    }
};
?>

<div class="min-h-screen flex flex-col items-center justify-center p-4">

    <header class="text-center">
        <h1 class="text-5xl font-extrabold p-2">
            Nagari Toboh Gadang Timur
        </h1>
        <p class="mt-5 text-lg max-w-3xl mx-auto italic">
            Toboh Gadang Timur Adalah Sebuah Nagari di...
        </p>
    </header>

    <h2 class="text-2xl font-semibold text-center mt-4">
        Akses Cepat
    </h2>

    <div class="flex flex-col md:flex-row gap-4 mt-2">
        @auth
            <x-button label="Ke Dasbor" link="/dashboard" class="btn-primary" />
            <x-button label="Logout" link="/logout" class="btn-ghost" />
        @else
            <x-button label="Login" link="/login" class="btn-primary" />
            <x-button label="Daftar" link="/register" class="btn-secondary" />
        @endauth
    </div>

    <div class="max-w-7xl mx-auto px-4 py-12 space-y-16">

        <!-- Statistik -->
        <section>
            <h2 class="text-3xl font-bold mb-8">
                Statistik Nagari
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($statistics as $stat)
                    <x-card class="bg-gray-800/40 backdrop-blur border border-gray-700/50 shadow-xl rounded-3xl">
                        <div class="p-8 text-center">
                            <p class="text-sm">{{ $stat['label'] }}</p>
                            <p class="text-5xl font-black mt-2">
                                {{ $stat['value'] }}
                            </p>
                            <p class="text-sm mt-1">{{ $stat['unit'] }}</p>
                        </div>
                    </x-card>
                @endforeach
            </div>
        </section>

        <!-- Fasilitas -->
        <section>
            <h2 class="text-3xl font-bold mb-8">
                Fasilitas Nagari
            </h2>

            <x-card class="bg-gray-800/30 backdrop-blur border border-gray-700/40 rounded-3xl">
                <div class="p-8 space-y-6">
                    @foreach ($facilities as $facility)
                        <div
                            class="flex justify-between items-center p-6 bg-gray-900/40 rounded-2xl border border-gray-700/30">
                            <div class="flex items-center gap-4">
                                <div class="text-3xl">{{ $facility['icon'] }}</div>
                                <div>
                                    <p class="font-bold">{{ $facility['name'] }}</p>
                                    <p class="text-sm">Fasilitas</p>
                                </div>
                            </div>
                            <x-badge value="{{ $facility['count'] }} unit" />
                        </div>
                    @endforeach
                </div>
            </x-card>
        </section>

        <!-- Korong -->
        <section>
            <h2 class="text-3xl font-bold mb-8">
                Daftar Korong
            </h2>

            <div class="space-y-6">
                @foreach ($korongs as $index => $korong)
                    <x-card class="bg-gray-800/40 backdrop-blur border border-gray-700/50 rounded-3xl">
                        <div class="p-8">
                            <h3 class="text-2xl font-bold">
                                {{ $korong['name'] }}
                            </h3>
                            <p class="text-4xl font-black mt-2">
                                {{ number_format($korong['population']) }}
                                <span class="text-base">jiwa</span>
                            </p>
                            <p class="mt-4 text-sm">
                                Wali Korong: {{ $korong['wali_korong'] }}
                            </p>
                        </div>
                    </x-card>
                @endforeach
            </div>
        </section>

        <!-- UMKM -->
        <section>
            <h2 class="text-3xl font-bold mb-8">
                UMKM Unggulan
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($umkm as $item)
                    <x-card class="bg-gray-800/40 backdrop-blur border border-gray-700/50 rounded-3xl text-center">
                        <div class="p-8">
                            <h3 class="text-xl font-bold mb-2">
                                {{ $item['name'] }}
                            </h3>
                            <x-badge value="{{ $item['category'] }}" />
                        </div>
                    </x-card>
                @endforeach
            </div>
        </section>

    </div>
</div>
