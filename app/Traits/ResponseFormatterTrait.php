<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

/**
 * Traits ResponseFormatterTrait
 * @package App\Traits
 */
trait ResponseFormatterTrait
{
    /**
     * @param string $message
     * @param array $data
     * @return JsonResponse
     */
    public static function responseSuccess(string $message = 'Success', array $data = []): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data'    => $data,
        ], Response::HTTP_OK);
    }


    /**
     * @param array $data
     * @param string $message
     * @return JsonResponse
     */
    public function createdResponse( string $message = 'Success', array $data = []): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data'    => $data,
        ], Response::HTTP_CREATED);
    }


    /**
     * @param array $data
     * @param $statusCode
     * @param string $message
     * @return JsonResponse
     */
    public static function responseError(string $message = 'Error', array $data, $statusCode): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data'    => $data,
        ], $statusCode);
    }

    /**
     * @param Validator $validator
     * @return mixed
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => "The given data was invalid.",
            'errors' => $validator->errors(),
        ], 422));
    }
}
