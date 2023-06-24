<?php

namespace App\Http\Controllers;

use App\Models\ProfileCategory;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use  App\Models\User;
use App\Models\UserProfileSettings;
use App\Models\userrole;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth as FacadesJWTAuth;
use Illuminate\Support\Facades\DB;


class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'refresh']]);
        $this->middleware('verifyemail', ['except' => ['register', 'refresh', 'logout', 
        'userData', 'getuser', 'makeadmin', 'removeadmin', 'getallroles', 'profile']]);
    }

    //////////CHECK IF AUTENTICATED//////////////////////////////////////
    public function authenticated()
    {

        if (Auth::check()) {
            return response()->json(
                [
                    'status' => 'success',
                    'is_auth' => Auth::check()
                ],
                200
            );
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorised user.',
            ], 400);
        }
    }

    //////////LOGIN////////////////////////////////////////////////////
    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|email|string',
            'password' => 'required|string'
        ]);


        $credentials = $request->only('email', 'password');

        //Kreiramo token
        //$token= Auth::attempt($credentials);
        $token = FacadesJWTAuth::attempt($credentials);
        //dd($token);
        if (!$token) {

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials.',
            ], 401);
        } else {
            $tokenToSend = AuthController::fullToken($token);
            //pamtimo usera ako postoji token i vracamo ga kao odgovor sa tokenom
            $user = FacadesJWTAuth::user();
            $userId = $user->id;
            $roles = userrole::select('user_id', 'role_id', 'roles.name as role_name')->join('roles', 'user_roles.role_id', '=', 'roles.id')->where('user_id', '=', $userId)->get();
            ////AKO NEMA ROLU ONDA IDE NA LOGIN KAO USER
            //$user=Auth::user();
            return response()->json([
                'status ' => 'success',
                'user' => $user,
                'roles' => $roles,
                'auth' => [
                    'token' => $tokenToSend['token'],
                    'type' => $tokenToSend['type'],
                    'expires' => $tokenToSend['expires']
                ]
            ], 200);
        }
    }


    //////REGISTER USER/////////////////////////////////////////////////////
    public function register(Request $request)
    {

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'gender' => 'min:1|max:1',
            'email' => 'required|string|email|unique:users,email|max:100',
            'password' => 'required|string|min:8',
            'roles' => 'required|array|min:1'
        ]);

        if ($request) {
            $res = DB::transaction(function () use ($request) {
                $user = User::create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'gender' => $request->gender,
                    'birth_date' => $request->birth_date,
                    'email' => $request->email,
                    'password' => bcrypt($request->password)
                ]);
                if ($user) {
                    $roles = $request->roles;
                    for ($i = 0; $i < count($roles); $i++) {
                        if ($roles[$i]['id'] != 0) {
                            $role_inserted = UserRole::create([
                                'user_id' => $user->id,
                                'role_id' => $roles[$i]['id']
                            ]);
                        } else {
                            $role_id = Role::where('name', '=', $roles[$i]['name'])
                            ->select('id')->get()->first();
                            $role_inserted = userrole::create([
                                'user_id' => $user->id,
                                'role_id' => $role_id->id
                            ]);
                        }
                    }
                    if ($role_inserted) {
                        $categories = ProfileCategory::all();
                        $data = [];
                        foreach ($categories as $category) {
                            $data[] = [
                                'user_id' => $user->id,
                                'category_id' => $category->id,
                                'status' => 1,
                                'created_at' => date('Y-m-d H:i:s')
                            ];
                        }
                        $profileSettings = UserProfileSettings::insert($data);
                        if ($profileSettings) {
                            $token = auth()->login($user); //::login($user);
                            $tokenToSend = AuthController::fullToken($token);
                            //dd($token);
                            //$token=Auth::login($user);
                            $payload = Auth::payload();
                            //FacadesJWTAuth::payload($token);
                            $ttl = $payload->get('exp');
                            $tokenToSend = AuthController::fullToken($token);

                            if ($tokenToSend) {
                                $user->sendEmailVerificationNotification();
                                $response = [
                                    'status' => 'success',
                                    'message' => 'User created succesfully',
                                    'user' => $user,
                                    'roles' => $this->getroles($user->id),
                                    'auth' => [
                                        'token' => $tokenToSend['token'],
                                        'type' => $tokenToSend['type'],
                                        'expires' => $ttl
                                    ]
                                ];
                                return $response;
                            }
                        }
                    }
                }
            });
            return response()->json($res, 201);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Request.'
            ], 400);
        }
    }


    /////////////LOGOUT//////////////////////////////////////////////
    public function logout()
    {
        //dovoljno je samo poslati token bez kredencijala
        // if(Auth::logout()){
        //auth()->logout();
        //FacadesJWTAuth::logout();
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out.'
        ], 200);

    }

    ////////////////REFRESH////////////////////////////////////////////
    public function refresh()
    {
        $current_token = FacadesJWTAuth::getToken();
        $token = FacadesJWTAuth::refresh($current_token);
        //$token=Auth::refresh(); //ovaj traje 1 minut
        //dd($token);
        //$payload=FacadesJWTAuth::decode($token); - ne funkcionise
        //$payload=FacadesJWTAuth::getPayload($token); -ne funkcionise  
        //$ttl=$payload['exp'];
        //$payload=FacadesJWTAuth::parseToken($token)->getPayload();
        //$ttl=date(($payload->get('exp'))*1000);
        //dd($ttl);
        $tokenToSend = AuthController::fullToken($token);
        return response()->json([
            'status' => 'success',
            'user' => FacadesJWTAuth::user(),
            'auth' => [
                'token' => $tokenToSend['token'],
                'type' => $tokenToSend['type'],
                'expires' => $tokenToSend['expires'],
            ]
        ]);
    }


    /////////USER PROFILE
    public function profile(Request $request)
    {
        if (!Auth::user()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized user.'
            ], 401);
            //preusmerava na login stranu
        } else {
            $user = Auth::user();
            $roles = $this->getroles($user->id);
            $user['roles'] = $roles;
            return response()->json($user);
        }

        //vraca samo podatke o useru bez tokena i da bi se otvorio 
        //ocekuje da se posalje token bez kredencijala
    }




    /////////USER DATA////////////////////////////////////////////
    public function userData()
    {

        $user = FacadesJWTAuth::user();
        $roles = $this->getroles($user->id);
        $user['roles'] = $roles;


        if ($user) {
            return response()->json($user);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Incorrect credentials.'
            ], 401);
        }
    }

    /////EDIT USER DATA////////////////////////////////////////////
    public function edituserdata(Request $request)
    {

        $request->validate([
            'first_name' => 'string|max:100',
            'last_name' => 'string|max:100',
            'gender' => 'string|max:1',
            'birth_date' => 'string|max:10',
            'email' => 'string|email|unique:users,email|max:100',
        ]);

        if ($request) {
            $user = User::find($request->user_id);
            if ($request->first_name) {
                $user->update(['first_name' => $request->first_name]);
            }
            if ($request->last_name) {
                $user->update(['last_name' =>  $request->last_name]);
            }
            if ($request->birth_date) {
                $user->update(['birth_date' =>  $request->birth_date]);
            }
            if ($request->gender) {
                $user->update(['gender' =>  $request->gender]);
            }
            if ($request->email) {
                $user->update(['email' =>  $request->email]);
            }

            if ($user) {
                return response()->json([
                    'status' => 'success',
                    'data' => $user
                ], 201);
            } else return response()->json([
                'status' => 'error',
                'message' => 'Update error'
            ], 500);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request'
            ], 400);
        }
    }


    /////TOKEN RESPONSE////////////////////////////////////////////
    public function fullToken($token)
    {

        $newToken = [
            'token' => $token,
            'type' => 'bearer',
            'expires' => Auth::factory()->getTTL() * 60
        ];
        return $newToken;
    }

    /////CHECK PASSWORD////////////////////////////////////////////
    public function checkpassword($password)
    {
        ///validirati request
        if (!Hash::check($password, auth()->user()->password)) {
            return false;
        }
        return true;
    }


    /////CHANGE PASSWORD////////////////////////////////////////////
    public function changepassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8',

        ]);

        if($request){
            $password = $request->old_password;
            $check = $this->checkpassword($password);
            $password = $request->new_password;

            if ($check) {
                $user = auth()->user();
                $id = $user->id;
                $done = User::where('id', '=', $id)->update(['password' => bcrypt($request->new_password)]);
                return response()->json($done);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Old password is incorect.'
                ], 403);
            }
        }
        else{
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request.'
            ], 403);
        }
        
    }

    /////GET ROLES////////////////////////////////////////////
    public function getroles($user_id)
    {
        $roles = userrole::select('user_id', 'role_id', 'roles.name as role_name')->join('roles', 'user_roles.role_id', '=', 'roles.id')->where('user_id', '=', $user_id)->get();
        return $roles;
    }


    /////GET USER////////////////////////////////////////////
    public function getuser($email)
    {
        if (!is_numeric($email) && $email != '') {
            $user = User::where('email', '=', $email)->select('first_name', 'last_name', 'email', 'id')->get()->first();
            if ($user) {
                $id = $user->id;
                $roles = $this->getroles($id);
                if ($roles) {
                    return response()->json(['user' => $user, 'roles' => $roles], 200);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No such entry.'
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request.'
            ], 403);
        }
    }




    /////MAKE ADMIN USER////////////////////////////////////////////
    public function makeadmin(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|min:1'
        ]);

        if ($request) {
            $id = $request->id;
            $role_id = Role::where('name', '=', 'admin')->select('id')->get()->first();
            //return response()->json($role_id->id);
            $done = userrole::create(['user_id' => $id, 'role_id' => $role_id->id]);
            if ($done) {
                $roles = $this->getroles($id);
                if ($roles) {
                    return response()->json($roles, 201);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Database error.'
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request.'
            ], 403);
        }
    }


    /////REMOVE ADMIN ROLE////////////////////////////////////////////
    public function removeadmin(Request $request)
    {
        $request->validate([
            'user_id' => 'required|numeric|min:1',
            'role_id' => 'required|numeric|min:1'
        ]);
        if ($request) {
            $user_id = $request->user_id;
            $role_id = $request->role_id;
            $done = userrole::where('user_id', '=', $user_id)->where('role_id', '=', $role_id)->delete();
            if ($done) {
                $roles = $this->getroles($user_id);
                if ($roles) {
                    return response()->json($roles, 200);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Database error.'
                    ], 500);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Database error.'
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request.'
            ], 403);
        }
    }



    /////GET ROLES////////////////////////////////////////////
    public function getallroles()
    {
        $roles = Role::all();
        if ($roles) {
            return response()->json($roles, 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Database error.'
            ], 500);
        }
    }
}
