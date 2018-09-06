<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Currency */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="year-currency-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'year')->textInput() ?>

    <?= $form->field($model, 'eur_avg')->textInput() ?>

    <?= $form->field($model, 'eur_rep')->textInput() ?>

    <?= $form->field($model, 'usd_avg')->textInput() ?>

    <?= $form->field($model, 'usd_rep')->textInput() ?>

    <?= $form->field($model, 'eurusd_avg')->textInput() ?>

    <?= $form->field($model, 'eurusd_rep')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
