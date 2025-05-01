<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vet extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'email',
        'no_telp',
        'alamat',
        'STR',
        'SIP',
        'jenis_kelamin',
        'foto',
        'tgl_lahir',
        'deskripsi',
        'harga',
    ];

    protected $casts = [
        'jenis_kelamin' => 'boolean',
        'tgl_lahir' => 'date'
    ];


    public function spesialisasis(): BelongsToMany
    {
        return $this->belongsToMany(Spesialisasi::class, 'spesialisasi_vets');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function vetDates(): HasMany
    {
        return $this->hasMany(VetDate::class);
    }

    public function vetTimes(): HasMany
    {
        return $this->hasMany(VetTime::class);
    }

    public function vetReviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
