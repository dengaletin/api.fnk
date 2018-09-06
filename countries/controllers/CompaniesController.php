<?php

namespace app\controllers;

use app\models\CompanyFile;
use app\models\CompanyPhotoForm;
use Yii;
use app\models\Company;
use app\models\search\CompanySearch;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\Url;

/**
 * CompaniesController implements the CRUD actions for Company model.
 */
class CompaniesController extends Controller
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
     * Lists all Company models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CompanySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Company model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $file = new CompanyFile();

        if ($file->load(Yii::$app->request->post())) {
            if (isset($_POST['CompanyFile']['photo_struct'])) {
                if (!$_FILES['CompanyFile']['error']['upload_file']) {
                    switch ($_FILES['CompanyFile']['type']['upload_file']) {
                        case 'image/jpeg':
                            $f_ext = 'jpg';
                            break;
                        case 'image/png':
                            $f_ext = 'png';
                            break;
                    }
                    $filename = "{$_POST['CompanyFile']['lang']}.{$f_ext}";

                    //
                    if (!file_exists(__DIR__."/../web/upload/companies/company-{$id}")) {
                        mkdir(__DIR__."/../web/upload/companies/company-{$id}");
                    }

                    if (
                        move_uploaded_file(
                            $_FILES['CompanyFile']['tmp_name']['upload_file'],
                            __DIR__."/../web/upload/companies/company-{$id}/{$filename}"
                        )
                    ) {
                        $company = Company::find()->where(['id' => $id])->limit(1)->one();

                        $prop = "photo_struct_{$_POST['CompanyFile']['lang']}";

                        $company->$prop = "/upload/companies/company-{$id}/{$filename}";
                        $company->save();
                    }
                    //
                }

                return $this->redirect(['', 'id' => $model->id]);
            }

            if ($exists = CompanyFile::findOne(['company_id' => $model->id, 'year' => $file->year, 'lang' => $file->lang])) {
                /** @var CompanyFile $exists */
                $exists->load(Yii::$app->request->post());
                $file = $exists;
            }

            $file->company_id = $model->id;

            if ($file->save()) {
                return $this->redirect(['', 'id' => $model->id]);
            }
        }

        $photo_form = new CompanyPhotoForm();
        if (Yii::$app->request->isPost) {
            $photo_form->imageFiles = UploadedFile::getInstances($photo_form, 'imageFiles');

            if($photo_form->upload($model->id)) {
                return $this->redirect(['', 'id' => $model->id]);
            }
        }

        return $this->render('view', [
            'model' => $model,
            'file' => $file,
            'photo' => $photo_form,
        ]);
    }

    /**
     * Creates a new Company model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Company();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            // Изображения структуры компании
            foreach (['ru', 'en'] as $lang) {
                if (!$_FILES['Company']['error']["photo_struct_{$lang}"]) {
                    switch ($_FILES['Company']['type']["photo_struct_{$lang}"]) {
                        case 'image/jpeg':
                            $f_ext = 'jpg';
                            break;
                        case 'image/png':
                            $f_ext = 'png';
                            break;
                    }
                    $filename = "{$lang}.{$f_ext}";

                    //
                    if (!file_exists(__DIR__."/../web/upload/companies/company-{$model->id}")) {
                        mkdir(__DIR__."/../web/upload/companies/company-{$model->id}");
                    }

                    if (
                        move_uploaded_file(
                            $_FILES['Company']['tmp_name']["photo_struct_{$lang}"],
                            __DIR__."/../web/upload/companies/company-{$model->id}/{$filename}"
                        )
                    ) {
                        $prop = "photo_struct_{$lang}";
                        $model->{$prop} = "/upload/companies/company-{$model->id}/{$filename}";
                        $model->save();
                    }
                    //
                }
            }
            //

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Company model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Company model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $id
     * @param string $param
     * @param string $return
     * @throws \yii\web\BadRequestHttpException
     * @return mixed
     */
    public function actionToggle($id, $param, $return = '')
    {
        if (!in_array($param, ['free'])) {
            throw new BadRequestHttpException('Invalid toggle parameter');
        }
        $model = $this->findModel($id);
        $model->$param = $model->$param ? 0 : 1;
        $model->save();

        return $this->redirect($return ?: ['index']);
    }

    /**
     * Finds the Company model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Company the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Company::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
