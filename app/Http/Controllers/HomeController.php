<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Partner;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $partners   = Partner::all();
        $categories = Category::withCount('events')->get();
        $events     = Event::with('category')->latest()->get();

        return view('welcome', compact('partners', 'categories', 'events'));
    }

    public function category($slug)
    {
        $category   = Category::where('slug', $slug)->firstOrFail();
        $categories = Category::withCount('events')->get();
        $events     = Event::with('category')
                        ->where('category_id', $category->id)
                        ->latest()
                        ->get();

        return view('category', compact('category', 'categories', 'events'));
    }
    public function categories()
{
    $categories = Category::withCount('events')->get();
    return view('categories', compact('categories'));
}
}