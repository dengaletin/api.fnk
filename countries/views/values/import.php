<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var app\models\ImportForm $model
 * @var yii\widgets\ActiveForm $form
 */

$draft_id = Yii::$app->request->getQueryParam('id');

$this->title = 'Импорт';
$this->params['breadcrumbs'][] = ['label' => 'Отчёты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<?= $form->field($model, 'file')->fileInput() ?>

<?= $form->field($model, 'clearValues')->checkbox() ?>

<div class="form-group">
    <?= Html::submitButton('Импортировать', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>