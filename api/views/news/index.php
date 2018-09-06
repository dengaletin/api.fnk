<?php

use app\components\grid\ToggleColumn;
use app\models\News;
use app\models\Group;
use app\models\Mode;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;

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
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'title',
            'text:ntext',
            'date',
            'publish:boolean',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
