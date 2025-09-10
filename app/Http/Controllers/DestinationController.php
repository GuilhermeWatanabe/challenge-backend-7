<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDestinationFormRequest;
use App\Http\Requests\UpdateDestinationFormRequest;
use App\Models\Destination;
use App\Services\GoogleAiService;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    public function index(Request $request)
    {
        $query = Destination::query();

        if($request->has('name')) {
            $query->where('name', 'like', "%{$request->name}%");

            if($query->count() == 0) {
                return response()->json(['message' => "Destination not found."], 404);
            }
        }

        return $query->get();
    }

    public function store(StoreDestinationFormRequest $request, GoogleAiService $googleAIService)
    {
        return Destination::create(array_merge(
            ['photo_1' => $request->file('photo_1')->store('destination_photo', 'public')],
            ['photo_2' => $request->file('photo_2')->store('destination_photo', 'public')],
            ['description' => $request->description ?? $googleAIService->generateDescription($request->name)],
            $request->except(['photo_1', 'photo_2', 'description'])
        ));
    }

    public function show(Destination $destination)
    {
        return $destination;
    }

    public function update(UpdateDestinationFormRequest $request, Destination $destination)
    {
        if($request->hasFile('photo_1')) {
            $destination->photo_1 = $request->file('photo_1')->store('destination_photo', 'public');
        }
        if($request->hasFile('photo_2')) {
            $destination->photo_2 = $request->file('photo_2')->store('destination_photo', 'public');
        }
        $destination->fill($request->except('photo_1', 'photo_2'));
        $destination->save();

        return $destination;
    }

    public function destroy(Destination $destination)
    {
        $destination->delete();

        return response()->noContent();
    }
}
