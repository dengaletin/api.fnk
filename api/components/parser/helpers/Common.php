<?php

namespace app\components\parser\helpers;


use yii\helpers\Html;

class Common
{
    public static function purify($text)
    {
        $text = trim($text);
        $text = Html::decode($text);
        $text = preg_replace('/(\s|\n|\t|\r){2,}/','$1',$text);
        return $text;
    }

    public static function mysqlDate($string, $tz)
    {
        return (new \DateTime($string,new \DateTimeZone($tz)))->format('y-m-d H:i:s');
    }
}