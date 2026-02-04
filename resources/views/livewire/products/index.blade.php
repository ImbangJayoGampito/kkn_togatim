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
    public $searchProductQuery = '';
    public $searchProductResults = null;
    public $searchProductType = null;
    public $searchProductMinPrice = null;
    public $searchProductMaxPrice = null;
    public $searchProductSortBy = 'newest';

    public $yourProduct = false;

    public function clearFilters()
    {
        $this->reset(['searchProductQuery', 'searchProductType', 'searchProductMinPrice', 'searchProductMaxPrice', 'searchProductSortBy']);

        $this->search();
    }
    public function sortChanged()
    {
        $this->search();
    }
    public $businessTypes = [];
    function viewAllProducts()
    {
        $this->search();
        $this->yourProduct = false;
    }
    function viewYourProducts()
    {
        $this->search(true);
        $this->yourProduct = true;
    }
    public function search(bool $yourProduct = false)
    {
        $query = Product::query();
        if ($yourProduct) {
            $query->where('user_id', auth()->user()->id);
        }
        $query->join('businesses', 'products.business_id', '=', 'businesses.id')->select([
            'products.*',
            'products.name as product_name', // Alias for product name
            'businesses.name as business_name', // Alias for business name
            'businesses.user_id',
        ]);
        if ($this->searchProductQuery != '') {
            $query->where(function ($q) {
                $q->where('products.name', 'like', '%' . $this->searchProductQuery . '%')
                    ->orWhere('products.description', 'like', '%' . $this->searchProductQuery . '%')
                    ->orWhere('businesses.name', 'like', '%' . $this->searchProductQuery . '%');
            });
        }
        if ($this->searchProductType) {
            // Join with businesses table and filter by business type
            $query->where('businesses.type', $this->searchProductType);
        }
        // Price filters - FIXED: Check for null, not falsy
        if ($this->searchProductMinPrice !== null) {
            $query->where('price', '>=', (int) $this->searchProductMinPrice);
        }

        if ($this->searchProductMaxPrice !== null) {
            $query->where('price', '<=', (int) $this->searchProductMaxPrice);
        }

        match ($this->searchProductSortBy) {
            'price_low' => $query->orderBy('products.price', 'asc'),
            'price_high' => $query->orderBy('products.price', 'desc'),
            'name_asc' => $query->orderBy('products.name', 'asc'), // ← you had this option in dropdown
            default => $query->orderBy('products.created_at', 'desc'), // newest + fallback
        };

        $this->searchProductResults = $query->limit(100)->get();
    }
    public function viewAllProduct()
    {
        $this->search();
        $this->yourProduct = false;
    }
    public function viewMyProduct()
    {
        $user = auth()->user();
        $this->search();
        $this->searchProductResults = $this->searchProductResults->where('business.user_id', $user->id);
        $this->yourProduct = true;
    }
    public function hasActiveFilters()
    {
        return $this->searchProductQuery != '' || $this->searchProductType !== null || $this->searchProductMinPrice !== null || $this->searchProductMaxPrice !== null;
    }
    public function updatedSearchProductQuery($value)
    {
        $this->search();
    }

    public function updatedSearchProductType()
    {
        $this->search();
    }

    public function updatedSearchProductMinPrice()
    {
        $this->search();
    }

    public function updatedSearchProductMaxPrice()
    {
        $this->search();
    }
    public function mount()
    {
        $values = BusinessType::values(); // Your array

        $this->businessTypes = array_map(function ($value) {
            return [
                'value' => $value,
                'label' => ucfirst($value), // Capitalize first letter
            ];
        }, $values);
        $this->search();
    }
    public function viewProduct($id = null)
    {
        if ($id) {
            return redirect()->route('products.show', ['id' => $id]);
        } else {
            return redirect()->route('products.index');
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

    <x-card title="Cari Produk" shadow separator>
        <!-- Search Header -->
        <x-slot:menu>
            @if ($this->hasActiveFilters())
                <x-button label="Reset semua filter" wire:click="clearFilters" icon="o-x-mark" class="btn-ghost btn-sm"
                    spinner />
            @endif
        </x-slot:menu>

        <!-- Search Form -->
        <form wire:submit.prevent="search" class="space-y-6">


            @if ($user = auth()->user())
                @if (!$this->yourProduct)
                    <x-button label="Lihat produk Anda" wire:click="viewYourProducts" icon="o-list-bullet"
                        class="btn-ghost" />
                @else
                    <x-button label="Lihat produk" wire:click="viewAllProducts" icon="o-list-bullet"
                        class="btn-primary" />
                @endif
            @endif
            <!-- Main Search -->
            <x-input label="Cari produk" placeholder="Nama produk, deskripsi, atau kata kunci..."
                wire:model.live.debounce.500ms="searchProductQuery" icon="o-magnifying-glass" class="w-full" />

            <!-- Filters Row -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Business Type -->
                <x-select label="Jenis UMKM" :options="$businessTypes" wire:model.live="searchProductType" option-label="label"
                    option-value="value" placeholder="Semua jenis" icon="o-building-storefront" class="w-full" />

                <!-- Min Price -->
                <x-input label="Harga minimal" type="number" wire:model.live.debounce.800ms="searchProductMinPrice"
                    placeholder="0" icon="o-arrow-trending-down" prefix="Rp" suffix=",00" min="0" />

                <!-- Max Price -->
                <x-input label="Harga maksimal" type="number" wire:model.live.debounce.800ms="searchProductMaxPrice"
                    placeholder="1000000" icon="o-arrow-trending-up" prefix="Rp" suffix=",00" min="0" />
            </div>

            <!-- Active Filters Chips -->
            @if ($this->hasActiveFilters())
                <div class="flex flex-wrap gap-2 pt-4 border-t">
                    <p class="w-full text-sm text-gray-500 mb-2">Filter aktif:</p>

                    @if ($searchProductQuery)
                        <x-badge value="{{ $searchProductQuery }}" class="gap-1">
                            <x-slot:icon>
                                <x-icon name="o-magnifying-glass" class="w-3 h-3" />
                            </x-slot:icon>
                            <x-slot:append>
                                <x-button icon="o-x-mark" class="btn-ghost btn-xs"
                                    wire:click="$set('searchProductQuery', '')" />
                            </x-slot:append>
                        </x-badge>
                    @endif

                    @if ($searchProductType && $searchProductType !== \App\Enums\BusinessType::LAINNYA->value)
                        <x-badge outline value="{{ \App\Enums\BusinessType::from($searchProductType)->value }}"
                            class="gap-1">
                            <x-slot:icon>
                                <x-icon name="o-tag" class="w-3 h-3" />
                            </x-slot:icon>
                            <x-slot:append>
                                <x-button icon="o-x-mark" class="btn-ghost btn-xs"
                                    wire:click="$set('searchProductType', \App\Enums\BusinessType::LAINNYA->value)" />
                            </x-slot:append>
                        </x-badge>
                    @endif

                    @if ($searchProductMinPrice !== null)
                        <x-badge outline value="Min: Rp {{ number_format($searchProductMinPrice, 0, ',', '.') }}"
                            class="gap-1">
                            <x-slot:append>
                                <x-button icon="o-x-mark" class="btn-ghost btn-xs"
                                    wire:click="$set('searchProductMinPrice', null)" />
                            </x-slot:append>
                        </x-badge>
                    @endif

                    @if ($searchProductMaxPrice !== null)
                        <x-badge outline value="Max: Rp {{ number_format($searchProductMaxPrice, 0, ',', '.') }}"
                            class="gap-1">
                            <x-slot:append>
                                <x-button icon="o-x-mark" class="btn-ghost btn-xs"
                                    wire:click="$set('searchProductMaxPrice', null)" />
                            </x-slot:append>
                        </x-badge>
                    @endif
                </div>
            @endif

            <!-- Sort Options -->
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 pt-4 border-t">
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium">Urutkan:</span>

                    <x-select :options="[
                        ['id' => 'newest', 'name' => 'Terbaru'],
                        ['id' => 'price_low', 'name' => 'Harga: Rendah ke Tinggi'],
                        ['id' => 'price_high', 'name' => 'Harga: Tinggi ke Rendah'],
                        ['id' => 'name_asc', 'name' => 'Nama: A-Z'],
                    ]" option-value="id" option-label="name" class="w-48"
                        wire:model.live="searchProductSortBy" wire:change="sortChanged" />
                </div>

                <div class="flex items-center gap-2">
                    <x-icon name="o-information-circle" class="w-5 h-5 text-gray-400" />
                    <span class="text-sm text-gray-500">
                        {{ $searchProductResults->count() }} produk ditemukan
                    </span>
                </div>
            </div>
        </form>
    </x-card>

    <!-- Results Section -->
    @if ($searchProductResults->isNotEmpty())
        <x-card title="Hasil Pencarian" shadow class="mt-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($searchProductResults as $product)
                    <x-card class="overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <!-- Product Image -->
                        <div class="relative h-48 overflow-hidden">
                            @if ($product->images->isNotEmpty())
                                <img src="{{ asset($product->images->first()->path) }}" alt="{{ $product->name }}"
                                    class="w-full h-full object-cover hover:scale-110 transition-transform duration-500" />
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-base-200">
                                    <x-icon name="o-shopping-bag" class="w-16 h-16" />
                                </div>
                            @endif
                            @if ($product->business_type)
                                <div class="absolute top-2 right-2">
                                    <x-badge class="badge-sm">
                                        {{ \App\Enums\BusinessType::tryFrom($product->description)?->label ?? '-' }}
                                    </x-badge>
                                </div>
                            @endif
                        </div>

                        <!-- Product Info -->
                        <div class="p-4">
                            <h4 class="font-bold  line-clamp-2 mb-2">{{ $product->name }}</h4>

                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <p class="text-primary font-bold text-lg">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ Str::limit($product->description ?? '', 100, '...') }}
                                    </p>
                                </div>


                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <x-button label="Detail" wire:click="viewProduct({{ $product->id }})"
                                    class="btn-ghost btn-sm flex-1" icon="o-eye" />

                            </div>
                        </div>
                    </x-card>
                @endforeach
            </div>

            <!-- Pagination -->
            @if ($searchProductResults instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="mt-8 border-t pt-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            Menampilkan {{ $searchProductResults->firstItem() }} -
                            {{ $searchProductResults->lastItem() }}
                            dari {{ $searchProductResults->total() }} produk
                        </div>
                        <div class="join">
                            @if ($searchProductResults->onFirstPage())
                                <button class="join-item btn btn-disabled" disabled>
                                    <x-icon name="o-chevron-left" class="w-4 h-4" />
                                </button>
                            @else
                                <button wire:click="previousPage" class="join-item btn">
                                    <x-icon name="o-chevron-left" class="w-4 h-4" />
                                </button>
                            @endif

                            @foreach ($searchProductResults->getUrlRange(1, $searchProductResults->lastPage()) as $page => $url)
                                @if ($page == $searchProductResults->currentPage())
                                    <button class="join-item btn btn-active">{{ $page }}</button>
                                @else
                                    <button wire:click="gotoPage({{ $page }})"
                                        class="join-item btn">{{ $page }}</button>
                                @endif
                            @endforeach

                            @if ($searchProductResults->hasMorePages())
                                <button wire:click="nextPage" class="join-item btn">
                                    <x-icon name="o-chevron-right" class="w-4 h-4" />
                                </button>
                            @else
                                <button class="join-item btn btn-disabled" disabled>
                                    <x-icon name="o-chevron-right" class="w-4 h-4" />
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </x-card>
    @elseif($this->hasActiveFilters())
        <x-card title="Hasil Pencarian" shadow class="mt-6">
            <div class="text-center py-12">
                <x-icon name="o-magnifying-glass" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                <h3 class="text-lg font-medium text-gray-700 mb-2">Tidak ada produk yang ditemukan</h3>
                <p class="text-gray-500 mb-6">Coba ubah filter pencarian atau kata kunci Anda</p>
                <div class="flex gap-3 justify-center">
                    <x-button label="Reset semua filter" wire:click="clearFilters" icon="o-x-mark"
                        class="btn-primary" />
                    <x-button label="Lihat semua produk" wire:click="viewAllProducts" icon="o-list-bullet"
                        class="btn-ghost" />
                </div>
            </div>
        </x-card>
    @endif
</div>
