<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Currency;

/**
 * YearCurrencySearch represents the model behind the search form about `app\models\Currency`.
 */
class CurrencySearch extends Currency
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['year'], 'integer'],
            [['eur_avg', 'eur_rep', 'usd_avg', 'usd_rep', 'eurusd_avg', 'eurusd_rep'], 'number'],
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
        $query = Currency::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'year' => $this->year,
            'eur_avg' => $this->eur_avg,
            'eur_rep' => $this->eur_rep,
            'usd_avg' => $this->usd_avg,
            'usd_rep' => $this->usd_rep,
            'eurusd_avg' => $this->eurusd_avg,
            'eurusd_rep' => $this->eurusd_rep
        ]);

        return $dataProvider;
    }
}
