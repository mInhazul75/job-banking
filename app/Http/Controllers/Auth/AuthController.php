<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
	public function createUser(Request $request)
	{
		try {
			$validator = Validator::make($request->all(), ['email' => 'required|string|email|max:255', 'account_type' => 'required|string|',]);
			if ($validator->fails()) {
				return response(['errors' => $validator->errors()->all()], 422);
			}
			if (filter_var($request['email'], FILTER_VALIDATE_EMAIL)) {
				$user = User::where('email', $request['email'])->first();
				if ($user) {
					return Helper::responseError("Account Already Exits by this email");
				}
			}
			$requestAll = $request->all();
			$requestAll['password'] = bcrypt($request->password);
			User::create($requestAll);
			return Helper::responseSuccess("Create User Succesfully");
		} catch (\Exception $e) {
			return Helper::responseError($e->getMessage());
		}
	}

	public function userLogin(Request $request)
	{
		try {
			$validator = Validator::make($request->all(), ['email' => 'required|string|email|max:255', 'password' => 'required|string|min:6',]);
			if ($validator->fails()) {
				return response(['errors' => $validator->errors()->all()], 422);
			}
			$user = User::where('email', $request->email)->first();
			if ($user) {
				if (Hash::check($request->password, $user->password)) {
					$token = $user->createToken("API TOKEN")->plainTextToken;
					$user->token = $token;
					$user->password = null;
					return Helper::responseSuccess("User info return", $user);
				} else {
					return Helper::responseError("Password mismatch");
				}
			} else {
				return Helper::responseError("User does not exist");
			}
		} catch (\Exception $e) {
			return Helper::responseError($e->getMessage());
		}
	}
}
