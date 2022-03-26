<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\UserProfileCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
// use App\Http\Resources\UserProfileCollection;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Storage;
use Validator;

class AuthController extends BaseController
{
	// UNAUTHORIZED ACCESS
	public function unauthorized_access()
	{
		return $this->sendFailed('YOU ARE NOT UNAUTHORIZED TO ACCESS THIS URL, PLEASE LOGIN AGAIN', 401);
	}

	// CREATE ACCOUNT API
	public function userRegister(Request $request)
	{
		$error_message = 	[
			// 'mobile.unique'  	              => 'mobile has been already taken',
			'mobile.required'            	  => 'Mobile should be required',
		];
		$rules = [
			'mobile'                       => 'required|min:10|max:10',
		];
		$validator = Validator::make($request->all(), $rules, $error_message);

		if ($validator->fails()) {
			return $this->sendFailed($validator->errors()->first(), 200);
		}

		try {
			\DB::beginTransaction();
			// $user = new User();
			// $user->fill($request->all());
			// $user->password = Hash::make($request->password);
			// $user->save();

			$user = User::updateOrCreate(
				$request->only('mobile')
			);
			Auth::loginUsingId($user->id);
			$access_token = auth()->user()->createToken(auth()->user()->mobile)->accessToken;
			\DB::commit();
			return $this->sendSuccess('LOGGED IN SUCCESSFULLY', ['access_token' => $access_token, 'profile_data' => new UserProfileCollection(auth()->user())]);
		} catch (\Throwable $e) {
			\DB::rollback();
			return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
		}
	}

	public function getUserProfile()
	{
		return $this->sendSuccess('LOGGED IN SUCCESSFULLY', ['profile_data' => new UserProfileCollection(auth()->user())]);
	}

	public function sentRegisterOtp(Request $request)
	{
		$error_message = 	[
			'mobile.required'  	=> 'Mobile address should be required',
		];
		$rules = [
			'mobile'       		=> 'required',
		];
		$validator = Validator::make($request->all(), $rules, $error_message);
		if ($validator->fails()) {
			return $this->sendFailed($validator->errors()->first(), 200);
		}
		try {

			$verifaction_otp = rand(111111, 999999);
			$email_data = ['verifaction_otp' => $verifaction_otp];
			// \Mail::to($request->email_address)->send(new \App\Mail\LoginOtp($email_data));
			return $this->sendSuccess('OTP SENT SUCCESSFULLY', ['verifaction_otp' => $verifaction_otp, 'mobile' => $request->mobile]);
		} catch (\Throwable $e) {
			return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
		}
	}

	// UPDATE PROFILE
	public function updateUserProfile(Request $request)
	{
		$user_details = auth()->user();
		$error_message = 	[
			'name.required'    	 => 'Name should be required',
			'email.required'	 => 'Email address should be required',
			'email.unique'  	 => 'Email address has been taken',
			'profile_pic.mimes'  => 'Profile photo format jpg,jpeg,png',
			'profile_pic.max'    => 'Profile photo max size 2 MB',
			'dob.required'		 => 'Date Of Birth should be required.',
			'dob.required'		 => 'Date Of Birth should be valid date format.'
		];
		$rules = [
			'name'            => 'required|max:20',
			'email'           => 'required|email',
			'dob'			  => 'required|date',
		];
		if (!empty($request->profile_pic)) {
			$rules['profile_pic'] = 'mimes:jpg,jpeg,png|max:2000';
		}
		$validator = Validator::make($request->all(), $rules, $error_message);
		if ($validator->fails()) {
			return $this->sendFailed(implode(", ", $validator->errors()->all()), 200);
		}
		$emailExist = User::where('id', '!=', auth()->user()->id)->where(['email' => $request->email])->count();
		if ($emailExist > 0) {
			return $this->sendFailed("Email address has been already taken", 200);
		}
		try {
			if (!empty($request->file('profile_pic'))) {
				if (Storage::disk('public')->exists('user_images/' . $user_details->user_pic_name)) {
					Storage::disk('public')->delete('user_images/' . $user_details->user_pic_name);
				}
				$user_pic = time() . '_' . rand(1111, 9999) . '.' . $request->file('profile_pic')->getClientOriginalExtension();
				$request->file('profile_pic')->storeAs('user_images', $user_pic, 'public');
				$request['profile_pic'] = $user_pic;
			}

			\DB::beginTransaction();
			$user_details->fill($request->all());
			$user_details->profile_pic = $user_pic;
			$user_details->save();
			\DB::commit();
			return $this->sendSuccess('PROFILE UPDATED SUCCESSFULLY', new UserProfileCollection($user_details));
		} catch (\Throwable $e) {
			\DB::rollback();
			return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
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
