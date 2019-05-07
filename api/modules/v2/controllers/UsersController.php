<?php

namespace app\modules\v2\controllers;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use app\models\Users;
use yii\web\UploadedFile;

/**
 * Class UsersController
 */
class UsersController extends Controller
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['class'] = HttpBearerAuth::className();
        $behaviors['authenticator']['only'] = ['nope'];
        return $behaviors;
    }

    /**
     * @SWG\Get(path="/api/v2/users",
     *     tags={"User"},
     *     summary="Retrieves the collection of User resources.",
     *     @SWG\Response(
     *         response= 200,
     *         description = "User collection response",
     *
     *     )
     * )
     */
    public function actionIndex()
    {

        die();
    }

    /**
     * @SWG\Get(
     *     path="/api/v2/users/{id}",
     *     tags={"User"},
     *     summary="Find user by ID",
     *     @SWG\Parameter(
     *         in="path",
     *         name="id",
     *         description="ID of user that needs to be fetched",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="User collection response"
     *     ),
     * )
     *
     * @param int $id
     */
    public function actionView($id)
    {
        $user = Users::findById($id);
        if ($user) {}
        $data = [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'email_new' => $user->email_new,
            'phone' => $user->phone,
            'phone_new' => $user->phone_new

        ];
    }

    /**
     * @SWG\Post(
     *     path="/api/v2/users",
     *     tags={"User"},
     *     summary="Create new user",
     *     @SWG\Parameter(
     *         in="formData",
     *         name="first_name",
     *         description="first_name",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         in="formData",
     *         name="last_name",
     *         description="last_name",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         in="formData",
     *         name="email",
     *         description="email",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         in="formData",
     *         name="phone",
     *         description="phone",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         in="formData",
     *         name="password",
     *         description="password",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         in="formData",
     *         name="password_repeat",
     *         description="password_repeat",
     *         required=true,
     *         type="string"
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="User created",
     *     )
     * )
     */
    public function actionCreate()
    {
        $user = new Users();
        if ($user->load(Yii::$app->request->post(), '')) {
            if ($user->validate()) {
                $user->email_code = Yii::$app->security->generateRandomString(6);
                $user->phone_code = Yii::$app->security->generateRandomString(6);
                $user->bearer_token = Yii::$app->security->generateRandomString();
                $user->password_hash = Yii::$app->getSecurity()->generatePasswordHash($user->password);
                $user->save();
                $data = [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                ];
                return $data;
            } else {
                Yii::$app->response->statusCode = 400;
                $errors = [];

                foreach ($user->getErrors() as $attribute => $attribute_errors) {
                    $errors[$attribute] = $attribute_errors;
                }
                $data['errors'] = $errors;

                return $data;
            }
        };
    }

    /**
     * @SWG\Patch(
     *     path="/api/v2/users{id}",
     *     tags={"User"},
     *     summary="Update user by id",
     *     @SWG\Parameter(
     *         in="path",
     *         name="id",
     *         description="id",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         in="formData",
     *         name="first_name",
     *         description="first_name",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         in="formData",
     *         name="last_name",
     *         description="last_name",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         in="formData",
     *         name="phone_new",
     *         description="phone_new",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         in="formData",
     *         name="email_new",
     *         description="email_new",
     *         required=false,
     *         type="string"
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="User with {id} updated",
     *     )
     * )
     */
    public function actionUpdate($id)
    {
        if (!$user = Users::find()->where(['id' => $id])->one()) {
            throw new NotFoundHttpException('User with id:' . $id . ' not found');
        }

        if ($first_name = Yii::$app->request->post('first_name')) {
            $user->first_name = $first_name;
        }

        if ($last_name = Yii::$app->request->post('last_name')) {
            $user->last_name = $last_name;
        }

        if ($phone_new = Yii::$app->request->post('phone_new')) {
            if ($phone_new == $user->phone) {
                throw new BadRequestHttpException('phone_new & phone ident');
            }
            $user->phone_code = Yii::$app->security->generateRandomString(6);
            $user->phone_new = $phone_new;
            $user->status = 0;
        }

        if ($email_new = Yii::$app->request->post('email_new')) {
            if ($email_new == $user->email) {
                throw new BadRequestHttpException('email_new & email ident');
            }
            $user->email_code = Yii::$app->security->generateRandomString(6);
            $user->email_new = $email_new;
            $user->status = 0;
        }

        if (!$user->save()) {
            throw new ServerErrorHttpException(json_encode($user->errors));
        }

        return [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone_new' => $user->phone_new,
            'email_new' => $user->email_new,
        ];
    }

    /**
     * @SWG\Post(
     *     path="/api/v2/users/{id}/email-confirm",
     *     tags={"User"},
     *     summary="Confirm user email",
     *     @SWG\Parameter(
     *         in="path",
     *         name="id",
     *         description="id",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         in="path",
     *         name="email_code",
     *         description="email_code",
     *         required=true,
     *         type="string"
     *     ),
     *
     *     @SWG\Response(
     *         response=204,
     *         description="Email confirmed",
     *     )
     * )
     */
    public function actionEmailConfirm($id)
    {
        if (!$email_code = Yii::$app->request->post('email_code')) {
            throw new BadRequestHttpException('Empty email_code param');
        }

        if (!$user = Users::find()->where(['id' => $id])->one()) {
            throw new NotFoundHttpException('User with id:' . $id . ' not found');
        }

        if (!$user->email) {
            throw new NotFoundHttpException('Empty Email');
        }

        if ($email_code != $user->email_code) {
            throw new BadRequestHttpException('Invalid email_code');
        }

        $user->email_code = Yii::$app->security->generateRandomString(6);
        $user->status = true;

        if (!$user->save()) {
            throw new ServerErrorHttpException(json_encode($user->errors));
        }

        Yii::$app->response->statusCode = 204;
    }

    /**
     * @SWG\Post(
     *     path="/api/v2/users/{id}/phone-confirm",
     *     tags={"User"},
     *     summary="Confirm user phone",
     *     @SWG\Parameter(
     *         in="path",
     *         name="id",
     *         description="id",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         in="path",
     *         name="phone_code",
     *         description="phone_code",
     *         required=true,
     *         type="string"
     *     ),
     *
     *     @SWG\Response(
     *         response=204,
     *         description="Phone confirmed",
     *     )
     * )
     */
    public function actionPhoneConfirm($id)
    {
        if (!$phone_code = Yii::$app->request->post('phone_code')) {
            throw new BadRequestHttpException('Empty phone_code param');
        }

        if (!$user = Users::find()->where(['id' => $id])->one()) {
            throw new NotFoundHttpException('User with id:' . $id . ' not found');
        }

        if (!$user->phone) {
            throw new NotFoundHttpException('Empty Phone');
        }

        if ($phone_code != $user->phone_code) {
            throw new BadRequestHttpException('Invalid phone_code');
        }

        $user->phone_code = Yii::$app->security->generateRandomString(6);
        $user->status = true;

        if (!$user->save()) {
            throw new ServerErrorHttpException(json_encode($user->errors));
        }

        Yii::$app->response->statusCode = 204;
    }

    /**
     * @SWG\Post(
     *     path="/api/v2/users/{id}/email-update-confirm",
     *     tags={"User"},
     *     summary="Confirm updated user email",
     *     @SWG\Parameter(
     *         in="path",
     *         name="id",
     *         description="id",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         in="path",
     *         name="email_code",
     *         description="email_code",
     *         required=true,
     *         type="string"
     *     ),
     *
     *     @SWG\Response(
     *         response=204,
     *         description="Email confirmed",
     *     )
     * )
     */
    public function actionEmailUpdateConfirm($id)
    {
        if (!$email_code = Yii::$app->request->post('email_code')) {
            throw new BadRequestHttpException('Empty email_code param');
        }

        if (!$user = Users::find()->where(['id' => $id])->one()) {
            throw new NotFoundHttpException('User with id:' . $id . ' not found');
        }

        if (!$email_new = $user->email_new) {
            throw new BadRequestHttpException('Empty email_new');
        }

        if ($email_code != $user->email_code) {
            throw new BadRequestHttpException('Invalid email_code');
        }

        $user->email_code = Yii::$app->security->generateRandomString(6);
        $user->email = $user->email_new;
        $user->email_new = null;
        $user->status = true;

        if (!$user->save()) {
            throw new ServerErrorHttpException(json_encode($user->errors));
        }

        Yii::$app->response->statusCode = 204;
    }

    /**
     * @SWG\Post(
     *     path="/api/v2/users/{id}/phone-update-confirm",
     *     tags={"User"},
     *     summary="Confirm updated user phone",
     *     @SWG\Parameter(
     *         in="path",
     *         name="id",
     *         description="id",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         in="path",
     *         name="phone_code",
     *         description="phone_code",
     *         required=true,
     *         type="string"
     *     ),
     *
     *     @SWG\Response(
     *         response=204,
     *         description="Phone confirmed",
     *     )
     * )
     */
    public function actionPhoneUpdateConfirm($id)
    {
        if (!$phone_code = Yii::$app->request->post('phone_code')) {
            throw new BadRequestHttpException('Empty phone_code param');
        }

        if (!$user = Users::find()->where(['id' => $id])->one()) {
            throw new NotFoundHttpException('User with id:' . $id . ' not found');
        }

        if (!$phone_new = $user->phone_new) {
            throw new BadRequestHttpException('Empty phone_new');
        }

        if ($phone_code != $user->phone_code) {
            throw new BadRequestHttpException('Invalid phone_code');
        }

        $user->phone_code = Yii::$app->security->generateRandomString(6);
        $user->phone = $user->phone_new;
        $user->phone_new = null;
        $user->status = true;

        if (!$user->save()) {
            throw new ServerErrorHttpException(json_encode($user->errors));
        }

        Yii::$app->response->statusCode = 204;
    }

    /**
     * @SWG\Post(
     *     path="/api/v2/users/{id}/avatar",
     *     tags={"User"},
     *     summary="Create user avatar",
     *     consumes={"multipart/form-data"},
     *     @SWG\Parameter(
     *         in="path",
     *         name="id",
     *         description="id",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         in="formData",
     *         name="file",
     *         description="file",
     *         required=true,
     *         type="file"
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="Avatar created",
     *     )
     * )
     */
    public function actionAvatarCreate($id)
    {
        $user = Users::findById($id);

        if (!$user) {
            throw new NotFoundHttpException('User with id:' . $id . ' not found');
        }

        $user->file = UploadedFile::getInstanceByName('file');

        if (!$user->file) {
            throw new BadRequestHttpException('File required');
        }

        if ($user->avatar != null) {
            throw new BadRequestHttpException('Image is already uploaded');
        }

        if (!$user->validate()) {
            foreach ($user->getErrors() as $attribute => $attribute_errors) {
                $errors[$attribute] = $attribute_errors;
            }
            $data['errors'] = $errors;
            return $data;
        }

        $user->avatar = Users::uploadFile($user, 'avatar');
        $user->file = null;

        $response = \Yii::$app->getResponse();
        if ($user->save()) {
            $response->setStatusCode(201);
            return [
                'avatar' => $user->avatar,
            ];
        } elseif (!$user->hasErrors()) {
            $response->setStatusCode(500);
            throw new ServerErrorHttpException ('Failed to create the object for unknown reason.');
        }
    }

    /**
     * @SWG\Post(
     *     path="/api/v2/users/{id}/avatar-update",
     *     tags={"User"},
     *     summary="Update user avatar",
     *     consumes={"multipart/form-data"},
     *     @SWG\Parameter(
     *         in="path",
     *         name="id",
     *         description="id",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         in="formData",
     *         name="file",
     *         description="file",
     *         required=true,
     *         type="file"
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="Avatar updated",
     *     )
     * )
     */
    public function actionAvatarUpdate($id)
    {
        $user = Users::findById($id);
        $response = \Yii::$app->getResponse();

        if (!$user) {
            throw new NotFoundHttpException('User with id:' . $id . ' not found');
        }

        $user->file = UploadedFile::getInstanceByName('file');

        if (!$user->file) {
            throw new BadRequestHttpException('File required');
        }

        if (!$user->validate()) {
            foreach ($user->getErrors() as $attribute => $attribute_errors) {
                $errors[$attribute] = $attribute_errors;
            }
            $data['errors'] = $errors;
            return $data;
        }

        if ($user->avatar) {
            $uploadDir = Yii::getAlias('@users_path');
            if (unlink($uploadDir . '/' . $user->id . '/avatar/' . $user->avatar)) {
                $user->avatar = null;
                if ($user->save()) {
                    $response->setStatusCode(204);
                } elseif (!$user->hasErrors()) {
                    $response->setStatusCode(500);
                    throw new ServerErrorHttpException ('Failed for unknown reason.');
                }
            } else {
                throw new ServerErrorHttpException ('Failed to delete the file for unknown reason.');
            }
        }

        $user->avatar = Users::uploadFile($user, 'avatar');
        $user->file = null;


        if ($user->save()) {
            $response->setStatusCode(201);
            return [
                'avatar' => $user->avatar,
            ];
        } elseif (!$user->hasErrors()) {
            $response->setStatusCode(500);
            throw new ServerErrorHttpException ('Failed to create the object for unknown reason.');
        }
    }

    /**
     * @SWG\Delete(
     *     path="/api/v2/users/{id}/avatar",
     *     tags={"User"},
     *     summary="Delete user avatar by id",
     *     @SWG\Parameter(
     *         in="path",
     *         name="id",
     *         description="id",
     *         required=true,
     *         type="integer"
     *     ),
     *
     *     @SWG\Response(
     *         response=204,
     *         description="Avatar deleted",
     *     )
     * )
     */
    public function actionAvatarDelete($id)
    {
        $user = Users::findById($id);

        if (!$user->avatar) {
            throw new NotFoundHttpException('Avatar is not exist');
        }

        $uploadDir = Yii::getAlias('@users_path');
        if (unlink($uploadDir . '/' . $user->id . '/avatar/' . $user->avatar)) {
            $user->avatar = null;

            $response = \Yii::$app->getResponse();
            if ($user->save()) {
                $response->setStatusCode(204);
            } elseif (!$user->hasErrors()) {
                $response->setStatusCode(500);
                throw new ServerErrorHttpException ('Failed for unknown reason.');
            }
        } else {
            throw new ServerErrorHttpException ('Failed to delete the file for unknown reason.');
        }
    }
}
