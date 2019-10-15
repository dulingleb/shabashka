<?php

namespace App\Http\Controllers\User;

use App\Category;
use App\Company;
use App\Response;
use App\Review;
use App\Task;
use App\User;
use Carbon\Carbon;
use Faker\Provider\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::with('responses')->with('category')->where('user_id', Auth::id())->latest()->paginate();
        foreach ($tasks as $task){
            $task->term = Task::dateTerm($task->term);
        }
        return view('user.task.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Storage::exists('public\tasks\temp\\' . Auth::id())){
            $file_names = array_diff(scandir(storage_path('app\public\tasks\temp\\' . Auth::id())), array('.', '..'));
            $files = [];
            foreach ($file_names as $file){
                $files[] = public_path('app\public\tasks\temp\\' . Auth::id().'\\') . $file;
            }
        } else {
            mkdir(storage_path('app\public\tasks\temp\\' . Auth::id()));
        }

        return view('user.task.create', compact('files'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
            'files' => [function($attribute, $value, $fail){
                    if(count(json_decode($value, true)) > 8)
                        $fail('Максимальное кол-во файлов - 8');
                }]
        ]);

        $files = json_decode($request->get('files'), true);
        $filesName = [];
        foreach($files as $file){
            $filesName[] = basename($file);
        }

        if(isset($request->id) && $request->id!='') {
            $task = Task::find($request->id);
            if($task->user_id !== Auth::id()) return \response('Что-то пошло не так', 419);
            $task->update([
                'category_id' => $request->category_id,
                'title' => $request->title,
                'description' => $request->description,
                'address' => $request->address,
                'term' => $request->term,
                'price' => $request->price,
                'phone' => $request->phone,
                'files' => json_encode($filesName),
            ]);

            Session::flash('message', 'Задание успешно отредактировано!');
        }
        else {
            $task = Task::create([
                'category_id' => $request->category_id,
                'title' => $request->title,
                'description' => $request->description,
                'address' => $request->address,
                'term' => $request->term,
                'price' => $request->price,
                'phone' => $request->phone,
                'user_id' => Auth::id(),
                'files' => json_encode($filesName),
            ]);

            Session::flash('message', 'Задание успешно добавлено!');
        }


        Storage::deleteDirectory('public\tasks\\' . $task->id);
        foreach ($filesName as $file){
            if(Storage::exists('public\tasks\temp\\' . Auth::id() . '\\' . $file))
                Storage::move('public\tasks\temp\\' . Auth::id() . '\\' . $file, 'public\tasks\\' . $task->id . '\\' . $file);
        }



        return route('tasks.show', ['id' => $task->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        $executorId = $task->executor ?? 0;
        $task->load(['responses' => function($q) use ($executorId){
            $q->selectRaw('responses.*, (SELECT executor FROM tasks WHERE responses.user_id=tasks.executor) as executor')->orderBy('executor', 'DESC');
        }])->load('category')->load('user');

        $task->term = Task::dateTerm($task->term);

        if ($task->executor == null || $task->executor === Auth::id())
            $myResponse = Response::with('user')->with('messages')->where('user_id', Auth::id())->where('task_id', $task->id)->first();

        return view('user.task.show', compact('task', 'category', 'customer', 'myResponse'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        if($task->user_id!=Auth::id() || $task->executor !== null) return redirect()->route('tasks.show', ['id' => $task->id]);
        $task = Task::with('category')->find($task->id);

        $files = [];

        foreach (json_decode($task->files, true) as $file){
            if(Storage::exists('public\tasks\\' . $task->id . '\\' . $file) && !Storage::exists('public\tasks\temp\\' . Auth::id() . '\\' . $file))
                Storage::copy('public\tasks\\' . $task->id . '\\' . $file, 'public\tasks\temp\\' . Auth::id() . '\\' . $file);
            $files[] = '/storage/tasks/temp/' . Auth::id() . '/' . $file ;
        }

        $task->files = $files;

        return view('user.task.edit', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        if($task->user_id === Auth::id() && $task->executor === null){
            Storage::deleteDirectory('public\tasks\\' . $task->id);
            $taskTitle = $task->title;
            $task->delete();

            return redirect()->route('user.tasks.index')->with(['message' => 'Задание "' . $taskTitle .'" успешно удалено']);
        } elseif ($task->executor !== null)
            return redirect()->back()->withErrors(['text_error' => 'Нельзя удалить задание, если уже назначен исполнитель.']);
        else
            return redirect()->back()->withErrors(['text_error' => 'Что-то пошло не так, или ты хаккер.']);

    }

    public function uploadFile(Request $request){
        $request->validate([
           'file' => 'required|file|mimes:jpeg,png,jpg,doc,docx,xls,xlsx,pdf,rtf|max:4096'
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            if(Storage::exists('public\tasks\temp\\' . Auth::id() . '\\' . $file->getClientOriginalName())){
                $item = 1;
                do {
                    $name = substr($file->getClientOriginalName(), 0, strrpos($file->getClientOriginalName(), ".")) . '_' . $item . '.' . $file->getClientOriginalExtension();
                    $item++;
                } while(Storage::exists('public\tasks\temp\\' . Auth::id() . $name));
            }
            else
                $name = $file->getClientOriginalName();

            $path = $file->move(storage_path('app\public\tasks\temp\\'. Auth::id().'\\'), $name );

            return response($path);
        }

    }

    public function removeFile(Request $request){
        if(Storage::exists('public\tasks\temp\\' . Auth::id() . '\\' . $request->file_name)){
            Storage::delete('public\tasks\temp\\' . Auth::id() . '\\' . $request->file_name);
        }
    }

    public function list(Request $request)
    {
        if($request->categories)
            return $tasks = Task::with('category')->whereIn('category_id', array_values($request->categories))->latest()->paginate(50);
        else
            return $tasks = Task::with('category')->latest()->paginate(50);
    }

    public function setExecutor(Response $response){
        $task = Task::find($response->task_id);
        if(!$task || $task->user_id !== Auth::id() || $task->executor != null) return redirect()->back()->withErrors(['text_error' => 'Что-то пошло не так']);

        $task->executor = $response->user_id;
        $task->status = 'in_work';
        $task->save();

        $response->load('user');

        return redirect()->back()->with(['message' => (($response->user->company && $response->user->company->is_active==1) ? $response->user->company->title : $response->user->name . ' ' . $response->user->surname) . ' успешно назначен исполнителем!' ]);
    }

    public function addReview(Task $task, Request $request) {
        $request->validate([
            'status' => [
                function($attribute, $value, $fail){
                    if($value !== 'success' && $value !== 'close') $fail('Неверный статус!');
                }
            ],
            'assessment' => 'required|numeric|min:0|max:1',
            'text' => 'nullable|string|max:1000'
        ]);

        if($task->user_id !== Auth::id()) return redirect()->back()->withErrors(['text_error' => 'Что-то пошло не так']);

        $task->status = $request->get('status');
        $task->save();

        if($task->status == 'success'){
            $review = new Review;
            $review->user_id = $task->executor;
            $review->task_id = $task->id;
            $review->text = $request->text;
            $review->assessment = $request->assessment;
            $review->save();
            $message = 'Отзыв успешно добавлен';
        } else
            $message = 'Статуc задания установлено "не выполнено"';

        return redirect()->back()->with(['message' => $message]);

    }
}
