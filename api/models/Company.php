<?php

namespace app\models;

use app\models\query\CompanyQuery;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use yiidreamteam\upload\ImageUploadBehavior;

/**
 * This is the model class for table "{{%company}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $name_eng
 * @property string $name_for_list
 * @property string $name_for_list_eng
 * @property string $name_full
 * @property string $name_full_eng
 * @property string $ticker
 * @property string $ticker_eng
 * @property integer $mode_id
 * @property integer $group_id
 * @property string $description
 * @property string $description_eng
 * @property string $site
 * @property string $logo
 * @property integer $free
 *
 * @property Group $group
 * @property Mode $mode
 * @property CompanyValue[] $companyValues
 *
 * @mixin ImageUploadBehavior
 */
class Company extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%company}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'ticker'], 'required'],
            [['mode_id', 'group_id'], 'integer'],
            ['free', 'boolean'],
            ['free', 'default', 'value' => 0],
            [['description', 'description_eng'], 'string'],
            [['name_full', 'name_full_eng', 'name', 'name_eng', 'name_for_list', 'name_for_list_eng', 'ticker', 'ticker_eng', 'site'], 'string', 'max' => 255],
            ['logo', 'image', 'extensions' => 'jpg, jpeg, gif, png'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $row = new FileRow();

        return ArrayHelper::merge($row->attributeLabels(), [
            'id' => 'ID',
            'mode_id' => 'Вид',
            'group_id' => 'Группа',
            'logo' => 'Лого',
            'free' => 'Бесплатная',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'imageUpload' => [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'logo',
                'createThumbsOnSave' => false,
                'createThumbsOnRequest' => true,
                'filePath' => '@webroot/upload/logo/[[filename]].[[extension]]',
                'fileUrl' => '/upload/logo/[[filename]].[[extension]]',
                'thumbPath' => '@webroot/upload/logo/[[profile]]-[[filename]].[[extension]]',
                'thumbUrl' => '/upload/logo/[[profile]]-[[filename]].[[extension]]',
                'thumbs' => [
                    'logo' => ['width' => 150, 'height' => 150],
                ],
            ],
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->logo instanceof UploadedFile) {
            $this->logo->name = uniqid($this->group_id) . '.' . pathinfo($this->logo->name, PATHINFO_EXTENSION);
        }
        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMode()
    {
        return $this->hasOne(Mode::className(), ['id' => 'mode_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompanyValues()
    {
        return $this->hasMany(CompanyValue::className(), ['company_id' => 'id'])->inverseOf('company');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany(CompanyFile::className(), ['company_id' => 'id'])->inverseOf('company');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhotos()
    {
        return $this->hasMany(CompanyPhoto::className(), ['company_id' => 'id'])->inverseOf('company');
    }

    public function fields()
    {
        $fields = parent::fields();

        unset($fields['mode_id']);
        unset($fields['group_id']);
        unset($fields['free']);

        $fields = ArrayHelper::merge($fields, [
            'mode' => function (self $model) { return ArrayHelper::getValue($model->mode, 'name'); },
            'mode_eng' => function (self $model) { return ArrayHelper::getValue($model->mode, 'name_eng'); },
            'group' => function (self $model) { return ArrayHelper::getValue($model->group, 'name'); },
            'group_eng' => function (self $model) { return ArrayHelper::getValue($model->group, 'name_eng'); },
            'logo' => function (self $model) { return $model->logo && file_exists($model->getUploadedFilePath('logo')) ? $model->getThumbFileUrl('logo', 'logo') : null; },
        ]);
        return $fields;
    }

    /**
     * @return CompanyQuery
     */
    public static function find()
    {
        return new CompanyQuery(get_called_class());
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
