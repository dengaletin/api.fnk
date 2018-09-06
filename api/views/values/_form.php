<?php

use app\models\ReportType;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CompanyValue */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="company-value-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'company_id')->textInput() ?>

    <?= $form->field($model, 'year')->textInput() ?>

    <?= $form->field($model, 'report_type_id')->dropDownList(ArrayHelper::map(ReportType::find()->asArray()->all(), 'id', 'name')) ?>

    <?= $form->field($model, 'currency')->textInput() ?>

    <?= $form->field($model, 'auditor')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'value_va')->textInput() ?>

    <?= $form->field($model, 'value_oa')->textInput() ?>

    <?= $form->field($model, 'value_ia')->textInput() ?>

    <?= $form->field($model, 'value_kir')->textInput() ?>

    <?= $form->field($model, 'value_dkiz')->textInput() ?>

    <?= $form->field($model, 'value_v')->textInput() ?>

    <?= $form->field($model, 'value_frn')->textInput() ?>

    <?= $form->field($model, 'value_chpzp')->textInput() ?>

    <?= $form->field($model, 'value_chdspood')->textInput() ?>

    <?= $form->field($model, 'value_ebitda')->textInput() ?>

    <?= $form->field($model, 'value_tebitda')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
