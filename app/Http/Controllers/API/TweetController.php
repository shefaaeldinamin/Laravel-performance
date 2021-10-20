<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tweet;

class TweetController extends Controller
{
   
    public function store(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:140',
            'description' => 'string|max:140',
        ]);

        $user = $request->user();
        $tweet = $user->tweets()->create([
            'text' => $request->text,
            'description' => $request->description
        ]);
        
        return response()->json([
            'tweet' => $tweet
        ]);
    }

    public function timeline(Request $request)
    {
        $followingsIds = $request->user()->followings()->pluck('user_id');
        $tweets = Tweet::whereIn('user_id', $followingsIds)->get();

        return response()->json([
            'tweets' => $tweets
        ]);
    }
}
