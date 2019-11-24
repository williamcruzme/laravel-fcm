<?php

namespace williamcruzme\FCM\Traits;

use Illuminate\Http\Request;

trait ManageDevices
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());

        // Retrieve device by token
        $device = $this->guard()->user()->devices()->whereToken($request->token)->first();

        if ($device) {
            $device->touch();
        } else {
            $this->guard()->user()->devices()->create($request->all());
        }

        return response()->json([
            'message' => 'Success',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        // Retrieve device by token
        $device = $this->guard()->user()->devices()->whereToken($request->token)->first();

        if (! $device) {
            return response()->json([
                'message' => 'Device not found.',
            ], 404);
        }

        $device->delete();

        return response()->json('', 204);
    }

    /**
     * Get the device management validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => ['required', 'string'],
        ];
    }

    /**
     * Get the device management validation error messages.
     *
     * @return array
     */
    protected function validationErrorMessages()
    {
        return [];
    }

    /**
     * Get the guard to be used during device management.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return auth()->guard();
    }
}
