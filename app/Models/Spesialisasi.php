<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Spesialisasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_hewan'
    ];

    public function vets(): BelongsToMany
    {
        return $this->belongsToMany(vet::class, 'spesialisasi_vets');
    }

}
