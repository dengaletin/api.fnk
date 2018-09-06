<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CompanyValue;

/**
 * CompanyValueSearch represents the model behind the search form about `app\models\CompanyValue`.
 */
class CompanyValueSearch extends CompanyValue
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['company_id', 'year', 'report_type_id', 'value_va', 'value_oa', 'value_ia', 'value_kir', 'value_dkiz', 'value_v', 'value_frn', 'value_chpzp', 'value_chdspood', 'value_ebitda'], 'integer'],
            [['currency', 'auditor'], 'safe'],
            [['value_tebitda'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = CompanyValue::find()->from(['t' => CompanyValue::tableName()])->joinWith(['company', 'reportType']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => [1, 100000],
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            't.company_id' => $this->company_id,
            't.year' => $this->year,
            't.report_type_id' => $this->report_type_id,
            't.currency' => $this->currency,
            't.value_va' => $this->value_va,
            't.value_oa' => $this->value_oa,
            't.value_ia' => $this->value_ia,
            't.value_kir' => $this->value_kir,
            't.value_dkiz' => $this->value_dkiz,
            't.value_v' => $this->value_v,
            't.value_frn' => $this->value_frn,
            't.value_chpzp' => $this->value_chpzp,
            't.value_chdspood' => $this->value_chdspood,
            't.value_ebitda' => $this->value_ebitda,
            't.value_tebitda' => $this->value_tebitda,
        ]);

        $query->andFilterWhere(['like', 't.auditor', $this->auditor]);

        return $dataProvider;
    }
}
