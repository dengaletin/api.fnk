<?php

namespace app\controllers;

use app\models\ExportForm;
use app\models\ImportForm;
use app\models\Version;
use Yii;
use app\models\CompanyValue;
use app\models\search\CompanyValueSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ValuesController implements the CRUD actions for CompanyValue model.
 */
class ValuesController extends Controller
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
                    'up-version' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all CompanyValue models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CompanyValueSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CompanyValue model.
     * @param integer $company_id
     * @param integer $year
     * @return mixed
     */
    public function actionView($company_id, $year)
    {
        return $this->render('view', [
            'model' => $this->findModel($company_id, $year),
        ]);
    }

    /**
     * Creates a new CompanyValue model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CompanyValue();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'company_id' => $model->company_id, 'year' => $model->year]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing CompanyValue model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $company_id
     * @param integer $year
     * @return mixed
     */
    public function actionUpdate($company_id, $year)
    {
        $model = $this->findModel($company_id, $year);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'company_id' => $model->company_id, 'year' => $model->year]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing CompanyValue model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $company_id
     * @param integer $year
     * @return mixed
     */
    public function actionDelete($company_id, $year)
    {
        $this->findModel($company_id, $year)->delete();

        return $this->redirect(['index']);
    }

    public function actionImport()
    {
        $model = new ImportForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->import()) {
                Yii::$app->session->setFlash('success', 'Отчёт импортирован.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('import', [
            'model' => $model,
        ]);
    }

    public function actionUpVersion()
    {
        $version = new Version();
        if ($version->save()) {
            Yii::$app->session->setFlash('success', 'Версия обновлена.');
        }

        return $this->redirect(['index']);
    }

    public function actionExport()
    {
        $model = new ExportForm();

        $file = $model->export();
        $version = Version::find()->max('id');
        $name = 'report-' . $version . '.xlsx';

        return Yii::$app->response->sendFile($file, $name);
    }

    /**
     * Finds the CompanyValue model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $company_id
     * @param integer $year
     * @return CompanyValue the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($company_id, $year)
    {
        if (($model = CompanyValue::findOne(['company_id' => $company_id, 'year' => $year])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
