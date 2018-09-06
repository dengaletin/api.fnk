<?php

namespace tests\api;

use \ApiTester;
use app\components\Transliterator;
use yii\helpers\Url;

class CompaniesCest extends ApiCestCase
{
    public function testAccess(ApiTester $I)
    {
        $I->sendGET('/api/companies');
        $I->seeResponseCodeIs(401);
    }

    /**
     * @before loginAsFree
     * @after logout
     */
    public function testAllFree(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/companies');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            [
                'id' => 1,
                'name' => 'Мосэнерго',
                'name_eng' => Transliterator::translit('Мосэнерго'),
                'name_for_list' => 'Мосэнерго',
                'name_for_list_eng' => Transliterator::translit('Мосэнерго'),
                'ticker' => 'МосБиржа: +МосЭнерго',
                'ticker_eng' => Transliterator::translit('МосБиржа: +МосЭнерго'),
                'description' => 'Производитель тепловой и электрической энергии',
                'description_eng' => Transliterator::translit('Производитель тепловой и электрической энергии'),
                'site' => 'www.mosenergo.ru',
                'name_full' => 'ОАО Мосэнерго',
                'name_full_eng' => 'OJSC ' . Transliterator::translit('Мосэнерго'),
                'mode' => 'обык.',
                'mode_eng' => 'obyk.',
                'group' => 'Энергогенерация и сбыт',
                'group_eng' => Transliterator::translit('Энергогенерация и сбыт'),
            ],
        ]);
    }

    /**
     * @before loginAsPurchase
     * @after logout
     */
    public function testAllPurchase(ApiTester $I)
    {
        $I->haveHttpHeader('X-User-Token', $this->accessToken);
        $I->sendGET('/api/companies');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            [
                'id' => 1,
                'name' => 'Мосэнерго',
            ],
            [
                'id' => 2,
                'name' => 'Мосэнерго 2',
            ],
        ]);
    }
}