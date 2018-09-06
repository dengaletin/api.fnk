<?php

namespace app\models;

use app\models\query\CompanyValueQuery;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%company_value}}".
 *
 * @property integer $company_id
 * @property integer $year
 * @property integer $report_type_id
 * @property string $currency
 * @property string $currency_eng
 * @property string $auditor
 * @property string $auditor_eng
 * @property integer $value_va
 * @property integer $value_oa
 * @property integer $value_ia
 * @property integer $value_kir
 * @property integer $value_dkiz
 * @property integer $value_kkiz
 * @property integer $value_v
 * @property integer $value_fr
 * @property integer $value_fd
 * @property integer $value_frn
 * @property integer $value_pdn
 * @property integer $value_chpzp
 * @property integer $value_aosina
 * @property integer $value_chdspood
 * @property integer $value_ebitda
 * @property double $value_tebitda
 * @property string $raw
 *
 * @property Company $company
 * @property ReportType $reportType
 */
class CompanyValue extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%company_value}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['year'], 'required'],
            [[
                'company_id',
                'year',
                'report_type_id',
                'value_va',
                'value_oa',
                'value_ia',
                'value_kir',
                'value_dkiz',
                'value_kkiz',
                'value_v',
                'value_fr',
                'value_fd',
                'value_frn',
                'value_pdn',
                'value_chpzp',
                'value_aosina',
                'value_chdspood',
                'value_ebitda',
            ], 'integer'],
            [['value_tebitda'], 'number'],
            [['currency', 'currency_eng'], 'string', 'max' => 32],
            [['auditor', 'auditor_eng'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $row = new FileRow();

        return ArrayHelper::merge($row->attributeLabels(), [
            'company_id' => 'Компания',
            'report_type_id' => 'Отчётность',
        ]);
    }

    /**
     * @return object
     **/
    public function getRaw()
    {
        return json_decode($this->raw);
    }

    /**
     * @param $row object|array
     **/
    public function setRaw($raw)
    {
        $this->raw = json_encode($raw);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportType()
    {
        return $this->hasOne(ReportType::className(), ['id' => 'report_type_id']);
    }

    public function fields()
    {
        $fields = parent::fields();

        unset($fields['report_type_id']);

        $fields = ArrayHelper::merge($fields, [
            'report_type' => function ($model) { return ArrayHelper::getValue($model->reportType, 'name'); },
            'report_type_eng' => function ($model) { return ArrayHelper::getValue($model->reportType, 'name_eng'); },
        ]);
        return $fields;
    }

    /**
     * @return CompanyValueQuery
     */
    public static function find()
    {
        return new CompanyValueQuery(get_called_class());
    }
}
