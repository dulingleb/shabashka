<?php

namespace App\Http\Controllers\User;

use App\Category;
use App\Company;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
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
        if(\Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password])){
            $user = auth('web')->user();
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

        return $this->_me_data($user);
    }

    public function me(){
        $user = auth('api')->user();
        return $this->_me_data($user);
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
                'company.title' => 'string|min:5|max:56',
                'company.inn' => 'numeric|digits_between:9,12',
                'company.description' => 'string',
                'company.address' => 'string|min:5|max:255',
                'company.categories' => 'array',
                'company.categories.*' => 'numeric|min:1|exists:categories,id',
                //'company.documents' => 'array',
                'company.documents.*' => 'file|mimes:jpeg,png,jpg,doc,docx,xls,xlsx,pdf,rtf|max:4096',
            ]);

            $company = $request->company;

            if($request->has('company.categories')){
                $categories = $request->input('company.categories');
                unset($company['categories']);
            }

            if($request->has('company.is_active')){
                $company['is_active'] = $company['is_active'] ? 1 : 0;

            }

            if($request->has('company.documents')){
                if(!File::exists(storage_path('app\public\users\\' . \auth('api')->id())))
                    File::makeDirectory(storage_path('app\public\users\\' . \auth('api')->id()));


                $filesName = [];

                foreach ($request->file('company.documents') as $file){
                    $name = $file->getClientOriginalName();
                    if(Storage::exists('public\users\\' . \auth('api')->id() . '\\' . $name)){
                        $item = 1;
                        do {
                            $name = substr($name, 0, strrpos($name, ".")) . '_' . $item . '.' . $file->getClientOriginalExtension();
                            $item++;
                        } while(Storage::exists('public\users\\' . \auth('api')->id() . '\\' . $name));
                    }

                    $path = $file->move(storage_path('app\public\users\\' . \auth('api')->id() . '\\'), $name );
                    $filesName[] = basename($name);
                }

                $company['documents'] = serialize($filesName);

                //$request->offsetUnset('company.documents');
            }


            $company = Company::updateOrCreate(
                ['user_id' => auth('api')->id()],
                $company
            );

            if(isset($categories))
                $company->categories()->sync($categories);

        }

        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            $name = md5(auth('api')->id() . auth('api')->user()->email).'.'.$image->getClientOriginalExtension();
            $image->move(public_path('img/logo'), $name);

            $user->logo = '/img/logo/' . $name;
            $user->save();
        } elseif ($request->get('logo') === null){
            if(\File::exists(public_path($user->logo))){
                \File::delete(public_path($user->logo));
            }
            $user->logo = NULL;
            $user->save();
        }


        if($request->has('password'))
            $request->replace(['password' => bcrypt($request->password)]);

        $user->update($request->except(['logo', 'email', 'c_password']));



        return $this->_me_data($user);
    }

    public function _me_data($user){
        return response()->json([
            'success' => true,
            'data' => [
                'name' => $user->name,
                'surname' => $user->surname,
                'logo' => $user->logo,
                'phone' => $user->phone,
                'email' => $user->email,
                'company' => [
                    'id' => $user->company->id,
                    'title' => $user->company->title,
                    'address' => $user->company->address,
                    'description' => $user->company->description,
                    'moderate_status' => $user->company->moderate_status,
                    'is_active' => $user->company->is_active,
                    'documents' => $user->company->documents ?? null,
                    'categories' => $user->company->categories->pluck('id')
                ]
            ]
        ], 200);
    }
}
