<?php
require_once('../../config.php');
//require_once('lib.php');
require_once('form/olympiad_form.php');

$id = optional_param('id', 0, PARAM_INT); // ID олимпиады (0 — новая запись)
$context = context_system::instance();

require_login();
require_capability('block/olympiads:manage', $context);

$PAGE->set_url(new moodle_url('/blocks/olympiads/add_edit.php', ['id' => $id]));
$PAGE->set_context($context);
$PAGE->set_title($id ? get_string('editolympiad', 'block_olympiads') : get_string('addolympiad', 'block_olympiads'));
$PAGE->set_heading($SITE->fullname);

// Загрузка существующей записи, если указано ID
if ($id) {
    $olympiad = $DB->get_record('olympiads', ['id' => $id], '*', MUST_EXIST);
} else {
    $olympiad = null;
}

$form = new olympiad_form(null, ['olympiad' => $olympiad]);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/blocks/olympiads/view.php'));
} else if ($data = $form->get_data()) {
    unset($data->submitbutton);

//    var_dump($data);die();
    if ($id) {
        $data->id = $id;
        $data->timemodified = time();
        $DB->update_record('olympiads', $data);
    } else {
        $data->timecreated = time();
        $data->timemodified = time();
        $data->createdby = $USER->id;
        $DB->insert_record('olympiads', $data);
    }
    redirect(new moodle_url('/blocks/olympiads/view.php'), get_string('changessaved', 'block_olympiads'));
}

echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();