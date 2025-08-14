<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewFormRequest;
use App\Models\Review;
use http\Env\Response;

class ReviewController extends Controller
{
    public function index()
    {
        return Review::all();
    }

    public function home()
    {
        return Review::inRandomOrder()->take(3)->get();
    }

    public function store(ReviewFormRequest $request)
    {
        return Review::create(array_merge(
            ['photo' => $request->file('photo')->store('review_photo', 'public')],
            $request->only(['review', 'user_name'])
        ));
    }

    public function show(int $id)
    {
        $review = Review::find($id);

        if(!$review) {
            return response()->json([
               'message' => 'Review not found.'
            ], 404);
        }

        return $review;
    }

    public function update(ReviewFormRequest $request, Review $review)
    {
        if($request->hasFile('photo')) {
            $review->photo = $request->file('photo')->store('review_photo', 'public');
        }
        $review->fill($request->except('photo'));
        $review->save();

        return $review;
    }

    public function destroy(int $id)
    {
        $review = Review::find($id);

        if(!$review) {
            return response()->json([
                'message' => 'Review not found.'
            ], 404);
        }

        $review->delete();

        return response()->noContent();
    }
}
