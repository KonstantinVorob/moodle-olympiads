<?php
require_once('../../config.php');

$id = required_param('id', PARAM_INT); // ID олимпиады для удаления
$context = context_system::instance();

require_login();
require_capability('block/olympiads:manage', $context);

// Проверяем, существует ли олимпиада
if (!$DB->record_exists('olympiads', ['id' => $id])) {
print_error('invalidrecord', 'error');
}

// Удаляем олимпиаду
$DB->delete_records('olympiads', ['id' => $id]);

// Перенаправляем обратно на список олимпиад с уведомлением
redirect(new moodle_url('/blocks/olympiads/view.php'), get_string('recorddeleted', 'block_olympiads'));
