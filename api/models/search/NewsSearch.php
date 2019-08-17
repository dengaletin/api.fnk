<?php

namespace app\models\search;

use app\models\News;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * NewsSearch represents the model behind the search form about `app\models\News`.
 */
class NewsSearch extends News
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'publish'], 'integer'],
            [['title', 'text', 'date'], 'safe'],
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
        $query = News::find()
            ->alias('n')
            ->joinWith(['source source', 'companies companies'])
            ->groupBy('n.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => [1, 100000],
            ],
            'sort' => [
                'defaultOrder' => ['date' => SORT_DESC],
                'attributes' => array_merge(array_keys($this->attributes), [
                    'source.post_time',
                    'source.source_host',
                    'companies.name',
                ])
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'n.id' => $this->id,
            'n.date' => $this->date,
            'n.publish' => $this->publish,
        ]);

        $query->andFilterWhere(['like', 'n.title', $this->title])
            ->andFilterWhere(['like', 'n.text', $this->text]);

        return $dataProvider;
    }
}
