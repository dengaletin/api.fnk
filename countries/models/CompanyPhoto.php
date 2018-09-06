<?php

namespace app\models;

use app\models\query\CompanyPhotoQuery;
use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yiidreamteam\upload\ImageUploadBehavior;

/**
 * This is the model class for table "{{%company_file}}".
 *
 * @property integer $id
 * @property integer $company_id
 * @property string $file
 *
 * @property Company $company
 *
 * @mixin ImageUploadBehavior
 */
class CompanyPhoto extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%company_photo}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file'], 'required'],
            ['file', 'image', 'extensions' => 'jpg, jpeg, gif, png'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'company_id' => 'Company ID',
            'file' => 'Файл',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'imageUpload' => [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'file',
                'createThumbsOnSave' => false,
                'createThumbsOnRequest' => true,
                'filePath' => '@webroot/upload/photo/[[filename]].[[extension]]',
                'fileUrl' => '/upload/photo/[[filename]].[[extension]]',
                'thumbPath' => '@webroot/upload/photo/[[profile]]-[[filename]].[[extension]]',
                'thumbUrl' => '/upload/photo/[[profile]]-[[filename]].[[extension]]',
                'thumbs' => [
                    'thumb' => ['width' => 150, 'height' => 150],
                    'photo' => ['width' => 1024, 'height' => 1024],
                ],
            ],
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->file instanceof UploadedFile) {
            $this->file->name = uniqid($this->company_id) . '.' . pathinfo($this->file->name, PATHINFO_EXTENSION);
        }
        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }

    public function fields()
    {
        $fields = [
            'id' => function (self $model) { return $model->file; },
        ];
        return $fields;
    }

    /**
     * @return CompanyPhotoQuery
     */
    public static function find()
    {
        return new CompanyPhotoQuery(get_called_class());
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
}
