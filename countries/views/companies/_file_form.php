<?php

use app\models\CompanyFile;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CompanyFile */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'],
]); ?>

<div class="row">
    <div class="col-md-8">
        <?= $form->field($model, 'year')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'lang')->dropDownList(CompanyFile::getLangArray()) ?>
    </div>
</div>

<?= $form->field($model, 'upload_file')->fileInput(['accept' => 'application/pdf']) ?>

<div class="form-group">
    <?= Html::submitButton('Загрузить', ['class' => 'btn btn-primary']) ?>
</div>


<?php ActiveForm::end(); ?>
