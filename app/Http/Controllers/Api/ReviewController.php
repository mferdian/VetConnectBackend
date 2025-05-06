<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // Menyimpan Review Baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|max:500',
        ]);

        $booking = Booking::with('vet')->findOrFail($validated['booking_id']);

        // Cek agar user hanya bisa review booking miliknya
        if ($booking->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke janji temu ini.'
            ], 403);
        }

        // Cek agar hanya bisa review booking yang belum direview dan sudah dikonfirmasi
        if ($booking->status !== 'confirmed' || $booking->review) {
            return response()->json([
                'message' => 'Janji temu ini tidak bisa direview.'
            ], 422);
        }

        $review = Review::create([
            'user_id' => $request->user()->id,
            'vet_id' => $booking->vet_id,
            'booking_id' => $booking->id,
            'rating' => $validated['rating'],
            'review' => $validated['review'],
        ]);

        return response()->json([
            'message' => 'Terima kasih atas review Anda!',
            'data' => $review
        ], 201);
    }

    // Menampilkan 3 Review Terbaik
    public function featured()
    {
        $reviews = Review::with(['user', 'vet'])
            ->orderBy('rating', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return response()->json([
            'reviews' => $reviews
        ]);
    }

    // Daftar Semua Review Milik User yang Login
    public function userReviews(Request $request)
    {
        $reviews = Review::with('vet')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'reviews' => $reviews
        ]);
    }
}
