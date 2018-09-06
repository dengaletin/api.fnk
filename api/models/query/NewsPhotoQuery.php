<?php

namespace app\models\query;

use app\models\News;
use yii\db\ActiveQuery;

class NewsPhotoQuery extends ActiveQuery
{
    public function free()
    {
        return $this
            ->innerJoin(['join_news' => News::tableName()], '[[news_id]] = {{join_news}}.[[id]]')
            ->andWhere(['join_news.free' => true]);
    }

    /**
     * @param null $db
     * @return \app\models\NewsPhoto
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param null $db
     * @return \app\models\NewsPhoto[]
     */
    public function all($db = null)
    {
        return parent::all($db);
    }
} 