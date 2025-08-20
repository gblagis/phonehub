<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Listing;
use Illuminate\Support\Facades\Storage;

class ListingFactory extends Factory
{
    protected $model = Listing::class;

    public function definition(): array
    {
        $brand = fake()->randomElement(['Apple','Samsung','Xiaomi','Google','OnePlus']);
        $models = [
            'Apple'=>['Iphone 13','Iphone 13 Pro','Iphone 14','Iphone 15','Iphone 15 Pro'],
            'Samsung'=>['Galaxy S22','Galaxy S23','Galaxy S24','Galaxy A54'],
            'Xiaomi'=>['13','14','Redmi Note 12'],
            'Google'=>['Pixel 7','Pixel 8','Pixel 8a'],
            'OnePlus'=>['10','11','12']
        ];
        $model = fake()->randomElement($models[$brand]);

        $raw = '69' . str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
        $phonePretty = '+30 ' . substr($raw, 0, 2) . ' ' . substr($raw, 2, 4) . ' ' . substr($raw, 6, 4);

        return [
            'user_id' => 1,
            'title' => "$brand $model",
            'brand' => $brand,
            'model' => $model,
            'year'  => fake()->numberBetween(2018, 2025),
            'price' => fake()->randomFloat(2, 80, 1600),
            'os' => $brand === 'Apple' ? 'iOS' : 'Android',
            'condition' => fake()->randomElement(['New','Like New','Good','Fair']),
            'color' => fake()->safeColorName(),
            'city'  => fake()->city(),
            'description' => fake()->paragraph(3),
            'contact_phone' => $phonePretty,
            'contact_email' => fake()->safeEmail(),
            'published_at' => now(),
            'status' => 'active',
        ];
    }
    public function configure(): static
{
    return $this->afterCreating(function (\App\Models\Listing $listing) {
        // μάζεψε όλα τα fixtures
        $fixtures = glob(database_path('seeders/fixtures/phones/*.{jpg,jpeg,png,webp}'), GLOB_BRACE);
        if (!$fixtures) return;

        shuffle($fixtures);
        $chosen = array_slice($fixtures, 0, random_int(1, 3)); // βάλε 1–3 εικόνες

        // από ποιο ordering ξεκινάμε
        $startOrder = (int) $listing->images()->max('ordering');
        $order = $startOrder >= 0 ? $startOrder + 1 : 0;

        $hasPrimary = $listing->primaryImage()->exists();

        foreach ($chosen as $i => $srcPath) {
            if (!is_file($srcPath)) continue;
            $ext = pathinfo($srcPath, PATHINFO_EXTENSION) ?: 'jpg';
            $filename = uniqid().'.'.$ext;
            $dest = "listings/{$listing->id}/{$filename}";

            // αντιγραφή στο public disk
            $bin = @file_get_contents($srcPath);
            if ($bin === false) continue;
            Storage::disk('public')->put($dest, $bin);

            // δημιούργησε ListingImage
            $listing->images()->create([
                'path'       => $dest,
                'ordering'   => $order + $i,
                'is_primary' => (!$hasPrimary && $i === 0),
            ]);
        }
    });
}


}
