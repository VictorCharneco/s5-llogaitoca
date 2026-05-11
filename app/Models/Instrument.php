<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Instrument extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'status',
        'image_path',
    ];

    public function getImageUrl(): ?string
    {
        if (!$this->image_path) return null;
        if (str_starts_with($this->image_path, 'demo/')) return url ($this->image_path);
        if (str_starts_with($this->image_path, 'http')) return $this->image_path;

        return url(Storage::url($this->image_path));
    }

    public function isStoreImage(): bool
    {
        return !empty($this->image_path)
            && !str_starts_with($this->image_path, 'demo/')
            && !str_starts_with($this->image_path, 'http');
    }
    
}
