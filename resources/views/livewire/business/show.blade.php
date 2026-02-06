<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Business;
use App\Models\User;
use App\Models\Korong;
use App\Models\Product;
use App\Models\Image;
use App\Enums\BusinessType;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

new #[Layout('components.layouts.app')] #[Title('Business Management')] class extends Component {
    use WithFileUploads, Toast;

    public function isOwner()
    {
        if (!auth()->user()) {
            return false;
        }
        return auth()->user()->id === $this->business->user_id || auth()->user()->hasRole('admin');
    }
    public string $mode = 'view';
    public ?Business $business = null;
    // Statistics
    public int $totalProducts = 0;
    public float $totalRevenue = 0.0;
    public int $employeeCount = 0;
    public $availableProducts = null;

    // Form properties
    public $name;
    public $address;
    public $phone;
    public $type;
    public $longitude;
    public $latitude;
    public $user_id;
    public $korong_id;
    public $description;
    // Image upload
    public $imagesToUpload = [];
    public $imageLimit = 5;
    public $slides = [];
    // Validation rules
    protected $rules = [
        'name' => 'required|string|max:255',
        'address' => 'required|string|max:500',
        'phone' => 'required|string|max:20',
        'type' => 'required|string',
        'longitude' => 'nullable|numeric|between:-180,180',
        'latitude' => 'nullable|numeric|between:-90,90',
        'user_id' => 'required|exists:users,id',
        'korong_id' => 'nullable|exists:korongs,id',
        'imagesToUpload.*' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        'description' => 'nullable|string',
    ];
    public function viewProduct($id)
    {
        $mode = 'view';
        redirect(route('products.show', ['id' => $id, 'mode' => $mode]));
    }
    public function editProduct($id)
    {
        $mode = 'edit';
        redirect(route('products.show', ['id' => $id, 'mode' => $mode]));
    }

    protected $messages = [
        'name.required' => 'Nama UMKM wajib diisi.',
        'address.required' => 'Alamat UMKM wajib diisi.',
        'phone.required' => 'Nomor telepon wajib diisi.',
        'type.required' => 'Tipe UMKM wajib dipilih.',
        'user_id.required' => 'Pemilik UMKM wajib dipilih.',
        'imagesToUpload.*.image' => 'File :filename bukan gambar yang valid.',
        'imagesToUpload.*.max' => 'File :filename terlalu besar (maksimal 5MB).',
    ];

    /**
     * Mount the component
     */

    public function deleteProduct(int $id)
    {
        if (!$this->isOwner()) {
            $this->error('Anda tidak memiliki izin untuk menghapus produk ini');
            return;
        }

        try {
            $product = Product::findOrFail($id);
            $product->delete();
            $this->availableProducts = $this->business->products;
            $this->loadStatistics();
            $this->success('Produk berhasil dihapus');
            if ($this->currentImageIndex < 0) {
                $this->currentImageIndex = 0;
            }
            if ($this->currentImageIndex >= $this->business->images->count()) {
                $this->currentImageIndex = $this->business->images->count() - 1;
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
    public function mount(int $id, string $mode = 'view')
    {
        $this->business = Business::with([
            'user',
            'korong',
            'products' => function ($query) {
                $query->latest()->limit(5);
            },
            'images',
            'employeeRoles.employees',
        ])->findOrFail($id);

        $this->loadStatistics();

        if (!$this->isOwner()) {
            return;
        }

        // Set form values
        $this->name = $this->business->name;
        $this->address = $this->business->address;
        $this->phone = $this->business->phone;
        $this->type = $this->business->type->value ?? '';
        $this->longitude = $this->business->longitude;
        $this->latitude = $this->business->latitude;
        $this->user_id = $this->business->user_id;
        $this->korong_id = $this->business->korong_id;
        $this->description = $this->business->description;
        $this->mode = $mode;
        $this->availableProducts = $this->business->products;
    }

    /**
     * Load business statistics
     */
    public function loadStatistics()
    {
        $this->totalProducts = $this->business->products()->count();
        $this->employeeCount = $this->business->employeeRoles()->withCount('employees')->get()->sum('employees_count');

        // Calculate revenue from product transactions
        $totalRevenue = $this->business
            ->products()
            ->withSum('transactions as total_quantity', 'quantity')
            ->get()
            ->sum(function ($product) {
                return $product->price * ($product->total_quantity ?? 0);
            });
        $this->totalRevenue = $totalRevenue;
        // dd($totalRevenue);
    }

    /**
     * Switch between view and edit modes
     */
    public function switchMode()
    {
        if ($this->isOwner()) {
            $this->mode = $this->mode === 'view' ? 'edit' : 'view';
        }
    }

    /**
     * Handle image upload validation
     */
    public function updatedImagesToUpload($files)
    {
        if (empty($files)) {
            return;
        }

        // Check total images
        $existingImages = $this->business->images->count();
        $newFilesCount = count($files);
        $totalAfterAdd = $existingImages + $newFilesCount;

        if ($totalAfterAdd > $this->imageLimit) {
            $this->imagesToUpload = [];
            $this->error('Maksimal ' . $this->imageLimit . ' gambar yang dapat diunggah.');
        }
    }

    /**
     * Remove a temporary image from upload queue
     */
    public function removeTemporaryImage($index)
    {
        try {
            if (!isset($this->imagesToUpload[$index])) {
                return;
            }
            unset($this->imagesToUpload[$index]);
            $this->imagesToUpload = array_values($this->imagesToUpload);
        } catch (\Exception $e) {
            $this->error('Gagal menghapus gambar: ' . $e->getMessage());
        }
    }

    /**
     * Remove an existing business image
     */
    public function removeExistingImage($id)
    {
        if (!$this->isOwner()) {
            $this->error('Anda tidak memiliki izin untuk menghapus gambar ini');
            return;
        }
        if (count($this->imagesToUpload) > $this->imageLimit) {
            $this->error('Anda tidak boleh upload lebih dari ' . $this->imageLimit . ' gambar! ');
        }
        try {
            DB::transaction(function () use ($id) {
                $image = Image::find($id);

                if (!$image) {
                    throw new \Exception('Gambar tidak ditemukan');
                }

                // Verify image belongs to this business
                if ($image->imageable_type !== Business::class || $image->imageable_id !== $this->business->id) {
                    throw new \Exception('Gambar tidak termasuk dalam UMKM ini');
                }

                $deleteResult = $image->deleteFile();
                if ($deleteResult !== null) {
                    throw new \Exception('Gagal menghapus file: ' . $deleteResult);
                }
                $image->delete();
            });

            // Reload business images
            $this->business->load('images');
            $this->success('Gambar berhasil dihapus');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Update business information
     */
    public function update()
    {
        if (!$this->isOwner()) {
            $this->error('Anda tidak memiliki izin untuk mengubah data UMKM ini');
            return;
        }

        $this->validate();
        $updated_user_id = $this->business->user_id;
        if (auth()->user()->hasRole('admin')) {
            $updated_user_id = $this->user_id;
        }

        try {
            DB::transaction(function () use ($updated_user_id) {
                // Update business
                $this->business->update([
                    'name' => $this->name,
                    'address' => $this->address,
                    'phone' => $this->phone,
                    'type' => BusinessType::from($this->type),
                    'longitude' => $this->longitude,
                    'latitude' => $this->latitude,
                    'user_id' => $updated_user_id,
                    'korong_id' => $this->korong_id,
                    'description' => $this->description,
                ]);

                // Upload new images
                foreach ($this->imagesToUpload as $image) {
                    Image::create($this->business, $image);
                }
            });

            // Clear uploads and reload
            $this->imagesToUpload = [];
            $this->business->refresh()->load('images');
            $this->loadStatistics();

            $this->success('Data UMKM berhasil diperbarui');
            $this->mode = 'view';
        } catch (\Exception $e) {
            $this->error('Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Get business types for dropdown
     */
    public function getBusinessTypesProperty()
    {
        return collect(BusinessType::cases())->map(function ($type) {
            return [
                'id' => $type->value,
                'name' => $type->label(),
            ];
        });
    }

    /**
     * Get available users for owner dropdown
     */
    public function getAvailableUsersProperty()
    {
        return User::orderBy('name')->get(['id', 'name', 'email']);
    }

    /**
     * Get available korongs for dropdown
     */
    public function getAvailableKorongsProperty()
    {
        return Korong::orderBy('name')->get(['id', 'name']);
    }

    /**
     * Refresh statistics
     */
    public function muatUlang()
    {
        $this->loadStatistics();
    }

    /**
     * Carousel Section
     */

    // CAROUSEL FUNCTIONALITY
    public $currentImageIndex = 0;

    public function previousImage()
    {
        $this->currentImageIndex = $this->integerWrapping($this->currentImageIndex - 1, $this->product->images->count() - 1);
    }

    public function nextImage()
    {
        $this->currentImageIndex = $this->integerWrapping($this->currentImageIndex + 1, $this->product->images->count() - 1);
    }
    public function integerWrapping(int $index, int $maxLen)
    {
        if ($index < 0) {
            $index = $maxLen;
        } elseif ($index >= $maxLen) {
            $index = 0;
        }
        return $index;
    }
    public function goToImage($index)
    {
        $this->integerWrapping($index, $this->product->images->count() - 1);
    }

    public function createProduct($id)
    {
        if (!$this->isOwner()) {
            $this->error('Anda tidak memiliki bisnis ini!');
        }
        return redirect()->route('products.add', ['id' => $id]);
    }
};
?>

<div>
    <x-card shadow separator class="mb-6">
        @if ($this->isOwner() && $this->mode === 'view')
            <x-slot:menu>
                <x-button icon="o-pencil" wire:click="switchMode" spinner class="btn-primary" label="Edit" />
                <x-button icon="o-plus" wire:click="createProduct({{ $this->business->id }})" spinner
                    class="btn-secondary" label="Buat Produk" />
            </x-slot:menu>
        @endif

        {{-- VIEW MODE --}}
        @if ($this->mode === 'view')
            <x-slot:title>
                <div class="flex items-center gap-4">
                    @php
                        $businessImage = $business->images->first();
                    @endphp
                    @if ($businessImage)
                        <x-avatar image="{{ asset($businessImage->path) }}" class="w-16 h-16 rounded-lg" />
                    @else
                        <x-avatar initials="{{ substr($business->name, 0, 2) }}" class="w-16 h-16 rounded-lg" />
                    @endif
                    <div>
                        <h2 class="text-xl font-bold">{{ $business->name }}</h2>
                        <p class="text-sm">{{ $business->type_label ?? 'N/A' }}</p>
                    </div>
                </div>
            </x-slot:title>

            {{-- Refresh Button --}}
            <x-slot:subtitle>
                <div class="flex items-center gap-2">
                    <x-icon name="o-arrow-path" class="w-4 h-4 cursor-pointer" wire:click="muatUlang"
                        tooltip="Refresh" />
                    <span class="text-sm">Diperbarui: {{ now()->format('d/m/Y H:i') }}</span>
                </div>
            </x-slot:subtitle>

            {{-- Business Statistics --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <x-stat title="Total Produk" value="{{ number_format($totalProducts, 0, ',', '.') }}"
                    icon="o-shopping-bag" />

                <x-stat title="Total Pendapatan" value="Rp {{ number_format($totalRevenue, 0, ',', '.') }}"
                    icon="o-banknotes" />

                <x-stat title="Karyawan" value="{{ number_format($employeeCount, 0, ',', '.') }}" icon="o-user-group" />

                <x-stat title="Pemilik" value="{{ $business->user->name ?? 'N/A' }}" icon="o-user" />
            </div>

            {{-- Detailed Information --}}
            <div class="space-y-6">
                <x-card title="Informasi Detail" shadow separator>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-badge value="Alamat" class="badge-ghost" />
                            <p class="mt-1">{{ $business->address }}</p>
                        </div>

                        <div>
                            <x-badge value="Telepon" class="badge-ghost" />
                            <p class="mt-1">{{ $business->phone }}</p>
                        </div>

                        <div>
                            <x-badge value="Korong" class="badge-ghost" />
                            <p class="mt-1">{{ $business->korong->name ?? 'Tidak terdaftar' }}</p>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-4">
                                <x-badge value="Deskripsi" class="badge-ghost" />
                                <x-icon name="o-document-text" class="w-5 h-5 text-base-content/50" />
                            </div>

                            <div class="prose prose-sm max-w-none">
                                <p class="text-base-content/80">
                                    {{ $this->business->description }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <x-badge value="Koordinat" class="badge-ghost" />
                            <p class="mt-1">
                                @if ($business->latitude && $business->longitude)
                                    {{ $business->latitude }}, {{ $business->longitude }}
                                @else
                                    Tidak ada data
                                @endif
                            </p>
                        </div>
                    </div>
                </x-card>

                {{-- Business Images --}}
                @if ($business->images->count() > 0)
                    <x-card title="Gambar UMKM" shadow separator>
                        @if ($business->images->isNotEmpty())
                            <!-- Simple Full Image Carousel -->
                            <div class="relative">
                                <!-- Image Display -->
                                <div class="flex items-center justify-center w-full h-96  rounded-lg overflow-hidden">
                                    <img src="{{ asset($business->images[$currentImageIndex]->path) }}"
                                        alt="Business Image {{ $currentImageIndex + 1 }}"
                                        class="max-w-full max-h-full object-contain">

                                    @if ($this->isOwner())
                                        <div class="absolute top-4 right-4">
                                            <x-button icon="o-trash"
                                                wire:click="removeExistingImage({{ $business->images[$currentImageIndex]->id }})"
                                                class=" btn-sm btn-danger" spinner wire:confirm="Hapus gambar ini?" />
                                        </div>
                                    @endif
                                </div>

                                <!-- Navigation -->
                                @if ($business->images->count() > 1)
                                    <div class="flex justify-between items-center mt-4">
                                        <!-- Previous Button -->
                                        <x-button icon="o-chevron-left" class="btn-circle btn-sm"
                                            wire:click="previousImage" />

                                        <!-- Image Counter -->
                                        <div class="text-center">
                                            <x-badge class="px-4 py-1">
                                                {{ $currentImageIndex + 1 }} / {{ $business->images->count() }}
                                            </x-badge>
                                        </div>

                                        <!-- Next Button -->
                                        <x-button icon="o-chevron-right" class="btn-circle btn-sm"
                                            wire:click="nextImage" />
                                    </div>

                                    <!-- Dots Indicator -->
                                    <div class="flex justify-center gap-2 mt-2">
                                        @foreach ($business->images as $index => $image)
                                            <button wire:click="goToImage({{ $index }})"
                                                class="w-3 h-3 rounded-full transition-all duration-300 {{ $index === $currentImageIndex ? 'bg-primary' : 'bg-gray-300' }}">
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-8">
                                <x-icon name="o-photo" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                                <p class="text-gray-500">Tidak ada gambar yang tersedia</p>
                            </div>
                        @endif
                    </x-card>
                @endif

                <x-card title="Produk yang Tersedia" shadow separator>
                    @if ($business->products->isNotEmpty())
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach ($business->products as $product)
                                <x-card class="overflow-hidden">
                                    <!-- Product Image -->
                                    <div class="relative h-48 overflow-hidden">
                                        @if ($product->images->isNotEmpty())
                                            <img src="{{ asset($product->images->first()->path) }}"
                                                alt="{{ $product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-base-200">
                                                <x-icon name="o-shopping-bag" class="w-16 h-16" />
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Product Info -->
                                    <div class="p-4">
                                        <h4 class="font-bold mb-2">{{ $product->name }}</h4>

                                        @if ($product->description)
                                            <p class="text-sm mb-3 line-clamp-2">
                                                {{ $product->description }}
                                            </p>
                                        @endif

                                        <!-- Price & Stock -->
                                        <div class="flex items-center justify-between mb-4">
                                            <div>
                                                <p class="font-bold text-lg">
                                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                                </p>
                                            </div>
                                            <div>
                                                @if ($product->stock > 0)
                                                    <x-badge class="badge-success">
                                                        Stok: {{ $product->stock }}
                                                    </x-badge>
                                                @else
                                                    <x-badge class="badge-error">
                                                        Habis
                                                    </x-badge>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex flex-col gap-2">
                                            <x-button label="Detail" wire:click="viewProduct({{ $product->id }})"
                                                class="btn-ghost btn-sm flex-1" icon="o-eye" />


                                            @if ($this->isOwner())
                                                <x-button label="Edit"
                                                    wire:click="editProduct({{ $product->id }})"
                                                    class="btn-ghost btn-sm flex-1" icon="o-pencil" />

                                                <x-button label="Hapus"
                                                    wire:click="deleteProduct({{ $product->id }})"
                                                    class="btn-danger btn-sm flex-1" icon="o-trash"
                                                    wire:confirm="Apakah anda lanjut menghapusnya? Produk yang dihapus TIDAK BISA dikembalikan." />
                                            @endif
                                        </div>
                                    </div>
                                </x-card>
                            @endforeach
                        </div>

                        <!-- Product Count -->
                        <div class="mt-6 pt-4 border-t text-center text-sm">
                            Menampilkan {{ $business->products->count() }} produk
                        </div>
                    @else
                        <div class="text-center py-8">
                            <x-icon name="o-shopping-bag" class="w-16 h-16 mx-auto mb-4" />
                            <p>Belum ada produk yang tersedia</p>
                        </div>
                    @endif
                </x-card>
            </div>

            {{-- EDIT MODE --}}
        @elseif ($this->mode === 'edit')
            <x-slot:title>
                <div class="flex items-center gap-4">
                    @php
                        $businessImage = $business->images->first();
                    @endphp
                    @if ($businessImage)
                        <x-avatar image="{{ asset($businessImage->path) }}" class="w-16 h-16 rounded-lg" />
                    @else
                        <x-avatar initials="{{ substr($business->name, 0, 2) }}" class="w-16 h-16 rounded-lg" />
                    @endif
                    <div>
                        <h2 class="text-xl font-bold">Edit UMKM</h2>
                        <p class="text-sm">ID: {{ $business->id }}</p>
                    </div>
                </div>
            </x-slot:title>

            <x-slot:menu>
                <x-button icon="o-arrow-left" wire:click="switchMode" class="btn-ghost" tooltip="Kembali" />
            </x-slot:menu>

            <form wire:submit="update" class="space-y-6">
                <x-card title="Informasi UMKM" shadow separator>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-input label="Nama UMKM" wire:model="name" required />

                        <x-input label="Telepon" wire:model="phone" required />

                        <x-textarea label="Alamat" wire:model="address" rows="3" required />

                        <x-select label="Tipe UMKM" wire:model="type" :options="$this->businessTypes" option-label="name"
                            option-value="id" required />

                        @if (auth()->user()->hasRole('admin'))
                            <x-select label="Pemilik" wire:model="user_id" :options="$this->availableUsers" option-label="name"
                                option-value="id" required searchable placeholder="Ketik untuk mencari..." />
                        @else
                            <x-select label="Pemilik" wire:model="user_id" :options="$this->availableUsers" option-label="name"
                                option-value="id" disabled />
                        @endif

                        <x-select required label="Korong" wire:model="korong_id" :options="$this->availableKorongs"
                            option-label="name" option-value="id" placeholder="Pilih Korong" />

                        <x-textarea label="Deskripsi" wire:model="description" rows="4"
                            placeholder="Masukkan deskripsi product" class="textarea-bordered" inline />
                    </div>
                </x-card>

                <x-card title="Koordinat" shadow separator>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-input label="Latitude" wire:model="latitude" type="number" step="any" />
                        <x-input label="Longitude" wire:model="longitude" type="number" step="any" />
                    </div>
                </x-card>

                {{-- Image Upload --}}
                <x-card title="Gambar UMKM" shadow separator>
                    @if ($business->images->count() > 0)
                        <div class="mb-6">
                            <p class="mb-2">Gambar saat ini:</p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                @foreach ($business->images as $image)
                                    <div class="relative">
                                        <img src="{{ asset($image->path) }}" alt="Business Image"
                                            class="w-full h-32 object-cover rounded-lg">
                                        <div class="absolute top-2 right-2">
                                            <x-button icon="o-trash"
                                                wire:click="removeExistingImage({{ $image->id }})"
                                                class="btn-circle btn-sm" spinner wire:confirm="Hapus gambar ini?" />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="form-control">
                        <x-input wire:model="imagesToUpload" type="file" accept="image/*"
                            class="file-input-bordered w-full" multiple
                            hint="Maksimal {{ $imageLimit }} gambar, 5MB per file" />
                    </div>

                    @if (count($imagesToUpload) > 0)
                        <div class="mt-6">
                            <p class="mb-2">Gambar baru yang akan diunggah:</p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                @foreach ($imagesToUpload as $index => $image)
                                    <div class="relative">
                                        @if ($image->isValid() && in_array($image->getClientOriginalExtension(), ['jpg', 'jpeg', 'png', 'gif']))
                                            <img src="{{ $image->temporaryUrl() }}"
                                                alt="Preview {{ $index + 1 }}"
                                                class="w-full h-32 object-cover rounded-lg">
                                            <div class="absolute top-2 right-2">
                                                <x-button icon="o-x-mark"
                                                    wire:click="removeTemporaryImage({{ $index }})"
                                                    class="btn-circle btn-sm" />
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </x-card>


                {{-- Action Buttons --}}
                <div class="flex justify-end gap-3">
                    <x-button type="button" wire:click="switchMode" class="btn-ghost" label="Batal" />
                    <x-button type="submit" icon="o-check" spinner class="btn-primary" label="Simpan Perubahan" />
                </div>
            </form>
        @endif
    </x-card>
</div>
