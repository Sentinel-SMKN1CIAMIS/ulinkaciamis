<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\View\View;

class MapController extends Controller
{
    /**
     * Display the main map & discovery page.
     */
    public function index(): View
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('home', compact('categories'));
    }
}
