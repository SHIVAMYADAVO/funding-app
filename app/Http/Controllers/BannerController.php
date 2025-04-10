<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BannerController extends Controller {
    public function index() {
        $banners = Banner::all();
        return view('banners.index', compact('banners'));
    }

    public function create() {
        return view('banners.create');
    }

    public function store(Request $request) {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('uploads/banners'), $imageName);
        }

        Banner::create([
            'image' => $imageName
        ]);

        return redirect()->route('banners.index')->with('success', 'Banner added successfully.');
    }

    public function edit(Banner $Banner) {
        return view('banners.edit', compact('Banner'));
    }

    public function update(Request $request, Banner $Banner) {
        $request->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($Banner->image) {
                File::delete(public_path('uploads/banners/' . $Banner->image));
            }
            
            // Upload new image
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('uploads/banners'), $imageName);
            $Banner->image = $imageName;
        }

        $Banner->save();

        return redirect()->route('banners.index')->with('success', 'Banner updated successfully.');
    }

    public function destroy(Banner $Banner) {
        if ($Banner->image) {
            File::delete(public_path('uploads/banners/' . $Banner->image));
        }

        $Banner->delete();
        return redirect()->route('banners.index')->with('success', 'Banner deleted successfully.');
    }
}
