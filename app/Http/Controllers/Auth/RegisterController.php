<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'full_name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:100', 'unique:users'], 
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @return User
     */
    protected function create(array $data)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            
            $user = \App\Models\User::create([
                'username' => $data['username'],
                'full_name' => $data['full_name'],
                'password_hash' => \Illuminate\Support\Facades\Hash::make($data['password']),
                'status' => 1,
            ]);

            $patientRole = \App\Models\Role::where('role_code', 'PATIENT')->first();
            if ($patientRole) {
                $user->roles()->attach($patientRole->id);
            }

            $patient = \App\Models\Patient::create([
                'user_id' => $user->id,
                'full_name' => $user->full_name,
                'status' => 1,
            ]);

            $patient->update([
                'patient_code' => 'BN' . str_pad($patient->id, 6, '0', STR_PAD_LEFT)
            ]);

            return $user; 
        });
    }
}
