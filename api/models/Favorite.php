<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use yiidreamteam\upload\ImageUploadBehavior;

/**
 * This is the model class for table "{{%favorites}}".
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
 */
class Favorite extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%favorites}}';
    }


}
