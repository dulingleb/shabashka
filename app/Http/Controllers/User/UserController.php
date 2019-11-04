<?php

namespace App\Http\Controllers\User;

use App\Category;
use App\Company;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function register(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:12',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:64',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        return response()->json([
            'success' => true,
            'token' => $user->createToken(config('app.name'))->accessToken
        ], 200);
    }

    public function login(Request $request){
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            return response()->json([
                'success' => true,
                'token' => $user->createToken(config('app.name'))->accessToken
            ], 200);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Неверный email или парль',
                'error'=>'Unauthorised'
            ], 401);
        }
    }

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

        $user->load('company');

        $company = null;
        if($user->company && $user->company->is_active && $user->company->moderate_status=='active'){
            $company = [
                'id' => $user->company->id,
                'title' => $user->company->title,
                'address' => $user->company->address,
                'description' => $user->company->description,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'surname' => $user->surname,
                'logo' => $user->logo,
                'phone' => $user->phone,
                'company' => $company,
            ]
        ], 200);
    }

    public function me(){
        $user = auth('api')->user();
        return response()->json([
            'success' => true,
            'data' => $user->load('company')
        ], 200);
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
