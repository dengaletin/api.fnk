<?php

namespace app\models;

use app\models\query\DeviceQuery;
use app\models\query\ProfileQuery;
use app\models\query\PurchaseQuery;
use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%device}}".
 *
 * @property integer $id
 * @property string $device_token
 * @property string $apns_token
 * @property string $firebase_token
 * @property string $access_token
 * @property string $language
 *
 * @property MessageQueue[] $messageQueues
 * @property Profile $profile
 * @property Purchase[] $purchases
 * @property integer $purchase
 */
class Device extends ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%device}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['device_token', 'unique'],
            ['device_token', 'string', 'min' => 1, 'max' => 1024],
            //['device_token', 'match', 'pattern' => '#^[A-Fa-f0-9]+$#'],

            ['apns_token', 'unique'],
            ['apns_token', 'match', 'pattern' => '#^[A-Fa-f0-9]+$#'],
            ['firebase_token', 'unique'],

            ['language', 'in', 'range' => array_keys(self::getLanguageArray()), 'message' => 'Language is incorrect.'],
            ['language', 'default', 'value' => 'EN'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'device_token' => 'Device Token',
            'firebase_token' => 'Firebase Token',
            'apns_token' => 'APNS Token',
            'access_token' => 'Access Token',
            'purchase' => 'Оплачен',
            'language' => 'Язык',
        ];
    }

    /**
     * @return ProfileQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['device_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessageQueues()
    {
        return $this->hasMany(MessageQueue::className(), ['device_id' => 'id'])->inverseOf('device');
    }

    /**
     * @return PurchaseQuery
     */
    public function getPurchases()
    {
        return $this->hasMany(Purchase::className(), ['device_id' => 'id'])->inverseOf('device');
    }

    public static function findIdentity($id)
    {
        throw new NotSupportedException('findIdentity is not implemented.');
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        if (!empty($token)) {
            if (!$device = self::findOne(['access_token' => $token])) {
                $device = new self();
                $device->access_token = $token;
            }
            
            return $device;
        }
    }

    public static function findIdentityByAccessToken_new($token, $type = null)
    {
        if (!empty($token)) {
            if (!$device = self::findOne(['access_token' => $token])) {
                //$device = new self();
                //$device->access_token = $token;
                return null;
            }
            return $device;
        }
        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        throw new NotSupportedException('getAuthKey is not implemented.');
    }

    public function validateAuthKey($authKey)
    {
        throw new NotSupportedException('validateAuthKey is not implemented.');
    }

    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
    }

    public function generateDeviceToken()
    {
        $this->device_token = bin2hex(Yii::$app->security->generateRandomString(32));
    }
    
    public function fields()
    {
        return [
            'device_token' => function (self $model) { return $model->device_token; },
            'apns_token' => function (self $model) { return $model->apns_token; },
            'firebase_token' => function (self $model) { return $model->firebase_token; },
            'purchase' => function (self $model) { return (bool)$model->getPurchase(); },
            'language' => function (self $model) { return $model->language; },
        ];
    }

    private $_purchase;

    public function getPurchase()
    {
        if ($this->_purchase === null) {
            $this->_purchase = (int)($this->getPurchases()->active()->exists() /*|| $this->getProfile()->confirmed()->active()->exists()*/);
        }
        return $this->_purchase;
    }

    public static function getPurchaseArray()
    {
        return [
            0 => 'Нет',
            1 => 'Да',
        ];
    }

    public function getPurchaseName()
    {
        return ArrayHelper::getValue(self::getPurchaseArray(), $this->purchase);
    }

    public static function getLanguageArray()
    {
        return [
            'EN' => 'Английский',
            'RU' => 'Русский',
        ];
    }

    public function getLanguageName()
    {
        return ArrayHelper::getValue(self::getLanguageArray(), $this->language);
    }

    /**
     * @return DeviceQuery
     */
    public static function find()
    {
        return new DeviceQuery(get_called_class());
    }
}
