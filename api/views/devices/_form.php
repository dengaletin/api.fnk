<?php

use app\models\Device;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Device */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="device-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'access_token')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_token')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'apns_token')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'firebase_token')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'language')->dropDownList(Device::getLanguageArray()) ?>

    <?php if ($profile = $model->profile): ?>

        <?= $form->field($profile, 'nickname')->textInput(['maxlength' => true]) ?>
        
        <?= $form->field($profile, 'login')->textInput(['maxlength' => true]) ?>

        <?= $form->field($profile, 'first_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($profile, 'last_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($profile, 'phone')->textInput(['maxlength' => true]) ?>

        <?= $form->field($profile, 'email')->textInput(['maxlength' => true]) ?>

        <?= $form->field($profile, 'confirm')->checkbox() ?>

        <?= $form->field($profile, 'avatar')->fileInput() ?>

        <?php if ($profile->avatar): ?>
            <p><?= Html::img($profile->getThumbFileUrl('avatar', 'avatar'), ['width' => 150, 'height' => 150, 'style' => 'border: 1px solid #ccc']) ?></p>
        <?php endif; ?>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавтиь' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
