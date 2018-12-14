<?php

namespace app\components\parser\helpers;


use app\models\Company;

class Candidates
{

    const TEMPLATE = '/[\W]+(%s)[\W]+/i';

    public static function get()
    {
        //$cols = preg_filter('/^name.*/', '$0', array_keys((new Company())->attributes));

        $companies = Company::find()->select(['id', 'parser_variations'])->asArray()->all();

        $companies = array_map(function ($item) {
            return [
                'id' => $item['id'],
                'preg_condition' => self::buildRegex($item['parser_variations'])
            ];
        }, $companies);

        return array_filter($companies);
    }

    /**
     * @return array
     */
    public static function matches($text)
    {
        $candidates = self::get();
        $result = [];

        foreach ($candidates as $candidate) {
            if (preg_match($candidate['preg_condition'], $text)) {
               $result[] = $candidate['id'];
            }
        }

        return $result;
    }

    private static function buildRegex($variations, $separator = Company::VARIATIONS_SEPARATOR)
    {
        $result = array_filter(explode($separator, $variations));
        if (count($result) < 1)
        {
            return '//';
        }

        $result = array_map(function($str){
            $str = mb_strtolower(trim($str));
            $str = preg_quote($str);

            return $str;
        }, $result);

        $result = implode('|',$result);

        $result = sprintf(self::TEMPLATE, $result);

        return $result;
    }
}