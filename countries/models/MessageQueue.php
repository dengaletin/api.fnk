<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%message_queue}}".
 *
 * @property integer $id
 * @property integer $message_id
 * @property integer $device_id
 * @property integer $status
 * @property string $response
 *
 * @property Device $device
 * @property Message $message
 */
class MessageQueue extends ActiveRecord
{
    const STATUS_NEW = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_ERROR = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%message_queue}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message_id', 'device_id'], 'required'],
            [['message_id', 'device_id', 'status'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'message_id' => 'Сообщение',
            'device_id' => 'Устройство',
            'status' => 'Статус',
            'response' => 'Ответ',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevice()
    {
        return $this->hasOne(Device::className(), ['id' => 'device_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessage()
    {
        return $this->hasOne(Message::className(), ['id' => 'message_id']);
    }

    public static function getStatusArray()
    {
        return [
            self::STATUS_NEW => 'Новое',
            self::STATUS_SUCCESS => 'Отправлено',
            self::STATUS_ERROR => 'Ошибка',
        ];
    }

    public function getStatusName()
    {
        $items = self::getStatusArray();
        return isset($items[$this->status]) ? $items[$this->status]  : '';
    }
}
