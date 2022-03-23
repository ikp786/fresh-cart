<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserProfileCollection;
use App\Models\User;
use Auth;
use Validator;

class AuthController extends BaseController
{
	// UNAUTHORIZED ACCESS
	public function unauthorized_access()
	{
		return $this->sendFailed('YOU ARE NOT UNAUTHORIZED TO ACCESS THIS URL, PLEASE LOGIN AGAIN', 401);
	}

	// CREATE ACCOUNT API
	public function create_account(Request $request)
	{
		$error_message = 	[
			'name.required'    	      		  => 'Name should be required',
			'email.required'	              => 'Email should be required',
			'email.unique'  	              => 'Email has been taken',
			'password.required'            	  => 'Password should be required',
		];

		$rules = [
			'name'                  	  => 'required|max:20',
			'email'                       => 'required|email|unique:users,email',
			'password'                    => 'required',
		];
		$validator = Validator::make($request->all(), $rules, $error_message);

		if ($validator->fails()) {
			return $this->sendFailed($validator->errors()->first(), 200);
		}

		try {
			\DB::beginTransaction();
			$user = new User();
			$user->fill($request->all());
			$user->password = Hash::make($request->password);
			$user->save();
			\DB::commit();
			return $this->sendSuccess('ACCOUNT CREATED SUCCESSFULLY');
		} catch (\Throwable $e) {
			\DB::rollback();
			return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
		}
	}

	public function login_account(Request $request)
	{
		$error_message = 	[
			'email.required'    => 'Email address should be required',
			'password.required' => 'Password should be required',
		];
		$rules = [
			'email'         	=> 'required',
			'password'      	=> 'required',
		];
		$validator = Validator::make($request->all(), $rules, $error_message);
		if ($validator->fails()) {
			return $this->sendFailed($validator->errors()->all(), 200);
		}
		try {
			if (auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
				\DB::beginTransaction();
				auth()->user()->tokens->each(function ($token, $key) {
					$token->delete();
				});
				$access_token = auth()->user()->createToken(auth()->user()->first_name)->accessToken;
				auth()->user()->fill($request->only(['first_name']))->save();
				\DB::commit();
				return $this->sendSuccess('LOGGED IN SUCCESSFULLY', ['access_token' => $access_token, 'profile_data' => new UserProfileCollection(auth()->user())]);
			} else {
				return $this->sendFailed('WE COULD NOT FOUND ANY ACCOUNT', 200);
			}
		} catch (\Throwable $e) {
			\DB::rollback();
			return $this->sendFailed($e->getMessage() . ' on line no. ' . $e->getLine(), 400);
		}
	}

	public function forgot_password(Request $request)
	{
		$error_message = 	[
			'email.required'    => 'Email address should be required',
			'email.exists'      => 'WE COULD NOT FOUND ANY EMAIL'
		];
		$rules = [
			'email'       		=> 'required|email|exists:users,email',
		];
		$validator = Validator::make($request->all(), $rules, $error_message);
		if ($validator->fails()) {
			return $this->sendFailed($validator->errors()->first(), 200);
		}
		try {
			$user_detail = User::where('email', $request->email)->first();
			if (!isset($user_detail)) {
				return $this->sendFailed('WE COULD NOT FOUND ANY ACCOUNT', 200);
			}
			$verifaction_otp = rand(111111, 999999);
			$email_data = ['user_name' => $user_detail->first_name, 'verifaction_otp' => $verifaction_otp];
			\Mail::to($user_detail->email)->send(new \App\Mail\ForgotPassword($email_data));
			return $this->sendSuccess('OTP SENT SUCCESSFULLY', ['user_id' => $user_detail->id, 'verifaction_otp' => $verifaction_otp, 'email' => $user_detail->email]);
		} catch (\Throwable $e) {
			return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
		}
	}

	public function reset_password(Request $request)
	{
		$error_message = 	[
			'id.required'  		=> 'Id should be required',
			'password.required' => 'Password should be required',
		];
		$rules = [
			'id'        		=> 'required|numeric|exists:users,id',
			'password'      	=> 'required',
		];
		$validator = Validator::make($request->all(), $rules, $error_message);
		if ($validator->fails()) {
			return $this->sendFailed($validator->errors()->first(), 200);
		}
		try {
			$user_detail = User::find($request->id);
			if (!isset($user_detail)) {
				return $this->sendFailed('WE COULD NOT FOUND ANY ACCOUNT', 200);
			}
			\DB::beginTransaction();
			$user_detail->password = Hash::make($request->user_password);
			$user_detail->save();
			\DB::commit();
			return $this->sendSuccess('PASSWORD UPDATED SUCCESSFULLY');
		} catch (\Throwable $e) {
			\DB::rollback();
			return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
		}
	}
}
