<?php
require_once('../../config.php');
require_once('lib.php');

$context = context_system::instance();

require_login();
require_capability('block/olympiads:manage', $context);
block_olympiads_specialization();

$PAGE->set_url(new moodle_url('/blocks/olympiads/view.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('viewolympiads', 'block_olympiads'));
$PAGE->set_heading($SITE->fullname);


// Подсчёт количества записавшихся на каждую олимпиаду
$registrations = $DB->get_records_sql('
            SELECT olympiadid, COUNT(id) AS count
            FROM {olympiads_participants}
            GROUP BY olympiadid
        ');

// Преобразуем результат в массив для быстрого доступа по ID олимпиады
$registration_counts = [];
foreach ($registrations as $registration) {
    $registration_counts[$registration->olympiadid] = $registration->count;
}

// Получаем список всех олимпиад из базы данных
$olympiads = $DB->get_records('olympiads');

// Выводим заголовок страницы
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('olympiadslist', 'block_olympiads'));

// Проверяем, есть ли олимпиады
if ($olympiads) {
    $table = new html_table();
    $table->head = [
        get_string('name', 'block_olympiads'),
        get_string('startdate', 'block_olympiads'),
        get_string('enddate', 'block_olympiads'),
        get_string('description', 'block_olympiads'),
        get_string('registrations', 'block_olympiads'),
        get_string('actions', 'block_olympiads')
    ];
    foreach ($olympiads as $olympiad) {
        // Кнопка и картинка для редктирования
        $editurl = new moodle_url('/blocks/olympiads/add_edit.php', ['id' => $olympiad->id]);
        $pencilsvg = html_writer::empty_tag('img', ['src' => new moodle_url('/blocks/olympiads/bootstrap/icons/pencil.svg'), 'alt' => get_string('edit', 'block_olympiads'), 'width' => 16, 'height' => 16]);

        // Кнопка и картинка для удаления
        $deleteurl = new moodle_url('/blocks/olympiads/delete.php', ['id' => $olympiad->id]);
        $trashsvg = html_writer::empty_tag('img', ['src' => new moodle_url('/blocks/olympiads/bootstrap/icons/trash.svg'), 'alt' => get_string('delete', 'block_olympiads'), 'width' => 16, 'height' => 16]);

        // Количество записавшихся (если нет записей, выводим 0)
        $count = isset($registration_counts[$olympiad->id]) ? $registration_counts[$olympiad->id] : 0;

        // Кнопка записавшиеся
        $participantsurl = new moodle_url('/blocks/olympiads/participants.php', ['id' => $olympiad->id]);


        $table->data[] = [
            format_string($olympiad->name),
            date('d.m.Y',$olympiad->startdate),
            date('d.m.Y',$olympiad->enddate),
            shorten_text(format_text($olympiad->description, FORMAT_HTML), 100),
            $count,
            html_writer::link($editurl, $pencilsvg, ['class' => 'btn', 'title' => get_string('edit', 'block_olympiads')]) . ' ' .
            html_writer::link($deleteurl, $trashsvg, ['class' => 'btn', 'title' => get_string('delete', 'block_olympiads')]) . ' ' .
            html_writer::link($participantsurl, get_string('viewparticipants', 'block_olympiads'), ['class' => 'btn btn-info'])
        ];
    }
    echo html_writer::table($table);
} else {
    echo html_writer::div(get_string('noolympiads', 'block_olympiads'), 'alert alert-info');
}

echo $OUTPUT->footer();