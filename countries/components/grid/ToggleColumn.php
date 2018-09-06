<?php

namespace app\components\grid;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use Yii;

class ToggleColumn extends SetColumn
{
    /**
     * @var callable
     */
    public $url;
    /**
     * @var string
     */
    public $controller;

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);
        $url = $this->createUrl($model, $key, $index);
        $name = $this->getLabelName($model, $key, $index, $value);
        $class = ArrayHelper::getValue($this->cssCLasses, $value, 'default');
        $html = Html::a(Html::tag('span', Html::encode($name), ['class' => 'label label-' . $class]), $url, ['data-method' => 'post']);
        return $value === null ? $this->grid->emptyCell : $html;
    }

    public function createUrl($model, $key, $index)
    {
        if ($this->url instanceof \Closure) {
            return call_user_func($this->url, $model, $key, $index);
        } else {
            $params = is_array($key) ? $key : ['id' => (string) $key];
            $params[0] = $this->controller ? $this->controller . '/toggle' : 'toggle';
            $params['param'] = $this->attribute;
            $params['return'] = Yii::$app->getRequest()->getUrl();
            return Url::toRoute($params);
        }
    }
}