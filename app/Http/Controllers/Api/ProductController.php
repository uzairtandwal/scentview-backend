<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log; // Logging add ki hai

class ProductController extends Controller
{
    public function index()
    {
        return Product::with(['images', 'category'])->orderByDesc('id')->get();
    }
    
    // ... Baki functions (featured, slider, show) same rahein ...
    public function featured() { return Product::with(['images', 'category'])->where('is_featured', true)->orderByDesc('id')->get(); }
    public function slider() { return Product::with(['images', 'category'])->where('is_slider', true)->orderByDesc('id')->get(); }
    public function show(Product $product) { return $product->load(['images', 'category']); }

    public function store(Request $request)
    {
        // 1. Error Catching Shuru
        try {
            // Log karo ke request server tak pohnchi
            file_put_contents(public_path('debug_log.txt'), "Step 1: Request aayi at " . now() . "\n", FILE_APPEND);

            // 2. Validation (Size barha kar 10MB kar diya hai: max:10000)
            $data = $request->validate([
                'category_id' => ['required', 'exists:categories,id'],
                'name' => ['required', 'string', 'max:255'],
                'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug'],
                'description' => ['nullable', 'string'],
                'price' => ['required', 'numeric'],
                'sale_price' => ['nullable', 'numeric'], 
                'stock' => ['nullable', 'integer'],
                'badge_text' => ['nullable', 'string'],
                'is_featured' => ['boolean'],
                'is_slider' => ['boolean'],
                'main_image' => ['nullable', 'image', 'max:10000'], // <--- FIX: Size limit 10MB kar di
                'fragrance_notes' => ['nullable'], 
            ]);

            file_put_contents(public_path('debug_log.txt'), "Step 2: Validation Pass\n", FILE_APPEND);

            // 3. Image Upload
            if ($request->hasFile('main_image')) {
                $path = $request->file('main_image')->store('uploads', 'public');
                $data['main_image_url'] = url('storage/' . $path);
                file_put_contents(public_path('debug_log.txt'), "Step 3: Image Uploaded to $path\n", FILE_APPEND);
            }

            // 4. IMPORTANT FIX: Unset Image File
            if (isset($data['main_image'])) {
                unset($data['main_image']);
            }

            // 5. Slug & Logic
            if (!isset($data['slug']) || empty($data['slug'])) { 
                $data['slug'] = Str::slug($data['name']) . '-' . rand(1000, 9999);
            }
            if (isset($data['sale_price']) && ($data['sale_price'] == 0 || $data['sale_price'] == '')) {
                $data['sale_price'] = null;
            }
            if (!isset($data['stock'])) $data['stock'] = 0;
            
            $data['is_featured'] = $request->input('is_featured', 0) ? true : false;
            $data['is_slider'] = $request->input('is_slider', 0) ? true : false;

            if (isset($data['fragrance_notes'])) {
                if (is_string($data['fragrance_notes'])) {
                    $decoded = json_decode($data['fragrance_notes'], true);
                    $data['fragrance_notes'] = is_array($decoded) ? $decoded : array_map('trim', explode(',', $data['fragrance_notes']));
                }
            } else {
                $data['fragrance_notes'] = [];
            }

            // 6. Create Product
            $product = Product::create($data);
            
            file_put_contents(public_path('debug_log.txt'), "Step 4: Product Created ID: " . $product->id . "\n-----------------\n", FILE_APPEND);
            
            return response()->json($product, 201);

        } catch (\Throwable $e) {
            // 🔴 AGAR ERROR AAYA TO FILE MEIN LIKH DEGA
            $errorMessage = "ERROR: " . $e->getMessage() . "\nLine: " . $e->getLine();
            file_put_contents(public_path('debug_log.txt'), $errorMessage . "\n-----------------\n", FILE_APPEND);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Product $product)
    {
        try {
            $data = $request->validate([
                'category_id' => ['sometimes','exists:categories,id'],
                'name' => ['sometimes','string','max:255'],
                'slug' => ['nullable','string','max:255','unique:products,slug,'.$product->id],
                'description' => ['nullable','string'],
                'price' => ['sometimes','numeric'],
                'sale_price' => ['nullable', 'numeric'],
                'stock' => ['nullable', 'integer'],
                'badge_text' => ['nullable', 'string'],
                'is_featured' => ['boolean'],
                'is_slider' => ['boolean'],
                'main_image' => ['nullable', 'image', 'max:10000'], // <--- FIX: 10MB
                'fragrance_notes' => ['nullable'],
            ]);

            if ($request->hasFile('main_image')) {
                $path = $request->file('main_image')->store('uploads', 'public');
                $data['main_image_url'] = url('storage/' . $path);
            }

            if (isset($data['main_image'])) unset($data['main_image']);

            if (isset($data['name']) && empty($data['slug'])) $data['slug'] = Str::slug($data['name']);
            if (isset($data['sale_price']) && ($data['sale_price'] == 0 || $data['sale_price'] == '')) $data['sale_price'] = null;

            if (isset($data['fragrance_notes']) && is_string($data['fragrance_notes'])) {
                $maybe = json_decode($data['fragrance_notes'], true);
                $data['fragrance_notes'] = is_array($maybe) ? $maybe : array_map('trim', explode(',', $data['fragrance_notes']));
            }

            // Boolean Fix for Update
            if ($request->has('is_featured')) $data['is_featured'] = $request->input('is_featured') ? true : false;
            if ($request->has('is_slider')) $data['is_slider'] = $request->input('is_slider') ? true : false;

            $product->update($data);
            return response()->json($product->load('images'));

        } catch (\Throwable $e) {
            file_put_contents(public_path('debug_log.txt'), "UPDATE ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        return Product::with(['images', 'category'])
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->orderByDesc('id')
            ->get();
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['status' => 'ok']);
    }
}