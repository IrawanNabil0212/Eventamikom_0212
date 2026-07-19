<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OrganizerEventController extends Controller
{
    /**
     * List event. Query Event:: di sini OTOMATIS ke-filter oleh
     * Global Scope (OrganizationScope) - organizer A tidak akan
     * pernah lihat event milik organizer B, meski tidak ada
     * `where('organization_id', ...)` ditulis manual di sini.
     */
    public function index()
    {
        $events = Event::with('category')->latest()->paginate(10);

        return view('organizer.events.index', compact('events'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('organizer.events.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'poster' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('poster')) {
            $validated['poster_path'] = $request->file('poster')->store('posters', 'public');
        }

        // organization_id diisi OTOMATIS dari organizer yang login -
        // TIDAK dari input form, supaya organizer tidak bisa
        // "titip" event ke organisasi lain lewat manipulasi form.
        $validated['organization_id'] = Auth::user()->organization_id;

        Event::create($validated);

        return redirect()->route('organizer.events.index')
            ->with('success', 'Event berhasil dibuat.');
    }

    /**
     * Route model binding {event} di sini otomatis aman: karena
     * Global Scope, Laravel TIDAK akan pernah menemukan event
     * milik organizer lain lewat URL manapun - otomatis 404
     * kalau organizer coba akses ID event milik organizer lain.
     */
    public function edit(Event $event)
    {
        $categories = Category::all();

        return view('organizer.events.edit', compact('event', 'categories'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'poster' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('poster')) {
            if ($event->poster_path) {
                Storage::disk('public')->delete($event->poster_path);
            }
            $validated['poster_path'] = $request->file('poster')->store('posters', 'public');
        }

        $event->update($validated);

        return redirect()->route('organizer.events.index')
            ->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event)
    {
        if ($event->poster_path) {
            Storage::disk('public')->delete($event->poster_path);
        }

        $event->delete();

        return redirect()->route('organizer.events.index')
            ->with('success', 'Event berhasil dihapus.');
    }
}