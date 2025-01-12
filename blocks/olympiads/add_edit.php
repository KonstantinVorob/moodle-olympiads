<?php
require_once('../../config.php');
require_once('form/olympiad_form.php');
require_once('lib.php');

block_olympiads_specialization();

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

    // Подготовка файла изображения для редактирования
    $draftitemid = file_get_submitted_draft_itemid('image');
    file_prepare_draft_area($draftitemid, $context->id, 'block_olympiads', 'image', $id, [
        'subdirs' => false,
        'maxfiles' => 1,
        'accepted_types' => ['.png', '.jpg', '.jpeg']
    ]);
    $olympiad->image = $draftitemid;
} else {
    $olympiad = null;
}

$form = new olympiad_form(null, ['olympiad' => $olympiad]);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/blocks/olympiads/view.php'));
} else if ($data = $form->get_data()) {
    unset($data->submitbutton);

    $draftitemid = file_get_submitted_draft_itemid('image');

    if ($id) {
        $data->id = $id;
        $data->timemodified = time();
        $DB->update_record('olympiads', $data);
    } else {
        $data->timecreated = time();
        $data->timemodified = time();
        $data->createdby = $USER->id;
        $data->id = $DB->insert_record('olympiads', $data);
    }

    // Сохранение файла изображения в файловой системе Moodle
    file_save_draft_area_files($draftitemid, $context->id, 'block_olympiads', 'image', $data->id, [
        'subdirs' => false,
        'maxfiles' => 1,
        'accepted_types' => ['.png', '.jpg', '.jpeg']
    ]);

    redirect(new moodle_url('/blocks/olympiads/view.php'), get_string('changessaved', 'block_olympiads'));
}

echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();