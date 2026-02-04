<?php

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
use App\Services\BusinessService;
use Illuminate\Support\Facades\Storage;

new #[Layout('components.layouts.app')] #[Title('Business Management')] class extends Component {
    use WithFileUploads, Toast;

    public $name;
    public $address;
    public $phone;
    public $type;
    public $longitude;
    public $latitude;
    public $user_id;
    public $korong_id;
    public $imagesToUpload = [];
    public $imageLimit = 10;

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
    ];

    protected $messages = [
        'type.required' => 'Tipe UMKM wajib dipilih.',
        'user_id.required' => 'Pemilik UMKM wajib dipilih.',
        'korong_id.required' => 'Korong wajib dipilih.',
    ];

    /**
     * Mount the component
     */
    public function mount()
    {
        // Set default values
        if (auth()->check() && !auth()->user()->hasRole('admin')) {
            $this->user_id = auth()->id();
            $this->type = BusinessType::LAINNYA->value;
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
        if (auth()->user()->hasRole('admin')) {
            return User::orderBy('name')->get(['id', 'name', 'email']);
        }

        // For non-admin users, only show themselves
        return collect([auth()->user()]);
    }

    /**
     * Get available korongs for dropdown
     */
    public function getAvailableKorongsProperty()
    {
        return Korong::orderBy('name')->get(['id', 'name']);
    }

    /**
     * Create new business
     */
    public function create()
    {
        // Validate first
        $this->validate();
        if (!auth()->user()->hasRole('admin')) {
            $this->error('Anda tidak diizinkan');
            return;
        }
        if (empty($this->type)) {
            $this->error('Tipe UMKM belum dipilih');
            return;
        }

        try {
            // Create the business
            $business = BusinessService::createBusiness(
                $this->longitude,
                $this->latitude,
                $this->name,
                $this->address,
                $this->phone,
                $this->type, // This should have a value now
                $this->korong_id,
                User::find($this->user_id),
                function ($business) {
                    // Upload images if any
                    foreach ($this->imagesToUpload as $image) {
                        Image::create($business, $image);
                    }
                },
            );

            // Upload images if any

            // Clear form and show success
            $this->reset(['name', 'address', 'phone', 'type', 'longitude', 'latitude', 'user_id', 'korong_id', 'imagesToUpload']);

            $this->success('UMKM berhasil dibuat!');

            // Redirect to business index or show page
            return redirect()->route('business.show', $business->id);
        } catch (\Exception $e) {
            $this->error('Gagal membuat UMKM: ' . $e->getMessage());
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
     * When type changes, log it for debugging
     */
    public function updatedType($value)
    {
        \Log::info('Type updated:', ['type' => $value]);
    }
};
?>

<div>
    <x-card shadow separator class="mb-6">
        <x-slot:title>
            <div class="flex items-center gap-4">
                <x-button icon="o-arrow-left" link="{{ route('business.index') }}" class="btn-ghost btn-sm" />
                <div>
                    <h2 class="text-xl font-bold">Tambah UMKM Baru</h2>
                </div>
            </div>
        </x-slot:title>



        <form wire:submit="create" class="space-y-6">
            <x-card title="Buat UMKM" shadow separator>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input label="Nama UMKM" wire:model="name" required />

                    <x-input label="Telepon" wire:model="phone" required />

                    <x-textarea label="Alamat" wire:model="address" rows="3" required />

                    <x-select label="Tipe UMKM" wire:model="type" :options="$this->businessTypes" option-label="name"
                        option-value="id" required placeholder="Pilih Tipe UMKM" />

                    @if (auth()->user()->hasRole('admin'))
                        <x-select label="Pemilik" wire:model="user_id" :options="$this->availableUsers" option-label="name"
                            option-value="id" required searchable placeholder="Ketik untuk mencari..." />
                    @else
                        <x-select label="Pemilik" wire:model="user_id" :options="$this->availableUsers" option-label="name"
                            option-value="id" disabled />
                    @endif

                    <x-select required label="Korong" wire:model="korong_id" :options="$this->availableKorongs" option-label="name"
                        option-value="id" placeholder="Pilih Korong" />
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
                <x-button type="button" wire:click="switchMode" class="btn-ghost" label="Batal" />
                <x-button type="submit" icon="o-check" spinner class="btn-primary" label="Simpan Perubahan" />
            </div>
        </form>

    </x-card>
</div>
