<?php

namespace app\models;

/**
 * This is the model class for table "{{%news}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $text
 * @property string $date
 * @property boolean $publish
 * @property-read Company[] $companies
 * @property-read ParserJobs $source
 * @property-read NewsPhoto[] $photos
 */
class News extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%news}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['publish'], 'default', 'value' => '0'],
            [['title', 'text', 'publish'], 'required'],
            [['text'], 'string'],
            [['date', 'companies'], 'safe'],
            [['publish'], 'boolean'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Заголовок',
            'text' => 'Текст',
            'date' => 'Дата ifinik',
            'publish' => 'Опубликовать',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        unset($fields['id']);
        unset($fields['publish']);

        return array_merge($fields, [
            'companies' => function (self $model) {
                return $model->companies;
            },
            'source' => function (self $model) {
                return $model->source->source_host;
            },
            'photos' => function (self $model) {
                $result = [];
                foreach ($model->photos as $photo) {
                    $result[] = $photo->getImageFileUrl('file');
                }
                return $result;
            },
            'thumbs' => function (self $model) {
                $result = [];
                foreach ($model->photos as $photo) {
                    $result[] = $photo->getThumbFileUrl('file');
                }
                return $result;
            }
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhotos()
    {
        return $this->hasMany(NewsPhoto::className(), ['news_id' => 'id'])->inverseOf('news');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSource()
    {
        return $this->hasOne(ParserJobs::className(), ['article_id' => 'id'])->inverseOf('article');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompanies()
    {
        return $this->hasMany(Company::className(), ['id' => 'company_id'])
            ->viaTable('{{%news_companies}}', ['news_id' => 'id']);
    }

    public function setCompanies($values)
    {
        \Yii::$app->db->createCommand()->delete('{{%news_companies}}',
            [
                'news_id'=>$this->id
            ])->execute();
        if (!$values) {
            return;
        }
        foreach ($values as $value)
        {
            \Yii::$app->db->createCommand()->insert('{{%news_companies}}', [
                'news_id' => $this->id,
                'company_id' => (int)$value,
            ])->execute();
        }
    }

    public function beforeSave($insert)
    {
        $this->date = date('Y-m-d H:i', strtotime($this->date));

        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        $this->date = date('d.m.Y H:i', strtotime($this->date));

        return true;
    }

}