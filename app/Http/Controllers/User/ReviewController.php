<?php

namespace App\Http\Controllers\User;

use App\Func\ResponseJson;
use App\Review;
use App\Task;
use App\User;
use App\Http\Controllers\Controller;
use http\Client\Request;

class ReviewController extends Controller
{
    public function index(User $user){
        $reviews = Review::where('user_id', $user->id)->get();
        $data = [];

        foreach ($reviews as $review) {
            $data[] = Review::getReviewArray($review);
        }

        return ResponseJson::getSuccess($data, count($reviews));
    }
}
