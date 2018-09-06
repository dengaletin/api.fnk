<?php

namespace app\models;

use yii\base\Model;

class FileRow extends Model
{
    public $name;
    public $name_eng;
    public $name_for_list;
    public $name_for_list_eng;
    public $name_full;
    public $name_full_eng;
    public $ticker;
    public $ticker_eng;
    public $mode;
    public $mode_eng;
    public $group;
    public $group_eng;
    public $description;
    public $description_eng;
    public $site;
    public $year;
    public $report_type;
    public $report_type_eng;
    public $currency;
    public $currency_eng;
    public $auditor;
    public $auditor_eng;
    public $value_va;
    public $value_oa;
    public $value_ia;
    public $value_kir;
    public $value_dkiz;
    public $value_kkiz;
    public $value_v;
    public $value_fr;
    public $value_fd;
    public $value_frn;
    public $value_pdn;
    public $value_chpzp;
    public $value_aosina;
    public $value_chdspood;
    public $value_ebitda;
    public $value_tebitda;
    public $id;
    public $raw_data;

    public function validate($attributeNames = null, $clearErrors = true)
    {
        foreach($this->attributes() as $attribute) {
            if (strpos($attribute, 'value_') === 0 && !is_numeric($this->$attribute)) {
                $this->$attribute = null;
            }
        }

        return !empty($this->year) && !empty($this->name) && !empty($this->ticker) && !empty($this->group);
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Сокращенное наименование',
            'name_eng' => 'Short name',
            'name_for_list' => 'Полное наименование для списка',
            'name_for_list_eng' => 'Full name for list',
            'name_full' => 'Полное наименование',
            'name_full_eng' => 'Full name and comments',
            'ticker' => 'Фондовая Биржа',
            'ticker_eng' => 'Stock Exchange',
            'mode' => 'Вид акций',
            'mode_eng' => 'Kind of shares',
            'group' => 'Отрасль',
            'group_eng' => 'Industry',
            'description' => 'Краткое описание деятельности',
            'description_eng' => 'Shirt activity discription',
            'site' => 'Сайт',
            'year' => 'Период',
            'report_type' => 'Вид отчетности',
            'report_type_eng' => 'Kind of statement',
            'currency' => 'Валюта публикации',
            'currency_eng' => 'Currency representation',
            'auditor' => 'Аудитор',
            'auditor_eng' => 'Audit firm',
            'value_va' => 'Внеоборотные активы',
            'value_oa' => 'Оборотные активы',
            'value_ia' => 'Итого АКТИВЫ',
            'value_kir' => 'Капитал и резервы',
            'value_dkiz' => 'Долгосрочные кредиты и займы',
            'value_kkiz' => 'Краткосрочные кредиты и займы',
            'value_v' => 'Выручка',
            'value_fr' => 'Финансовые расходы',
            'value_fd' => 'Финансовые доходы',
            'value_frn' => 'Финансовые расходы, нетто',
            'value_pdn' => 'Прибыль до налогообложения',
            'value_chpzp' => 'Чистая прибыль за период',
            'value_aosina' => 'Амортизация основных средств и нематериальных активов',
            'value_chdspood' => 'Чистые денежные средства, полученные от операционной деятельности',
            'value_ebitda' => 'EBITDA',
            'value_tebitda' => 'Total debt/EBITDA',
            'id' => 'ID',
        ];
    }

    public function fillByNames($columns)
    {
        $attributes = array_flip(array_map('trim', $this->attributeLabels()));
        foreach ($columns as $name => $value) {
            if (array_key_exists(trim($name), $attributes)) {
                $this->{$attributes[trim($name)]} = trim($value);
            }
        }
    }
}