<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTweetRequest;
use App\Http\Resources\TweetResource;
use Illuminate\Http\Request;
use App\Models\Tweet;

class TweetController extends Controller
{
   
    public function store(CreateTweetRequest $request)
    {
        $user = $request->user();
        $tweet = $user->tweets()->create($request->validated());
        return new TweetResource($tweet);
    }

    public function timeline(Request $request)
    {
        $followingsIds = $request->user()->followings()->pluck('user_id');
        $tweets = Tweet::whereIn('user_id', $followingsIds)->paginate();
        return TweetResource::collection($tweets);
    }
}
