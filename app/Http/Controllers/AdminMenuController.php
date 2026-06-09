<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminMenuController extends Controller
{
    /**
     * Display a listing of menus.
     */
    public function index()
    {
        $menus = Menu::orderBy('name')->get();
        return view('admin.menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new menu.
     */
    public function create()
    {
        return view('admin.menus.create');
    }

    /**
     * Store a newly created menu.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'is_available' => 'boolean',
            'is_spicy_variant_enabled' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menus', 'public');
        }

        Menu::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'is_available' => $request->has('is_available') && $request->stock > 0,
            'is_spicy_variant_enabled' => $request->has('is_spicy_variant_enabled'),
            'image_path' => $imagePath,
        ]);

        \App\Models\Setting::touchMenuVersion();

        return redirect()->route('menus.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified menu.
     */
    public function edit(Menu $menu)
    {
        return view('admin.menus.edit', compact('menu'));
    }

    /**
     * Update the specified menu.
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = $menu->image_path;
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            $imagePath = $request->file('image')->store('menus', 'public');
        }

        $menu->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'is_available' => $request->has('is_available') && $request->stock > 0,
            'is_spicy_variant_enabled' => $request->has('is_spicy_variant_enabled'),
            'image_path' => $imagePath,
        ]);

        \App\Models\Setting::touchMenuVersion();

        return redirect()->route('menus.index')->with('success', 'Menu berhasil diperbarui.');
    }

    /**
     * Remove the specified menu.
     */
    public function destroy(Menu $menu)
    {
        if ($menu->image_path && File::exists(public_path($menu->image_path))) {
            File::delete(public_path($menu->image_path));
        }

        $menu->delete();

        \App\Models\Setting::touchMenuVersion();

        return redirect()->route('menus.index')->with('success', 'Menu berhasil dihapus.');
    }

    /**
     * Quick toggle menu stock availability.
     */
    public function toggleStock(Menu $menu)
    {
        $menu->update([
            'is_available' => !$menu->is_available
        ]);

        \App\Models\Setting::touchMenuVersion();

        return back()->with('success', 'Ketersediaan stok menu ' . $menu->name . ' diperbarui.');
    }
}
