<?php

namespace app\models;

use app\components\ChunkReadFilter;
use app\components\Transliterator;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\UploadedFile;

class ImportForm extends Model
{
    public $file;
    public $clearValues;
    public $clearCompanies;

    public function rules()
    {
        return [
            [['file'], 'file'],
            [['clearValues', 'clearCompanies'], 'number'],
        ];
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if (empty($this->file)) {
                $this->file = UploadedFile::getInstance($this, 'file');
            }
            return true;
        }
        return false;
    }

    public function import()
    {
        $new_data_versions = [];
        $file = $this->file;
        if ($file instanceof UploadedFile) {
            $items = $this->parseXsls($file->tempName);

            if ($this->clearValues) {
                CompanyValue::deleteAll();
                ReportType::deleteAll();
            }

            if ($this->clearCompanies) {
                foreach (CompanyFile::find()->each() as $companyFile) {
                    /** @var CompanyFile $companyFile */
                    $companyFile->delete();
                };
                Company::deleteAll();
                Mode::deleteAll();
                Group::deleteAll();
            }

            foreach ($items as $item) {
                if ($item->id) {
                    $company = Company::find()->where(['id' => $item->id])->limit(1)->one();
                } else {
                    $company = Company::find()->where(['ticker' => $item->ticker])->limit(1)->one();
                }

                /** @var Mode $mode */
                $mode = $this->findOrCreate(Mode::className(), ['name' => $item->mode, 'name_eng' => $item->mode_eng]);

                /** @var Group $group */
                $group = $this->findOrCreate(Group::className(), ['name' => $item->group, 'name_eng' => $item->group_eng]);

                if (!$company) {
                    $company = new Company();
                    $company->id = $item->id;
                }

                $company->mode_id = $mode->id;
                $company->group_id = $group->id;

                $company->name = $item->name;
                $company->name_eng = $item->name_eng;
                $company->name_for_list = $item->name_for_list;
                $company->name_for_list_eng = $item->name_for_list_eng;
                $company->name_full = $item->name_full;
                $company->name_full_eng = $item->name_full_eng;
                $company->ticker = $item->ticker;
                $company->ticker_eng = $item->ticker_eng;
                $company->description = $item->description;
                $company->description_eng = $item->description_eng;
                $company->site = $item->site;
                $company->country_residency = $item->raw_data[12];
                $company->country_residency_eng = $item->raw_data[13];

                if (!in_array($item->id, $new_data_versions)) {
                    $company->version++;

                    $new_data_versions[] = $item->id;
                }

                $company->save(false);

                /** @var ReportType $reportType */
                $reportType = $this->findOrCreate(ReportType::className(), ['name' => $item->report_type, 'name_eng' => $item->report_type_eng]);

                if (!$value = CompanyValue::find()->where(['company_id' => $company->id, 'year' => $item->year])->limit(1)->one()) {
                    $value = new CompanyValue();
                }

                $value->company_id = $company->id;
                $value->report_type_id = $reportType->id;

                $value->year = $item->year;
                $value->auditor = $item->auditor;
                $value->auditor_eng = $item->auditor_eng;
                $value->currency = $item->currency;
                $value->currency_eng = $item->currency_eng;
                $value->value_va = $item->value_va;
                $value->value_oa = $item->value_oa;
                $value->value_ia = $item->value_ia;
                $value->value_kir = $item->value_kir;
                $value->value_dkiz = $item->value_dkiz;
                $value->value_kkiz = $item->value_kkiz;
                $value->value_v = $item->value_v;
                $value->value_fr = $item->value_fr;
                $value->value_fd = $item->value_fd;
                $value->value_frn = $item->value_frn;
                $value->value_pdn = $item->value_pdn;
                $value->value_chpzp = $item->value_chpzp;
                $value->value_aosina = $item->value_aosina;
                $value->value_chdspood = $item->value_chdspood;
                $value->value_ebitda = $item->value_ebitda;
                $value->value_tebitda = $item->value_tebitda;
                $value->setRaw($item->raw_data);
                $value->save(false);
            }

            $this->clearEmptyRows();

            $version = new Version();
            $version->save();

            return true;
        }

        return false;
    }

    /**
     * @param $file
     * @throws \PHPExcel_Calculation_Exception
     * @return FileRow[]
     */
    private function parseXsls($file)
    {
        $items = [];

        $inputFileType = 'Excel2007';
        /** @var \PHPExcel_Reader_Excel2007 $objReader */
        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
        $chunkFilter = new ChunkReadFilter();
        $startRow = 1;
        $chunkSize = 100;
        $colNames = [];
        $colNamesShort = [ ];
        $current_row = 0;

        while ($startRow <= 5000) {
            $chunkFilter->setRows($startRow, $chunkSize);
            $objReader->setReadFilter($chunkFilter);
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($file);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            foreach ($objWorksheet->getRowIterator() as $row) {
                /** @var \PHPExcel_Worksheet_Row $row */
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $cols = [];
                foreach ($cellIterator as $cell) {
                    /** @var \PHPExcel_Cell $cell */
                    $value = $cell->getValue();

                    if (\PHPExcel_Cell::columnIndexFromString($cell->getColumn()) >= 36 and ('n/a' == $value or '' == $value)) {
                        $cols[] = 'n/a';
                    } else if($value === 'n/a') {
                        $cols[] = null;
                    } else if (mb_strpos($value, '=IF(LEN(') === 0) {
                        $cols[] = '';
                    } elseif (preg_match('#^=translit\((?P<col>[A-Z]+)\d+\)#is', $value, $matches)) {
                        $cols[] = Transliterator::translit($cols[\PHPExcel_Cell::columnIndexFromString($matches['col']) - 1]);
                    } elseif (preg_match('#^=\"(?P<jsc>\w+)\s\"\&(?P<col>[A-Z]+)\d+$#is', $value, $matches)) {
                        $cols[] = $matches['jsc'] . ' ' . $cols[\PHPExcel_Cell::columnIndexFromString($matches['col']) - 1];
                    } else {
                        try {
                            $cols[] = $cell->getCalculatedValue();
                        } catch (\PHPExcel_Calculation_Exception $e) {
                            throw new \PHPExcel_Calculation_Exception('Error on ' . $cell->getCoordinate() . ': ' . $cell->getValue());
                        }
                    }
                }

                if (array_filter($cols)) {
                    if ($cols[2] == 'Сокращенное наименование') {
                        $colNames = $cols;
                    } else if($cols[0] == '1' && $cols[1] == '2' && $cols[2] == '3') {
                        $colNamesShort = [ ];
                        foreach($cols as $v) {
                            $colNamesShort[] = str_replace('Т', 'T', is_float($v) ? (string)round($v, 0) : (string)$v);
                        }
                    } else {//if ($current_row > 1) {
                        $item = new FileRow();

                        $item->raw_data = array_combine($colNamesShort, array_slice($cols, 0, count($colNamesShort)));

                        $item->fillByNames(array_combine($colNames, array_slice($cols, 0, count($colNames))));
                        if ($item->validate()) {
                            $items[] = $item;
                        }
                    }
                }

                $current_row++;
            }
            $startRow += $chunkSize;
            unset($objPHPExcel);
        }

        return $items;
    }

    public function attributeLabels()
    {
        return [
            'file' => 'Файл',
            'clearValues' => 'Удалить предыдущие отчёты',
            'clearCompanies' => 'Удалить компании и файлы',
        ];
    }

    private $_models = [];

    /**
     * @param $class
     * @param array $attributes
     * @return ActiveRecord
     * @internal param $item
     */
    private function findOrCreate($class, $attributes)
    {
        $key = $class . '_' . serialize($attributes);
        if (!array_key_exists($key, $this->_models)) {
            if (!$model = call_user_func([$class, 'find'])->where($attributes)->limit(1)->one()) {
                /** @var ActiveRecord $model */
                $model = new $class;
                $model->attributes = $attributes;
                $model->save(false);
            }
            $this->_models[$key] = $model;
        }
        return $this->_models[$key];
    }

    private function clearEmptyRows()
    {
        ReportType::deleteAll([
            'id' => ReportType::find()
                ->select('report_type.id')
                ->from(['report_type' => ReportType::tableName()])
                ->where([
                    'not exists',
                    CompanyValue::find()
                        ->from(['company_value' => CompanyValue::tableName()])
                        ->select([new Expression(1)])
                        ->where([
                            'company_value.report_type_id' => new Expression('[[report_type.id]]'),
                        ])
                ])
                ->column(),
        ]);

        Mode::deleteAll([
            'id' => Mode::find()
                ->select('mode.id')
                ->from(['mode' => Mode::tableName()])
                ->where([
                    'not exists',
                    Company::find()
                        ->from(['company' => Company::tableName()])
                        ->select([new Expression(1)])
                        ->where([
                            'company.mode_id' => new Expression('mode.id'),
                        ])
                ])
                ->column(),
        ]);

        Group::deleteAll([
            'id' => Group::find()
                ->select('group.id')
                ->from(['group' => Group::tableName()])
                ->where([
                    'not exists',
                    Company::find()
                        ->from(['company' => Company::tableName()])
                        ->select([new Expression(1)])
                        ->where([
                            'company.group_id' => new Expression('group.id'),
                        ])
                ])
                ->column(),
        ]);
    }
}