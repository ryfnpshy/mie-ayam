<?php

namespace App\Http\Controllers;

use App\Models\AddOn;
use Illuminate\Http\Request;

class AdminAddonController extends Controller
{
    /**
     * Display a listing of add-ons (toppings).
     */
    public function index()
    {
        $addons = AddOn::all();
        return view('admin.addons.index', compact('addons'));
    }

    /**
     * Store a newly created add-on in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        AddOn::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'is_available' => true,
        ]);

        \App\Models\Setting::touchMenuVersion();

        return redirect()->route('addons.index')->with('success', 'Topping baru berhasil ditambahkan.');
    }

    /**
     * Update the specified add-on in storage.
     */
    public function update(Request $request, AddOn $addon)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        $addon->update([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'is_available' => $request->has('is_available'),
        ]);

        \App\Models\Setting::touchMenuVersion();

        return redirect()->route('addons.index')->with('success', 'Topping ' . $addon->name . ' berhasil diperbarui.');
    }

    /**
     * Remove the specified add-on from storage.
     */
    public function destroy(AddOn $addon)
    {
        $addon->delete();

        \App\Models\Setting::touchMenuVersion();

        return redirect()->route('addons.index')->with('success', 'Topping berhasil dihapus.');
    }
}
