<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Mail;
use App\User;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    protected $redirectTo ='home';

    use AuthenticatesAndRegistersUsers;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $user =new User ([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
        
        $user->registration_token = str_random(40);
        $user->save ();
        
        $url= route('confirmation',['token'=>$user->registration_token]);
        Mail::send('emails/registration', compact('user','url'), function ($m) use ($user){
            $m->to($user->email, $user->name)->subject('Activar tu cuenta');
                    
        });
        return $user;
    }
    
    public function postRegister(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

       $user=$this->create($request->all());

        return redirect()->route('auth/login')
                ->with('alert','por favor confirma tu email'.$user->email);
    }
    
    
    protected function getConfirmation ($token)
    {
        $user= User::where('registration_token',$token)->firstOrfail();
        $user->registration_token =null;
        $user->save();
        
        return redirect()->route('auth/login')
                ->with('alert','Email confirmado puedes iniciar Session');
    }

    





    protected function getCredentials($request) {
        return [
            'email' => $request->get('email'),
            'password' => $request->get('password'),
            'registration_token' => null
        ];
    }
    
    
}
