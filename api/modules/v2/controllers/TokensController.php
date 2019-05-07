<?php

namespace app\modules\v2\controllers;

use app\models\Users;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;

use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * Class TokensController
 */
class TokensController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['class'] = HttpBearerAuth::className();
        $behaviors['authenticator']['only'] = ['nope'];
        return $behaviors;
    }

    /**
     * @SWG\Get(path="/api/v2/tokens/email",
     *     tags={"Tokens"},
     *     summary="Simple (email+password) auth",
     *      @SWG\Parameter(
     *         in="path",
     *         name="email",
     *         description="email",
     *         required=true,
     *         type="string"
     *     ),
     *      @SWG\Parameter(
     *         in="path",
     *         name="password",
     *         description="password",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response= 200,
     *         description = "token",
     *
     *     )
     * )
     */
    public function actionEmail()
    {
        if (!$email = Yii::$app->request->post('email')) {
            throw new BadRequestHttpException('Empty email param');
        }

        if (!$password = Yii::$app->request->post('password')) {
            throw new BadRequestHttpException('Empty password param');
        }

        if (!$user = Users::findByEmail($email)) {
            throw new NotFoundHttpException('User with email:' . $email . ' not found');
        }

        if (Yii::$app->getSecurity()->validatePassword($password, $user->password_hash)) {
            return [
                'token' => $user->bearer_token,
            ];
        } else {
            throw new UnauthorizedHttpException('Wrong password');
        }
    }

    /**
     * @SWG\Get(path="/api/v2/tokens/phone",
     *     tags={"Tokens"},
     *     summary="Simple (phone+password) auth",
     *      @SWG\Parameter(
     *         in="path",
     *         name="phone",
     *         description="phone",
     *         required=true,
     *         type="string"
     *     ),
     *      @SWG\Parameter(
     *         in="path",
     *         name="password",
     *         description="password",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response= 200,
     *         description = "token",
     *
     *     )
     * )
     */
    public function actionPhone()
    {
        if (!$phone = Yii::$app->request->post('phone')) {
            throw new BadRequestHttpException('Empty phone param');
        }

        if (!$password = Yii::$app->request->post('password')) {
            throw new BadRequestHttpException('Empty password param');
        }

        if (!$user = Users::findByPhone($phone)) {
            throw new NotFoundHttpException('User with phone:' . $phone . ' not found');
        }

        if (Yii::$app->getSecurity()->validatePassword($password, $user->password_hash)) {
            return [
                'token' => $user->bearer_token,
            ];
        } else {
            throw new UnauthorizedHttpException('Wrong password');
        }
    }

    public function action2fa()
    {
        return "2fa";
    }

    /**
     * @SWG\Get(path="/api/v2/tokens/oauth",
     *     tags={"Tokens"},
     *     summary="OAuth authentication",
     *      @SWG\Parameter(
     *         in="path",
     *         name="service",
     *         description="service",
     *         required=true,
     *         type="string"
     *     ),
     *      @SWG\Parameter(
     *         in="path",
     *         name="access_token",
     *         description="access_token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response= 200,
     *         description = "token",
     *
     *     )
     * )
     */
    public function actionOauth()
    {
        return "Oauth";
    }
}
