@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-center mb-4">Настройки</h1>

        <setting-component
            :user="{{ json_encode(auth()->user()) }}"
            :company_old="{{ json_encode($company) }}"
            :categories="{{ json_encode(\App\Category::getAllCategories()) }}"
        ></setting-component>

    </div>
@endsection
