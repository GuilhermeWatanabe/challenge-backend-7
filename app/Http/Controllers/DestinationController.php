<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDestinationFormRequest;
use App\Http\Requests\UpdateDestinationFormRequest;
use App\Models\Destination;
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

    public function store(StoreDestinationFormRequest $request)
    {
        return Destination::create(array_merge(
            ['photo' => $request->file('photo')->store('destination_photo', 'public')],
            $request->only(['name', 'price'])
        ));
    }

    public function show(Destination $destination)
    {
        return $destination;
    }

    public function update(UpdateDestinationFormRequest $request, Destination $destination)
    {
        if($request->hasFile('photo')) {
            $destination->photo = $request->file('photo')->store('destination_photo', 'public');
        }
        $destination->fill($request->except('photo'));
        $destination->save();

        return $destination;
    }

    public function destroy(Destination $destination)
    {
        $destination->delete();

        return response()->noContent();
    }
}
