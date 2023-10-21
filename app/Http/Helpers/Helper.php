<?php
namespace App\Helpers;

use Session;

use App\Models\Language;
use DB;
use Auth;

class Helper
{
	
	public static function baseUrl()
	{
		return "";
	}



	public static function responseSuccess(string $message = "",$data="")
	{

		if(empty($data)){
			return response()->json(
				[
					"status" => 200,
					"message" => $message,
				],
			);
		}else{
			return response()->json(
				[
					"status" => 200,
					"message" => $message,
					"data" => $data,
				],
			);

		}

		
	}

	public static function responseError(string $message = "", int $status = 501)
	{
		return response()->json(
			[
				"status" => $status,
				"message" => $message
			],
			$status
		);
	}

}
