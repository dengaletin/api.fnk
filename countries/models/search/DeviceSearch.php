<?php

namespace app\models\search;

use app\models\Profile;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Device;
use yii\db\ActiveQuery;

/**
 * DeviceSearch represents the model behind the search form about `app\models\Device`.
 */
class DeviceSearch extends Device
{
    public $nickname;
    public $first_name;
    public $last_name;
    public $phone;
    public $email;
    public $confirm;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['device_token', 'access_token', 'language', 'nickname', 'first_name', 'last_name', 'phone', 'email', 'confirm'], 'safe'],
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
        $query = Device::find()->from(['device' => Device::tableName()])->joinWith(['profile' => function (ActiveQuery $query) {
                $query->from(['profile' => Profile::tableName()]);
            }]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [ 'id' => SORT_DESC ]
            ],
            'pagination' => [
                'pageSizeLimit' => [1, 100000],
            ]
        ]);

        foreach (['nickname', 'first_name', 'last_name', 'phone', 'email'] as $attribute) {
            $dataProvider->sort->attributes[$attribute] = [
                'asc' => ['profile.' . $attribute => SORT_ASC],
                'desc' => ['profile.' . $attribute => SORT_DESC],
            ];
        }

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'device.id' => $this->id,
            'device.language' => $this->language,
            'profile.confirm' => $this->confirm,
        ]);

        $query->andFilterWhere(['like', 'device.device_token', $this->device_token]);
        $query->andFilterWhere(['like', 'device.access_token', $this->access_token]);
        $query->andFilterWhere(['like', 'profile.nickname', $this->nickname]);
        $query->andFilterWhere(['like', 'profile.first_name', $this->first_name]);
        $query->andFilterWhere(['like', 'profile.last_name', $this->last_name]);
        $query->andFilterWhere(['like', 'profile.phone', $this->phone]);
        $query->andFilterWhere(['like', 'profile.email', $this->email]);

        return $dataProvider;
    }
}
