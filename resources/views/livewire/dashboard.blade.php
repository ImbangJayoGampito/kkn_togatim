<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Business;
use App\Models\Product;
use App\Models\ProductTransaction;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Storage;

new #[Layout('components.layouts.app')] #[Title('Dashboard')] class extends Component {
    use WithPagination, Toast;

    public $search = '';
    public $viewMode = 'businesses';
    public $perPage = 10;

    // Switch view mode
    public function switchView($mode)
    {
        $this->viewMode = $mode;
        $this->resetPage();
    }

    // Get ALL businesses owned by current user
    public function getAllBusinessesProperty()
    {
        return Business::where('user_id', auth()->id())
            ->with(['korong', 'products'])
            ->withCount('products')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')->orWhere('address', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }

    // Get ALL products from ALL businesses owned by current user
    public function getAllProductsProperty()
    {
        return Product::whereHas('business', function ($query) {
            $query->where('user_id', auth()->id());
        })
            ->with(['business', 'images'])
            ->when($this->search, function ($query) {
                $query
                    ->where('products.name', 'like', '%' . $this->search . '%')
                    ->orWhere('products.description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('business', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }

    // Get recent transactions for ALL products
    public function getRecentTransactionsProperty()
    {
        return ProductTransaction::whereHas('product.business', function ($query) {
            $query->where('user_id', auth()->id());
        })
            ->with(['product', 'user'])
            ->latest()
            ->limit(10)
            ->get();
    }

    // Get comprehensive statistics
    public function getStatsProperty()
    {
        $userId = auth()->id();
        $businessIds = Business::where('user_id', $userId)->pluck('id');

        if ($businessIds->isEmpty()) {
            return [
                'total_businesses' => 0,
                'total_products' => 0,
                'total_product_value' => 0,
                'total_transactions' => 0,
                'low_stock_products' => 0,
                'out_of_stock' => 0,
            ];
        }

        $totalProductValue = Product::whereIn('business_id', $businessIds)->selectRaw('COALESCE(SUM(price * stock), 0) as total_value')->first()->total_value;

        return [
            'total_businesses' => $businessIds->count(),
            'total_products' => Product::whereIn('business_id', $businessIds)->count(),
            'total_product_value' => $totalProductValue,
            'total_transactions' => ProductTransaction::whereHas('product', function ($query) use ($businessIds) {
                $query->whereIn('business_id', $businessIds);
            })->count(),
            'low_stock_products' => Product::whereIn('business_id', $businessIds)->where('stock', '<', 10)->where('stock', '>', 0)->count(),
            'out_of_stock' => Product::whereIn('business_id', $businessIds)->where('stock', 0)->count(),
        ];
    }

    // Navigation methods
    public function viewBusiness($businessId)
    {
        return redirect()->route('business.show', ['id' => (int) $businessId]);
    }

    public function addProductToBusiness($businessId)
    {
        return redirect()->route('products.add', ['id' => (int) $businessId]);
    }

    public function viewProduct($productId)
    {
        return redirect()->route('products.show', ['id' => (int) $productId]);
    }

    public function editProduct($productId)
    {
        return redirect()->route('products.edit', ['id' => (int) $productId]);
    }
};
?>

<div>
    <x-header title="Dashboard" separator progress-indicator>
        <x-slot:actions>
            @if (auth()->user()->hasRole('admin'))
                <x-button label="Tambah UMKM" link="{{ route('business.create') }}" class="btn-primary"
                    icon="o-building-storefront" />
            @endif
        </x-slot:actions>
    </x-header>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
        <x-card class="bg-base-100 shadow">
            <div class="text-center">
                <p class="text-sm text-gray-500">Total UMKM</p>
                <p class="text-2xl font-bold">{{ $this->getStatsProperty()['total_businesses'] }}</p>
            </div>
        </x-card>

        <x-card class="bg-base-100 shadow">
            <div class="text-center">
                <p class="text-sm text-gray-500">Total Produk</p>
                <p class="text-2xl font-bold">{{ $this->getStatsProperty()['total_products'] }}</p>
            </div>
        </x-card>

        <x-card class="bg-base-100 shadow">
            <div class="text-center">
                <p class="text-sm text-gray-500">Nilai Produk</p>
                <p class="text-2xl font-bold">Rp
                    {{ number_format($this->getStatsProperty()['total_product_value'], 0, ',', '.') }}</p>
            </div>
        </x-card>

        <x-card
            class="bg-base-100 shadow {{ $this->getStatsProperty()['low_stock_products'] > 0 ? 'bg-warning/10 border-warning' : '' }}">
            <div class="text-center">
                <p class="text-sm text-gray-500">Stok Rendah</p>
                <p class="text-2xl font-bold">{{ $this->getStatsProperty()['low_stock_products'] }}</p>
            </div>
        </x-card>

        <x-card
            class="bg-base-100 shadow {{ $this->getStatsProperty()['out_of_stock'] > 0 ? 'bg-error/10 border-error' : '' }}">
            <div class="text-center">
                <p class="text-sm text-gray-500">Habis</p>
                <p class="text-2xl font-bold">{{ $this->getStatsProperty()['out_of_stock'] }}</p>
            </div>
        </x-card>

        <x-card class="bg-base-100 shadow">
            <div class="text-center">
                <p class="text-sm text-gray-500">Transaksi</p>
                <p class="text-2xl font-bold">{{ $this->getStatsProperty()['total_transactions'] }}</p>
            </div>
        </x-card>
    </div>

    <!-- Search and View Toggle -->
    <div class="flex flex-col md:flex-row gap-4 mb-6">
        <div class="flex-1">
            <x-input label="Cari semua UMKM & produk" wire:model.live.debounce.500ms="search"
                placeholder="Ketik nama UMKM, produk, atau alamat..." icon="o-magnifying-glass" />
        </div>
        <div class="flex gap-2">
            <x-button label="Tampilkan UMKM" wire:click="switchView('businesses')"
                class="{{ $viewMode == 'businesses' ? 'btn-primary' : 'btn-ghost' }}" icon="o-building-storefront" />
            <x-button label="Tampilkan Produk" wire:click="switchView('products')"
                class="{{ $viewMode == 'products' ? 'btn-primary' : 'btn-ghost' }}" icon="o-shopping-bag" />
        </div>
    </div>

    <!-- Main Content Area -->
    @if ($viewMode == 'businesses')
        <!-- ALL Businesses View -->
        <x-card title="Semua UMKM Anda" shadow separator>
            @if ($this->getAllBusinessesProperty()->isNotEmpty())
                <div class="space-y-4">
                    @foreach ($this->getAllBusinessesProperty() as $business)
                        <div class="border rounded-lg p-4 hover:bg-base-50 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h4 class="font-bold text-lg">{{ $business->name }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ $business->address }}</p>
                                            <div class="flex items-center gap-3 mt-2">
                                                <x-badge class="badge-sm">{{ $business->type_label }}</x-badge>
                                                <span class="text-sm text-gray-500">
                                                    <x-icon name="o-shopping-bag" class="w-4 h-4 inline mr-1" />
                                                    {{ $business->products_count }} produk
                                                </span>
                                                @if ($business->korong)
                                                    <span class="text-sm text-gray-500">
                                                        <x-icon name="o-map-pin" class="w-4 h-4 inline mr-1" />
                                                        {{ $business->korong->name }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-lg font-bold text-green-600">
                                                Rp {{ number_format($business->products->sum('price'), 0, ',', '.') }}
                                            </p>
                                            <p class="text-xs text-gray-500">Nilai Produk</p>
                                        </div>
                                    </div>

                                    <!-- Quick Actions -->
                                    <div class="flex gap-2 mt-4">
                                        <x-button label="Lihat" wire:click="viewBusiness({{ $business->id }})"
                                            class="btn-ghost btn-sm" icon="o-eye" />
                                        <x-button label="Tambah Produk"
                                            wire:click="addProductToBusiness({{ $business->id }})"
                                            class="btn-primary btn-sm" icon="o-plus" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if ($this->getAllBusinessesProperty()->hasPages())
                    <div class="mt-6 pt-4 border-t">
                        {{ $this->getAllBusinessesProperty()->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <x-icon name="o-building-storefront" class="w-20 h-20 text-gray-300 mx-auto mb-4" />
                    <h3 class="text-lg font-medium mb-2">Belum ada UMKM</h3>
                    @if (auth()->user()->hasRole('admin'))
                        <p class="text-gray-500 mb-6">Mulai dengan menambahkan UMKM pertama Anda</p>
                        <x-button label="Tambah UMKM Pertama" link="{{ route('business.create') }}" class="btn-primary"
                            icon="o-plus" />
                    @endif
                </div>


            @endif
        </x-card>
    @else
        <!-- ALL Products View -->
        <x-card title="Semua Produk Anda" shadow separator>
            @if ($this->getAllProductsProperty()->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($this->getAllProductsProperty() as $product)
                        <x-card class="overflow-hidden">
                            <!-- Product Image -->
                            <div class="relative h-48 overflow-hidden">
                                @if ($product->images->isNotEmpty())
                                    <img src="{{ asset($product->images->first()->image_url) }}"
                                        alt="{{ $product->name }}" class="w-full h-full object-cover" />
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-base-200">
                                        <x-icon name="o-shopping-bag" class="w-16 h-16" />
                                    </div>
                                @endif
                                <div class="absolute top-2 right-2">
                                    <x-badge
                                        class="badge-sm {{ $product->stock > 0 ? 'badge-success' : 'badge-error' }}">
                                        {{ $product->stock > 0 ? $product->stock : 'Habis' }}
                                    </x-badge>
                                </div>
                            </div>

                            <!-- Product Info -->
                            <div class="p-4">
                                <h4 class="font-bold line-clamp-1">{{ $product->name }}</h4>
                                <p class="text-primary font-bold text-lg mt-1">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </p>

                                @if ($product->description)
                                    <p class="text-sm text-gray-600 mt-2 line-clamp-2">
                                        {{ $product->description }}
                                    </p>
                                @endif

                                <!-- Business Info -->
                                <div class="mt-3 pt-3 border-t">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500">Dari UMKM:</span>
                                        <span class="text-xs font-medium">{{ $product->business->name }}</span>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex gap-2 mt-4">
                                    <x-button label="Detail" wire:click="viewProduct({{ $product->id }})"
                                        class="btn-ghost btn-sm flex-1" icon="o-eye" />
                                    <x-button label="Edit" wire:click="editProduct({{ $product->id }})"
                                        class="btn-ghost btn-sm flex-1" icon="o-pencil" />
                                </div>
                            </div>
                        </x-card>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if ($this->getAllProductsProperty()->hasPages())
                    <div class="mt-6 pt-4 border-t">
                        {{ $this->getAllProductsProperty()->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <x-icon name="o-shopping-bag" class="w-20 h-20 text-gray-300 mx-auto mb-4" />
                    <h3 class="text-lg font-medium mb-2">Belum ada produk</h3>
                    <p class="text-gray-500 mb-6">Mulai dengan menambahkan produk pertama Anda</p>
                    @if ($this->getStatsProperty()['total_businesses'] > 0)
                        <x-button label="Tambah Produk" link="{{ route('business.index') }}" class="btn-primary"
                            icon="o-plus" />
                    @else
                        <x-button label="Tambah UMKM Dulu" link="{{ route('business.add') }}" class="btn-primary"
                            icon="o-building-storefront" />
                    @endif
                </div>
            @endif
        </x-card>
    @endif

    <!-- Recent Transactions -->
    @if ($this->getRecentTransactionsProperty()->isNotEmpty())
        <x-card title="Transaksi Terbaru" shadow class="mt-8">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Pembeli</th>
                            <th>Jumlah</th>
                            <th>Tanggal</th>
                            <th>UMKM</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->getRecentTransactionsProperty() as $transaction)
                            <tr>
                                <td>{{ $transaction->product_name }}</td>
                                <td>{{ $transaction->user->name ?? 'Guest' }}</td>
                                <td>{{ $transaction->quantity }}</td>
                                <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $transaction->product->business->name ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    @endif
</div>
