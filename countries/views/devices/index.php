<?php

use app\components\grid\SetColumn;
use app\models\Device;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\DeviceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Устройства';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="device-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить устройство', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Сообщения', ['messages/index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Экспорт профилей', ['export'], ['class' => 'btn btn-primary', 'data' => ['confirm' => 'Are you sure?']]) ?>
        <?= Html::a('Удалить отмеченные', ['delete', 'returnUrl' => Yii::$app->request->url], ['id' => 'delete-selected', 'class' => 'btn btn-danger']) ?>
        <span style="margin-left: 10px; display: inline-block;">
            <?= Html::dropDownList('per-page', $dataProvider->pagination->pageSize, [
                20 => '20',
                100 => '100',
                1000 => '1000',
                $dataProvider->getTotalCount() => 'Все',
            ], ['class' => 'form-control']) ?>
        </span>
    </p>

    <?= GridView::widget([
        'id' => 'devices-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterSelector' => 'select[name="per-page"]',
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'ids',
            ],
            'id',
            [
                'attribute' => 'access_token',
                'value' => function (Device $data) { return StringHelper::truncate($data->access_token, 10); },
            ],
            [
                'attribute' => 'device_token',
                'value' => function (Device $data) { return StringHelper::truncate($data->device_token, 10); },
            ],
            [
                'label' => 'Псевдоним',
                'attribute' => 'nickname',
                'value' => 'profile.nickname'
            ],
            [
                'label' => 'Имя',
                'attribute' => 'first_name',
                'value' => 'profile.first_name'
            ],
            [
                'label' => 'Фамилия',
                'attribute' => 'last_name',
                'value' => 'profile.last_name'
            ],
            [
                'label' => 'Телефон',
                'attribute' => 'phone',
                'value' => 'profile.phone'
            ],
            [
                'label' => 'Email',
                'attribute' => 'email',
                'value' => 'profile.email'
            ],
            [
                'attribute' => 'language',
                'filter' => Device::getLanguageArray(),
                'value' => function (Device $data) { return $data->getLanguageName(); },
            ],
            [
                'class' => SetColumn::className(),
                'attribute' => 'purchase',
                'name' => function (Device $data) { return $data->getPurchaseName(); },
                'cssCLasses' => [0 => 'default', 1 => 'success'],
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'white-space: nowrap'],
            ],
        ],
    ]); ?>

</div>

<?php $this->registerJs("
$(document).on('click', '#delete-selected', function () {
    var ids = $('#devices-grid').yiiGridView('getSelectedRows');
    if (ids) {
        if (confirm('Удалить?')) {
            $.ajax({
                method: 'post',
                url: $(this).attr('href'),
                data: {
                    ids: ids
                }
            });
        }
    }
    return false;
});
");