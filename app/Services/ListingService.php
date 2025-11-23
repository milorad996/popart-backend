<?php

namespace App\Services;

use App\Models\Listing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ListingService
{
    public function create(array $data, $images = null): Listing
    {
        DB::beginTransaction();

        try {
            $listing = Listing::create($data);

            if ($images) {
                foreach ($images as $index => $image) {
                    $path = $image->store('listings', 'public');

                    $listing->images()->create([
                        'path' => $path,
                        'alt' => $listing->title,
                        'is_main' => $index === 0,
                        'position' => $index,
                    ]);
                }
            }

            DB::commit();

            return $listing->load('images');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(Listing $listing, array $data, $images = null): Listing
    {
        DB::beginTransaction();

        try {
            $listing->update($data);

            if ($images) {
                // Obriši stare slike
                foreach ($listing->images as $img) {
                    Storage::disk('public')->delete($img->path);
                    $img->delete();
                }

                // Dodaj nove slike
                foreach ($images as $index => $image) {
                    $path = $image->store('listings', 'public');

                    $listing->images()->create([
                        'path' => $path,
                        'alt' => $listing->title,
                        'is_main' => $index === 0,
                        'position' => $index,
                    ]);
                }
            }

            DB::commit();

            return $listing->load('images');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(Listing $listing): void
    {
        DB::transaction(function () use ($listing) {
            foreach ($listing->images as $img) {
                Storage::disk('public')->delete($img->path);
            }

            $listing->delete();
        });
    }

    public function deleteImage(Listing $listing, $imageId): void
    {
        $image = $listing->images()->findOrFail($imageId);
        Storage::disk('public')->delete($image->path);
        $image->delete();
    }
}
