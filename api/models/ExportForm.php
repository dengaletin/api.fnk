<?php

namespace app\models;

use yii\base\Model;

class ExportForm extends Model
{
    public function export()
    {
        $items = [];

        $item = new FileRow();

        foreach ($item->attributes() as $attribute) {
            $item->$attribute = $item->getAttributeLabel($attribute);
        }

        $items[] = $item;

        foreach (CompanyValue::find()->orderBy('{{%company}}.id')->joinWith([
            'company',
            'reportType',
            'company.mode',
            'company.group',
        ])->each() as $value) {

            /** @var CompanyValue $value */
            
            $item = new FileRow();

            $item->name = $value->company->name;
            $item->name_eng = $value->company->name_eng;
            $item->name_for_list = $value->company->name_for_list;
            $item->name_for_list_eng = $value->company->name_for_list_eng;
            $item->name_full = $value->company->name_full;
            $item->name_full_eng = $value->company->name_full_eng;
            $item->ticker = $value->company->ticker;
            $item->ticker_eng = $value->company->ticker_eng;
            $item->mode = $value->company->mode->name;
            $item->mode_eng = $value->company->mode->name_eng;
            $item->group = $value->company->group->name;
            $item->group_eng = $value->company->group->name_eng;
            $item->description = $value->company->description;
            $item->description_eng = $value->company->description_eng;
            $item->site = $value->company->site;

            $item->year = $value->year;
            $item->report_type = $value->reportType->name;
            $item->currency = $value->currency;
            $item->currency_eng = $value->currency_eng;
            $item->auditor = $value->auditor;
            $item->auditor_eng = $value->auditor_eng;
            $item->value_va = $value->value_va;
            $item->value_oa = $value->value_oa;
            $item->value_ia = $value->value_ia;
            $item->value_kir = $value->value_kir;
            $item->value_dkiz = $value->value_dkiz;
            $item->value_kkiz = $value->value_kkiz;
            $item->value_v = $value->value_v;
            $item->value_fr = $value->value_fr;
            $item->value_fd = $value->value_fd;
            $item->value_frn = $value->value_frn;
            $item->value_pdn = $value->value_pdn;
            $item->value_chpzp = $value->value_chpzp;
            $item->value_aosina = $value->value_aosina;
            $item->value_chdspood = $value->value_chdspood;
            $item->value_ebitda = $value->value_ebitda;
            $item->value_tebitda = $value->value_tebitda;

            $item->id = $value->company->id;

            $items[] = $item;
        }

        return $this->exportXsls($items);
    }

    /**
     * @param FileRow[] $items
     * @return array
     */
    private function exportXsls($items)
	{
        $inputFileType = 'Excel2007';
        $objPHPExcel = new \PHPExcel();

        $worksheet = $objPHPExcel->getActiveSheet();
        foreach($items as $row => $item) {
            foreach ($item->attributes() as $column => $attribute) {
                $worksheet->setCellValueByColumnAndRow($column, $row + 1, $item->$attribute);
            }
        }

        $file = tempnam(sys_get_temp_dir(), 'export');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, $inputFileType);
        $objWriter->save($file);

        return $file;
	}
}