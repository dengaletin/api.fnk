<?php

namespace app\models;

use yii\base\Model;

class ProfileExportForm extends Model
{
    public function export()
    {
        $query = Profile::find()->confirmed()->orderBy('id')->with('device');

        $inputFileType = 'Excel2007';
        $objPHPExcel = new \PHPExcel();

        $worksheet = $objPHPExcel->getActiveSheet();
        foreach($query->each() as $row => $profile) {
            $i = 0;
            $worksheet->setCellValueByColumnAndRow($i++, $row, $profile->device->device_token);
            $worksheet->setCellValueByColumnAndRow($i++, $row, $profile->last_name);
            $worksheet->setCellValueByColumnAndRow($i++, $row, $profile->first_name);
            $worksheet->setCellValueByColumnAndRow($i++, $row, $profile->phone);
            $worksheet->setCellValueByColumnAndRow($i, $row, $profile->email);
        }

        $file = tempnam(sys_get_temp_dir(), 'export');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, $inputFileType);
        $objWriter->save($file);

        return $file;
	}
}