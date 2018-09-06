<?php
namespace app\models;

use Yii;
use app\models\query\CompanyQuery;

/**
 * This is the model class for table "{{%news}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $text
 * @property string $date
 * @property boolean $publish
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
            [['title', 'text', 'publish'], 'required'],
            [['text'], 'string'],
            [['date'], 'safe'],
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
            'date' => 'Дата',
            'publish' => 'Опубликовать',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhotos()
    {
        return $this->hasMany(NewsPhoto::className(), ['news_id' => 'id'])->inverseOf('news');
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