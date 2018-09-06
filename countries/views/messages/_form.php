<?php

use app\models\Message;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Message */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="message-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if ($model->isNewRecord): ?>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'target')->dropDownList(Message::getTargetArray()) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'language')->dropDownList(Message::getLanguageArray()) ?>
            </div>
        </div>
    <?php endif; ?>

    <?= $form->field($model, 'message')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
