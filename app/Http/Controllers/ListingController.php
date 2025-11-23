<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreListingRequest;
use App\Http\Requests\UpdateListingRequest;
use App\Models\Category;
use App\Models\Listing;
use App\Services\ListingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListingController extends Controller
{
    protected $listingService;

    public function __construct(ListingService $listingService)
    {
        $this->listingService = $listingService;
    }

    public function index()
    {
        $listings = Listing::with(['category', 'images', 'user'])->paginate(10);
        return response()->json($listings);
    }

    public function store(StoreListingRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        $images = $request->file('images');

        try {
            $listing = $this->listingService->create($validated, $images);

            return response()->json([
                'message' => 'Listing je uspešno kreiran',
                'listing' => $listing,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Došlo je do greške prilikom kreiranja listinga.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $listing = Listing::with(['category', 'images', 'user'])->findOrFail($id);
        return response()->json($listing);
    }


    public function update(UpdateListingRequest $request, $id)
    {
        $listing = Listing::with('images')->findOrFail($id);

        if ($listing->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validated();
        $images = $request->file('images');

        try {
            $listing = $this->listingService->update($listing, $validated, $images);

            return response()->json($listing);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Došlo je do greške prilikom ažuriranja listinga.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        $listing = Listing::with('images')->findOrFail($id);

        if ($listing->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $this->listingService->delete($listing);

            return response()->json(['message' => 'Listing deleted']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Došlo je do greške prilikom brisanja listinga.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteImage($listingId, $imageId)
    {
        $listing = Listing::findOrFail($listingId);

        if ($listing->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $this->listingService->deleteImage($listing, $imageId);

            return response()->json(['message' => 'Image deleted']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Došlo je do greške prilikom brisanja slike.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function filter(Request $request)
    {
        $query = Listing::with(['category', 'images', 'user']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->input('location') . '%');
        }

        if ($request->filled('category_id')) {
            $categoryId = $request->input('category_id');

            $category = Category::with('children')->find($categoryId);

            if ($category) {
                $categoryIds = $category->children->pluck('id')->push($category->id);

                $query->whereIn('category_id', $categoryIds);
            } else {
                $query->where('category_id', $categoryId);
            }
        }

        $query->orderBy('created_at', 'desc');

        $perPage = 10;
        $listings = $query->paginate($perPage);

        return response()->json($listings);
    }

    public function listingsByCategory($id)
    {
        $categoryIds = Category::where('id', $id)
            ->orWhere('parent_id', $id)
            ->pluck('id');

        $listings = Listing::with(['category', 'images', 'user'])
            ->whereIn('category_id', $categoryIds)
            ->paginate(10);

        return response()->json($listings);
    }
}
