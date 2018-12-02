<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Company;

/**
 * CompanySearch represents the model behind the search form about `app\models\Company`.
 */
class CompanySearch extends Company
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'mode_id', 'group_id'], 'integer'],
            [['name', 'name_full', 'ticker', 'description', 'site', 'parser_variations'], 'safe'],
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
        $query = Company::find()->from(['t' => Company::tableName()])->joinWith(['mode', 'group']);

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
            't.id' => $this->id,
            't.mode_id' => $this->mode_id,
            't.group_id' => $this->group_id,
        ]);

        $query->andFilterWhere(['like', 't.name', $this->name])
            ->andFilterWhere(['like', 't.name_full', $this->name_full])
            ->andFilterWhere(['like', 't.ticker', $this->ticker])
            ->andFilterWhere(['like', 't.description', $this->description])
            ->andFilterWhere(['like', 't.site', $this->site]);

        return $dataProvider;
    }
}
