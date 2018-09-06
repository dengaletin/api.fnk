<?php

namespace app\models\query;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\app\models\Profile]].
 *
 * @see \app\models\Profile
 */
class ProfileQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function confirmed()
    {
        return $this->andWhere(['confirm' => true]);
    }

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
     * @inheritdoc
     * @return \app\models\Profile[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Profile|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param int $batchSize
     * @param null $db
     * @return \app\models\Profile[]
     */
    public function each($batchSize = 100, $db = null)
    {
        return parent::each($batchSize, $db);
    }
}
