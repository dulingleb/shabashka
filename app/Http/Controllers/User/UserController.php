<?php

namespace App\Http\Controllers\User;

use App\Category;
use App\Company;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $user->load('company')->load('reviews');
        return view('user.profile.index', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $company = Company::with('categories')->where('user_id', Auth::id())->first();
        return view('user.setting.index', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = User::find(Auth::id());

        $request->validate([
            'name' => 'required|string|alpha|min:2|max:16',
            'surname' => 'required|string|alpha|min:2|max:16',
            'phone' => 'required|string|size:18|unique:users,id,' . Auth::id(),
            'logo' => 'image|mimes:jpeg,png,jpg|max:1024'
        ]);

        if($request->is_active == 1){
            $request->validate([
                'title' => 'required|string|min:5|max:56',
                'inn' => 'required|numeric|digits_between:9,12',
                'description' => 'required|string',
                'address' => 'required|string|min:5|max:255'
            ]);

            $company = Company::updateOrCreate(
                ['user_id' => Auth::id()],
                [
                    'is_active' => 1,
                    'title' => $request->input('title'),
                    'description' => $request->input('description'),
                    'address' => $request->input('address'),
                    'inn' => $request->input('inn')
                ]
            );

            $categories = json_decode($request->categories, true);

            DB::table('categoriables')->where('categoriable_id', $company->id)->where('categoriable_type', Company::class)->delete();
            foreach ($categories as $category) {
                if(Category::where('id', $category['id'])->exists()) {
                    DB::table('categoriables')->insert([
                        'category_id' => $category['id'],
                        'categoriable_id' => $company->id,
                        'categoriable_type' => Company::class
                    ]);
                }
            }

        } else {
            $company = Company::where('user_id', Auth::id())->first();
            if($company) {
                $company->is_active = 0;
                $company->save();
            }
        }

        if($request->remove_logo){
            if(\File::exists(public_path($user->logo))){
                \File::delete(public_path($user->logo));
            }
            $user->logo = NULL;
            $user->save();
        }

        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            $name = md5(Auth::id() . Auth::user()->email).'.'.$image->getClientOriginalExtension();
            $image->move(public_path('img/logo'), $name);

            $user->logo = '/img/logo/' . $name;
            $user->save();
        }

        $user->update($request->except(['_token', '_method', 'logo', 'email']));


        return response('Настройки успешно сохранены');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
