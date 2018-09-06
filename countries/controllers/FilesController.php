<?php

namespace app\controllers;

use Yii;
use app\models\CompanyFile;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FilesController implements the CRUD actions for CompanyFileFile model.
 */
class FilesController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Updates an existing CompanyFile model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $company_id
     * @param string $year
     * @param string $lang
     * @return mixed
     */
    public function actionUpdate($company_id, $year, $lang)
    {
        $model = $this->findModel($company_id, $year, $lang);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['companies/view', 'id' => $model->company_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing CompanyFile model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $company_id
     * @param string $year
     * @param string $lang
     * @return mixed
     */
    public function actionDelete($company_id, $year, $lang)
    {
        $model = $this->findModel($company_id, $year, $lang);
        $model->delete();

        return $this->redirect(['companies/view', 'id' => $model->company_id]);
    }

    /**
     * Finds the CompanyFile model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $company_id
     * @param string $year
     * @param string $lang
     * @return CompanyFile the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($company_id, $year, $lang)
    {
        if (($model = CompanyFile::findOne(['company_id' => $company_id, 'year' => $year, 'lang' => $lang])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
