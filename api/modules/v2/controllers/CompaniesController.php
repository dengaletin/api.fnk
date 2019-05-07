<?php

namespace app\modules\v2\controllers;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;

use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * Class CompaniesController
 */
class CompaniesController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['class'] = HttpBearerAuth::className();
        $behaviors['authenticator']['only'] = ['nope'];
        return $behaviors;
    }

    /**
     * @SWG\Get(path="/api/v2/companies",
     *     tags={"Companies"},
     *     summary="Companies list",
     *
     *     @SWG\Response(
     *         response= 200,
     *         description = "Retrieves the collection of Companies resources.",
     *     )
     * )
     */
    public function actionIndex()
    {
        return "Companies list";
    }

    /**
     * @SWG\Get(path="/api/v2/companies/{id}",
     *     tags={"Companies"},
     *     summary="Get company by id",
     *     @SWG\Parameter(
     *         in="path",
     *         name="id",
     *         description="ID of company that needs to be fetched",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response= 200,
     *         description = "Retrieves company by id.",
     *     )
     * )
     */
    public function actionView($id)
    {
        return "Company by id";
    }
}
