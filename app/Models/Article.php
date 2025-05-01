<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'vet_id',
        'judul',
        'isi',
        'gambar'
    ];

    protected $casts = [
        'gambar' => 'array',
    ];

    public function vet():BelongsTo {
        return $this->belongsTo(Vet::class,'vet_id');
    }
}
