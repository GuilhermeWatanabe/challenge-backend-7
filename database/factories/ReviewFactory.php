<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'photo' => null,
            'review' => fake()->realTextBetween(20, 150),
            'user_name' => fake()->name
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
