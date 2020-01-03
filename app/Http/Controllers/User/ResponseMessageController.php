<?php

namespace App\Http\Controllers\User;

use App\Func\ResponseJson;
use App\Response;
use App\ResponseMessage;
use App\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

        $response_message = ResponseMessage::create([
            'response_id' => $request->response_id,
            'text' => $request->text,
            'user_id' => \auth('api')->id()
        ]);

        $data = $this->getResponseMessageArray($response_message);

        return ResponseJson::getSuccess($data);
    }

    private function getResponseMessageArray(ResponseMessage $response_message){
        $data = [
            'id' => $response_message->id,
            'user_id' => $response_message->user_id,
            'response_id' => $response_message->response_id,
            'text' => $response_message->text,
            'created_at' => $response_message->created_at
        ];

        return $data;
    }
}
