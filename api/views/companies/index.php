<?php

use app\components\grid\ToggleColumn;
use app\models\Company;
use app\models\Group;
use app\models\Mode;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\CompanySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Компании';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить компанию', ['create'], ['class' => 'btn btn-success']) ?>
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

            [
                'attribute' => 'logo',
                'value' => function (Company $data) {
                        return $data->logo ? Html::img($data->getThumbFileUrl('logo', 'logo'), ['width' => 50, 'height' => 50, 'style' => 'border: 1px solid #ccc']) : '';
                    },
                'format' => 'raw',
            ],
            'id',
            [
                'attribute' => 'name',
                'value' => function (Company $data) { return Html::a(Html::encode($data->name), ['view', 'id' => $data->id]); },
                'format' => 'raw',
            ],
            'parser_variations',
            'ticker',
            'name_full',
            [
                'attribute' => 'group_id',
                'filter' => ArrayHelper::map(Group::find()->asArray()->all(), 'id', 'name'),
                'value' => function (Company $data) { return $data->group ? $data->group->name : ''; },
            ],
            [
                'attribute' => 'mode_id',
                'filter' => ArrayHelper::map(Mode::find()->asArray()->all(), 'id', 'name'),
                'value' => function (Company $data) { return $data->mode ? $data->mode->name : ''; },
            ],
            [
                'class' => ToggleColumn::className(),
                'label' => 'Free',
                'attribute' => 'free',
                'name' => function (Company $data) { return $data->free ? 'Да' : 'Нет'; },
                'cssCLasses' => [0 => 'default', 1 => 'success'],
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'white-space: nowrap'],
            ],
        ],
    ]); ?>

</div>
