<?php
defined('MOODLE_INTERNAL') || die();

$functions = array(
    'block_olympiads_pluginfile' => array(
        'classname'   => 'block_olympiads',
        'methodname'  => 'pluginfile',
        'classpath'   => 'blocks/olympiads/lib.php',
        'type'        => 'file',
        'contextlevel'=> CONTEXT_SYSTEM,
        'fileareas'   => array('image')
    )
);