<?php
namespace App\Traits;

trait ApiResponse{
	public function success($code, $data, $message  ) {
		$newData = [];


		return response()->json([
            'success' => true,
			'message' => $message,
			'data' => $data
		], $code);

		if(isset($message) && !is_null($message) && $message != '') $newData['message'] = $message;
		if(isset($data) && !is_null($data)){
			$newData['data'] = $data;
		}
		return response()->json($newData, $code);
	}

	public function error($code, $errors, $message){
		return response()->json([
		    'success' => false,
			'message' => $message,
			'errors' => $errors
		], $code);
	}


}
