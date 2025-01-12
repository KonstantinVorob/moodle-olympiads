<?php
require_once('../../config.php');

$context = context_system::instance();

require_login();
require_capability('block/olympiads:manage', $context);

$PAGE->set_url(new moodle_url('/blocks/olympiads/view.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('viewolympiads', 'block_olympiads'));
$PAGE->set_heading($SITE->fullname);


// Получаем список всех олимпиад из базы данных
$olympiads = $DB->get_records('olympiads');

// Выводим заголовок страницы
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('olympiadslist', 'block_olympiads'));

// Проверяем, есть ли олимпиады
if ($olympiads) {
    $table = new html_table();
    $table->head = [get_string('name', 'block_olympiads'), get_string('startdate', 'block_olympiads'), get_string('enddate', 'block_olympiads'), get_string('actions', 'block_olympiads')];
    foreach ($olympiads as $olympiad) {
        $editurl = new moodle_url('/blocks/olympiads/add_edit.php', ['id' => $olympiad->id]);
        $deleteurl = new moodle_url('/blocks/olympiads/delete.php', ['id' => $olympiad->id]);
        $table->data[] = [
            format_string($olympiad->name),
            userdate($olympiad->startdate),
            userdate($olympiad->enddate),
            html_writer::link($editurl, get_string('edit'), ['class' => 'btn btn-primary']) . ' ' .
            html_writer::link($deleteurl, get_string('delete'), ['class' => 'btn btn-danger'])
        ];
    }
    echo html_writer::table($table);
} else {
    echo html_writer::div(get_string('noolympiads', 'block_olympiads'), 'alert alert-info');
}

echo $OUTPUT->footer();