<?php

namespace tests\unit\models;

use app\components\Transliterator;
use app\models\Company;
use app\models\ImportForm;
use app\models\Version;
use Codeception\Test\Unit;
use tests\fixtures\CompanyFixture;
use tests\fixtures\CompanyValueFixture;
use tests\fixtures\GroupFixture;
use tests\fixtures\ModeFixture;
use tests\fixtures\ReportTypeFixture;
use tests\fixtures\VersionFixture;
use yii\web\UploadedFile;

class ImportFormReplaceTest extends Unit
{
    /**
     * @var \UnitTester
     */
    public $tester;

    public function _before()
    {
        $this->tester->haveFixtures([
            'versions' => [
                'class' => VersionFixture::className(),
                'dataFile' => '@tests/unit/_data/fixtures/replace/version.php',
            ],
            'modes' => [
                'class' => ModeFixture::className(),
                'dataFile' => '@tests/unit/_data/fixtures/replace/mode.php',
            ],
            'groups' => [
                'class' => GroupFixture::className(),
                'dataFile' => '@tests/unit/_data/fixtures/replace/group.php',
            ],
            'companies' => [
                'class' => CompanyFixture::className(),
                'dataFile' => '@tests/unit/_data/fixtures/replace/company.php',
            ],
            'reportTypes' => [
                'class' => ReportTypeFixture::className(),
                'dataFile' => '@tests/unit/_data/fixtures/replace/report-type.php',
            ],
            'companyValues' => [
                'class' => CompanyValueFixture::className(),
                'dataFile' => '@tests/unit/_data/fixtures/replace/company-value.php',
            ],
        ]);
    }

    public function testReplace()
    {
        $model = new ImportForm();

        $model->file = $this->getUploadedFile();

        expect('model is valid', $model->validate())->true();
        expect('import is true', $model->import())->true();

        /** @var Company[] $companies */
        $companies = Company::find()->where(['name' => 'Мосэнерго', 'name_eng' => 'Mosenergo'])->all();

        expect('company is set', count($companies))->equals(1);

        $company = $companies[0];

        expect('company mode is set', $mode = $company->mode)->notNull();
        expect('company mode is correct', $mode->getAttributes(['name', 'name_eng']))->equals([
            'name' => 'обык., Прив.',
            'name_eng' => 'obyk., Priv.',
        ]);

        expect('company group is set', $group = $company->group)->notNull();
        expect('company group is correct', $group->getAttributes(['name', 'name_eng']))->equals([
            'name' => 'Добыча топливно-энергетических полезных ископаемых',
            'name_eng' => 'Mining energo'
        ]);

        $actual = $company->attributes;
        $expected = [
            'id' => 1338,
            'name' => 'Мосэнерго',
            'name_eng' => Transliterator::translit('Мосэнерго'),
            'name_for_list' => 'Мосэнерго',
            'name_for_list_eng' => Transliterator::translit('Мосэнерго'),
            'name_full' => 'ОАО Мосэнерго',
            'name_full_eng' => 'OJSC ' . Transliterator::translit('Мосэнерго'),
            'ticker' => 'МосБиржа: +МосЭнерго',
            'ticker_eng' => 'MosBirzha: Mosenergo',
            'mode_id' => 2,
            'group_id' => 2,
            'description' => 'Разведка, добыча, переработка и реализация нефти и нефтепродуктов',
            'description_eng' => 'Exploration, mining, processing',
            'site' => 'www.mosenergo.ru',
            'logo' => null,
            'free' => 1,
        ];

        ksort($actual);
        ksort($expected);

        expect('company attributes are correct', $actual)->equals($expected);

        $values = $company->companyValues;

        expect('unique values exists', count($values))->equals(1);

        $actual = $values[0]->attributes;
        $expected = [
            'company_id' => $company->id,
            'year' => 2010,
            'report_type_id' => 2,
            'auditor' => 'КПМГ',
            'auditor_eng' => 'KPMG',
            'value_va' => '1932235.460',
            'value_oa' => '628342.247',
            'value_ia' => '2560577.707',
            'value_kir' => null,
            'value_dkiz' => '276395.006',
            'value_kkiz' => null,
            'value_v' => '3187464.118',
            'value_fr' => '-21623.104',
            'value_fd' => '5284.298',
            'value_frn' => '-16338.806',
            'value_pdn' => '348338.479',
            'value_chpzp' => null,
            'value_aosina' => '-126155.017',
            'value_chdspood' => '411233.770',
            'value_ebitda' => '490832.302',
            'value_tebitda' => null,
            'currency' => 'Млн долл.',
            'currency_eng' => 'M USD',
        ];

        ksort($actual);
        ksort($expected);

        expect('first values are correct', $actual)->equals($expected);

        expect('value report type is set', $type = $values[0]->reportType)->notNull();
        expect('value report type group is correct', $type->getAttributes(['name', 'name_eng']))->equals([
            'name' => 'US GAAP',
            'name_eng' => 'US GAAP'
        ]);

        expect('version is up', (int)Version::find()->max('id'))->equals(1);
    }

    /**
     * @return UploadedFile
     */
    private function getUploadedFile()
    {
        $file = dirname(__DIR__) . '/_data/xlsx/replace.xlsx';
        return new UploadedFile([
            'name' => pathinfo($file, PATHINFO_BASENAME),
            'tempName' => $file,
            'type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'size' => filesize($file),
            'error' => 0,
        ]);
    }
}