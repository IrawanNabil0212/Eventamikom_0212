<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index(Request $request)
{
    $query = Event::with('category')->latest();

    if ($request->filled('search')) {
        $query->where('title', 'LIKE', '%' . $request->search . '%');
    }

    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    $events     = $query->get();
    $categories = Category::all();

    return view('admin.events.index', compact('events', 'categories'));
}

    public function create()
    {
        $categories = Category::all();
        return view('admin.events.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title'       => 'required|string|max:255',
            'description' => 'required',
            'date'        => 'required|date',
            'location'    => 'required',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|numeric|min:0',
            'poster'      => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('poster')) {
            $data['poster_path'] = $request->file('poster')->store('posters', 'public');
        }

        unset($data['poster']);
        Event::create($data);

        return redirect()->route('admin.events.index')->with('success', 'Event berhasil dibuat.');
    }

    public function show(Event $event)
    {
        return redirect()->route('events.show', $event->id);
    }

    public function edit(Event $event)
    {
        $categories = Category::all();
        return view('admin.events.edit', compact('event', 'categories'));
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title'       => 'required|string|max:255',
            'description' => 'required',
            'date'        => 'required|date',
            'location'    => 'required',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|numeric|min:0',
            'poster'      => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('poster')) {
        // Hapus gambar lama jika sebelumnya sudah memiliki poster
        if ($event->poster_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($event->poster_path);
        }
        // Upload gambar baru
        $data['poster_path'] = $request->file('poster')->store('posters', 'public');
    }

    $event->update($data);
    return redirect()->route('admin.events.index')->with('success', 'Event berhasil diperbarui.');
}


    public function destroy(Event $event)
    {
        if ($event->poster_path) Storage::disk('public')->delete($event->poster_path);
        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Event berhasil dihapus.');
    }
}