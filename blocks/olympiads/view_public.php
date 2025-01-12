<?php
require_once('../../config.php');

$context = context_system::instance();

require_login();

$PAGE->set_url(new moodle_url('/blocks/olympiads/view_public.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('availableolympiads', 'block_olympiads'));
$PAGE->set_heading($SITE->fullname);

// Получаем список всех доступных олимпиад
$olympiads = $DB->get_records('olympiads');

// Выводим заголовок страницы
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('availableolympiads', 'block_olympiads'));

if ($olympiads) {
    $table = new html_table();
    $table->head = [get_string('name', 'block_olympiads'), get_string('startdate', 'block_olympiads'), get_string('enddate', 'block_olympiads'), get_string('actions', 'block_olympiads')];
    foreach ($olympiads as $olympiad) {
        $registerurl = new moodle_url('/blocks/olympiads/register.php', ['id' => $olympiad->id]);
        $table->data[] = [
            format_string($olympiad->name),
            userdate($olympiad->startdate),
            userdate($olympiad->enddate),
            html_writer::link($registerurl, get_string('register', 'block_olympiads'), ['class' => 'btn btn-success'])
        ];
    }
    echo html_writer::table($table);
} else {
    echo html_writer::div(get_string('noavailableolympiads', 'block_olympiads'), 'alert alert-info');
}

echo $OUTPUT->footer();