<?php

use app\models\Group;
use app\models\Mode;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Company */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="company-form">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'name_eng')->textInput(['maxlength' => 255]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name_for_list')->textInput(['maxlength' => 255]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'name_for_list_eng')->textInput(['maxlength' => 255]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name_full')->textInput(['maxlength' => 255]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'name_full_eng')->textInput(['maxlength' => 255]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'ticker')->textInput(['maxlength' => 255]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'ticker_eng')->textInput(['maxlength' => 255]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'photo_struct_ru')->fileInput(['accept' => 'image/jpeg,image/png'])->label('Изображение структуры компании') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'photo_struct_en')->fileInput(['accept' => 'image/jpeg,image/png'])->label('Company structure image') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'description_eng')->textarea(['rows' => 6]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'mode_id')->dropDownList(ArrayHelper::map(Mode::find()->asArray()->all(), 'id', 'name')) ?>

            <?= $form->field($model, 'group_id')->dropDownList(ArrayHelper::map(Group::find()->asArray()->all(), 'id', 'name')) ?>

            <?= $form->field($model, 'site')->textInput(['maxlength' => 255]) ?>

            <?= $form->field($model, 'free')->checkbox() ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'logo')->fileInput() ?>

            <?php if ($model->logo): ?>
                <p><?= Html::img($model->getThumbFileUrl('logo', 'logo'), ['width' => 150, 'height' => 150, 'style' => 'border: 1px solid #ccc']) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
