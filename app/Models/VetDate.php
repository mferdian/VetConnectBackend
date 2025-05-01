<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VetDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'vet_id',
        'tanggal'
    ];

    protected $casts = ['tanggal' => 'date'];

    public function vet(): BelongsTo
    {
        return $this->belongsTo(Vet::class);
    }

    public function vetTimes(): HasMany
    {
        return $this->hasMany(VetTime::class);
    }
}
