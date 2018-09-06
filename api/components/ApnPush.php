<?php

namespace app\components;

use Apple\ApnPush\Certificate\Certificate;
use yii\base\Component;
use Apple\ApnPush\Notification\Connection;
use Apple\ApnPush\Notification\Notification;
use Yii;

class ApnPush extends Component
{
    /**
     * @var string
     */
    public $certificateFile;

    /**
     * @var string
     */
    public $certificatePassPhrase;

    /**
     * @var bool
     */
    public $sandboxMode;

    /**
     * @var bool
     */
    public $active = true;

    /**
     * @var Notification
     */
    protected $notification;

    public function init()
    {
        if ($this->active) {
            $certificate = new Certificate(Yii::getAlias($this->certificateFile), $this->certificatePassPhrase);
            $connection = new Connection($certificate, $this->sandboxMode);
            $this->notification = new Notification($connection);
        }
    }

    public function send($deviceToken, $message)
    {
        if ($this->active) {
            return $this->notification->sendMessage($deviceToken, $message);
        } else {
            return true;
        }
    }
} 