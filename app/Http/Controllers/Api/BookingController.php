<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Midtrans\Snap;
use App\Models\Vet;
use Midtrans\Config;
use App\Models\Review;
use App\Models\Booking;
use App\Models\VetTime;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
   public function store(Request $request)
    {
        $validated = $request->validate([
            'vet_id' => 'required|exists:vets,id',
            'vet_date_id' => 'required|exists:vet_dates,id',
            'vet_time_id' => 'required|exists:vet_times,id',
            'keluhan' => 'nullable|string',
            'total_harga' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:transfer_bank,e-wallet,cash,lainnya',
        ]);

        $booking = Booking::create([
            'order_id' => 'ORD-' . Str::uuid(),
            'user_id' => $request->user()->id,
            'vet_id' => $validated['vet_id'],
            'vet_date_id' => $validated['vet_date_id'],
            'vet_time_id' => $validated['vet_time_id'],
            'keluhan' => $validated['keluhan'] ?? null,
            'total_harga' => $validated['total_harga'],
            'status' => 'confirmed',
            'status_bayar' => 'berhasil',
            'metode_pembayaran' => $validated['metode_pembayaran'],
        ]);

        return response()->json([
            'message' => 'Booking berhasil dibuat',
            'data' => $booking
        ], 201);
    }

    public function index(Request $request)
    {
        $bookings = Booking::with(['vet', 'vetDate', 'vetTime'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($bookings);
    }
}
