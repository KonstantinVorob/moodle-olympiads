<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = [

    // Возможность управлять олимпиадами (создавать, редактировать, удалять)
    'block/olympiads:manage' => [
        'captype' => 'write',                // Тип действия — запись
        'contextlevel' => CONTEXT_SYSTEM,    // Доступно на уровне системного контекста
    ],

    // Возможность просматривать список олимпиад
    'block/olympiads:view' => [
        'captype' => 'read',                 // Тип действия — чтение
        'contextlevel' => CONTEXT_SYSTEM,    // Доступно на уровне системного контекста
    ],

    // Возможность записываться на олимпиады
    'block/olympiads:register' => [
        'captype' => 'write',                // Тип действия — запись
        'contextlevel' => CONTEXT_SYSTEM,    // Доступно на уровне системного контекста
    ],

    // Возможность просматривать список записавшихся студентов
    'block/olympiads:viewregistrations' => [
        'captype' => 'read',                 // Тип действия — чтение
        'contextlevel' => CONTEXT_SYSTEM,    // Доступно на уровне системного контекста
    ],
];