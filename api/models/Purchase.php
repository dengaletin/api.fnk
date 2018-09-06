<?php

namespace app\models;

use app\models\query\PurchaseQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%purchase}}".
 *
 * @property integer $id
 * @property integer $device_id
 * @property integer $product_id
 * @property string $receipt
 * @property integer $created_at
 * @property integer $expired_at
 * @property bool $notified
 *
 * @property Product $product
 * @property Device $device
 */
class Purchase extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%purchase}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['device_id', 'product_id'], 'required'],
            [['device_id', 'product_id', 'expired_at'], 'integer'],
            [['receipt'], 'string'],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
            [['device_id'], 'exist', 'skipOnError' => true, 'targetClass' => Device::className(), 'targetAttribute' => ['device_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'device_id' => 'Устройство',
            'product_id' => 'Продукт',
            'receipt' => 'Рецепт',
            'created_at' => 'Создан',
            'expired_at' => 'Истекает',
        ];
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevice()
    {
        return $this->hasOne(Device::className(), ['id' => 'device_id']);
    }

    /**
     * @return PurchaseQuery
     */
    public static function find()
    {
        return new PurchaseQuery(get_called_class());
    }
}
