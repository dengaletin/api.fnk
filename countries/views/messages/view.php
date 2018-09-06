<?php

use app\components\MessageQueueStatusColumn;
use app\components\MessageStatus;
use app\models\MessageQueue;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Message */
/* @var $searchModel app\models\search\MessageQueueSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Сообщения', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Отправить', ['send', 'id' => $model->id], [
            'class' => 'btn btn-success',
            'data' => [
                'confirm' => 'Are you sure you want to send this message?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'created_at:datetime',
            'message:ntext',
            [
                'attribute' => 'target',
                'value' => $model->getTargetName(),
            ],
            'language',
        ],
    ]) ?>

    <?= MessageStatus::widget(['model' => $model]); ?>

    <br />

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'device_id',
            'response',
            [
                'class' => MessageQueueStatusColumn::className(),
                'attribute' => 'status',
                'filter' => MessageQueue::getStatusArray(),
            ],
        ],
    ]); ?>

</div>
