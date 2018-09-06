<?php

namespace app\components;

use app\models\MessageQueue;
use yii\grid\DataColumn;
use yii\helpers\Html;

class MessageQueueStatusColumn extends DataColumn
{
    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        /** @var MessageQueue $model */
        $value = $this->getDataCellValue($model, $key, $index);
        switch ($value) {
            case MessageQueue::STATUS_SUCCESS:
                $class = 'success';
                break;
            case MessageQueue::STATUS_ERROR:
                $class = 'danger';
                break;
            default:
                $class = 'primary';
        };
        $html = '<span class="label label-' . $class . '">' . Html::encode($model->getStatusName()) . '</span>';
        return $value === null ? $this->grid->emptyCell : $html;
    }
}