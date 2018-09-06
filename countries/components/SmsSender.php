<?php

namespace app\components;

use yii\base\Component;
use yii\base\InvalidConfigException;

class SMSSender extends Component
{
    public $active = true;
    public $appId;
    public $sender;

    public function init()
    {
        if (empty($this->appId)) {
            throw new InvalidConfigException('Sms appId must be set.');
        }

        if(empty($this->sender)) {
            throw new InvalidConfigException('Sender must be set.');
        }

        parent::init();
    }

    public function send($number, $text)
    {
        if (!$this->active) {
            return;
        }

        $ch = curl_init('http://sms.ru/sms/send');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'api_id' => $this->appId,
            'to' => $number,
            'text' => $text,
            'from' => $this->sender,
        ]);

        /*
        var_dump([
            'api_id' => $this->appId,
            'to' => $number,
            'text' => $text,
            'from' => $this->sender,
        ]);
        */

        curl_exec($ch);
        curl_close($ch);
    }
}