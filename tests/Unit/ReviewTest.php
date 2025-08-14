<?php

namespace Tests\Unit;

use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    private string $storagePhotoFolder = 'review_photo/';

    public function test_must_return_3_reviews_in_reviews_home_url()
    {
        Review::factory()->count(10)->create();

        $response = $this->getJson(route('home'));

        $response->assertOk();
        $response->assertJsonCount(3);
    }

    public function test_store_a_review_must_return_201(): void
    {
        $review = Review::factory()->uploadedImage()->make();

        $response = $this->postJson(route('reviews.store'), $review->toArray());

        $response->assertCreated();
        $response->assertJson([
            'photo' => $this->storagePhotoFolder . $review->photo->hashName(),
            'review' => $review->review,
            'user_name' => $review->user_name
        ]);
        $this->assertDatabaseCount('reviews', 1);
        $this->assertDatabaseHas('reviews', [
            'photo' => $this->storagePhotoFolder . $review->photo->hashName(),
            'review' => $review->review,
            'user_name' => $review->user_name
        ]);
    }

    public function test_store_a_empty_review_must_return_error_messages()
    {
        $response = $this->postJson(route('reviews.store'));

        $response->assertStatus(422);
        $response->assertJsonPath('errors.photo.0', 'The photo field is required.');
        $response->assertJsonPath('errors.review.0', 'The review field is required.');
        $response->assertJsonPath('errors.user_name.0', 'The user name field is required.');
        $this->assertDatabaseEmpty('reviews');
    }

    public function test_store_a_file_instead_of_a_photo_must_return_error_message()
    {
        $review = Review::factory()->make();
        $review->photo = UploadedFile::fake()->create('document.pdf');

        $response = $this->postJson(route('reviews.store'), $review->toArray());

        $response->assertStatus(422);
        $response->assertJsonPath('errors.photo.0', 'The photo field must be an image.');
        $this->assertDatabaseEmpty('reviews');
    }

    public function test_store_a_review_with_less_than_10_characters_must_return_error_message()
    {
        $review = Review::factory()->uploadedImage()->make();
        $review->review = fake()->realTextBetween(10, 19);

        $response = $this->postJson(route('reviews.store'), $review->toArray());

        $response->assertStatus(422);
        $response->assertJsonPath('errors.review.0', 'The review field must be at least 20 characters.');
        $this->assertDatabaseEmpty('reviews');
    }

    public function test_store_a_review_with_more_than_150_characters_must_return_error_message()
    {
        $review = Review::factory()->uploadedImage()->make();
        $review->review = fake()->realTextBetween(150, 160);

        $response = $this->postJson(route('reviews.store'), $review->toArray());

        $response->assertStatus(422);
        $response->assertJsonPath('errors.review.0', 'The review field must not be greater than 150 characters.');
        $this->assertDatabaseEmpty('reviews');
    }

    public function test_store_a_user_name_with_less_than_2_characters_must_return_error_message()
    {
        $review = Review::factory()->uploadedImage()->make();
        $review->user_name = 'A';

        $response = $this->postJson(route('reviews.store'), $review->toArray());

        $response->assertStatus(422);
        $response->assertJsonPath('errors.user_name.0', 'The user name field must be at least 2 characters.');
        $this->assertDatabaseEmpty('reviews');
    }

    public function test_show_must_return_a_review()
    {
        $review = Review::factory()->create();

        $response = $this->getJson(route('reviews.show', ['review' => $review->id]));

        $response->assertOk();
        $response->assertJson([
            'photo' => $review->photo,
            'review' => $review->review,
            'user_name' => $review->user_name
        ]);
    }

    public function test_show_must_return_not_found_with_invalid_id()
    {
        $response = $this->getJson(route('reviews.show', ['review' => 99999]));

        $response->assertStatus(404);
        $response->assertJsonPath('message', 'Review not found.');
    }

    public function test_update_must_return_updated_info()
    {
        $id = Review::factory()->create()->id;
        $review = Review::factory()->uploadedImage()->make();

        $response = $this->putJson(route('reviews.update', ['review' => $id]), $review->toArray());

        $response->assertOk();
        $response->assertJson([
            'photo' => $this->storagePhotoFolder . $review->photo->hashName(),
            'review' => $review->review,
            'user_name' => $review->user_name,
        ]);
        $this->assertDatabaseCount('reviews', 1);
        $this->assertDatabaseHas('reviews', [
            'photo' => $this->storagePhotoFolder . $review->photo->hashName(),
            'review' => $review->review,
            'user_name' => $review->user_name,
        ]);
    }

    function test_must_return_not_found_with_invalid_id()
    {
        $response = $this->deleteJson(route('reviews.show', ['review' => 99999]));

        $response->assertStatus(404);
        $response->assertJsonPath('message', 'Review not found.');
    }

    function test_must_delete_a_review()
    {
        $id = Review::factory()->create()->id;

        $response = $this->deleteJson(route('reviews.update', ['review' => $id]));

        $response->assertStatus(204);
        $this->assertDatabaseEmpty('reviews');
    }
}
