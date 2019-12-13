<?php

namespace App\Http\Controllers;

use App\Category;
use App\Func\Filter;
use App\Response;
use App\Task;
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
                'category' => $task->category_id,
                'created_at' => time_ago($task->created_at),
                'term' => date_month_ru($task->term)
            ];
        }

        return response()->json([
            "success" => true,
            "data" => $data,
            "total" => $total
        ], 200);
    }

    public function store(Request $request)
    {
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
            'term' => 'required|date|date-format:Y-m-d|after:yesterday',
            'price' => 'required|numeric|min:0|max:999999999',
            'phone' => 'required|string|size:18',
            'files' => 'required',
            'files.*' => 'file|mimes:jpeg,png,jpg,doc,docx,xls,xlsx,pdf,rtf|max:4096'
        ]);


        $task = Task::create([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'address' => $request->address,
            'term' => $request->term,
            'price' => $request->price,
            'phone' => $request->phone,
            'user_id' => auth('api')->id(),
        ]);

        $filesName = [];

        if ($request->hasFile('files')) {
            File::makeDirectory(storage_path('app\public\tasks\\' . $task->id));

            foreach ($request->file('files') as $file){
                $name = $file->getClientOriginalName();
                if(Storage::exists('public\tasks\\' . $task->id . '\\' . $name)){
                    $item = 1;
                    do {
                        $name = substr($name, 0, strrpos($name, ".")) . '_' . $item . '.' . $file->getClientOriginalExtension();
                        $item++;
                    } while(Storage::exists('public\tasks\\' . $task->id . '\\' . $name));
                }

                $path = $file->move(storage_path('app\public\tasks\\' . $task->id . '\\'), $name );
                $filesName[] = basename($name);
            }
        }

        $task->files = json_encode($filesName);
        $task->save();

        return response()->json([
            'success' => true,
            'message' => 'Задание успешно доавлено!'
        ]);
    }

    public function show(Task $task){
        $task->load(['responses' => function($q){
            $q->selectRaw('responses.*, (SELECT executor FROM tasks WHERE responses.user_id=tasks.executor) as executor')->orderBy('executor', 'DESC');
        }])->load('category')->load('user');

        if ($task->executor == null || $task->executor === Auth::id())
            $myResponse = Response::with('user')->with('messages')->where('user_id', Auth::id())->where('task_id', $task->id)->first();

        $files = json_decode($task->files);
        for($i=0;$i<count($files);$i++)
            $files[$i] = '/storage/tasks/' . $task->id . '/' . $files[$i];

        $data = [
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'address' => $task->address,
            'files' => $files,
            'price' => $task->price,
            'category_id' => $task->category_id,
            'user_id' => $task->user_id,
            'executor' => $task->executor,
            'status' => $task->status,
            'term' => date_month_ru($task->term)
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

    public function destroy(Task $task){
        if($task->user_id === auth('api')->id() && $task->executor === null){
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
