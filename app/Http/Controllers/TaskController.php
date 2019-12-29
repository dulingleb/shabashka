<?php

namespace App\Http\Controllers;

use App\Category;
use App\Func\Filter;
use App\Response;
use App\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $tasks = (new Task)->newQuery();

        if($request->categories) {
            $categories =  json_decode($request->categories);
            $tasks = $tasks->whereIn('category_id', array_values($categories));
            $total = $tasks->count();
        } else {
            $total = Task::all()->count();
        }

        if($request->search)
            $tasks = $tasks->where("title", 'LIKE', '%'.$request->search.'%')->orWhere('description', 'LIKE', '%'.$request->search.'%');
        if($request->user_id)
            $tasks = $tasks->where('user_id', (int)$request->user_id);

        $tasks = Filter::SLS($tasks, $request);

        $data = [];
        foreach ($tasks->get() as $task){
            $data[] = [
                'id' => $task->id,
                'user_id' => $task->user_id,
                'title' => $task->title,
                'description' => $task->description,
                'price' => $task->price,
                'category_id' => $task->category_id,
                'created_at' => $task->created_at,
                'term' => $task->term
            ];
        }

        return response()->json([
            "success" => true,
            "data" => $data,
            "total" => $total
        ], 200);
    }

    private function _validate($request){
        $request->validate([
            'category_id' => [
                'required',
                function($attribute, $value, $fail) {
                    $category = Category::find($value);
                    if(!$category || $category->parent==0) return $fail('Неверная категория');
                    else
                        return true;
                },
            ],
            'title' => 'required|string|min:5|max:100',
            'description' => 'required|string|min:20|max:1000',
            'address' => 'nullable|string|min:5|max:255',
            'term' => 'required|date|date-format:Y-m-d\TH:i:s.\0\0\0\Z|after:yesterday',
            'price' => 'required|numeric|min:0|max:999999999',
            'phone' => 'required|string|size:18',
            'files' => 'array',
            'files.*' => 'file|mimes:jpeg,png,jpg,doc,docx,xls,xlsx,pdf,rtf|max:4096'
        ]);
    }

    public function store(Request $request)
    {
        $this->_validate($request);

        $task = Task::create([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'address' => $request->address,
            'term' => Carbon::parse($request->term),
            'price' => $request->price,
            'phone' => $request->phone,
            'user_id' => auth('api')->id(),
        ]);

        if($request->has('files')){
            $filesName = Task::uploadFiles($request->file('files'), $task->id);
            $task->files = $filesName;
            $task->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Задание успешно доавлено!'
        ]);
    }

    public function update(Task $task, Request $request){
        if($task->user_id !== \auth('api')->id())
            return response()->json([
                'success' => false,
                'message' => 'Не ваше задание'
            ], 419);

        if($task->status !== 'search_executor')
            return response()->json([
                'success' => false,
                'message' => 'Задание в работе или завершенное редактировать нельзя'
            ], 401);

        $this->_validate($request);


        $task->category_id = $request->category_id;
        $task->title = $request->title;
        $task->description = $request->description;
        $task->address = $request->address;
        $task->term = Carbon::parse($request->term);
        $task->price = $request->price;
        $task->phone = $request->phone;

        if($request->has('files')){
            $filesName = Task::uploadFiles($request->file('files'), $task->id);
            $task->files = $filesName;
            $task->save();
        }

        if($request->has('files_remove')){
            foreach ($request->input('files_remove') as $item){
                $item = basename($item);
                if(File::exists(storage_path('app\public\tasks\\' . $task->id . '\\' . $item)))
                    File::delete(storage_path('app\public\tasks\\' .$task->id . '\\' . $item));
            }

            $task->files = array_diff($task->files, $request->input('files_remove'));
            $task->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Задание успешно обновлено!'
        ]);
    }

    public function show(Task $task){
        $task->load(['responses' => function($q){
            $q->selectRaw('responses.*, (SELECT executor FROM tasks WHERE responses.user_id=tasks.executor) as executor')->orderBy('executor', 'DESC');
        }])->load('category')->load('user');

        if ($task->executor == null || $task->executor === Auth::id())
            $myResponse = Response::with('user')->with('messages')->where('user_id', Auth::id())->where('task_id', $task->id)->first();

        $data = [
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'address' => $task->address,
            'files' => $task->files,
            'price' => $task->price,
            'category_id' => $task->category_id,
            'user_id' => $task->user_id,
            'executor' => $task->executor,
            'status' => $task->status,
            'term' => $task->term,
            'created_at' => $task->created_at,
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

    public function destroy(Task $task){
        if($task->user_id === auth('api')->id() && $task->executor === null){
            if(Storage::exists('public\tasks\\' . $task->id))
                Storage::deleteDirectory('public\tasks\\' . $task->id);
            $task->delete();

            return response()->json([
                'success' => true
            ], 200);
        } elseif ($task->executor !== null)
            return response()->json([
                'success' => false,
                'error' => "Нельзя удалить задание, если уже назначен исполнитель"
            ], 200);
        else
            return response()->json([
                'success' => false,
                'error' => "Что-то пошло не так или ты хаккер"
            ], 419);
    }

    public function categories(){
        return response()->json([
            'success' => true,
            'data' => Category::all()
        ]);
    }

}
