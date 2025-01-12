<?php
require_once('../../config.php');

$id = required_param('id', PARAM_INT); // ID олимпиады для записи
$context = context_system::instance();

require_login();

$PAGE->set_url(new moodle_url('/blocks/olympiads/register.php', ['id' => $id]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('registerolympiad', 'block_olympiads'));
$PAGE->set_heading($SITE->fullname);

// Проверяем, существует ли олимпиада
if (!$DB->record_exists('olympiads', ['id' => $id])) {
    print_error('invalidrecord', 'error');
}

// Проверяем, уже записан ли пользователь на эту олимпиаду
if ($DB->record_exists('olympiads_participants', ['olympiadid' => $id, 'userid' => $USER->id])) {
    redirect(new moodle_url('/blocks/olympiads/view_public.php'), get_string('alreadyregistered', 'block_olympiads'));
}

// Записываем пользователя на олимпиаду
$record = new stdClass();
$record->olympiadid = $id;
$record->userid = $USER->id;
$record->timecreated = time();
$DB->insert_record('olympiads_participants', $record);

// Перенаправляем с уведомлением об успешной записи
redirect(new moodle_url('/blocks/olympiads/view_public.php'), get_string('registrationsuccess', 'block_olympiads'));