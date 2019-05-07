<?php

namespace app\models;

use Yii;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "msfo_users".
 * @SWG\Definition(required={"id", "first_name", "last_name"})
 *
 * @SWG\Property(property="id", type="integer", description="Element id")
 * @SWG\Property(property="first_name", type="string", description="User firstname")
 * @SWG\Property(property="last_name", type="string", description="User lastname")
 * @SWG\Property(property="email", type="string", description="User email")
 * @SWG\Property(property="email_code", type="string", description="Email code confirmation")
 * @SWG\Property(property="email_new", type="string", description="Updated email")
 * @SWG\Property(property="phone", type="string", description="User phone")
 * @SWG\Property(property="password_hash", type="string", description="User password hash")
 * @SWG\Property(property="status", type="integer", description="User status")
 * @SWG\Property(property="updated_at", type="string", description="User updated date")
 * @SWG\Property(property="created_at", type="string", description="User created date")
 * @SWG\Property(property="password", type="string", description="User password")
 * @SWG\Property(property="password_repeat", type="string", description="User password repeat")
 * @SWG\Property(property="phone_code", type="string", description="Phone code confirmation")
 * @SWG\Property(property="phone_new", type="string", description="Updated user phone")
 * @SWG\Property(property="bearer_token", type="string", description="Bearer token")
 * @SWG\Property(property="avatar", type="string", description="User avatar")
 *
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $email_code
 * @property string $email_new
 * @property string $phone
 * @property string $password_hash
 * @property integer $status
 * @property string $updated_at
 * @property string $created_at
 * @property string $password
 * @property string $password_repeat
 * @property string $phone_code
 * @property string $phone_new
 * @property string $bearer_token
 * @property string $avatar
 */
class Users extends \yii\db\ActiveRecord
{
    public $password;
    public $password_repeat;
    public $file;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'msfo_users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name'], 'required'],
            [['password', 'password_repeat'], 'string', 'min' => 6, 'max' => 20],
            ['password_repeat', 'compare', 'compareAttribute' => 'password'],
            [['email', 'phone'], 'validateFields', 'skipOnEmpty' => false, 'skipOnError' => false],
            [
                ['phone', 'phone_new'],
                'match',
                'pattern' => '/^(8)(\d{3})(\d{3})(\d{2})(\d{2})/',
                'message' => 'Phone number format incorrect - 8XXXXXXXXXX'
            ],
            [['email', 'email_new'], 'email'],
            [['status'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [
                [
                    'first_name',
                    'last_name',
                    'email',
                    'email_code',
                    'email_new',
                    'password_hash',
                    'phone_code',
                    'bearer_token',
                    'avatar',
                ],
                'string',
                'max' => 255
            ],
            [['phone', 'phone_new'], 'string', 'max' => 20],
            [['email'], 'unique'],
            [['phone'], 'unique'],
            [['file'], 'file', 'extensions' => 'png, jpg, jpeg'],
        ];
    }

    public function validateFields($attribute)
    {
        if (empty($this->email) && empty($this->phone)) {
            $this->addError($attribute, 'Email or Phone is required');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'email_code' => 'Email Code',
            'email_new' => 'Email New',
            'phone' => 'Phone',
            'password_hash' => 'Password Hash',
            'status' => 'Status',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
            'phone_code' => 'Phone Code',
            'phone_new' => 'Phone New',
            'bearer_token' => 'Bearer Token',
            'avatar' => 'Avatar',
        ];
    }

    public static function findById($id)
    {
        return static::findOne(['id' => $id]);
    }

    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    public static function findByPhone($phone)
    {
        return static::findOne(['phone' => $phone]);
    }

    public static function uploadFile($model, $folder)
    {
        if (is_object($model->file)) {
            $uploadDir = Yii::getAlias('@users_path');
            $name = time() . "_" . uniqid() . '.' . $model->file->extension;
            FileHelper::createDirectory($uploadDir . '/' . $model->id . '/' . $folder);
            $model->file->saveAs($uploadDir . '/' . $model->id . '/' . $folder . '/' . $name);
            return $name;
        }
    }

    public static function findIdentityByAccessToken($token, $userType = null)
    {
        return static::findOne(['bearer_token' => $token]);
    }
}