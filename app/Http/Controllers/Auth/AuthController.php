<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use SocialAuth;
use Validator;

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

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => 'required|max:255',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     *
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function facebookLogin()
    {
        return SocialAuth::authorize('facebook');
    }

    public function facebookRedirect()
    {
        // Automatically log in existing users
        // or create a new user if necessary.
        SocialAuth::login('facebook', function ($user, $details) {
            $existing_user = User::where('email', $details->email)->first();

            if ($existing_user !== NULL) {
                $existing_user->avatar = $details->avatar;
                $existing_user->save();

                return $existing_user; // Tell the package to use this user instead of creating a new one.
            }
            $user->name = $details->full_name;
            $user->avatar = $details->avatar;
            $user->email = $details->email;
            $user->save();
            $roles_allowed = [2];
            $user->syncRoles($roles_allowed);
        });

        // Current user is now available via Auth facade
        $user = Auth::user();
        if ($user->wasRecentlyCreated == TRUE) {
            return redirect('payment')->with('message', 'Update Payment details!');
        }

        return redirect()->intended('/backend/home');
    }
}
