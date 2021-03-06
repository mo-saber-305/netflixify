<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

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
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /***********************************************************************************************/

    //socialite methods

    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        try {
            $getInfo = Socialite::driver($provider)->user();
            $user = User::where('email', $getInfo->email)->first();
            if($user){
                Auth::login($user);
            }else{
                $newUser = new User();
                $newUser->name = $getInfo->getName();
                $newUser->email = $getInfo->getEmail();
                            $newUser->provider_id = $getInfo->getId();
                $newUser->provider = $provider;
                $newUser->save();

                $newUser->attachRole('super_admin');

                Auth::login($newUser);

            }

        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        return redirect()->route('dashboard.welcome');
    }


}
