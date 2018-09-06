<?php

namespace app\models\query;

use yii\db\ActiveQuery;

class DeviceQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function withToken()
    {
        return $this->andWhere(['IS NOT', 'device_token', null]);
    }

    /**
     * @param $language
     * @return $this
     */
    public function withLanguage($language)
    {
        return $this->andWhere(['language' => $language]);
    }

    /**
     * @param null $db
     * @return \app\models\Device
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param null $db
     * @return \app\models\Device[]
     */
    public function all($db = null)
    {
        return parent::all($db);
    }
} 