<?php

use app\components\Transliterator;

return [
    [
        'id' => 1338,
        'name' => 'Мосэнерго',
        'name_eng' => Transliterator::translit('Мосэнерго'),
        'name_for_list' => 'Мосэнерго',
        'name_for_list_eng' => Transliterator::translit('Мосэнерго'),
        'name_full' => 'ОАО Мосэнерго',
        'name_full_eng' => 'OJSC ' . Transliterator::translit('Мосэнерго'),
        'ticker' => 'МосБиржа: +МосЭнерго',
        'ticker_eng' => Transliterator::translit('МосБиржа: +МосЭнерго'),
        'mode_id' => 1,
        'group_id' => 1,
        'description' => 'Производитель тепловой и электрической энергии',
        'description_eng' => Transliterator::translit('Производитель тепловой и электрической энергии'),
        'site' => 'www.mosenergo.ru',
        'logo' => null,
        'free' => 1,
    ],
];
