<?php

namespace app\models;

use app\models\query\PurchaseQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%message}}".
 *
 * @property integer $id
 * @property integer $created_at
 * @property string $message
 * @property string $target
 * @property string $language
 *
 * @property MessageQueue[] $queues
 */
class Message extends ActiveRecord
{
    const TARGET_ALL = 'all';
    const TARGET_PURCHASE = 'purchase';
    const TARGET_FREE = 'free';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%message}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message', 'language'], 'required'],
            [['message'], 'string'],
            ['target', 'in', 'range' => array_keys(self::getTargetArray())],
            ['language', 'in', 'range' => array_keys(self::getLanguageArray())],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Дата',
            'message' => 'Сообщение',
            'target' => 'Тариф',
            'language' => 'Язык',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQueues()
    {
        return $this->hasMany(MessageQueue::className(), ['message_id' => 'id'])->inverseOf('message');
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => 'created_at',
                ],
            ],
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $this->addQueueRows();
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function getTargetName()
    {
        return ArrayHelper::getValue(self::getTargetArray(), $this->target);
    }

    public static function getTargetArray()
    {
        return [
            self::TARGET_ALL => 'Всем',
            self::TARGET_PURCHASE => 'Платным',
            self::TARGET_FREE => 'Бесплатным',
        ];
    }

    public static function getLanguageArray()
    {
        return Device::getLanguageArray();
    }

    public function getLanguageName()
    {
        return ArrayHelper::getValue(self::getLanguageArray(), $this->language);
    }

    private function addQueueRows()
    {
        if ($this->target == self::TARGET_PURCHASE) {
            $deviceIds = array_unique(Device::find()->withToken()->withLanguage($this->language)->select(Device::tableName() . '.id')->joinWith([
                'purchases' => function (PurchaseQuery $query) {
                    $query->active();
                },
            ], false)->andWhere(['IS NOT', Purchase::tableName() . '.id', null])->column());
        } elseif ($this->target == self::TARGET_FREE) {
            $purchasedIds = Device::find()->withToken()->withLanguage($this->language)->select(Device::tableName() . '.id')->joinWith([
                'purchases' => function (PurchaseQuery $query) {
                        $query->active();
                    },
            ], false)->andWhere(['IS NOT', Purchase::tableName() . '.id', null])->column();
            $allIds = array_unique(Device::find()->withToken()->withLanguage($this->language)->select('id')->column());
            $deviceIds = array_diff($allIds, $purchasedIds);
        } else {
            $deviceIds = array_unique(Device::find()->withToken()->withLanguage($this->language)->select('id')->column());
        }

        sort($deviceIds);

        $toInsert = [];
        $messageId = $this->id;
        foreach ($deviceIds as $deviceId) {
            $toInsert[] = [
                'message_id' => $messageId,
                'device_id' => (int)$deviceId,
                'status' => 0,
            ];
        }

        if ($toInsert) {
            MessageQueue::getDb()
                ->createCommand()
                ->batchInsert(MessageQueue::tableName(), ['message_id', 'device_id', 'status'], $toInsert)
                ->execute();
        }
    }
}
