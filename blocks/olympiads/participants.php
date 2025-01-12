<?php
require_once('../../config.php');
require_once('lib.php');

block_olympiads_specialization();

$id = required_param('id', PARAM_INT); // ID олимпиады
$context = context_system::instance();

require_login();
require_capability('block/olympiads:manage', $context);

$PAGE->set_url(new moodle_url('/blocks/olympiads/participants.php', ['id' => $id]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('participantslist', 'block_olympiads'));
$PAGE->set_heading($SITE->fullname);

// Проверяем, существует ли олимпиада
if (!$DB->record_exists('olympiads', ['id' => $id])) {
    print_error('invalidrecord', 'error');
}

// Получаем список записанных студентов
$sql = "SELECT p.id, u.firstname, u.lastname, u.email, p.timecreated
        FROM {olympiads_participants} p
        JOIN {user} u ON p.userid = u.id
        WHERE p.olympiadid = :olympiadid
        ORDER BY p.timecreated ASC";

$participants = $DB->get_records_sql($sql, ['olympiadid' => $id]);

// Выводим заголовок страницы
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('participantslist', 'block_olympiads'));

// Проверяем, есть ли участники
if ($participants) {
    $table = new html_table();
    $table->head = [get_string('fullname'), get_string('email'), get_string('registrationdate', 'block_olympiads')];
    foreach ($participants as $participant) {
        $table->data[] = [
            fullname($participant),
            $participant->email,
            date('d.m.Y',$participant->timecreated)
        ];
    }
    echo html_writer::table($table);
} else {
    echo html_writer::div(get_string('noparticipants', 'block_olympiads'), 'alert alert-info');
}

echo $OUTPUT->footer();