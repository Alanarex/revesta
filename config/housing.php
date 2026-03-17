<?php

return [
    'dpe_classes' => [
        'A' => 'A - Excellent',
        'B' => 'B - Très bon',
        'C' => 'C - Bon',
        'D' => 'D - Moyen',
        'E' => 'E - Passable',
        'F' => 'F - Médiocre (passoire thermique)',
        'G' => 'G - Mauvais (passoire thermique)',
    ],

    'dpe_numeric_map' => [
        '1' => 'A',
        '2' => 'B',
        '3' => 'C',
        '4' => 'D',
        '5' => 'E',
        '6' => 'F',
        '7' => 'G',
    ],

    'construction_periods' => [
        'plus_15_ans' => 'au moins 15 ans',
        'moins_15_ans' => 'moins de 15 ans',
        'moins_2_ans' => 'moins de 2 ans',
    ],

    'construction_period_aliases' => [
        'au moins 15 ans' => 'plus_15_ans',
        'plus de 15 ans' => 'plus_15_ans',
        'moins de 15 ans' => 'moins_15_ans',
        'moins de 2 ans' => 'moins_2_ans',
        '1949-1974' => 'plus_15_ans',
        'avant 1949' => 'plus_15_ans',
        '1975-1988' => 'plus_15_ans',
        '1989-2000' => 'plus_15_ans',
        '2001-2012' => 'moins_15_ans',
        'après 2012' => 'moins_15_ans',
    ],

    'housing_types' => [
        'maison' => 'Maison à rénover',
        'appartement' => 'Appartement à rénover',
        'indifferent' => 'Indifférent',
    ],

    'energy_gain_targets' => [
        2 => 'Au moins 2 classes DPE',
        3 => 'Au moins 3 classes DPE',
        4 => 'Au moins 4 classes DPE',
    ],
];
