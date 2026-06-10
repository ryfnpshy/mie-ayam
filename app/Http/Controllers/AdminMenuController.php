<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $this->storeMenuImage($request->file('image'));
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $imagePath = $menu->image_path;
        if ($request->hasFile('image')) {
            // Delete old image from its original disk
            if ($imagePath && !str_contains($imagePath, 'default-menu.jpg')) {
                if (str_starts_with($imagePath, 'supabase:')) {
                    $purePath = substr($imagePath, 9);
                    if (!empty($purePath)) {
                        Storage::disk('supabase')->delete($purePath);
                    }
                } elseif (str_starts_with($imagePath, 'local:')) {
                    $purePath = substr($imagePath, 6);
                    if (!empty($purePath)) {
                        Storage::disk('public')->delete($purePath);
                    }
                } else {
                    // Legacy path without prefix
                    Storage::disk(Menu::storageDisk())->delete($imagePath);
                }
            }

            $imagePath = $this->storeMenuImage($request->file('image'));
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
        if ($menu->image_path && !str_contains($menu->image_path, 'default-menu.jpg')) {
            if (str_starts_with($menu->image_path, 'supabase:')) {
                $purePath = substr($menu->image_path, 9);
                if (!empty($purePath)) {
                    Storage::disk('supabase')->delete($purePath);
                }
            } elseif (str_starts_with($menu->image_path, 'local:')) {
                $purePath = substr($menu->image_path, 6);
                if (!empty($purePath)) {
                    Storage::disk('public')->delete($purePath);
                }
            } else {
                Storage::disk(Menu::storageDisk())->delete($menu->image_path);
            }
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

    private function storeMenuImage(UploadedFile $image): string
    {
        $disk = Menu::storageDisk();

        try {
            $storedPath = Storage::disk($disk)->putFile('menus', $image, 'public');

            if (!$storedPath) {
                throw new \RuntimeException('Storage returned an empty upload path.');
            }

            Log::info('Menu image uploaded.', [
                'disk' => $disk,
                'bucket' => config("filesystems.disks.{$disk}.bucket"),
                'path' => $storedPath,
                'mime' => $image->getMimeType(),
                'size' => $image->getSize(),
            ]);

            return $disk . ':' . $storedPath;
        } catch (Throwable $exception) {
            Log::error('Menu image upload failed.', [
                'disk' => $disk,
                'bucket' => config("filesystems.disks.{$disk}.bucket"),
                'endpoint' => config("filesystems.disks.{$disk}.endpoint"),
                'mime' => $image->getMimeType(),
                'size' => $image->getSize(),
                'error' => $exception->getMessage(),
            ]);

            return back()
                ->withErrors(['image' => 'Gagal mengunggah gambar ke storage. Periksa konfigurasi Supabase Storage dan coba lagi.'])
                ->withInput()
                ->throwResponse();
        }
    }
}
