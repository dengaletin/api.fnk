<?php

namespace app\modules\v2\controllers;


use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;

use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * Class NewsController
 */
class NewsController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['class'] = HttpBearerAuth::className();
        $behaviors['authenticator']['only'] = ['nope'];
        return $behaviors;
    }

    /**
     * @SWG\Get(path="/api/v2/news",
     *     tags={"News"},
     *     summary="News list",
     *
     *     @SWG\Response(
     *         response= 200,
     *         description = "Retrieves the collection of News resources.",
     *     )
     * )
     */
    public function actionIndex()
    {
        return "News list";
    }

    /**
     * @SWG\Get(path="/api/v2/news/{id}",
     *     tags={"News"},
     *     summary="Get publication by id",
     *     @SWG\Parameter(
     *         in="path",
     *         name="id",
     *         description="ID of publication that needs to be fetched",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response= 200,
     *         description = "Retrieves publication by id.",
     *     )
     * )
     */
    public function actionView($id)
    {
        return "Publication by id";
    }
}
