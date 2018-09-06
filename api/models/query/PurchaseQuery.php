<?php

namespace app\models\query;

use yii\db\ActiveQuery;

class PurchaseQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function active()
    {
        return $this->andWhere(['OR', ['expired_at' => null], ['>', 'expired_at', time()]]);
    }

    /**
     * @param $timeout
     * @return $this
     */
    public function expiredSoon($timeout)
    {
        return $this->andWhere(['NOT', ['expired_at' => null]])->andWhere(['BETWEEN', 'expired_at', time(), time() + $timeout]);
    }

    /**
     * @return $this
     */
    public function notNotified()
    {
        return $this->andWhere(['notified' => false]);
    }

    /**
     * @param null $db
     * @return \app\models\Purchase
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param null $db
     * @return \app\models\Purchase[]
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @param int $batchSize
     * @param null $db
     * @return \app\models\Purchase[]
     */
    public function each($batchSize = 100, $db = null)
    {
        return parent::each($batchSize, $db);
    }
} 