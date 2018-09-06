<?php

use app\components\Transliterator;

$keys = [
    'company_id',
    'year',
    'report_type_id',
    'auditor',
    'auditor_eng',
    'value_va',
    'value_oa',
    'value_ia',
    'value_kir',
    'value_dkiz',
    'value_kkiz',
    'value_v',
    'value_fr',
    'value_fd',
    'value_frn',
    'value_pdn',
    'value_chpzp',
    'value_aosina',
    'value_chdspood',
    'value_ebitda',
    'value_tebitda',
    'currency',
    'currency_eng',
];

return [
    array_combine($keys, [1338, 2010, 1, 'PWC', Transliterator::translit('PWC'), 197147.000, 58554.000, 255701.000, 193769.000, 11770.000, 4976.000, 145298.000, -169.000, 2428.000, 2259.000, 10998.000, 8668.000, -12214.000, 22710.000, 20953.000, 0.7992172958526226, 'Млн руб.', Transliterator::translit('Млн руб.')]),
];
