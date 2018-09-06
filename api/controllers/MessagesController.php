<?php

namespace app\controllers;

use app\models\MessageQueue;
use app\models\search\MessageQueueSearch;
use Apple\ApnPush\Exception\ApnPushException;
use Yii;
use app\models\Message;
use app\models\search\MessageSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MessagesController implements the CRUD actions for Message model.
 */
class MessagesController extends Controller
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
                    //'send' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Message models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MessageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Message model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $searchModel = new MessageQueueSearch();
        $params = ArrayHelper::merge(Yii::$app->request->queryParams, [
            $searchModel->formName() => [
                'message_id' => $model->id,
            ],
        ]);
        $dataProvider = $searchModel->search($params);

        return $this->render('view', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Message model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Message();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Message model.
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
     * Updates an existing Message model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param integer $type - 0|1
     * @return mixed
     */
    public function actionSend($id, $type = 0)
    {
        //die('#0');

        $model = $this->findModel($id);

        $apnPush = Yii::$app->get('apnPush');
        $fireSend = Yii::$app->get('fireSend');

        $query = $model->getQueues()->with(['device', 'message'])
            ->andWhere(['status' => MessageQueue::STATUS_NEW])
            ->andWhere(['type' => $type])
            ->orWhere(['status' => MessageQueue::STATUS_ERROR])
            ->orderBy(['id' => SORT_ASC]);

        $fireSend->setMessage('iFinik', $model->message);

        $fireSend->onError(function($token, $queue, $message) {
            $queue->status = MessageQueue::STATUS_ERROR;
            $queue->response = $message;
            $queue->save();
        });

        $fireSend->onSuccess(function($token, $queue) {
            $queue->status = MessageQueue::STATUS_SUCCESS;
            $queue->response = null;
            $queue->save();
        });

        $fireSend->onReplace(function($token, $newToken, $queue) {
            $device = $queue->device;
            $device->firebase_token = $newToken;
            $device->save();

            \Yii::warning('FIREBASE: Replace firebase_token: from ' . $token . ' to ' . newToken);
        });

        foreach ($query->each() as $queue) {
            //echo "Process {$queue->device->id}<br>";

            //die('#1');

            /** @var MessageQueue $queue */
            if (!empty($queue->device->firebase_token)) {
                //die('#2');

                //echo "-- send using firebase<br>";
                $fireSend->send($queue->device->firebase_token, $queue);
            } else if (!empty($queue->device->apns_token)) {
                //die('#3');

                //echo "-- send using apns<br>";

                try {
                    $success = $apnPush->send($queue->device->device_token, $queue->message->message);

                    $queue->status = $success ? MessageQueue::STATUS_SUCCESS : MessageQueue::STATUS_ERROR;
                    $queue->response = null;
                    //echo "-- sending complete: {$success}<br>";
                } catch (ApnPushException $e) {
                    //echo "-- sending error: {$e->getMessage()}<br>";
                    $queue->status = MessageQueue::STATUS_ERROR;
                    $queue->response = $e->getMessage();
                }
                $queue->save();
            }
        }

        //die('#4');

        //exit;
        $fireSend->flush();

        //die('#5');

        if (!$type) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return true;
    }

    /**
     * Deletes an existing Device model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id = null)
    {
        if ($id) {
            $this->findModel($id)->delete();
        } elseif ($ids = Yii::$app->request->getBodyParam('ids')) {
            Message::deleteAll(['id' => $ids]);
        }

        return $this->redirect(Yii::$app->request->get('returnUrl', ['index']));
    }

    /**
     * Finds the Message model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Message the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Message::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
