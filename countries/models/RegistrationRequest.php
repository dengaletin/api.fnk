<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%registration_request}}".
 *
 * @property integer $id
 * @property string $confirm_code
 * @property string $phone
 * @property bool $confirmed
 */
class RegistrationRequest extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%registration_request}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['confirm_code', 'unique'],
            ['phone', 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'confirm_code' => 'Код подтверждения',
            'phone' => 'Телефон',
            'confirmed' => 'Подтвержден',
        ];
    }
    
    public static function prepareRequest($phone) {
        $confirmCode = 0;
        $i = 0;
        do {
            $confirmCode = rand(1000, 9999);

            if($i++ > 100) {
                throw new Exception('Can`t find free confirm code value');
            }
        } while (RegistrationRequest::find()->where(['confirm_code' => $confirmCode])->limit(1)->one());

        $reg_request = new RegistrationRequest();
        
        $reg_request->phone = $phone;
        $reg_request->confirm_code = $confirmCode;
        $reg_request->confirmed = false;
        
        return $reg_request;
    }
}
