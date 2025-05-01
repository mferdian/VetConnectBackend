<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'vet_id',
        'vet_date_id',
        'vet_time_id',
        'keluhan',
        'total_harga',
        'status',
        'status_bayar',
        'metode_pembayaran'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vet(): BelongsTo
    {
        return $this->belongsTo(Vet::class);
    }

    public function vetDate(): BelongsTo
    {
        return $this->belongsTo(VetDate::class);
    }

    public function vetTime(): BelongsTo
    {
        return $this->belongsTo(VetTime::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}
