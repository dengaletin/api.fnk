<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Company */

$this->title = 'Добавить';
$this->params['breadcrumbs'][] = ['label' => 'Компании', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="company-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>


    <?/*
        $script = <<< JS
        $( ".btn-success" ).click(function() {
            
                if (confirm('Создать чат для компании')) {
                    alert( "OK" );
                } else {
                    alert( "NO" );
                }

        });
JS;
        $this->registerJs($script, yii\web\View::POS_READY);


*/
    ?>


</div>
