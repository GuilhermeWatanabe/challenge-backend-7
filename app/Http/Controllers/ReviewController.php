<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewFormRequest;
use App\Http\Requests\UpdateReviewFormRequest;
use App\Models\Review;

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

    public function store(StoreReviewFormRequest $request)
    {
        return Review::create(array_merge(
            ['photo' => $request->file('photo')->store('review_photo', 'public')],
            $request->only(['review', 'user_name'])
        ));
    }

    public function show(Review $review)
    {
        return $review;
    }

    public function update(UpdateReviewFormRequest $request, Review $review)
    {
        if($request->hasFile('photo')) {
            $review->photo = $request->file('photo')->store('review_photo', 'public');
        }
        $review->fill($request->except('photo'));
        $review->save();

        return $review;
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return response()->noContent();
    }
}
