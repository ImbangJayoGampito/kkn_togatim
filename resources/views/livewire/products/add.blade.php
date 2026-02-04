<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Business;
use App\Models\Product;
use App\Models\Image;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

new #[Layout('components.layouts.app')] #[Title('Add Product')] class extends Component {
    use WithFileUploads, Toast;

    public ?Business $business;
    public $productName;
    public $productDescription;
    public $productPrice;
    public $productStock;
    public $business_id;

    public $is_active;

    public $productImages = [];
    public $imageLimit = 10;

    // Validation rules
    protected $rules = [
        'productName' => 'required|string|max:255',
        'productDescription' => 'nullable|string',
        'productPrice' => 'required|numeric|min:0',
        'productStock' => 'required|integer|min:0',
        'productImages.*' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
    ];

    protected $messages = [
        'productName.required' => 'Nama produk wajib diisi.',
        'productPrice.required' => 'Harga wajib diisi.',
        'productPrice.numeric' => 'Harga harus berupa angka.',
        'productPrice.min' => 'Harga tidak boleh kurang dari 0.',
        'productStock.required' => 'Stok wajib diisi.',
        'productStock.integer' => 'Stok harus berupa bilangan bulat.',
        'productImages.*.image' => 'File :filename bukan gambar yang valid.',
        'productImages.*.max' => 'File :filename terlalu besar (maksimal 5MB).',
    ];

    /**
     * Mount the component
     */
    public function mount(int $id)
    {
        if (!$id) {
            abort(404, 'Tidak ditemukannya ID');
        }
        $this->business = Business::findOrFail($id);
        if (!$this->business) {
            abort(404, 'Tidak ditemukannya UMKM');
        }

        // Check if user is owner or admin
        if (!auth()->check()) {
            redirect()->route('login');
        }

        if (auth()->id() !== $this->business->user_id && !auth()->user()->hasRole('admin')) {
            $this->error('Anda tidak memiliki izin untuk menambah produk.');
            redirect()->route('business.show', $this->business->id);
        }
    }

    /**
     * Handle image upload validation
     */
    public function updatedProductImages($files)
    {
        if (empty($files)) {
            return;
        }

        // Check total images
        $newFilesCount = count($files);

        if ($newFilesCount > $this->imageLimit) {
            $this->productImages = [];
            $this->error('Maksimal ' . $this->imageLimit . ' gambar yang dapat diunggah.');
        }
    }

    /**
     * Remove a temporary image from upload queue
     */
    public function removeTemporaryImage($index)
    {
        try {
            if (!isset($this->productImages[$index])) {
                return;
            }
            unset($this->productImages[$index]);
            $this->productImages = array_values($this->productImages);
        } catch (\Exception $e) {
            $this->error('Gagal menghapus gambar: ' . $e->getMessage());
        }
    }

    /**
     * Save the product
     */
    public function save()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                // Create the product
                $product = Product::create([
                    'business_id' => $this->business->id,
                    'name' => $this->productName,
                    'description' => $this->productDescription,
                    'price' => $this->productPrice,
                    'stock' => $this->productStock,
                ]);

                // Upload product images
                foreach ($this->productImages as $image) {
                    Image::create($product, $image);
                }
            });

            // Clear form
            $this->reset(['productName', 'productDescription', 'productPrice', 'productStock', 'productImages']);

            $this->success('Produk berhasil ditambahkan!');

            // Redirect back to business view or products list
            $this->dispatch('product-created');
        } catch (\Exception $e) {
            $this->error('Gagal menambahkan produk: ' . $e->getMessage());
        }
    }

    /**
     * Cancel and go back
     */
    public function cancel()
    {
        redirect()->route('business.show', $this->business->id);
    }
};
?>

<div>
    <x-card shadow separator>
        <x-slot:title>
            <div class="flex items-center gap-4">
                <x-button icon="o-arrow-left" link="{{ route('business.show', $business->id) }}"
                    class="btn-ghost btn-sm" />
                <div>
                    <h2 class="text-xl font-bold">Tambah Produk Baru</h2>

                </div>
            </div>
        </x-slot:title>

        <form wire:submit="save" class="space-y-6">
            {{-- Product Information --}}
            <x-card title="Informasi Produk" shadow separator>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input label="Nama Produk" wire:model="productName" required placeholder="Masukkan nama produk" />

                    <x-input label="Harga (Rp)" wire:model="productPrice" type="number" min="0" required
                        prefix="Rp" />

                    <x-input label="Stok" wire:model="productStock" type="number" min="0" required />


                </div>

                <x-textarea label="Deskripsi" wire:model="productDescription" rows="4"
                    placeholder="Masukkan deskripsi produk (opsional)" />
            </x-card>

            {{-- Product Images --}}
            <x-card title="Gambar Produk" shadow separator>
                <div class="form-control">
                    <x-input wire:model="productImages" type="file" accept="image/*"
                        class="file-input-bordered w-full" multiple
                        hint="Maksimal {{ $imageLimit }} gambar, 5MB per file" />
                </div>

                @if (count($productImages) > 0)
                    <div class="mt-6">
                        <p class="mb-2">Preview gambar yang akan diunggah:</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            @foreach ($productImages as $index => $image)
                                <div class="relative">
                                    @if ($image->isValid() && in_array($image->getClientOriginalExtension(), ['jpg', 'jpeg', 'png', 'gif']))
                                        <img src="{{ $image->temporaryUrl() }}" alt="Preview {{ $index + 1 }}"
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
                <x-button type="button" wire:click="cancel" class="btn-ghost" label="Batal" />
                <x-button type="submit" icon="o-plus" spinner class="btn-primary" label="Tambah Produk" />
            </div>
        </form>
    </x-card>
</div>
