<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CompanyFile */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Компании', 'url' => ['index']];
if ($model->company) $this->params['breadcrumbs'][] = ['label' => $model->company->name, 'url' => ['companies/view', 'id' => $model->company->id]];
$this->params['breadcrumbs'][] = $model->name;
?>
<div class="file-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
