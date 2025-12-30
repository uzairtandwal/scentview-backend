<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Storage library add ki hai

class BannerController extends Controller
{
    public function index()
    {
        return Banner::where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();
    }

    public function store(Request $request)
    {
        // 1. Validation Change: 'image_url' required nahi hai ab.
        // Sirf 'image' (File) required hai.
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'target_screen' => ['nullable', 'string', 'max:255'],
            'target_id' => ['nullable', 'string', 'max:255'],
            'image' => ['required', 'image', 'max:10240'], // 10MB Max file size
            'is_active' => ['boolean'],
            'sort_order' => ['integer'],
        ]);

        $data = $request->all();

        // 2. Logic: File upload karo aur Link banao
        if ($request->hasFile('image')) {
            // File ko 'uploads' folder mein save karein
            $path = $request->file('image')->store('uploads', 'public');
            
            // Pura URL generate karein (http://domain.com/storage/uploads/filename.jpg)
            $realUrl = url('storage/' . $path);
            
            // Database mein ye Pura Link save karein
            $data['image_url'] = $realUrl;
        }

        $banner = Banner::create($data);
        return response()->json($banner, 201);
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'target_screen' => ['nullable', 'string', 'max:255'],
            'target_id' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:10240'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer'],
        ]);

        $data = $request->all();

        // Update ke waqt agar nayi image ayi hai
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads', 'public');
            $realUrl = url('storage/' . $path);
            $data['image_url'] = $realUrl;
        } else {
            // Agar image nahi ayi, to purana link rehne do (overwrite mat karo)
            unset($data['image_url']);
        }

        $banner->update($data);
        return response()->json($banner);
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();
        return response()->json(['status' => 'ok']);
    }
}