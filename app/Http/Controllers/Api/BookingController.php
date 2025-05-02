<?php

namespace App\Http\Controllers;

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
    // Display the details of the vet and available time slots
    public function bookingDetail($id)
    {
        $vet = Vet::with(['vetReviews', 'vetDates.vetTimes'])->findOrFail($id);

        // Generate dates if vet's dates are empty
        if ($vet->vetDates->isEmpty()) {
            $this->generateAvailableDates($vet->id);
            $vet = Vet::with(['vetReviews', 'vetDates.vetTimes'])->findOrFail($id);
        }

        return response()->json(['vet' => $vet]);
    }

    // Generate available dates and times
    private function generateAvailableDates($vetId)
    {
        $startDate = Carbon::today();

        for ($i = 0; $i < 14; $i++) {
            $date = $startDate->copy()->addDays($i);

            // Skip Sundays
            if ($date->dayOfWeek === Carbon::SUNDAY) {
                continue;
            }

            $vetDate = \App\Models\VetDate::create([
                'vet_id' => $vetId,
                'tanggal' => $date
            ]);

            for ($hour = 8; $hour < 17; $hour++) {
                \App\Models\VetTime::create([
                    'vet_date_id' => $vetDate->id,
                    'jam' => sprintf('%02d:00', $hour)
                ]);
            }
        }
    }

    // Generate unique order ID
    public static function generateUniqueOrderId(): string
    {
        $prefix = 'ORDER-';

        do {
            $randomString = $prefix . mt_rand(100000, 999999); // Random 6 digits
        } while (Booking::where('order_id', $randomString)->exists());

        return $randomString;
    }

    // Store a new booking and return Snap Token for payment
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'vet_id' => 'required|exists:vets,id',
    //         'vet_date_id' => 'required|exists:vet_dates,id',
    //         'vet_time_id' => 'required|exists:vet_times,id',
    //         'keluhan' => 'required|string',
    //         'harga' => 'required|numeric',
    //     ]);

    //     // Generate order ID
    //     $orderId = self::generateUniqueOrderId();

    //     // Store booking data in the database
    //     $booking = Booking::create([
    //         'user_id' => Auth::id(),
    //         'vet_id' => $request->vet_id,
    //         'vet_date_id' => $request->vet_date_id,
    //         'vet_time_id' => $request->vet_time_id,
    //         'keluhan' => $request->keluhan,
    //         'total_harga' => $request->harga,
    //         'status' => 'pending',
    //         'status_bayar' => 'pending',
    //         'metode_pembayaran' => 'transfer_bank',
    //         'order_id' => $orderId,
    //     ]);

    //     // Midtrans setup
    //     Config::$serverKey = config('midtrans.serverKey');
    //     Config::$isProduction = config('midtrans.isProduction');
    //     Config::$isSanitized = true;
    //     Config::$is3ds = true;

    //     // Prepare Midtrans Snap Token
    //     $params = [
    //         'transaction_details' => [
    //             'order_id' => $orderId,
    //             'gross_amount' => $request->harga,
    //         ],
    //         'customer_details' => [
    //             'first_name' => Auth::user()->name,
    //             'email' => Auth::user()->email,
    //         ],
    //     ];

    //     $snapToken = Snap::getSnapToken($params);

    //     // Return Snap Token with booking details
    //     return response()->json([
    //         'message' => 'Booking data saved successfully.',
    //         'booking' => $booking,
    //         'snap_token' => $snapToken,
    //     ]);
    // }

    // Get available times for a specific date
    public function getTimes($vetDateId)
    {
        $vetTimes = VetTime::where('vet_date_id', $vetDateId)->get();
        return response()->json($vetTimes);
    }

    // Show booking details and payment page
    // public function show($vetId)
    // {
    //     $vet = Vet::findOrFail($vetId);

    //     $bookingData = session('booking_data');

    //     if (!$bookingData) {
    //         return response()->json(['error' => 'Booking data not found.'], 404);
    //     }

    //     // Midtrans setup
    //     Config::$serverKey = config('midtrans.serverKey');
    //     Config::$isProduction = config('midtrans.isProduction');
    //     Config::$isSanitized = true;
    //     Config::$is3ds = true;

    //     // Generate Snap Token if not available
    //     if (empty($bookingData['snap_token'])) {
    //         $params = [
    //             'transaction_details' => [
    //                 'order_id' => $bookingData['order_id'],
    //                 'gross_amount' => $bookingData['total_harga'],
    //             ],
    //             'customer_details' => [
    //                 'first_name' => Auth::user()->name,
    //                 'email' => Auth::user()->email,
    //             ],
    //         ];

    //         $snapToken = Snap::getSnapToken($params);
    //         $bookingData['snap_token'] = $snapToken;
    //     }

    //     return response()->json([
    //         'vet' => $vet,
    //         'snap_token' => $bookingData['snap_token'],
    //     ]);
    // }

    // Confirm the payment after Midtrans response
    public function confirmPayment(Request $request)
    {
        $paymentStatus = $request->input('status');
        $bookingData = session('booking_data');

        if ($paymentStatus === 'berhasil' && $bookingData) {
            Booking::create(array_merge($bookingData, [
                'status' => 'confirmed',
                'status_bayar' => 'berhasil',
            ]));

            session()->forget('booking_data');

            // Return successful response
            return response()->json(['message' => 'Booking confirmed successfully!']);
        }

        return response()->json(['error' => 'Payment failed, please try again.'], 400);
    }

    // Create a review for the booking
    public function create(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $existingReview = Review::where('booking_id', $booking->id)->first();
        if ($existingReview) {
            return response()->json(['error' => 'You have already reviewed this booking.'], 400);
        }

        return response()->json(['message' => 'Review creation allowed.']);
    }

    // Store the review for the booking
    public function make_review(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        Review::create([
            'user_id' => Auth::id(),
            'vet_id' => $booking->vet_id,
            'booking_id' => $booking->id,
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return response()->json(['message' => 'Review submitted successfully!']);
    }

    // Get the booking history of the authenticated user
    public function history()
    {
        $paymentHistory = Booking::where('user_id', Auth::id())
            ->whereNotNull('status_bayar')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($paymentHistory);
    }
}
