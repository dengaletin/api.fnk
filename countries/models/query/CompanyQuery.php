<?php

namespace app\models\query;

use yii\db\ActiveQuery;

class CompanyQuery extends ActiveQuery
{
    public function free()
    {
        return $this->andWhere(['free' => true]);
    }

    /**
     * @param null $db
     * @return \app\models\Company
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param null $db
     * @return \app\models\Company[]
     */
    public function all($db = null)
    {
        return parent::all($db);
    }
} 