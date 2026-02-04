<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use App\Models\Business;
use App\Models\Korong;
use App\Enums\BusinessType;
use Livewire\WithPagination;

new #[Layout('components.layouts.app')] #[Title('Cari UMKM')] class extends Component {
    use WithPagination;

    public $searchBusinessQuery = '';
    public $searchBusinessResults = null;
    public $searchBusinessType = null;
    public $searchBusinessSortBy = 'newest';
    public $yourBusiness = false;
    public $businessTypes = [];

    public function clearFilters()
    {
        $this->reset(['searchBusinessQuery', 'searchBusinessType', 'searchBusinessSortBy']);
        $this->search();
    }

    public function sortChanged()
    {
        $this->search();
    }

    public function viewAllBusinesses()
    {
        $this->search();
        $this->yourBusiness = false;
    }

    public function viewYourBusinesses()
    {
        $this->search(true);
        $this->yourBusiness = true;
    }

    public function search(bool $yourBusiness = false)
    {
        $query = Business::query()
            ->with(['user', 'korong', 'images'])
            ->withCount('products');

        if ($yourBusiness) {
            $query->where('user_id', auth()->id());
        }

        if ($this->searchBusinessQuery != '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->searchBusinessQuery . '%')
                    ->orWhere('address', 'like', '%' . $this->searchBusinessQuery . '%')
                    ->orWhere('phone', 'like', '%' . $this->searchBusinessQuery . '%');
            });
        }

        if ($this->searchBusinessType) {
            $query->where('type', $this->searchBusinessType);
        }

        match ($this->searchBusinessSortBy) {
            'name_asc' => $query->orderBy('name', 'asc'),
            'products_desc' => $query->orderBy('products_count', 'desc'),
            'revenue_desc' => $query->orderBy('total_revenue', 'desc'),
            default => $query->orderBy('created_at', 'desc'), // newest
        };

        $this->searchBusinessResults = $query->limit(40)->get();
    }

    public function hasActiveFilters()
    {
        return $this->searchBusinessQuery != '' || $this->searchBusinessType !== null;
    }

    public function updatedSearchBusinessQuery($value)
    {
        $this->search();
    }

    public function updatedSearchBusinessType()
    {
        $this->search();
    }

    public function mount()
    {
        $values = BusinessType::values();
        $this->businessTypes = array_map(function ($value) {
            return [
                'value' => $value,
                'label' => ucfirst($value),
            ];
        }, $values);
        $this->search();
    }

    public function viewBusiness($id = null)
    {
        if ($id) {
            return redirect()->route('business.show', ['id' => $id]);
        } else {
            return redirect()->route('business.index');
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

    <x-card title="Cari UMKM" shadow separator>
        <!-- Search Header -->
        <x-slot:menu>
            @if ($this->hasActiveFilters())
                <x-button label="Reset semua filter" wire:click="clearFilters" icon="o-x-mark" class="btn-ghost btn-sm"
                    spinner />
            @endif
        </x-slot:menu>

        <!-- Search Form -->
        <form wire:submit.prevent="search" class="space-y-6">
            @if (auth()->user() && !$this->yourBusiness)
                <x-button label="Lihat UMKM Anda" wire:click="viewYourBusinesses" icon="o-building-storefront"
                    class="btn-ghost" />
            @elseif (auth()->user())
                <x-button label="Lihat semua UMKM" wire:click="viewAllBusinesses" icon="o-building-storefront"
                    class="btn-primary" />
            @endif

            <!-- Main Search -->
            <x-input label="Cari UMKM" placeholder="Nama UMKM, alamat, atau kata kunci..."
                wire:model.live.debounce.500ms="searchBusinessQuery" icon="o-magnifying-glass" class="w-full" />

            <!-- Filters Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Business Type -->
                <x-select label="Jenis UMKM" :options="$businessTypes" wire:model.live="searchBusinessType" option-label="label"
                    option-value="value" placeholder="Semua jenis" icon="o-building-storefront" class="w-full" />

                <!-- Sort Options -->
                <x-select label="Urutkan" :options="[
                    ['id' => 'newest', 'name' => 'Terbaru'],
                    ['id' => 'name_asc', 'name' => 'Nama: A-Z'],
                    ['id' => 'products_desc', 'name' => 'Produk terbanyak'],
                    ['id' => 'revenue_desc', 'name' => 'Pendapatan tertinggi'],
                ]" wire:model.live="searchBusinessSortBy" option-value="id"
                    option-label="name" />
            </div>

            <!-- Active Filters Chips -->
            @if ($this->hasActiveFilters())
                <div class="flex flex-wrap gap-2 pt-4 border-t">
                    <p class="w-full text-sm text-gray-500 mb-2">Filter aktif:</p>

                    @if ($searchBusinessQuery)
                        <x-badge value="{{ $searchBusinessQuery }}" class="gap-1">
                            <x-slot:icon>
                                <x-icon name="o-magnifying-glass" class="w-3 h-3" />
                            </x-slot:icon>
                            <x-slot:append>
                                <x-button icon="o-x-mark" class="btn-ghost btn-xs"
                                    wire:click="$set('searchBusinessQuery', '')" />
                            </x-slot:append>
                        </x-badge>
                    @endif

                    @if ($searchBusinessType)
                        <x-badge outline value="{{ ucfirst($searchBusinessType) }}" class="gap-1">
                            <x-slot:icon>
                                <x-icon name="o-tag" class="w-3 h-3" />
                            </x-slot:icon>
                            <x-slot:append>
                                <x-button icon="o-x-mark" class="btn-ghost btn-xs"
                                    wire:click="$set('searchBusinessType', null)" />
                            </x-slot:append>
                        </x-badge>
                    @endif
                </div>
            @endif

            <!-- Results Count -->
            <div class="flex items-center gap-2 pt-4 border-t">
                <x-icon name="o-information-circle" class="w-5 h-5 text-gray-400" />
                <span class="text-sm text-gray-500">
                    {{ $searchBusinessResults->count() }} UMKM ditemukan
                </span>
            </div>
        </form>
    </x-card>

    <!-- Results Section -->
    @if ($searchBusinessResults->isNotEmpty())
        <x-card title="Hasil Pencarian" shadow class="w-full max-w-6xl mt-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($searchBusinessResults as $business)
                    <x-card class="overflow-hidden hover:shadow-lg transition-shadow duration-300 cursor-pointer">
                        <!-- Business Image -->
                        <div class="relative h-48 overflow-hidden">
                            @if ($business->images->isNotEmpty())
                                <img src="{{ Storage::url($business->images->first()->path) }}"
                                    alt="{{ $business->name }}"
                                    class="w-full h-full object-cover hover:scale-110 transition-transform duration-500" />
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-base-200">
                                    <x-icon name="o-building-storefront" class="w-16 h-16" />
                                </div>
                            @endif
                            <div class="absolute top-2 right-2">
                                <x-badge class="badge-sm">{{ $business->type_label }}</x-badge>
                            </div>
                        </div>

                        <!-- Business Info -->
                        <div class="p-4">
                            <h4 class="font-bold line-clamp-1 mb-1">{{ $business->name }}</h4>

                            <p class="text-sm line-clamp-2 mb-3">{{ $business->address }}</p>

                            <div class="flex justify-between items-end">
                                <!-- Your main content on the left -->
                                <div>
                                    <!-- Your other stats/content -->
                                </div>

                                <!-- Korong info on the right edge -->
                                <div class="flex flex-col text-right">
                                    <span class="text-xs text-gray-500">Korong</span>
                                    <span class="text-sm font-medium">{{ $business->korong->name ?? 'N/A' }}</span>
                                </div>
                            </div>

                            <!-- Owner Info -->
                            <div class="flex items-center gap-2 border-t pt-3">
                                <x-avatar :image="$business->user->profile_photo_url ?? null" :initials="substr($business->user->name, 0, 2)" class="w-8 h-8" />
                                <div class="flex-1">
                                    <p class="text-xs font-medium">{{ $business->user->name }}</p>
                                    <p class="text-xs">{{ $business->phone }}</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <x-button label="Detail" wire:click="viewBusiness({{ $business->id }})"
                                    class="btn-ghost btn-sm flex-1" icon="o-eye" />

                            </div>
                        </div>
                    </x-card>
                @endforeach
            </div>

            <!-- Show total count -->
            <div class="mt-6 pt-4 border-t text-center text-sm text-gray-500">
                Menampilkan {{ $searchBusinessResults->count() }} UMKM
            </div>
        </x-card>
    @elseif($this->hasActiveFilters())
        <x-card title="Hasil Pencarian" shadow class="w-full max-w-6xl mt-6">
            <div class="text-center py-12">
                <x-icon name="o-magnifying-glass" class="w-16 h-16 mx-auto mb-4" />
                <h3 class="text-lg font-medium mb-2">Tidak ada UMKM yang ditemukan</h3>
                <p class="mb-6">Coba ubah filter pencarian atau kata kunci Anda</p>
                <div class="flex gap-3 justify-center">
                    <x-button label="Reset semua filter" wire:click="clearFilters" icon="o-x-mark"
                        class="btn-primary" />
                    <x-button label="Lihat semua UMKM" wire:click="viewAllBusinesses" icon="o-building-storefront"
                        class="btn-ghost" />
                </div>
            </div>
        </x-card>
    @endif
</div>
