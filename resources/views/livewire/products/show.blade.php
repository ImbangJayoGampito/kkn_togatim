<?php
declare(strict_types=1);
use Livewire\Volt\Component;
use App\Models\Product;
use App\Models\ProductTransaction;
use App\Models\Image;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
new #[Layout('components.layouts.app')] #[Title('Welcome')] class extends Component {
    use WithFileUploads, Toast;
    public ?Product $product = null;
    public int $totalTransaksi = 0;
    public $transaksiTerbaru = null;
    public int $totalPendapatan;
    public int $imageLimit = 10;
    public int $recentTransactionAmount = 20;
    public bool $stokRendah = false;
    public function isOwner()
    {
        if (!auth()->user()) {
            return false;
        }
        return auth()->user()->id === $this->product->business->user_id || auth()->user()->hasRole('admin');
    }

    public string $mode = 'view';
    public array $allowedModes = ['view', 'edit', 'transaction'];
    public array $imagesToUpload = [];
    public function gotoBusiness($id)
    {
        $this->redirect(route('business.show', $id));
    }
    public function updatedImagesToUpload($files)
    {
        if (empty($files)) {
            return;
        }
        $this->validate();

        // Check total images
        $existingImages = $this->product->images->count();
        $newFilesCount = count($files);
        $totalAfterAdd = $existingImages + $newFilesCount;

        if ($totalAfterAdd > $this->imageLimit) {
            // Clear the upload
            $this->imagesToUpload = [];
            $this->error('Maksimal ' . $this->imageLimit . ' gambar yang dapat diunggah.');
        }
    }
    public $transactions = null;
    public function toTransaction()
    {
        if (!$this->isOwner()) {
            return;
        }
        $this->mode = 'transaction';
        $this->transactions = ProductTransaction::find($this->product->id);
    }
    public function removeTemporaryImage($index)
    {
        try {
            if (!isset($this->imagesToUpload[$index])) {
                //$this->dispatch('notify', title: 'Error', description: 'Failed to remove image: ' . $e->getMessage(), icon: 'o-x-circle', iconColor: 'text-error');
                return;
            }
            $imageToRemove = $this->imagesToUpload[$index];
            unset($this->imagesToUpload[$index]);
            $this->imagesToUpload = array_values($this->imagesToUpload);
        } catch (\Exception $e) {
            // $this->dispatch('notify', title: 'Error', description: 'Failed to remove image: ' . $e->getMessage(), icon: 'o-x-circle', iconColor: 'text-error');
            $this->error('Gagal menghapus gambar karena ' . $e->getMessage());
        }
    }
    public function cancel()
    {
        $this->mode = 'view';
    }
    public function update()
    {
        if (!auth()->check()) {
            $this->error('Anda harus login terlebih dahulu');
            return;
        }
        if (auth()->user()->id !== $this->product->business->user_id && !auth()->user()->hasRole('admin')) {
            $this->error('Anda tidak memiliki izin untuk menghapus gambar ini');
            return;
        }
        $this->validate();
        $totalImages = count($this->imagesToUpload) + $this->product->images->count();
        if ($totalImages > $this->imageLimit) {
            $this->error('Gambal maksimal yang diperbolehkan hanya ' . $this->imageLimit);
        }
        $product = $this->product;
        $name = $this->name;
        $description = $this->description;
        $price = $this->price;
        $stock = $this->stock;
        $imagesToUpload = $this->imagesToUpload;

        try {
            DB::transaction(function () use ($product, $name, $description, $price, $stock, $imagesToUpload) {
                $product->name = $name;
                $product->description = $description;
                $product->price = $price;
                $product->stock = $stock;

                $product->save();

                foreach ($imagesToUpload as $tempImageUrl) {
                    // $contents = file_get_contents($tempImageUrl);

                    $image = Image::create($product, $tempImageUrl);
                    if ($image) {
                    } else {
                        throw new \Exception('Gagal melakukan upload gambar');
                    }
                }
            });
            $this->imagesToUpload = [];
            $this->success('Berhasil melakukan update');
        } catch (\Exception $e) {
            $this->error('Gagal melakukan update', $e->getMessage());
        }
    }
    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'business_id' => 'required|exists:businesses,id',
        'imagesToUpload.*' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
    ];

    // MESSAGES property (separate!)
    protected $messages = [
        'name.required' => 'Nama product wajib diisi.',
        'name.max' => 'Nama product maksimal 255 karakter.',
        'price.required' => 'Harga wajib diisi.',
        'price.numeric' => 'Harga harus berupa angka.',
        'price.min' => 'Harga tidak boleh kurang dari 0.',
        'stock.required' => 'Stok wajib diisi.',
        'stock.integer' => 'Stok harus berupa bilangan bulat.',
        'business_id.required' => 'Pilih bisnis terlebih dahulu.',
        'business_id.exists' => 'Bisnis yang dipilih tidak valid.',
        'imagesToUpload.*.image' => 'File :filename bukan gambar yang valid.',
        'imagesToUpload.*.mimes' => 'File :filename harus format JPG, PNG, GIF, atau WebP.',
        'imagesToUpload.*.max' => 'File :filename terlalu besar (maksimal 5MB).',
    ];
    public $name;
    public $description;
    public $stock;
    public $business_id;
    public $is_active;

    public function removeExistingImage($id)
    {
        if (!auth()->check()) {
            $this->error('Anda harus login terlebih dahulu');
        }
        if (auth()->user()->id !== $this->product->business->user_id && !auth()->user()->hasRole('admin')) {
            $this->error('Anda tidak memiliki izin untuk menghapus gambar ini');
        }
        try {
            DB::transaction(function () use ($id) {
                $image = Image::find($id);

                if (!$image) {
                    throw new \Exception('Gambar tidak ditemukan');
                }

                $deleteResult = $image->deleteFile();
                if ($deleteResult !== null) {
                    throw new \Exception('Gagal menghapus file');
                }
                $image->delete();
            });
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
    public function mount(int $id, string $mode = 'view')
    {
        $this->product = Product::with([
            'business.user', // Include business owner relationship
            'images',
            'transactions' => function ($query) {
                $query->latest()->limit($this->recentTransactionAmount); // Limit transactions for performance
            },
        ])->findOrFail($id);

        $this->loadStatistics();

        $this->name = $this->product->name;

        $this->description = $this->product->description;
        $this->price = $this->product->price;
        $this->stock = $this->product->stock;
        $this->business_id = $this->product->business_id;
        $this->is_active = $this->product->is_active;
        $this->mode = $mode;
        $this->users = User::all();
        $this->recentTransactions = $this->product->transactions()->with('user')->latest()->take($this->recentTransactionAmount)->get();
    }
    public function switchMode()
    {
        if ($this->isOwner()) {
            $this->mode = $this->allowedModes[0] === $this->mode ? $this->allowedModes[1] : $this->allowedModes[0];
        }
    }
    public $recentTransactions = [];
    public function loadStatistics()
    {
        $this->totalTransaksi = $this->product->transactions()->count();

        // Change this line - get count of recent transactions instead of the object
        $this->transaksiTerbaru = $this->product
            ->transactions()
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $this->totalPendapatan = $this->product->transactions()->sum('price') * $this->product->transactions()->sum('quantity');
        $this->stokRendah = $this->product->stock < 10;
    }
    public function muatUlang()
    {
        $this->loadStatistics();
    }
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
    // ONWER VERIFICATION (IMPORTANT!!!!)
    public function createProduct($id)
    {
        if (!$this->isOwner()) {
            $this->error('Anda tidak memiliki bisnis ini!');
            return;
        }
        return redirect()->route('product.add', ['id' => $id]);
    }

    // Transaksi Produk
    public $quantity = 1;
    public $price = 0;
    public $user_id = null;

    public $users = [];
    public $availableStock = 0;
    public function addTransaction()
    {
        if (!$this->isOwner()) {
            $this->error('Anda tidak memiliki izin untuk menambah transaksi');
            return;
        }
        $this->validate($this->transactionRules(), $this->transactionErrors());
        $quantity = $this->quantity;
        if ($quantity > $this->product->stock) {
            $this->error('Stok produk tidak cukup');
            return;
        }

        $this->product->transactions()->create([
            'quantity' => $quantity,
            'price' => $this->product->price,
            'user_id' => $this->user_id,
        ]);
        $this->product->stock -= $quantity;
        $this->product->save();
        $this->success('Transaksi produk berhasil ditambahkan');
        $this->muatUlang();
    }
    public function transactionRules()
    {
        return [
            'quantity' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'user_id' => ['nullable', 'exists:users,id'],
        ];
    }
    public function transactionErrors()
    {
        return [
            'quantity.required' => 'Jumlah produk wajib diisi.',
            'quantity.integer' => 'Jumlah produk harus berupa angka.',
            'quantity.min' => 'Jumlah produk minimal adalah 1.',
            'price.required' => 'Harga produk wajib diisi.',
            'price.numeric' => 'Harga produk harus berupa angka.',
            'price.min' => 'Harga produk minimal adalah 0.',
            'user_id.exists' => 'Pengguna tidak ditemukan.',
        ];
    }
};

?>


{{-- resources/views/livewire/statistik-product.blade.php --}}
<div>
    <x-card shadow separator class="mb-6">
        @if ($this->isOwner())
            <x-slot:menu>
                <x-button icon="o-shopping-bag" wire:click="toTransaction" spinner class="btn-primary btn-md"
                    tooltip-bottom="" label="Transaksi" />
                <x-button icon="o-pencil" wire:click="switchMode" spinner class="btn-primary btn-md" tooltip-bottom=""
                    label="Edit" />

            </x-slot:menu>
        @endif
       
        {{-- Kartu Statistik Produk --}}
        @if ($this->mode === 'view')

            {{-- Header dengan Info Produk --}}
            <x-slot:title>
                <div class="flex items-center gap-4">
                    @php
                        $productImage = $product->images->first();
                    @endphp
                    @if ($productImage)
                        <x-avatar image="{{ $productImage->path }}" class="w-16 h-16 rounded-lg" />
                    @else
                        <x-avatar initials="{{ substr($product->name, 0, 2) }}"
                            class="w-16 h-16 rounded-lg bg-base-200" />
                    @endif
                    <div>
                        <h2 class="text-xl font-bold">{{ $product->name }}</h2>


                        <x-button label="UMKM: {{ $product->business->name ?? 'Tidak ada UMKM' }}"
                            wire:click="gotoBusiness({{ $product->business_id }})"
                            class="btn-primary btn-sm"></x-button>
                    </div>
                </div>
            </x-slot:title>

            {{-- Tombol Refresh --}}


            {{-- Informasi Dasar Produk --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div>
                    <p class="text-sm text-gray-500">Stok Saat Ini</p>
                    <p class="text-2xl font-bold flex items-center gap-2">
                        {{ number_format($this->product->stock ?? 0, 0, ',', '.') }}
                        @if ($stokRendah)
                            <x-badge value="Rendah" class="badge-warning badge-sm" />
                        @endif
                    </p>
                </div>

                <div>
                    <p class="text-sm ">Harga</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($product->price ?? 0, 0, ',', '.') }}</p>
                </div>

                <div>
                    <p class="text-sm ">Total Transaksi</p>
                    <p class="text-2xl font-bold">{{ number_format($totalTransaksi ?? 0, 0, ',', '.') }}</p>
                </div>

                <div>
                    <p class="text-sm ">Total Pendapatan</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</p>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <x-badge value="Deskripsi" class="badge-ghost" />
                        <x-icon name="o-document-text" class="w-5 h-5 text-base-content/50" />
                    </div>

                    <div class="prose prose-sm max-w-none">
                        <p class="text-base-content/80">
                            {{ $this->product->business->description }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Statistik Detail --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <x-stat title="Transaksi 30 Hari" value="{{ number_format($transaksiTerbaru ?? 0, 0, ',', '.') }}"
                    icon="o-arrow-trending-up" description="Terakhir 30 hari" />

                <x-stat title="Status Stok" :value="$stokRendah ? 'Stok Rendah' : 'Stok Aman'" :icon="$stokRendah ? 'o-exclamation-triangle' : 'o-check-circle'" :value-class="$stokRendah ? 'text-warning' : 'text-success'"
                    description="{{ $product->stock }} unit tersedia" />

                <x-stat title="Rata-rata Pendapatan"
                    value="Rp {{ $totalTransaksi > 0 ? number_format($totalPendapatan / $totalTransaksi ?? 0, 0, ',', '.') : '0' }}"
                    icon="o-banknotes" description="Per transaksi" />
            </div>

            {{-- Statistik Saat Ini --}}
            <div class="divider my-4"></div>
            <div>
                <h3 class="font-semibold mb-4 text-lg">Statistik Saat Ini</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-base-100 p-4 rounded-lg border">
                        <p class="text-sm text-gray-500">Total Transaksi</p>
                        <p class="text-xl font-bold">{{ number_format($totalTransaksi ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-base-100 p-4 rounded-lg border">
                        <p class="text-sm text-gray-500">Total Pendapatan</p>
                        <p class="text-xl font-bold">Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-base-100 p-4 rounded-lg border">
                        <p class="text-sm text-gray-500">Transaksi 30 Hari</p>
                        <p class="text-xl font-bold">{{ number_format($transaksiTerbaru ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            {{-- Informasi Tambahan --}}
            @if ($product->images->count() > 0)
                <x-card title="Gambar UMKM" shadow separator>
                    @if ($product->images->isNotEmpty())
                        <!-- Simple Full Image Carousel -->
                        <div class="relative">
                            <!-- Image Display -->
                            <div class="flex items-center justify-center w-full h-96  rounded-lg overflow-hidden">
                                <img src="{{ asset($product->images[$currentImageIndex]->path) }}"
                                    alt="Business Image {{ $currentImageIndex + 1 }}"
                                    class="max-w-full max-h-full object-contain">

                                @if ($this->isOwner())
                                    <div class="absolute top-4 right-4">
                                        <x-button icon="o-trash"
                                            wire:click="removeExistingImage({{ $product->images[$currentImageIndex]->id }})"
                                            class=" btn-sm btn-danger" spinner wire:confirm="Hapus gambar ini?" />
                                    </div>
                                @endif
                            </div>

                            <!-- Navigation -->
                            @if ($product->images->count() > 1)
                                <div class="flex justify-between items-center mt-4">
                                    <!-- Previous Button -->
                                    <x-button icon="o-chevron-left" class="btn-circle btn-sm"
                                        wire:click="previousImage" />

                                    <!-- Image Counter -->
                                    <div class="text-center">
                                        <x-badge class="px-4 py-1">
                                            {{ $currentImageIndex + 1 }} / {{ $product->images->count() }}
                                        </x-badge>
                                    </div>

                                    <!-- Next Button -->
                                    <x-button icon="o-chevron-right" class="btn-circle btn-sm" wire:click="nextImage" />
                                </div>

                                <!-- Dots Indicator -->
                                <div class="flex justify-center gap-2 mt-2">
                                    @foreach ($product->images as $index => $image)
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

            {{-- Informasi Tambahan --}}
            <div class="divider my-6"></div>
            <div class="space-y-4">
                <div class="flex justify-between items-center text-sm">
                    <div class="flex items-center gap-2">
                        <x-icon name="o-clock" class="h-4 w-4" />
                        <span>Diperbarui: {{ now()->format('d/m/Y H:i') }}</span>
                    </div>

                </div>
            </div>

            {{-- Recent Transactions Table --}}
            @if ($this->recentTransactions->isNotEmpty())
                <x-card title="{{ $this->recentTransactionAmount }} Transaksi Terbaru" separator shadow class="mt-6">
                    <div class="overflow-x-auto">
                        <table class="table table-zebra">
                            <thead>
                                <tr>
                                    <th class="text-left">Tanggal</th>
                                    <th class="text-left">Pelanggan</th>
                                    <th class="text-left">Jumlah</th>
                                    <th class="text-left">Harga Satuan</th>
                                    <th class="text-left">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($this->recentTransactions as $transaction)
                                    <tr>
                                        <td class="whitespace-nowrap">
                                            {{ $transaction->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="whitespace-nowrap">
                                            @if ($transaction->user)
                                                <div class="flex items-center gap-2">
                                                    <x-avatar :image="$transaction->user->profile_photo_url ?? null" :initials="substr($transaction->user->name, 0, 2)" class="w-6 h-6" />
                                                    <span>{{ $transaction->user->name }}</span>
                                                </div>
                                            @else
                                                <span class="text-gray-500">Anonim</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap">
                                            {{ number_format($transaction->quantity, 0, ',', '.') }} unit
                                        </td>
                                        <td class="whitespace-nowrap">
                                            Rp {{ number_format($transaction->price, 0, ',', '.') }}
                                        </td>
                                        <td class="whitespace-nowrap font-semibold">
                                            Rp
                                            {{ number_format($transaction->quantity * $transaction->price, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            @if ($this->totalTransaksi > $this->recentTransactionAmount)
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-center text-sm text-gray-500 pt-4">
                                            Menampilkan {{ $this->recentTransactionAmount }} dari
                                            {{ number_format($this->totalTransaksi, 0, ',', '.') }}
                                            transaksi
                                            <x-button
                                                wire:click="$dispatch('openModal', { component: 'transactions.modal', arguments: { productId: {{ $product->id }} } })"
                                                class="btn-link btn-sm ml-2" label="Lihat Semua" />
                                        </td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>

                    @if ($this->totalTransaksi === 0)
                        <div class="text-center py-8">
                            <x-icon name="o-receipt-percent" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                            <p class="text-gray-500">Belum ada transaksi untuk produk ini</p>
                            @if ($this->isOwner())
                                <x-button wire:click="toTransaction" icon="o-plus" class="btn-primary btn-sm mt-4"
                                    label="Buat Transaksi Pertama" />
                            @endif
                        </div>
                    @endif
                </x-card>
            @endif
        @elseif ($this->mode === 'edit')
            {{-- Header dengan Info Produk --}}
            <x-slot:title>
                <div class="flex items-center gap-4">
                    @php
                        $productImage = $product->images->first();
                    @endphp
                    @if ($productImage)
                        <x-avatar image="{{ $productImage->path }}" class="w-16 h-16 rounded-lg" />
                    @else
                        <x-avatar initials="{{ substr($product->name, 0, 2) }}"
                            class="w-16 h-16 rounded-lg bg-base-200" />
                    @endif
                    <div>
                        <h2 class="text-xl font-bold">Edit Produk</h2>
                        <p class="text-sm text-gray-500">ID: {{ $product->id }}</p>
                    </div>
                </div>
            </x-slot:title>

            {{-- Tombol Kembali --}}
            <x-slot:menu>
                <x-button icon="o-arrow-left" wire:click="cancel" class="btn-ghost btn-sm"
                    tooltip-bottom="Kembali ke detail" />
            </x-slot:menu>

            {{-- Header dengan Info Produk --}}
            <x-slot:title>
                <div class="flex items-center gap-4">
                    @php
                        $productImage = $product->images->first();
                    @endphp
                    @if ($productImage)
                        <x-avatar image="{{ $productImage->path }}" class="w-16 h-16 rounded-lg" />
                    @else
                        <x-avatar initials="{{ substr($product->name, 0, 2) }}"
                            class="w-16 h-16 rounded-lg bg-base-200" />
                    @endif
                    <div>
                        <h2 class="text-xl font-bold">Edit Produk</h2>
                        <p class="text-sm text-gray-500">ID: {{ $product->id }}</p>
                    </div>
                </div>
            </x-slot:title>

            {{-- Tombol Kembali --}}
            <x-slot:menu>
                <x-button icon="o-arrow-left" wire:click="cancel" class="btn-ghost btn-sm"
                    tooltip-bottom="Kembali ke detail" />
            </x-slot:menu>

            <form wire:submit="update">
                <div class="space-y-6">
                    {{-- Informasi Dasar Produk --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Nama Produk --}}
                        <x-input label="Nama Produk" wire:model="name" type="text"
                            placeholder="Masukkan nama product" class="input-bordered" inline />

                        {{-- UMKM --}}


                        {{-- Harga --}}
                        <x-input label="Harga (Rp)" wire:model="price" type="number" min="0"
                            placeholder="Masukkan harga" class="input-bordered" inline prefix="Rp" />

                        {{-- Stok --}}
                        <x-input label="Stok" wire:model="stock" type="number" min="0"
                            placeholder="Masukkan jumlah stok" class="input-bordered" inline>
                            @if ($stokRendah)
                                <x-slot:hint>
                                    <span class="text-warning">
                                        <x-icon name="o-exclamation-triangle" class="w-4 h-4 inline mr-1" />
                                        Stok rendah
                                    </span>
                                </x-slot:hint>
                            @endif
                        </x-input>

                        {{-- Status --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Status</span>
                            </label>
                            <div class="flex items-center gap-6 mt-2">

                            </div>
                            @error('is_active')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>
                        @if ($product->images->count() > 0)
                            <div class="mt-6">
                                <p class="text-sm font-medium mb-2">Gambar Produk Anda</p>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                    @foreach ($product->images as $image)
                                        <div class="relative border rounded-lg overflow-hidden bg-base-100 group">
                                            <img src="{{ asset($image->path) }}" alt="Product Image"
                                                class="w-full h-32 object-cover">
                                            <div
                                                class="absolute inset-0 bg-black/60 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                <x-button icon="o-trash"
                                                    wire:click="removeExistingImage({{ $image->id }})"
                                                    class="btn-circle btn-error" spinner tooltip="Delete Image" />
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Upload Gambar --}}
                        {{-- Image Upload Section --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Yang akan di unggah</span>
                            </label>
                            <div class="mt-2">
                                {{-- MaryUI file input for selecting multiple images --}}
                                <x-input wire:model="imagesToUpload" type="file" accept="image/*"
                                    class="file-input-bordered file-input-primary w-full" multiple
                                    hint="Unggah beberapa gambar." limit="1" />
                                @error('imagesToUpload.*')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            {{-- Preview of Selected Images to Upload --}}
                            @if (count($imagesToUpload) > 0)
                                <div class="mt-4">
                                    <p class="text-sm font-medium mb-2">Gambar baru yang akan diunggah:
                                        ({{ count($imagesToUpload) }})</p>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                        @foreach ($imagesToUpload as $index => $image)
                                            @php
                                                $isValidImage =
                                                    $image->isValid() &&
                                                    in_array($image->getClientOriginalExtension(), [
                                                        'jpg',
                                                        'jpeg',
                                                        'png',
                                                        'gif',
                                                    ]);
                                            @endphp
                                            @if ($isValidImage)
                                                <div class="relative border rounded-lg overflow-hidden bg-base-100">
                                                    <img src="{{ $image->temporaryUrl() }}"
                                                        alt="Preview {{ $index + 1 }}"
                                                        class="w-full h-32 object-cover">
                                                    <div class="absolute top-1 right-1">
                                                        <x-button icon="o-x-mark"
                                                            wire:click="removeTemporaryImage({{ $index }})"
                                                            class="btn-circle btn-xs btn-error" tooltip="Remove"
                                                            spinner />
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Display Existing Product Images --}}

                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <x-textarea label="Deskripsi" wire:model="description" rows="4"
                        placeholder="Masukkan deskripsi product" class="textarea-bordered" inline />



                    {{-- Tombol Aksi --}}
                    <div class="flex justify-end gap-3 pt-6 border-t">
                        <x-button type="button" wire:click="cancel" class="btn-ghost" label="Batal" />
                        <x-button type="submit" icon="o-check" spinner class="btn-primary"
                            label="Simpan Perubahan" />
                    </div>
                </div>
            </form>
        @elseif($this->mode === 'transaction')
            <x-slot:title>
                <div class="flex items-center gap-4">
                    @php
                        $productImage = $product->images->first();
                    @endphp
                    @if ($productImage)
                        <x-avatar image="{{ $productImage->path }}" class="w-16 h-16 rounded-lg" />
                    @else
                        <x-avatar initials="{{ substr($product->name, 0, 2) }}"
                            class="w-16 h-16 rounded-lg bg-base-200" />
                    @endif
                    <div>
                        <h2 class="text-xl font-bold">Transaksi Baru</h2>
                        <p class="text-sm text-gray-500">Untuk produk: {{ $product->name }}</p>
                    </div>
                </div>
            </x-slot:title>

            <!-- Action Buttons in Menu -->
            <x-slot:menu>
                <x-button icon="o-arrow-left" wire:click="cancel" class="btn-ghost btn-sm"
                    tooltip-bottom="Kembali ke detail produk" />
            </x-slot:menu>

            <!-- Product Summary Section -->
            <div class="mb-8">
                <h3 class="font-semibold mb-4 text-lg">Ringkasan Produk</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-base-100 p-4 rounded-lg border">
                        <p class="text-sm text-gray-500">Stok Tersedia</p>
                        <p class="text-xl font-bold">{{ number_format($stock ?? 0, 0, ',', '.') }} unit</p>
                    </div>
                    <div class="bg-base-100 p-4 rounded-lg border">
                        <p class="text-sm text-gray-500">Harga Default</p>
                        <p class="text-xl font-bold">Rp {{ number_format($product->price ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-base-100 p-4 rounded-lg border">
                        <p class="text-sm text-gray-500">ID Produk</p>
                        <p class="text-xl font-bold">{{ $product->id }}</p>
                    </div>
                </div>

                @if ($product->description)
                    <div class="mt-4 p-3 bg-base-200 rounded-lg">
                        <p class="text-sm text-gray-500 mb-1">Deskripsi:</p>
                        <p>{{ $product->description }}</p>
                    </div>
                @endif
            </div>

            <!-- Transaction Form -->
            <form wire:submit="addTransaction">
                <div class="space-y-6">
                    <!-- Form Grid Layout -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Quantity Input -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Jumlah *</span>
                            </label>
                            <x-input wire:model.live="quantity" type="number" min="1"
                                placeholder="Masukkan jumlah" class="input-bordered" />
                            @if ($stock > 0)
                                <label class="label">
                                    <span class="label-text-alt">Tersedia:
                                        {{ number_format($stock ?? 0, 0, ',', '.') }}
                                        unit</span>
                                </label>
                            @endif
                            @error('quantity')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Price Input -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Harga per Unit (Rp) *</span>
                            </label>
                            <x-input disabled wire:model="price" type="number" min="0"
                                placeholder="Masukkan harga" class="input-bordered" />
                            <label class="label">
                                <span class="label-text-alt">Default: Rp
                                    {{ number_format($product->price ?? 0, 0, ',', '.') }}</span>
                            </label>
                            @error('price')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Customer Selection -->
                        <div class="md:col-span-2">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Pelanggan (Opsional)</span>
                                </label>
                                <select wire:model="user_id" class="select select-bordered w-full">
                                    <option value="">Pilih pelanggan...</option>
                                    @foreach ($this->users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}
                                            ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <label class="label">
                                    <span class="label-text-alt">Kosongkan untuk transaksi anonim</span>
                                </label>
                                @error('user_id')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Total Calculation -->
                    <div class="divider my-4"></div>
                    <div class="bg-base-200 p-4 rounded-lg">
                        <h4 class="font-semibold mb-3">Ringkasan Transaksi</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Jumlah Barang</p>
                                <p class="text-lg font-semibold">{{ number_format($quantity ?? 0, 0, ',', '.') }} unit
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Harga per Unit</p>
                                <p class="text-lg font-semibold">Rp {{ number_format($price ?? 0, 0, ',', '.') }}</p>
                            </div>
                            <div class="col-span-2 pt-3 border-t">
                                <div class="flex justify-between items-center">
                                    <p class="text-lg font-semibold">Total</p>
                                    <p class="text-2xl font-bold text-primary">
                                        Rp {{ number_format($this->quantity * $this->price ?? 0, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-3 pt-6 border-t">
                        <x-button type="button" wire:click="cancel" class="btn-ghost" label="Batal" />
                        <x-button type="submit" icon="o-check" spinner class="btn-primary"
                            label="Simpan Transaksi" />
                    </div>
                </div>
            </form>
        @endif
    </x-card>






</div>
