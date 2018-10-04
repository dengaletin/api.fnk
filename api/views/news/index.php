<?php

use app\models\News;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\NewsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Новости';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить новость', ['create'], ['class' => 'btn btn-success']) ?>
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
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterSelector' => 'select[name="per-page"]',
        'columns' => [
            'id',
            'source.post_time',
            'date',
            [
                'attribute' => 'source.source_host',
                'value' => function (News $model) {
                    return Html::a(Html::encode($model->source->source_host),
                        $model->source->source_url,
                        ['target' => '_blank']);
                },
                'format' => 'raw',
            ],
            'title',
            [
                'attribute' => 'text',
                'value' => function ($model) {
                    return StringHelper::truncateWords($model->text, 20, '...', false);
                }
            ],
            [
                'attribute' => 'companies.name',
                'value' => function (News $model) {// TODO: Галочка здесь!
                    return implode(', ',array_map(function (){}, $model->companies, 'id', 'name'));
//                    return Html::a(Html::encode($model->companyies->name),
//                        ['companies/view', 'id' => $model->company->id],
//                        ['target' => '_blank']);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'logo',
                'value' => function (News $data) {
                    return $data->photos ? Html::img($data->photos[0]->getThumbFileUrl('file'), ['width' => 50, 'height' => 50, 'style' => 'border: 1px solid #ccc']) : '';
                },
                'format' => 'raw',
            ],
            'publish:boolean',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
