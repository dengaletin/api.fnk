<?php

use yii\helpers\Html;
use app\models\NewsPhoto;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\widgets\DetailView;

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Новости', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="news-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="col-lg-8">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
    <div class="col-lg-4">
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
                            'value' => function (NewsPhoto $data) {
                                return Html::a(Html::img($data->getThumbFileUrl('file', 'thumb')),
                                    $data->getImageFileUrl('file'));
                            },
                            'format' => 'raw',
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{update} {delete}',
                            'controller' => 'news-photos',
                        ],
                    ],
                ]); ?>

                <?= $this->render('_photo_form', [
                    'model' => $photo,
                ]) ?>
            </div>
        </div>
    </div>

</div>
