<?php

use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Device */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Устройства', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="device-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
            'device_token',
            'access_token',
            'apns_token',
            'firebase_token',
            [
                'attribute' => 'language',
                'value' => $model->getLanguageName(),
            ],
            [
                'attribute' => 'purchase',
                'value' => $model->getPurchaseName(),
            ],
            [
                'attribute' => 'profile.nickname',
                'visible' => $model->profile !== null,
            ],
            [
                'attribute' => 'profile.login',
                'visible' => $model->profile !== null,
            ],
            [
                'attribute' => 'profile.avatar',
                'visible' => $model->profile !== null,
                'value' => $model->profile !== null ? Html::img($model->profile->getThumbFileUrl('avatar', 'avatar'), ['width' => 150, 'height' => 150, 'style' => 'border: 1px solid #ccc']) : '',
                'format' => 'raw',
            ],
            [
                'attribute' => 'profile.first_name',
                'visible' => $model->profile !== null,
            ],
            [
                'attribute' => 'profile.last_name',
                'visible' => $model->profile !== null,
            ],
            [
                'attribute' => 'profile.phone',
                'visible' => $model->profile !== null,
            ],
            [
                'attribute' => 'profile.email',
                'visible' => $model->profile !== null,
            ],
            [
                'attribute' => 'profile.registered_on',
                'visible' => $model->profile !== null,
            ],
            [
                'attribute' => 'profile.confirm',
                'visible' => $model->profile !== null,
                'format' => 'boolean'
            ],
            [
                'attribute' => 'profile.expired_at',
                'visible' => $model->profile !== null,
                'format' => 'datetime'
            ],
        ],
    ]) ?>

    <?= GridView::widget([
        'id' => 'devices-grid',
        'dataProvider' => new ActiveDataProvider(['query' => $model->getPurchases()->with(['product'])->orderBy('id')]),
        'columns' => [
            'id',
            [
                'attribute' => 'product_id',
                'value' => 'product.name',
            ],
            'created_at:datetime',
            'expired_at:datetime',
        ],
    ]); ?>

</div>
