<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = [
    // Возможность управлять олимпиадами (создавать, редактировать, удалять)
    'block/olympiads:manage' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [] // Для роли «Сотрудник приемной комиссии» настраивается вручную
    ],

    // Возможность просматривать список олимпиад
    'block/olympiads:view' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [] // Для роли «Абитуриент» настраивается вручную
    ],

    // Возможность записываться на олимпиады
    'block/olympiads:register' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [] // Для роли «Абитуриент» настраивается вручную
    ],

    // Возможность просматривать список записавшихся студентов
    'block/olympiads:viewregistrations' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [] // Для роли «Сотрудник приемной комиссии» настраивается вручную
    ],
];