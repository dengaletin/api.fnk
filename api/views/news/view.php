<?php

use app\models\NewsPhoto;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;


$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Новости', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-view">

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

    <div class="row">
        <div class="col-lg-9">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'title',
                    'text:ntext',
                    'date',
                    [
                        'label' => 'Опубликовано',
                        'value' => $model->publish ? 'Да' : 'Нет',
                    ]
                ],
            ]) ?>
        </div>
        <div class="col-lg-3">
            <div class="panel panel-default">
                <div class="panel-heading">Фотографии</div>
                <div class="panel-body" style="padding-bottom: 2px">
                    <?= GridView::widget([
                        'dataProvider' => new ActiveDataProvider([
                                'query' => $model->getPhotos()->orderBy(['id' => SORT_ASC]),
                                'pagination' => false,
                                'sort' => false,
                            ]),
                        'layout' => '{items}',
                        'columns' => [
                            [
                                'attribute' => 'name',
                                'value' => function (NewsPhoto $data) { return Html::a(Html::img($data->getThumbFileUrl('file', 'thumb')), $data->getImageFileUrl('file')); },
                                'format' => 'raw',
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>



</div>
