<?php

namespace App\Http\Controllers\User;

use App\Response;
use App\ResponseMessage;
use App\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ResponseMessageController extends Controller
{
    public function store(Task $task, Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:1000',
            'response_id' => [
                function ($attribute, $value, $fail) use ($task){
                    $response = Response::find($value);
                    if(!$task || ($task->user_id !== \auth('api')->id() && $response->user_id !== \auth('api')->id())) $fail('Что-то пошло не так!');
                },
                'required'
            ]
        ]);

        $message = ResponseMessage::create([
            'response_id' => $request->response_id,
            'text' => $request->text,
            'user_id' => \auth('api')->id()
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $message->id,
                'user_id' => $message->user_id,
                'response_id' => $message->response_id,
                'text' => $message->text,
                'created_at' => Carbon::parse($message->created_at)
            ]
        ], 200);
    }
}
