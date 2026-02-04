<?php
new #[Layout('components.layouts.empty')] #[Title('Reset Password')] class extends Component {
    public function mount()
    {
        // No additional logic needed for now
    }
};
?>


<div class="py-6">
    <!-- Header -->
    <div class="mb-10">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Produk UMKM</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Temukan produk unggulan dari UMKM Nagari Toboh Gadang
                    Timur</p>
            </div>
            <x-button label="+ Tambah Produk" icon="o-plus" class="btn-primary" />
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-8">
            <x-card
                class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border-blue-200 dark:border-blue-800">
                <div class="p-4 text-center">
                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-300">{{ $this->stats['total'] }}</p>
                    <p class="text-sm text-blue-700 dark:text-blue-400">Total Produk</p>
                </div>
            </x-card>
            <x-card
                class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border-green-200 dark:border-green-800">
                <div class="p-4 text-center">
                    <p class="text-3xl font-bold text-green-600 dark:text-green-300">{{ $this->stats['available'] }}</p>
                    <p class="text-sm text-green-700 dark:text-green-400">Tersedia</p>
                </div>
            </x-card>
            <x-card
                class="bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 border-amber-200 dark:border-amber-800">
                <div class="p-4 text-center">
                    <p class="text-3xl font-bold text-amber-600 dark:text-amber-300">{{ $this->stats['low_stock'] }}</p>
                    <p class="text-sm text-amber-700 dark:text-amber-400">Stok Sedikit</p>
                </div>
            </x-card>
            <x-card
                class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 border-purple-200 dark:border-purple-800">
                <div class="p-4 text-center">
                    <p class="text-3xl font-bold text-purple-600 dark:text-purple-300">{{ $this->stats['featured'] }}
                    </p>
                    <p class="text-sm text-purple-700 dark:text-purple-400">Featured</p>
                </div>
            </x-card>
            <x-card
                class="bg-gradient-to-br from-cyan-50 to-cyan-100 dark:from-cyan-900/20 dark:to-cyan-800/20 border-cyan-200 dark:border-cyan-800">
                <div class="p-4 text-center">
                    <p class="text-3xl font-bold text-cyan-600 dark:text-cyan-300">
                        {{ number_format($this->stats['total_sales']) }}</p>
                    <p class="text-sm text-cyan-700 dark:text-cyan-400">Total Penjualan</p>
                </div>
            </x-card>
            <x-card
                class="bg-gradient-to-br from-pink-50 to-pink-100 dark:from-pink-900/20 dark:to-pink-800/20 border-pink-200 dark:border-pink-800">
                <div class="p-4 text-center">
                    <p class="text-3xl font-bold text-pink-600 dark:text-pink-300">Rp
                        {{ number_format($this->stats['total_value']) }}</p>
                    <p class="text-sm text-pink-700 dark:text-pink-400">Nilai Stok</p>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Search & Filters -->
    <x-card class="mb-8">
        <div class="p-6">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex-1">
                    <x-input placeholder="Cari produk atau UMKM..." wire:model.live.debounce.300ms="search"
                        icon="o-magnifying-glass" class="w-full" />
                </div>
                <div class="flex items-center gap-4">
                    <x-select wire:model.live="selectedCategory" :options="$this->categories" option-label="name" option-value="id"
                        placeholder="Pilih Kategori" class="w-48" />
                    <x-select wire:model.live="sortBy" :options="[
                        ['id' => 'name', 'name' => 'Nama A-Z'],
                        ['id' => 'price', 'name' => 'Harga Terendah'],
                        ['id' => 'rating', 'name' => 'Rating Tertinggi'],
                        ['id' => 'sales', 'name' => 'Penjualan Terbanyak'],
                    ]" option-label="name" option-value="id"
                        placeholder="Urutkan" class="w-48" />
                </div>
            </div>
        </div>
    </x-card>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach ($this->filteredProducts as $product)
            <x-card
                class="hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-gray-200 dark:border-gray-700"
                title="" separator shadow>
                <!-- Product Image/Icon -->
                <div class="relative">
                    <div
                        class="h-48 w-full bg-gradient-to-br
                        @if ($product['category'] == 'Kerajinan') from-amber-50 to-orange-100 dark:from-amber-900/20 dark:to-orange-900/20
                        @elseif($product['category'] == 'Kuliner') from-red-50 to-pink-100 dark:from-red-900/20 dark:to-pink-900/20
                        @elseif($product['category'] == 'Fashion') from-purple-50 to-indigo-100 dark:from-purple-900/20 dark:to-indigo-900/20
                        @else from-cyan-50 to-blue-100 dark:from-cyan-900/20 dark:to-blue-900/20 @endif
                        rounded-t-lg flex items-center justify-center">
                        <div class="text-6xl">
                            @if ($product['category'] == 'Kerajinan')
                                🪵
                            @elseif($product['category'] == 'Kuliner')
                                🍲
                            @elseif($product['category'] == 'Fashion')
                                👘
                            @else
                                ☕
                            @endif
                        </div>

                        <!-- Badges -->
                        <div class="absolute top-3 left-3 flex flex-col gap-1">
                            @if ($product['featured'])
                                <x-badge value="Featured" class="bg-pink-500 text-white border-0" />
                            @endif
                            @if ($product['status'] == 'low_stock')
                                <x-badge value="Stok Sedikit" class="bg-amber-500 text-white border-0" />
                            @endif
                        </div>

                        <!-- Category Badge -->
                        <div class="absolute top-3 right-3">
                            <x-badge :value="$product['category']" class="bg-gray-800 text-white border-0" />
                        </div>
                    </div>
                </div>

                <!-- Product Content -->
                <div class="p-6">
                    <!-- Title & Seller -->
                    <div class="mb-4">
                        <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-1 line-clamp-1">
                            {{ $product['name'] }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                            {{ $product['subcategory'] }}
                        </p>
                        <div class="flex items-center gap-2 text-sm">
                            <x-icon name="o-user" class="w-4 h-4 text-gray-400" />
                            <span class="text-gray-700 dark:text-gray-300">{{ $product['seller'] }}</span>
                        </div>
                    </div>

                    <!-- Description -->
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-6 line-clamp-2">
                        {{ $product['description'] }}
                    </p>

                    <!-- Stats Row -->
                    <div class="grid grid-cols-3 gap-3 mb-6">
                        <div class="text-center p-2 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <p class="text-lg font-bold text-gray-900 dark:text-white">
                                Rp {{ number_format($product['price']) }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Harga</p>
                        </div>
                        <div class="text-center p-2 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <p
                                class="text-lg font-bold
                                @if ($product['stock'] < 20) text-amber-600 dark:text-amber-400
                                @else text-green-600 dark:text-green-400 @endif">
                                {{ $product['stock'] }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Stok</p>
                        </div>
                        <div class="text-center p-2 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex items-center justify-center">
                                <x-icon name="o-star" class="w-4 h-4 text-yellow-500 mr-1" />
                                <span class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ $product['rating'] }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Rating</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                        <div class="text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Terjual:</span>
                            <span class="font-semibold ml-1 text-gray-700 dark:text-gray-300">
                                {{ number_format($product['sales']) }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-button icon="o-heart" class="btn-ghost btn-sm"
                                wire:click="toggleFeatured({{ $product['id'] }})" :tooltip="$product['featured'] ? 'Hapus dari featured' : 'Jadikan featured'" />
                            <x-button label="Beli" icon="o-shopping-cart" class="btn-primary btn-sm"
                                wire:click="addToCart({{ $product['id'] }})" />
                        </div>
                    </div>
                </div>
            </x-card>
        @endforeach
    </div>

    <!-- Empty State -->
    @if (count($this->filteredProducts) == 0)
        <x-card class="mt-8 text-center py-12">
            <div class="text-6xl mb-4">📦</div>
            <h3 class="text-xl font-bold mb-2">Produk tidak ditemukan</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">
                Coba gunakan kata kunci lain atau ubah filter pencarian
            </p>
            <x-button label="Reset Pencarian" icon="o-arrow-path"
                wire:click="$set(['search' => '', 'selectedCategory' => 'all'])" />
        </x-card>
    @endif

    <!-- Load More -->
    @if (count($this->filteredProducts) >= $perPage)
        <div class="mt-10 text-center">
            <x-button label="Muat Lebih Banyak" wire:click="$set('perPage', perPage + 4)" icon="o-chevron-down"
                class="btn-ghost" />
        </div>
    @endif

    <!-- Toast Container -->
    <x-toast />
</div>
