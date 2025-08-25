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
        Storage::fake('public');

        $filePath = UploadedFile::fake()->image('photo.jpg')->store('destination_photo', 'public');

        return [
            'photo' => $filePath,
            'name' => fake()->country(),
            'price' => fake()->randomFloat(2)
        ];
    }

    public function uploadedImage()
    {
        return $this->state(function () {
           return [
               'photo' => UploadedFile::fake()->image('photo.jpg')
           ];
        });
    }
}
