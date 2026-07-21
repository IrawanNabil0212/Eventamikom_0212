<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationApprovalController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $organizations = Organization::with('owner')
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10);

        return view('admin.organizations.index', compact('organizations', 'status'));
    }

    public function show(Organization $organization)
    {
        $organization->load('owner', 'events');

        return view('admin.organizations.show', compact('organization'));
    }

    /**
     * Setujui organizer -> status jadi 'approved', langsung bisa
     * login & bikin event.
     */
    public function approve(Organization $organization)
    {
        $organization->update([
            'status' => 'approved',
            'rejection_reason' => null,
        ]);

        return back()->with('success', "Organisasi \"{$organization->name}\" telah disetujui.");
    }

    /**
     * Tolak pendaftaran organizer, dengan alasan (ditampilkan ke
     * organizer di halaman pending mereka).
     */
    public function reject(Request $request, Organization $organization)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $organization->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', "Organisasi \"{$organization->name}\" telah ditolak.");
    }
}