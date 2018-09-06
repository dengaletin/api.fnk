<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\CompanyValue */

$this->title = $model->company_id;
$this->params['breadcrumbs'][] = ['label' => 'Отчёты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-value-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'company_id' => $model->company_id, 'year' => $model->year], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'company_id' => $model->company_id, 'year' => $model->year], [
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
            [
                'attribute' => 'company_id',
                'value' => $model->company ? $model->company->name : '',
            ],
            'year',
            [
                'attribute' => 'report_type_id',
                'value' => $model->reportType ? $model->reportType->name : '',
            ],
            'currency',
            'currency_eng',
            'auditor',
            'auditor_eng',
            'value_va',
            'value_oa',
            'value_ia',
            'value_kir',
            'value_dkiz',
            'value_v',
            'value_frn',
            'value_chpzp',
            'value_chdspood',
            'value_ebitda',
            'value_tebitda',
        ],
    ]) ?>

</div>
