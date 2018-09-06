<?php

use app\models\CompanyValue;
use app\models\ReportType;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\CompanyValueSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Отчёты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-value-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить отчёт', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Импорт', ['import'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Экспорт', ['export'], ['class' => 'btn btn-primary', 'data' => ['confirm' => 'Are you sure?']]) ?>
        <?= Html::a('Изменить версию', ['up-version'], ['class' => 'btn btn-primary', 'data' => ['method' => 'post', 'confirm' => 'Are you sure?']]) ?>
        <span style="margin-left: 10px; display: inline-block;">
            <?= Html::dropDownList('per-page', $dataProvider->pagination->pageSize, [
                20=> '20',
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
            'company_id',
            'year',
            'auditor',
            [
                'attribute' => 'report_type_id',
                'filter' => ArrayHelper::map(ReportType::find()->asArray()->all(), 'id', 'name'),
                'value' => function (CompanyValue $data) { return $data->reportType ? $data->reportType->name : ''; },
            ],
            'currency',

            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'white-space: nowrap'],
            ],
        ],
    ]); ?>

</div>
