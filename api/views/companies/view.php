<?php

use app\models\CompanyFile;
use app\models\CompanyPhoto;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;
use Yii;

/* @var $this yii\web\View */
/* @var $model app\models\Company */
/* @var $file app\models\CompanyFile */
/* @var $photo app\models\CompanyPhoto */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Компании', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {

            // Удаление RU/EN-изображения струткруры компании
            case 'photo_struct_remove':
                $prop = "photo_struct_{$_POST['lang']}";
                $filepath = $model->{$prop};
                $model->{$prop} = null;

                if ($model->save()) {
                    unlink(getcwd()."/../web{$filepath}");
                }

                Yii::$app->response->redirect(
                    Yii::$app->request->referrer
                )->send();

                die;

        }
    }
}
?>
<div class="company-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="row">
        <div class="col-lg-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    [
                        'attribute' => 'free',
                        'value' => $model->free ? 'Да' : 'Нет',
                    ],
                    'name',
                    'name_eng',
                    'name_full',
                    'name_full_eng',
                    'ticker',
                    'ticker_eng',
                    [
                        'label' => 'Вид акций',
                        'value' => $model->mode ? $model->mode->name : '',
                    ],
                    [
                        'attribute' => 'Kind of shares',
                        'value' => $model->mode ? $model->mode->name_eng : '',
                    ],
                    [
                        'attribute' => 'Отрасль',
                        'value' => $model->group ? $model->group->name : '',
                    ],
                    [
                        'attribute' => 'Industry',
                        'value' => $model->group ? $model->group->name_eng : '',
                    ],
                    'description',
                    'description_eng',
                    'site',
                ],
            ]) ?>
        </div>
        <div class="col-lg-6">

            <?php if ($model->logo): ?>
                <p><?= Html::img($model->getThumbFileUrl('logo', 'logo'), ['width' => 150, 'height' => 150, 'style' => 'border: 1px solid #ccc']) ?></p>
                <br />
            <?php endif; ?>

            <div class="panel panel-default">
                <div class="panel-heading">Файлы</div>
                <div class="panel-body" style="padding-bottom: 2px">
                    <?= GridView::widget([
                        'dataProvider' => new ActiveDataProvider([
                                'query' => $model->getFiles()->orderBy(['year' => SORT_ASC]),
                                'pagination' => false,
                                'sort' => false,
                            ]),
                        'layout' => '{items}',
                        'columns' => [
                            'year',
                            [
                                'attribute' => 'lang',
                                'value' => 'langName',
                            ],
                            [
                                'attribute' => 'name',
                                'value' => function (CompanyFile $data) { return Html::a(Html::encode($data->name), $data->getFileUrl()); },
                                'format' => 'raw',
                            ],

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{update} {delete}',
                                'controller' => 'files',
                            ],
                        ],
                    ]); ?>

                    <?= $this->render('_file_form', [
                        'model' => $file,
                    ]) ?>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">Изображение структуры компании / Company structure image</div>
                <div class="panel-body" style="padding-bottom: 2px">
                    <div class="grid-view">
                        <table class="table table-striped table-bordered">
                            <tbody>
                                <?php if ($model->photo_struct_ru): ?>
                                    <tr>
                                        <td>RU</td>
                                        <td>
                                            <a href="<?= $model->photo_struct_ru ?>">
                                                <img src="<?= $model->photo_struct_ru ?>" width="150" alt="">
                                            </a>
                                        </td>
                                        <td>
                                            <?= Html::a(
                                                'Удалить',
                                                [
                                                    "/companies/{$model->id}"
                                                ],
                                                [
                                                    'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?',
                                                    'data-method' => 'POST',
                                                    'data-params' => [
                                                        'action' => 'photo_struct_remove',
                                                        'lang' => 'ru'
                                                    ]
                                                ]
                                            ) ?>
                                        </td>
                                    </tr>
                                <?php endif ?>
                                <?php if ($model->photo_struct_en): ?>
                                    <tr>
                                        <td>EN</td>
                                        <td>
                                            <a href="<?= $model->photo_struct_en ?>">
                                                <img src="<?= $model->photo_struct_en ?>" width="150" alt="">
                                            </a>
                                        </td>
                                        <td>
                                            <?= Html::a(
                                                'Удалить',
                                                [
                                                    "/companies/{$model->id}"
                                                ],
                                                [
                                                    'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?',
                                                    'data-method' => 'POST',
                                                    'data-params' => [
                                                        'action' => 'photo_struct_remove',
                                                        'lang' => 'en'
                                                    ]
                                                ]
                                            ) ?>
                                        </td>
                                    </tr>
                                <?php endif ?>
                            </tbody>
                        </table>
                    </div>
                    <?= $this->render('_photo_struct_form', [
                        'model' => $file,
                    ]) ?>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">Фотографии</div>
                <div class="panel-body" style="padding-bottom: 2px">
                    <?= GridView::widget([
                        'dataProvider' => new ActiveDataProvider([
                                'query' => $model->getPhotos()->orderBy(['id' => SORT_ASC]),
                                'pagination' => false,
                                'sort' => false,
                            ]),
                        'layout' => '{items}',
                        'columns' => [
                            [
                                'attribute' => 'name',
                                'value' => function (CompanyPhoto $data) { return Html::a(Html::img($data->getThumbFileUrl('file', 'thumb')), $data->getImageFileUrl('file')); },
                                'format' => 'raw',
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{update} {delete}',
                                'controller' => 'photos',
                            ],
                        ],
                    ]); ?>

                    <?= $this->render('_photo_form', [
                        'model' => $photo,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>



</div>
