<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Destination>
 */
class DestinationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'photo_1' => null,
            'photo_2' => null,
            'name' => fake()->country(),
            'meta_description' => fake()->text(maxNbChars: 160),
            'description' => fake()->paragraphs(2, true),
            'price' => fake()->randomFloat(2)
        ];
    }

    public function uploadedImage()
    {
        return $this->state(function () {
           return [
               'photo_1' => UploadedFile::fake()->image('photo1.jpg', 100, 100),
               'photo_2' => UploadedFile::fake()->image('photo2.jpg', 200, 200)
           ];
        });
    }
}
