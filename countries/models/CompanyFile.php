<?php

namespace app\models;

use app\models\query\CompanyFileQuery;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%company_file}}".
 *
 * @property integer $company_id
 * @property integer $year
 * @property string $name
 * @property string $lang
 * @property string $file
 *
 * @property Company $company
 */
class CompanyFile extends ActiveRecord
{
    const FILE_PATH = 'upload';

    public $upload_file;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%company_file}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['year', 'upload_file', 'lang'], 'required'],
            //[['upload_file', 'lang'], 'required'],
            [['year'], 'integer'],
            [['name'], 'string'],
            ['lang', 'in', 'range' => array_keys(self::getLangArray())],
            //[['upload_file'], 'file', 'mimeTypes' => ['application/pdf', 'image/jpeg', 'image/png']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'company_id' => 'Company ID',
            'year' => 'Год',
            'file' => 'Файл',
            'upload_file' => 'Файл',
            'name' => 'Название',
            'lang' => 'Язык',
        ];
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->upload_file = UploadedFile::getInstance($this, 'upload_file');
            return true;
        }
        return false;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->upload_file instanceof UploadedFile) {
                if ($this->upload_file->name) {
                    $this->name = $this->upload_file->name;
                    $this->file = $this->company_id . $this->year . rand(10000, 99999). '.pdf';
                    $this->upload_file->saveAs($this->getFileSrc());
                }
            }
            return true;
        }
        return false;
    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            $src = $this->getFileSrc();
            if (file_exists($src) && is_file($src)) {
                unlink($src);
            }
            return true;
        }
        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }

    public function getFileUrl()
    {
        return Yii::$app->request->baseUrl . '/' . $this->getFilePath();
    }

    public function getFileSrc()
    {
        return Yii::getAlias('@webroot') . '/' . $this->getFilePath();
    }

    public function getFilePath()
    {
        return self::FILE_PATH . '/' . $this->file;
    }

    public function fields()
    {
        $fields = parent::fields();

        unset($fields['company_id']);
        unset($fields['file']);

        $fields = ArrayHelper::merge($fields, [
            'id' => function (CompanyFile $model) { return substr($model->file, 0, -4); },
        ]);
        return $fields;
    }

    public function getLangName()
    {
        return ArrayHelper::getValue(self::getLangArray(), $this->lang);
    }

    public static function getLangArray()
    {
        return [
            'ru' => 'RU',
            'en' => 'EN',
        ];
    }

    /**
     * @return CompanyFileQuery
     */
    public static function find()
    {
        return new CompanyFileQuery(get_called_class());
    }
}
