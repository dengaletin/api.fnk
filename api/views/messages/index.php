<?php

use app\components\grid\SetColumn;
use app\components\MessageStatus;
use app\models\Message;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\MessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Сообщения';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить сообщение', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Устройства', ['devices/index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить отмеченные', ['delete', 'returnUrl' => Yii::$app->request->url], ['id' => 'delete-selected', 'class' => 'btn btn-danger']) ?>
    </p>

    <?= GridView::widget([
        'id' => 'messages-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'ids',
            ],
            'id',
            'created_at:datetime',
            'message',
            [
                'attribute' => 'language',
                'filter' => Message::getLanguageArray(),
                'value' => function (Message $data) { return $data->getLanguageName(); },
            ],
            [
                'class' => SetColumn::className(),
                'attribute' => 'target',
                'filter' => Message::getTargetArray(),
                'name' => function (Message $data) { return $data->getTargetName(); },
                'cssCLasses' => [
                    Message::TARGET_ALL => 'primary',
                    Message::TARGET_PURCHASE => 'danger',
                    Message::TARGET_FREE => 'success',
                ],
            ],

            [
                'label' => 'Прогресс',
                'value' => function (Message $data) { return MessageStatus::widget(['model' => $data]); },
                'format' => 'raw',
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
    var ids = $('#messages-grid').yiiGridView('getSelectedRows');
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
