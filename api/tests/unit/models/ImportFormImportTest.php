<?php

namespace tests\unit\models;

use app\components\Transliterator;
use app\models\Company;
use app\models\Group;
use app\models\ImportForm;
use app\models\Mode;
use app\models\ReportType;
use app\models\Version;
use Codeception\Test\Unit;
use tests\fixtures\CompanyFixture;
use tests\fixtures\CompanyValueFixture;
use tests\fixtures\GroupFixture;
use tests\fixtures\ModeFixture;
use tests\fixtures\ReportTypeFixture;
use tests\fixtures\VersionFixture;
use yii\web\UploadedFile;

class ImportFormImportTest extends Unit
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
                'dataFile' => '@tests/unit/_data/fixtures/import/version.php',
            ],
            'modes' => [
                'class' => ModeFixture::className(),
                'dataFile' => '@tests/unit/_data/fixtures/import/mode.php',
            ],
            'groups' => [
                'class' => GroupFixture::className(),
                'dataFile' => '@tests/unit/_data/fixtures/import/group.php',
            ],
            'companies' => [
                'class' => CompanyFixture::className(),
                'dataFile' => '@tests/unit/_data/fixtures/import/company.php',
            ],
            'reportTypes' => [
                'class' => ReportTypeFixture::className(),
                'dataFile' => '@tests/unit/_data/fixtures/import/report-type.php',
            ],
            'companyValues' => [
                'class' => CompanyValueFixture::className(),
                'dataFile' => '@tests/unit/_data/fixtures/import/company-value.php',
            ],
        ]);
    }

    public function testImport()
    {
        $model = new ImportForm();

        $model->file = $this->getUploadedFile();

        expect('model is valid', $model->validate())->true();
        expect('import is true', $model->import())->true();

        /** @var Company[] $companies */
        $companies = Company::find()->where(['name' => 'ЛУКОЙЛ', 'name_eng' => 'LUKOJL'])->all();

        expect('company is set', count($companies))->equals(1);

        $company = $companies[0];

        expect('company mode is set', $mode = $company->mode)->notNull();
        expect('company mode is correct', $mode->getAttributes(['name', 'name_eng']))->equals([
            'name' => 'обык.',
            'name_eng' => 'obyk.',
        ]);

        expect('company group is set', $group = $company->group)->notNull();
        expect('company group is correct', $group->getAttributes(['name', 'name_eng']))->equals([
            'name' => 'Добыча полезных ископаемых',
            'name_eng' => 'Mining'
        ]);

        $actual = $company->attributes;
        $expected = [
            'id' => 1337,
            'name' => 'ЛУКОЙЛ',
            'name_eng' => Transliterator::translit('ЛУКОЙЛ'),
            'name_for_list' => 'Нефтяная компания "ЛУКОЙЛ"',
            'name_for_list_eng' => Transliterator::translit('Нефтяная компания "ЛУКОЙЛ"'),
            'name_full' => 'ОАО Нефтяная компания "ЛУКОЙЛ"',
            'name_full_eng' => 'OJSC ' . Transliterator::translit('Нефтяная компания "ЛУКОЙЛ"'),
            'ticker' => 'МосБиржа: Лукойл',
            'ticker_eng' => Transliterator::translit('МосБиржа: Лукойл'),
            'mode_id' => $mode->id,
            'group_id' => $group->id,
            'description' => 'Разведка, добыча, переработка и реализация нефти и нефтепродуктов',
            'description_eng' => 'Exploration, mining, processing',
            'site' => 'www.lukoil.ru',
            'logo' => null,
            'free' => 0,
        ];

        ksort($actual);
        ksort($expected);

        expect('company attributes are correct', $actual)->equals($expected);

        $values = $company->companyValues;

        expect('unique values exists', count($values))->equals(4);

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

        $actual = $values[1]->attributes;
        $expected = [
            'company_id' => $company->id,
            'year' => 2011,
            'report_type_id' => 2,
            'auditor' => 'КПМГ',
            'auditor_eng' => 'KPMG',
            'value_va' => '2178484.714',
            'value_oa' => '757542.037',
            'value_ia' => '2936026.751',
            'value_kir' => '2172142.083',
            'value_dkiz' => '235031.530',
            'value_kkiz' => '57695.411',
            'value_v' => '3927631.173',
            'value_fr' => '-20394.882',
            'value_fd' => '6200.750',
            'value_frn' => '-14194.133',
            'value_pdn' => '385533.807',
            'value_chpzp' => '288760.972',
            'value_aosina' => '-131450.013',
            'value_chdspood' => '455916.723',
            'value_ebitda' => '531177.953',
            'value_tebitda' => 0.5510901561606968,
            'currency' => 'Млн долл.',
            'currency_eng' => 'M USD',
        ];

        ksort($actual);
        ksort($expected);

        expect('second values are correct', $actual)->equals($expected);

        expect('value report type is set', $type = $values[0]->reportType)->notNull();
        expect('value report type group is correct', $type->getAttributes(['name', 'name_eng']))->equals([
            'name' => 'МСФО',
            'name_eng' => 'MSFO'
        ]);

        expect('unused mode is deleted', Mode::findOne(['name' => 'Неиспользуемый вид']))->null();
        expect('unused group is deleted', Group::findOne(['name' => 'Неиспользуемая группа']))->null();
        expect('unused report type is deleted', ReportType::findOne(['name' => 'Неиспользуемый тип']))->null();

        expect('version is up', (int)Version::find()->max('id'))->equals(1);
    }

    /**
     * @return UploadedFile
     */
    private function getUploadedFile()
    {
        $file = dirname(__DIR__) . '/_data/xlsx/import.xlsx';
        return new UploadedFile([
            'name' => pathinfo($file, PATHINFO_BASENAME),
            'tempName' => $file,
            'type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'size' => filesize($file),
            'error' => 0,
        ]);
    }
}