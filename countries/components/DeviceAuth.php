<?php

namespace app\components;

use yii\filters\auth\AuthMethod;

class DeviceAuth extends AuthMethod
{
    public function authenticate($user, $request, $response)
    {
        $authToken = $request->getHeaders()->get('X-User-Token');

        if (!empty($authToken)) {
            $identity = $user->loginByAccessToken($authToken, get_class($this));
            if ($identity === null) {
                $this->handleFailure($response);
            }
            return $identity;
        }

        return null;
    }
}