<?php

namespace App\Http\Controllers\User;

use App\Response;
use App\ResponseMessage;
use App\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ResponseMessageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'response_id' => [
                function ($attribute, $value, $fail){
                    $response = Response::find($value);
                    $task = Task::find($response->task_id);
                    if(!$task || ($task->user_id !== Auth::id() && $response->user_id !== Auth::id())) $fail('Что-то пошло не так!');
                },
                'required'
            ]
        ]);

        $message = new ResponseMessage;

        $message->response_id = $request->response_id;
        $message->text = $request->message;
        $message->user_id = Auth::id();

        $message->save();

        return redirect()->back()->with(['message' => 'Ваше сообщение успешно отправлено!']);
    }
}
