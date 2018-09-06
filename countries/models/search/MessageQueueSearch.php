<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\MessageQueue;

/**
 * MessageQueueSearch represents the model behind the search form about `app\models\MessageQueue`.
 */
class MessageQueueSearch extends MessageQueue
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'message_id', 'device_id', 'status'], 'integer'],
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
        $query = MessageQueue::find();

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
            'id' => $this->id,
            'message_id' => $this->message_id,
            'device_id' => $this->device_id,
            'status' => $this->status,
        ]);

        return $dataProvider;
    }
}
