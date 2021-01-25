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
        $request->validate($this->createRules(), $this->validationErrorMessages());

        $device = $this->guard()->user()->devices()->whereToken($request->token)->first();

        if ($device) {
            $device->touch();
        } else {
            $device = $this->guard()->user()->devices()->create($request->all());
        }

        return $this->sendResponse($device);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $request->validate($this->deleteRules(), $this->validationErrorMessages());

        $model = $this->guard()->user()->devices()->firstWhere('token', $request->token);

        optional($model)->delete();

        return $this->sendDestroyResponse($model);
    }

    /**
     * Get the validation rules that apply to the create a device.
     *
     * @return array
     */
    protected function createRules()
    {
        return [
            'token' => ['required', 'string'],
        ];
    }

    /**
     * Get the validation rules that apply to the delete a device.
     *
     * @return array
     */
    protected function deleteRules()
    {
        return [
            'token' => ['required', 'string', 'exists:devices,token'],
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
     * Get the response for a successful storing device.
     *
     * @param  williamcruzme\FCM\Device  $model
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResponse($model)
    {
        return response()->json($model);
    }

    /**
     * Get the response for a successful deleting device.
     *
     * @param  williamcruzme\FCM\Device  $model
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendDestroyResponse($model)
    {
        return response()->json('', 204);
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
