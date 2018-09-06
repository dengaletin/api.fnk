<?php
namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;
use app\models\NewsPhoto;

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
class NewsPhotoForm extends Model
{
    /**
     * @var UploadedFile[]
     */
    public $imageFiles;

    public function rules()
    {
        return [
            [['imageFiles'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg', 'maxFiles' => 8],
        ];
    }
    
    public function upload($newsId)
    {
        if ($this->validate()) { 
            foreach ($this->imageFiles as $file) {
                $photo = new NewsPhoto();
                $photo->news_id = $newsId;
                $photo->file = $file;
                
                $photo->save();
            }
            return true;
        } else {
            return false;
        }
    }
}
