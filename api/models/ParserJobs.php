<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%parser_jobs}}".
 *
 * @property integer $id
 * @property string $source_url
 * @property string $source_host
 * @property integer $article_id
 * @property string $post_time
 * @property string $parse_time
 * @property-read News $article
 */
class ParserJobs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%parser_jobs}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['source_url', 'source_host', 'article_id'], 'required'],
            [['article_id'], 'integer'],
            [['post_time', 'parse_time'], 'safe'],
            [['source_url', 'source_host'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'source_url' => 'Адрес источника',
            'source_host' => 'Хост источника',
            'article_id' => 'Новость',
            'post_time' => 'Дата источника',
            'parse_time' => 'Дата парсинга',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticle()
    {
        return $this->hasOne(News::className(), ['id' => 'article_id']);
    }

    public function beforeValidate()
    {
        $this->source_host = parse_url($this->source_url, PHP_URL_HOST);

        return parent::beforeValidate();
    }
}
