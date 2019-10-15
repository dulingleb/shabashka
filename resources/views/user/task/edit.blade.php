@extends('layouts.app')

@section('content')
    <div class="container task create">
        <h1 class="text-center mb-4">Редактирование задания</h1>

        <task-component
            :categories="{{ json_encode(\App\Category::getAllCategories()) }}"
            :old_task="{{ json_encode($task) }}"
            :user="{{ json_encode(auth()->user()) }}"
        ></task-component>

    </div>
@endsection
