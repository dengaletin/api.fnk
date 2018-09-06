<?php

namespace app\models;

use app\models\query\NewsPhotoQuery;
use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yiidreamteam\upload\ImageUploadBehavior;

/**
 * This is the model class for table "{{%news_file}}".
 *
 * @property integer $id
 * @property integer $news_id
 * @property string $file
 *
 * @property News $news
 *
 * @mixin ImageUploadBehavior
 */
class NewsPhoto extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%news_photo}}';
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
            'news_id' => 'News ID',
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
                'filePath' => '@webroot/upload/news/[[filename]].[[extension]]',
                'fileUrl' => '/upload/news/[[filename]].[[extension]]',
                'thumbPath' => '@webroot/upload/news/[[profile]]-[[filename]].[[extension]]',
                'thumbUrl' => '/upload/news/[[profile]]-[[filename]].[[extension]]',
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
            $this->file->name = uniqid($this->news_id) . '.' . pathinfo($this->file->name, PATHINFO_EXTENSION);
        }
        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return $this->hasOne(News::className(), ['id' => 'news_id']);
    }

    public function fields()
    {
        $fields = [
            'id' => function (self $model) { return $model->file; },
        ];
        return $fields;
    }

    /**
     * @return NewsPhotoQuery
     */
    public static function find()
    {
        return new NewsPhotoQuery(get_called_class());
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
