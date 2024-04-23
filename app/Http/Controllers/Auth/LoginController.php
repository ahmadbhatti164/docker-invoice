<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Models\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $user;
    public function __construct(User $user)
    {
        $this->middleware('guest')->except('logout');
        $this->user = $user;
    }

    public function login(LoginRequest $request){

        $userData = $this->user->where('email', $request->email)->first();

        if($userData && $userData->status == 0){
            return redirect()->back()->withErrors(['email'=>'Your account is inactive.']);
        }
        else if($userData && $userData->is_verified != 1){
            return redirect()->back()->withErrors(['email'=>'Your account is not verified.']);
        } else {
            $credentials = Auth::attempt(['email'=>$request->email, 'password'=>$request->password, 'status' => 1]);
            if ($credentials) {
                return redirect(route('home'));
            } 
            // If the login attempt was unsuccessful we will increment the number of attempts to login and redirect the user back
            $this->incrementLoginAttempts($request);
            return redirect()->back()->withErrors(['email'=>'Invalid login credentials.']);
        }
    }
}
