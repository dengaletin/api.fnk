<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\News */
/* @var $form yii\widgets\ActiveForm */
?>

    <div class="news-form">
        <?php $form = ActiveForm::begin([
            'options' => ['enctype' => 'multipart/form-data'],
        ]); ?>

        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'publish')->checkbox(['class' => 'publish-checkbox', 'id' => $model->id]) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

<?php
$js = <<<JS
        $('.publish-checkbox').on('change', function () {
            if (this.checked == true) {
                checked = 1;    
            } else {
                checked = 0;
            }
            
            $.ajax({
                type: 'POST',
                url: '/news/ajax',
                data: {checked: checked, id: this.id},
                success: function (data) {
                    $.notify({
	                    message: data 
                    },{
	                    type: 'success'
                    });
                },
                error: function (data) {
                    $.notify({
	                    message: 'Something went wrong...' 
                    },{
	                    type: 'danger'
                    });
                }
            });
        });
JS;
$this->registerJs($js);