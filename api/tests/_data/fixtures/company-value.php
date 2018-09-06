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
    array_combine($keys, [1, 2010, 1, 'PWC', Transliterator::translit('PWC'), 197147.000, 58554.000, 255701.000, 193769.000, 11770.000, 4976.000, 145298.000, -169.000, 2428.000, 2259.000, 10998.000, 8668.000, -12214.000, 22710.000, 20953.000, 0.7992172958526226, 'Млн руб.', Transliterator::translit('Млн руб.')]),
    array_combine($keys, [1, 2011, 1, 'PWC', Transliterator::translit('PWC'), 199803.000, 62618.000, 262421.000, 200033.000, 10223.000, 5354.000, 161119.000, -198.000, 1277.000, 1079.000, 11966.000, 9892.000, -13041.000, 16562.000, 23928.000, 0.6509946506185222, 'Млн руб.', Transliterator::translit('Млн руб.')]),
    array_combine($keys, [2, 2011, 1, 'PWC', Transliterator::translit('PWC'), 199803.000, 62618.000, 262421.000, 200033.000, 10223.000, 5354.000, 161119.000, -198.000, 1277.000, 1079.000, 11966.000, 9892.000, -13041.000, 16562.000, 23928.000, 0.6509946506185222, 'Млн руб.', Transliterator::translit('Млн руб.')]),
];
