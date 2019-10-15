@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="display-1">ШАБАШКИ</h1>

        <show-tasks-component
            :categories="{{ json_encode(\App\Category::getAllCategories()) }}"
        ></show-tasks-component>
    </div>
@endsection
