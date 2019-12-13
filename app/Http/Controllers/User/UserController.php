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
        if(auth('api')->attempt(['email' => $request->email, 'password' => $request->password])){
            $user = auth('api')->user();
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
        $company = Company::with('categories')->where('user_id', auth('api')->id())->first();
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

        $user = User::find(auth('api')->user()->id);

        $request->validate([
            'name' => 'string|alpha|min:2|max:16',
            'surname' => 'string|alpha|min:2|max:16',
            'phone' => 'string|size:18|unique:users,id,' . auth('api')->id(),
            'logo' => 'image|mimes:jpeg,png,jpg|max:1024',
            'password' => 'string|min:6|max:64',
            'c_password' => 'same:password',
            'company' => 'array'
        ]);

        if($request->has('company')){

            $request->validate([
                'company.title' => 'required|string|min:5|max:56',
                'company.inn' => 'numeric|digits_between:9,12',
                'company.description' => 'string',
                'company.address' => 'string|min:5|max:255',
                'company.categories' => 'array',
                'company.categories.*' => 'numeric|min:1|exists:categories,id'
            ]);

            if($request->has('company.categories')){
                $categories = $request->input('company.categories');
                $request->offsetUnset('company.categories');
            }

            $company = Company::updateOrCreate(
                ['user_id' => auth('api')->id()],
                array_merge($request->get('company'), ['is_active' => 1])
            );

            if(isset($categories))
                $company->categories()->sync($categories);

        } elseif ($request->company_hide) {
            $company = Company::where('user_id', auth('api')->id())->first();
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
            $name = md5(auth('api')->id() . auth('api')->user()->email).'.'.$image->getClientOriginalExtension();
            $image->move(public_path('img/logo'), $name);

            $user->logo = '/img/logo/' . $name;
            $user->save();
        }



        $user->update($request->except(['_token', '_method', 'logo', 'email', 'c_password']));


        return response()->json([
            'success' => true,
            'data' => $user->load('company')
        ], 200);
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
