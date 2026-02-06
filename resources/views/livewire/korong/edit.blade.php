<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Korong;
use App\Models\Nagari;
use App\Models\WaliKorong;
use App\Models\Image;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

new #[Layout('components.layouts.app')] #[Title('Edit Korong')] class extends Component {
    use WithFileUploads, Toast;

    public Korong $korong;

    // Form fields
    public $name;
    public $address;
    public $phone;
    public $email;
    public $description;
    public $latitude;
    public $longitude;

    public $total_households;
    public $male_population;
    public $female_population;
    public $area_size_km2;
    public $population_data = [];
    public $wali_korong_id;
    public $nagari_id;

    // Image handling
    public $imagesToUpload = [];
    public $imageLimit = 10;
    public $existingImages = [];

    // Rules
    protected $rules = [
        'name' => 'required|string|max:255',
        'address' => 'required|string|max:500',
        'phone' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'description' => 'nullable|string',
        'latitude' => 'nullable|numeric|between:-90,90',
        'longitude' => 'nullable|numeric|between:-180,180',
        'total_households' => 'nullable|integer|min:0',
        'male_population' => 'nullable|integer|min:0',
        'female_population' => 'nullable|integer|min:0',
        'area_size_km2' => 'nullable|numeric|min:0',
        'population_data' => 'nullable|array',
        'wali_korong_id' => 'nullable|exists:wali_korongs,id',
        'nagari_id' => 'required|exists:nagaris,id',
        'imagesToUpload.*' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
    ];

    protected $messages = [
        'name.required' => 'Nama Korong wajib diisi.',
        'nagari_id.required' => 'Nagari wajib dipilih.',
    ];

    /**
     * Mount the component
     */
    public function mount(int $id)
    {
        $this->korong = Korong::findOrFail($id)->load(['images', 'waliKorong', 'nagari']);

        // Populate form fields
        $this->name = $this->korong->name;
        $this->address = $this->korong->address;
        $this->phone = $this->korong->phone;
        $this->email = $this->korong->email;
        $this->description = $this->korong->description;
        $this->latitude = $this->korong->latitude;
        $this->longitude = $this->korong->longitude;

        $this->total_households = $this->korong->total_households;
        $this->male_population = $this->korong->male_population;
        $this->female_population = $this->korong->female_population;
        $this->area_size_km2 = $this->korong->area_size_km2;
        $this->population_data = $this->korong->population_data ?: [];
        $this->wali_korong_id = $this->korong->wali_korong_id;
        $this->nagari_id = $this->korong->nagari_id;

        // Load existing images
        $this->loadExistingImages();
    }

    /**
     * Get available nagaris for dropdown
     */
    public function getAvailableNagarisProperty()
    {
        return Nagari::orderBy('name')->get(['id', 'name']);
    }

    /**
     * Get available wali korongs for dropdown
     */
    public function getAvailableWaliKorongsProperty()
    {
        return WaliKorong::with('user')
            ->orderBy('name')
            ->get(['id', 'name', 'user_id']);
    }

    /**
     * Update korong
     */
    public function update()
    {
        // Validate first
        $this->validate();
        if (auth()->user()) {
            $this->error('Anda tidak diautentikasi!');
            return;
        }
        if (auth()->user()->hasRole('admin')) {
            $this->error('Anda bukan admin sistem!');
        }
        try {
            DB::beginTransaction();

            // Update the korong
            $this->korong->update([
                'name' => $this->name,
                'address' => $this->address,
                'phone' => $this->phone,
                'email' => $this->email,
                'description' => $this->description,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,

                'total_households' => $this->total_households,
                'male_population' => $this->male_population,
                'female_population' => $this->female_population,
                'area_size_km2' => $this->area_size_km2,
                'population_data' => $this->population_data,
                'wali_korong_id' => $this->wali_korong_id,
                'nagari_id' => $this->nagari_id,
            ]);

            // Upload new images if any
            foreach ($this->imagesToUpload as $image) {
                // Assuming you have a method to handle image uploads

                Image::create($this->korong, $image);
            }

            DB::commit();

            $this->success('Korong berhasil diperbarui!');

            // Refresh data
            $this->korong->refresh();
            $this->loadExistingImages();

            // Clear temporary uploads
            $this->imagesToUpload = [];
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Gagal memperbarui Korong: ' . $e->getMessage());
        }
    }

    /**
     * Load existing images
     */
    private function loadExistingImages()
    {
        $this->existingImages = $this->korong->images
            ->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => Storage::url($image->path),
                    'path' => $image->path,
                ];
            })
            ->toArray();
    }

    /**
     * Remove an existing image
     */
    public function removeExistingImage($imageId)
    {
        try {
            $image = Image::findOrFail($imageId);

            // Check if image belongs to this korong
            if ($image->imageable_id == $this->korong->id && $image->imageable_type == Korong::class) {
                // Delete from storage
                Storage::delete($image->path);

                // Delete from database
                $image->delete();

                // Remove from existing images array
                $this->existingImages = array_filter($this->existingImages, function ($img) use ($imageId) {
                    return $img['id'] != $imageId;
                });

                $this->success('Gambar berhasil dihapus!');
            } else {
                $this->error('Gambar tidak ditemukan!');
            }
        } catch (\Exception $e) {
            $this->error('Gagal menghapus gambar: ' . $e->getMessage());
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
     * Cancel and go back
     */
    public function cancel()
    {
        return redirect()->route('korong.index');
    }

    /**
     * Add population data item
     */
    public function addPopulationData()
    {
        $this->population_data[] = [
            'age_group' => '',
            'male' => 0,
            'female' => 0,
            'total' => 0,
        ];
    }

    /**
     * Remove population data item
     */
    public function removePopulationData($index)
    {
        unset($this->population_data[$index]);
        $this->population_data = array_values($this->population_data);
    }

    /**
     * Update population data total
     */
    public function updatedPopulationData($value, $key)
    {
        $keys = explode('.', $key);
        if (count($keys) >= 3) {
            $index = $keys[0];
            $field = $keys[1];

            if ($field === 'male' || $field === 'female') {
                $male = $this->population_data[$index]['male'] ?? 0;
                $female = $this->population_data[$index]['female'] ?? 0;
                $this->population_data[$index]['total'] = $male + $female;
            }
        }
    }
};
?>

<div>
    <x-card shadow separator class="mb-6">
        <x-slot:title>
            <div class="flex items-center gap-4">
                <x-button icon="o-arrow-left" link="{{ route('korong.index') }}" class="btn-ghost btn-sm" />
                <div>
                    <h2 class="text-xl font-bold">Edit Korong: {{ $korong->name }}</h2>
                    <p class="text-sm text-gray-500">ID: {{ $korong->id }}</p>
                </div>
            </div>
        </x-slot:title>

        <form wire:submit="update" class="space-y-6">
            {{-- Basic Information --}}
            <x-card title="Informasi Dasar" shadow separator>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input label="Nama Korong" wire:model="name" required />

                    <x-input label="Telepon" wire:model="phone" />

                    <x-input label="Email" wire:model="email" type="email" />

                    <x-textarea label="Alamat" wire:model="address" rows="3" required />

                    <x-textarea label="Deskripsi" wire:model="description" rows="3" />

                    <x-select required label="Nagari" wire:model="nagari_id" :options="$this->availableNagaris" option-label="name"
                        option-value="id" placeholder="Pilih Nagari" disabled />

                    <x-select label="Wali Korong" wire:model="wali_korong_id" :options="$this->availableWaliKorongs" option-label="name"
                        option-value="id" placeholder="Pilih Wali Korong" searchable disabled />
                </div>
            </x-card>

            {{-- Coordinates --}}
            <x-card title="Koordinat" shadow separator>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input label="Latitude" wire:model="latitude" type="number" step="any" />
                    <x-input label="Longitude" wire:model="longitude" type="number" step="any" />
                </div>
            </x-card>

            {{-- Population Data --}}
            <x-card title="Data Kependudukan" shadow separator>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                    <x-input label="Total Rumah Tangga" wire:model="total_households" type="number" min="0" />
                    <x-input label="Penduduk Laki-laki" wire:model="male_population" type="number" min="0" />
                    <x-input label="Penduduk Perempuan" wire:model="female_population" type="number" min="0" />
                    <x-input label="Luas Wilayah (km²)" wire:model="area_size_km2" type="number" step="0.01"
                        min="0" />
                </div>

                {{-- Population Data by Age Group --}}
                <div class="mt-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold">Data Penduduk Berdasarkan Kelompok Umur</h3>
                        <x-button type="button" wire:click="addPopulationData" icon="o-plus" label="Tambah Kelompok"
                            class="btn-sm" />
                    </div>

                    @foreach ($population_data as $index => $data)
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4 p-4 border rounded-lg">
                            <x-input label="Kelompok Umur" wire:model="population_data.{{ $index }}.age_group"
                                placeholder="Contoh: 0-4 tahun" />
                            <x-input label="Laki-laki" wire:model="population_data.{{ $index }}.male"
                                type="number" min="0" />
                            <x-input label="Perempuan" wire:model="population_data.{{ $index }}.female"
                                type="number" min="0" />
                            <x-input label="Total" wire:model="population_data.{{ $index }}.total"
                                type="number" min="0" disabled />
                            <div class="flex items-end">
                                <x-button type="button" wire:click="removePopulationData({{ $index }})"
                                    icon="o-trash" class="btn-error btn-sm" />
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>

            {{-- Existing Images --}}
            @if (count($existingImages) > 0)
                <x-card title="Gambar Saat Ini" shadow separator>
                    <p class="mb-4 text-sm text-gray-600">Klik ikon X untuk menghapus gambar</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach ($existingImages as $image)
                            <div class="relative">
                                <img src="{{ $image['path'] }}" alt="Gambar Korong"
                                    class="w-full h-32 object-cover rounded-lg border">
                                <div class="absolute top-2 right-2 ">
                                    <x-button icon="o-trash" class="bg-black"
                                        wire:click="removeExistingImage({{ $image['id'] }})"
                                        class="btn-circle btn-black" spinner tooltip="Delete Image" />
                                </div>


                            </div>
                        @endforeach
                    </div>
                </x-card>
            @endif

            {{-- New Image Upload --}}
            <x-card title="Tambah Gambar Baru" shadow separator>
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
                <x-button type="button" wire:click="cancel" class="btn-ghost" label="Batal" />
                <x-button type="submit" icon="o-check" spinner class="btn-primary" label="Simpan Perubahan" />
            </div>
        </form>
    </x-card>
</div>
