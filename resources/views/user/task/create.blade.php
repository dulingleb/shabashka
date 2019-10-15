@extends('layouts.app')

@section('content')
    <div class="container task create">
        <h1 class="text-center mb-4">Размещение задания</h1>

        <task-component
                :categories="{{ json_encode(\App\Category::getAllCategories()) }}"
                @if(isset($files)) :files="{{ json_encode($files) }}" @endif
                :user="{{ json_encode(auth()->user()) }}"
        ></task-component>

    </div>
@endsection
