<?php

namespace app\models\query;

use app\models\Company;
use yii\db\ActiveQuery;

class CompanyFileQuery extends ActiveQuery
{
    public function free()
    {
        return $this
            ->innerJoin(['join_company' => Company::tableName()], '[[company_id]] = {{join_company}}.[[id]]')
            ->andWhere(['join_company.free' => true]);
    }

    /**
     * @param null $db
     * @return \app\models\CompanyFile
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param null $db
     * @return \app\models\CompanyFile[]
     */
    public function all($db = null)
    {
        return parent::all($db);
    }
} 