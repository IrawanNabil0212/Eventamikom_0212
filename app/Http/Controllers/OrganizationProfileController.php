<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Review;

class OrganizationProfileController extends Controller
{
    /**
     * Halaman profil publik penyelenggara - inilah "rekam jejak
     * penilaian" yang dimaksud di spesifikasi tugas, untuk menarik
     * kepercayaan calon pembeli sebelum beli tiket event berikutnya
     * dari organizer yang sama.
     */
    public function show(string $slug)
    {
        $organization = Organization::where('slug', $slug)
            ->where('status', 'approved') // organizer belum di-approve tidak punya halaman publik
            ->firstOrFail();

        // Event:: otomatis TIDAK ke-filter Global Scope di sini karena
        // halaman ini route publik (bukan prefix "organizer."), jadi aman
        // dipakai walau yang buka kebetulan sedang login sebagai organizer lain.
        $events = $organization->events()
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->latest('date')
            ->paginate(9);

        $overallAverageRating = Review::whereHas('event', function ($q) use ($organization) {
                $q->where('organization_id', $organization->id);
            })->avg('rating');

        $overallReviewCount = Review::whereHas('event', function ($q) use ($organization) {
                $q->where('organization_id', $organization->id);
            })->count();

        $recentReviews = Review::whereHas('event', function ($q) use ($organization) {
                $q->where('organization_id', $organization->id);
            })
            ->with(['user', 'event'])
            ->latest()
            ->take(6)
            ->get();

        return view('organizations.profile', compact(
            'organization',
            'events',
            'overallAverageRating',
            'overallReviewCount',
            'recentReviews'
        ));
    }
}