<?php

namespace app\components;

use app\models\Message;
use app\models\MessageQueue;
use yii\base\Widget;
use yii\helpers\Html;

class MessageStatus extends Widget
{
    /** @var Message */
    public $model;

    public function run()
    {
        $count = [
            'default' => $this->model->getQueues()->andWhere(['status' => MessageQueue::STATUS_NEW])->count(),
            'success' => $this->model->getQueues()->andWhere(['status' => MessageQueue::STATUS_SUCCESS])->count(),
            'danger' => $this->model->getQueues()->andWhere(['status' => MessageQueue::STATUS_ERROR])->count(),
        ];
        $count['all'] = array_sum($count) ?: 1;
        $progress = [
            'default' => $count['default'] / $count['all'] * 100,
            'success' => $count['success'] / $count['all'] * 100,
            'danger' => $count['danger'] / $count['all'] * 100,
        ];
        $segments = [];
        foreach ($progress as $type => $percent) {
            $segments[] = Html::tag('div', $count[$type], [
                'class' => 'progress-bar progress-bar-' . $type,
                'role' => 'progressbar',
                'title' => $count[$type],
                'style' => 'width: ' . $percent . '%',
            ]);
        }
        return Html::tag('div', implode('', $segments), ['class' => 'progress', 'style' => 'margin: 0']);
    }
} 