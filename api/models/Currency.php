<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%year_currency}}".
 *
 * @property integer $year
 * @property double $eur_avg
 * @property double $eur_rep
 * @property double $usd_avg
 * @property double $usd_rep
 * @property double $eurusd_avg
 * @property double $eurusd_rep
 */
class Currency extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%currency}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['year'], 'required'],
            [['year'], 'integer'],
            [['eur_avg', 'eur_rep', 'usd_avg', 'usd_rep', 'eurusd_avg', 'eurusd_rep'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'year' => 'Year',
            'eur_avg' => 'Eur Avg',
            'eur_rep' => 'Eur Rep',
            'usd_avg' => 'Usd Avg',
            'usd_rep' => 'Usd Rep',
            'eurusd_avg' => 'Euro/Dollar Rep',
            'eurusd_rep' => 'Euro/Dollar Avg',
        ];
    }
}
