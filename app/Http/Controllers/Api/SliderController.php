<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SliderResource;
use App\Models\Slider;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SliderController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get all active sliders for mobile home page
     */
    public function index()
    {
        $sliders = Slider::active()
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'data' => SliderResource::collection($sliders),
        ]);
    }

    /**
     * Get a specific slider
     */
    public function show(Slider $slider)
    {
        return response()->json([
            'success' => true,
            'data' => new SliderResource($slider),
        ]);
    }

    /**
     * Store a new slider (Admin only)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'order' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return $this->handleValidationErrors($validator);
        }

        $data = $request->only(['title', 'order', 'is_active']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = 'slider_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('sliders', $filename, 'public');
            $data['image'] = $path;
        }

        $slider = Slider::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Slider created successfully',
            'data' => new SliderResource($slider),
        ], 201);
    }

    /**
     * Update a slider (Admin only)
     */
    public function update(Request $request, Slider $slider)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'order' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return $this->handleValidationErrors($validator);
        }

        $data = $request->only(['title', 'order', 'is_active']);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($slider->image && file_exists(storage_path('app/public/' . $slider->image))) {
                unlink(storage_path('app/public/' . $slider->image));
            }

            $image = $request->file('image');
            $filename = 'slider_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('sliders', $filename, 'public');
            $data['image'] = $path;
        }

        $slider->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Slider updated successfully',
            'data' => new SliderResource($slider),
        ]);
    }

    /**
     * Delete a slider (Admin only)
     */
    public function destroy(Slider $slider)
    {
        // Delete image file if exists
        if ($slider->image && file_exists(storage_path('app/public/' . $slider->image))) {
            unlink(storage_path('app/public/' . $slider->image));
        }

        $slider->delete();

        return response()->json([
            'success' => true,
            'message' => 'Slider deleted successfully',
        ]);
    }
}
