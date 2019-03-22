<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CompanyPhoto */

$this->title = 'Фото ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Новости', 'url' => ['index']];
if ($model->news) {
    $this->params['breadcrumbs'][] = ['label' => $model->news->title, 'url' => ['news/view', 'id' => $model->news->id]];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <h4>Текущее изображение</h4>
    <?= Html::img($model->getThumbFileUrl('file')); ?>

    <h4>Загрузка нового изображения</h4>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
