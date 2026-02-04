<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;
use \Illuminate\Http\UploadedFile;

class Image extends Model
{
    protected $fillable = [
        'image_url',
        'image_alt',
        'image_title',
        'path',
        'imageable_id',
        'imageable_type'
    ];


    public function updateFile($newFile, array $attributes = []): bool
    {
        // Delete old file
        $this->deleteFile();

        // Create new file
        $filename = time() . '_' . uniqid() . '.' . $newFile->getClientOriginalExtension();
        $directory = dirname($this->path) ?: 'images';
        $path = $newFile->storeAs($directory, $filename, 'public');

        // Update attributes
        $this->update(array_merge([
            'image_url' => Storage::url($path),
            'image_alt' => pathinfo($newFile->getClientOriginalName(), PATHINFO_FILENAME),
            'image_title' => $filename,
            'path' => $path,
        ], $attributes));

        return true;
    }
    public static function create(Model $model, UploadedFile $file): ?Image
    {

        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $directory = strtolower(class_basename($model)) . 's/' . $model->id;
        $path = $file->storeAs($directory, $filename, 'public');

        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \Exception('Invalid file type. Only images are allowed.');
        }
        $defaultAttributes = [
            'image_url' => Storage::url($path),
            'image_alt' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'image_title' => $filename,
            'path' => $path,
        ];

        $image = new self($defaultAttributes);
        $image->imageable()->associate($model);
        $image->save();
        return $image;
    }


    public function getImageUrlAttribute($value)
    {
        if ($this->path && Storage::disk('public')->exists($this->path)) {
            return Storage::url($this->path);
        }
        return $value;
    }

    protected static function booted(): void
    {
        // Delete file when model is being deleted
        static::deleting(function (self $image) {
            $image->deleteFile();
        });

        // Log after deletion
        static::deleted(function (self $image) {
            Log::info("Image record deleted from database: ID {$image->id}");
        });

        // Log creation
        static::created(function (self $image) {
            Log::info("Image created: ID {$image->id}, Path: {$image->path}");
        });
    }
    public function deleteFile(): ?string
    {
        if (!$this->path) {
            return "Tidak ada jalan";
        }

        try {
            if (!Storage::disk('public')->exists($this->path)) {
                // File doesn't exist - log and return success message
                Log::warning("File tidak ditemukan: {$this->path}");
                return null; // Or return "File tidak ditemukan" if you want message
            }

            $deleted = Storage::disk('public')->delete($this->path);

            if ($deleted) {
                Log::info("Image file deleted: {$this->path}");
                return null; // Success
            } else {
                Log::error("Gagal menghapus file (delete() returned false): {$this->path}");
                return "Gagal menghapus file dari storage";
            }
        } catch (Exception $e) {
            Log::error("Failed to delete image file {$this->path}: " . $e->getMessage());
            return $e->getMessage();
        }
    }

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
