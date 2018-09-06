<?php

namespace app\models;

use app\models\query\ProfileQuery;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use yiidreamteam\upload\ImageUploadBehavior;

/**
 * This is the model class for table "{{%profile}}".
 *
 * @property integer $id
 * @property integer $device_id
 * @property string $nickname
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property string $phone_confirm_code
 * @property string $email
 * @property string $avatar
 * @property integer $expired_at
 * @property bool $confirm
 * @property bool $notified
 *
 * @property Device $device
 *
 *
 * @mixin ImageUploadBehavior
 */
class Profile extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%profile}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['first_name', 'last_name', 'phone', 'email'], 'required'],
            [['first_name', 'last_name', 'phone', 'email', 'nickname', 'registered_on'], 'string', 'max' => 255],
            ['avatar', 'image', 'extensions' => 'jpg, jpeg, gif, png'],
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
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'phone' => 'Телефон',
            'email' => 'Email',
            'nickname' => 'Псевдоним',
            'avatar' => 'Фото',
            'confirm' => 'Подтверждён',
            'expired_at' => 'Профиль истекает',
            'registered_on' => 'Первая регистрация',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevice()
    {
        return $this->hasOne(Device::className(), ['profile_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return ProfileQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProfileQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'imageUpload' => [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'avatar',
                'createThumbsOnSave' => false,
                'createThumbsOnRequest' => true,
                'filePath' => '@webroot/upload/avatar/[[filename]].[[extension]]',
                'fileUrl' => '/upload/avatar/[[filename]].[[extension]]',
                'thumbPath' => '@webroot/upload/avatar/[[profile]]-[[filename]].[[extension]]',
                'thumbUrl' => '/upload/avatar/[[profile]]-[[filename]].[[extension]]',
                'thumbs' => [
                    'avatar' => ['width' => 150, 'height' => 150],
                ],
            ],
        ];
    }

    public function getThumbFileUrl($attribute, $profile = 'thumb', $emptyUrl = '@web/images/no-photo.png')
    {
        /** @var ImageUploadBehavior $behavior */
        $behavior = $this->getBehavior('imageUpload');
        try {
            return $behavior->getThumbFileUrl($attribute, $profile, $emptyUrl);
        } catch (\InvalidArgumentException $e) {
            return $emptyUrl;
        }
    }

    public function fields()
    {
        $fields = parent::fields();

        $fields = ArrayHelper::merge($fields, [
            'avatar' => function (self $model) { return $model->avatar && file_exists($model->getUploadedFilePath('avatar')) ? $model->getThumbFileUrl('avatar', 'avatar') : null; },
        ]);

        return $fields;
    }
    
}
