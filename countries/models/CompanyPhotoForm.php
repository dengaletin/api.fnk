<?php
namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;
use app\models\CompanyPhoto;

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
class CompanyPhotoForm extends Model
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
    
    public function upload($companyId)
    {
        if ($this->validate()) { 
            foreach ($this->imageFiles as $file) {
                $photo = new CompanyPhoto();
                $photo->company_id = $companyId;
                $photo->file = $file;
                
                $photo->save();
            }
            return true;
        } else {
            return false;
        }
    }
}
