<?php

namespace Tests\Feature;

use App\Models\Destination;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class DestinationTest extends TestCase
{
    use RefreshDatabase;

    private string $storagePhotoFolder = 'destination_photo/';

    public function text_index_must_return_empty_array_with_code_200()
    {
        $response = $this->getJson(route('destinations.index'));

        $response->assertOk();
        $response->assertJsonCount(0);
        $this->assertDatabaseEmpty('destinations');
    }

    public function test_index_must_return_five_registers()
    {
        $destinations = Destination::factory()->count(5)->create();

        $response = $this->getJson(route('destinations.index'));

        foreach ($destinations as $d) {
            $response->assertJsonFragment([
                'photo' => $d->photo,
                'name' => $d->name,
                'price' => $d->price
            ]);
        }
        $response->assertOk();
        $response->assertJsonCount(5);
        $this->assertDatabaseCount('destinations',5);
    }

    public function test_index_must_one_result_with_query_parameters()
    {
        Destination::factory()->count(5)->create();
        $destination = Destination::factory()->create();

        $response = $this->getJson(route('destinations.index', ['name' => $destination->name]));

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonFragment([
            'photo' => $destination->photo,
            'name' => $destination->name,
            'price' => $destination->price
        ]);
    }

    public function test_index_must_return_not_found_with_query_parameters()
    {
        $response = $this->getJson(route('destinations.index', ['name' => 'notACity']));

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Destination not found.']);
    }

    public function test_store_a_destination_must_return_201()
    {
        $destination = Destination::factory()->uploadedImage()->make();

        $response = $this->postJson(route('destinations.store'), $destination->toArray());

        $response->assertCreated();
        $response->assertJson([
            'photo' => $this->storagePhotoFolder . $destination->photo->hashName(),
            'name' => $destination->name,
            'price' => $destination->price
        ]);
        $this->assertDatabaseCount('destinations', 1);
        $this->assertDatabaseHas('destinations', [
            'photo' => $this->storagePhotoFolder . $destination->photo->hashName(),
            'name' => $destination->name,
            'price' => $destination->price
        ]);
    }

    public function test_store_a_empty_destination_must_return_error_messages()
    {
        $response = $this->postJson(route('destinations.store'));

        $response->assertStatus(422);
        $response->assertJsonPath('errors.photo.0', 'The photo field is required.');
        $response->assertJsonPath('errors.name.0', 'The name field is required.');
        $response->assertJsonPath('errors.price.0', 'The price field is required.');
        $this->assertDatabaseEmpty('destinations');
    }

    public function test_store_a_file_instead_of_a_photo_must_return_error_message()
    {
        $destination = Destination::factory()->make();
        $destination->photo = UploadedFile::fake()->create('document.pdf');

        $response = $this->postJson(route('destinations.store'), $destination->toArray());

        $response->assertStatus(422);
        $response->assertJsonPath('errors.photo.0', 'The photo field must be an image.');
        $this->assertDatabaseEmpty('destinations');
    }

    public function test_store_a_destination_with_a_price_with_more_than_two_decimal_places_must_return_error_message()
    {
        $destination = Destination::factory()->make();
        $destination->price = 12.345;

        $response = $this->postJson(route('destinations.store'), $destination->toArray());

        $response->assertStatus(422);
        $response->assertJsonPath('errors.price.0', 'The price field must have 0-2 decimal places.');
        $this->assertDatabaseEmpty('destinations');
    }

    public function test_store_a_destination_with_a_negative_price_must_return_error_message()
    {
        $destination = Destination::factory()->make();
        $destination->price = -1;

        $response = $this->postJson(route('destinations.store'), $destination->toArray());

        $response->assertStatus(422);
        $response->assertJsonPath('errors.price.0', 'The price field must be at least 1.');
        $this->assertDatabaseEmpty('destinations');
    }

    public function test_show_must_return_a_destination()
    {
        $destination = Destination::factory()->create();

        $response = $this->getJson(route('destinations.show', ['destination' => $destination->id]));

        $response->assertOk();
        $response->assertJson([
            'photo' => $destination->photo,
            'name' => $destination->name,
            'price' => $destination->price
        ]);
    }

    public function test_show_must_return_not_found_with_invalid_id()
    {
        $response = $this->getJson(route('destinations.show', ['destination' => 99999]));

        $response->assertStatus(404);
        $response->assertJsonPath('message', 'Destination not found.');
    }

    public function test_update_must_return_updated_info()
    {
        $id = Destination::factory()->create()->id;
        $destination = Destination::factory()->uploadedImage()->make();

        $response = $this->putJson(route('destinations.update', ['destination' => $id]), $destination->toArray());

        $response->assertOk();
        $response->assertJson([
            'photo' => $this->storagePhotoFolder . $destination->photo->hashName(),
            'name' => $destination->name,
            'price' => $destination->price
        ]);
        $this->assertDatabaseCount('destinations', 1);
        $this->assertDatabaseHas('destinations', [
            'photo' => $this->storagePhotoFolder . $destination->photo->hashName(),
            'name' => $destination->name,
            'price' => $destination->price
        ]);
    }

    public function test_must_return_not_found_with_invalid_id()
    {
        $response = $this->getJson(route('destinations.show', ['destination' => 99999]));

        $response->assertStatus(404);
        $response->assertJsonPath('message', 'Destination not found.');
    }

    public function test_must_delete_a_destination()
    {
        $id = Destination::factory()->create()->id;

        $response = $this->deleteJson(route('destinations.destroy', ['destination' => $id]));

        $response->assertStatus(204);
        $this->assertDatabaseEmpty('destinations');
    }
}
