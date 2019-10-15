<?php

namespace App\Http\Controllers\User;

use App\Response;
use App\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ResponseController extends Controller
{
    public function store(Request $request){
        $request['price'] = preg_replace('/\D/', '', $request->price);
        $request->validate([
            'text' => 'required|string|max:1000',
            'task_id' => [
                function ($attribute, $value, $fail) {
                    if(Response::where('task_id', $value)->where('user_id', Auth::id())->exists() || !Task::where('id', $value)->where('executor', NULL)->exists()){
                        $fail('Что-то пошло не так');
                    }
                },
                'required', 'exists:tasks,id', 'numeric'
            ],
            'price' => 'required|numeric|min:1|max:99999999'
        ]);

        Response::create([
            'user_id' => Auth::id(),
            'task_id' => $request->input('task_id'),
            'text' => $request->input('text'),
            'price' => $request->input('price')
        ]);

        return redirect()->back()->with(['message' => 'Ваш отклик успешно добавлен.']);
    }
}
