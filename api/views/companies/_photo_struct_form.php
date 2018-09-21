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

<?= $form->field($model, 'photo_struct')->hiddenInput(['value' => '1'])->label(false) ?>
<?= $form->field($model, 'year')->hiddenInput(['value' => '0'])->label(false) ?>

<div class="row">
    <div class="col-md-4">
        <?= $form->field($model, 'lang')->dropDownList(CompanyFile::getLangArray()) ?>
    </div>
</div>

<?= $form->field($model, 'upload_file')->fileInput(['accept' => 'image/jpeg,image/png']) ?>

<div class="form-group">
    <?= Html::submitButton('Загрузить', ['class' => 'btn btn-primary']) ?>
</div>


<?php ActiveForm::end(); ?>
