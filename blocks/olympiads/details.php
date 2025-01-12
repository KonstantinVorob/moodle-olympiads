<?php
require_once('../../config.php');
require_once('lib.php');

block_olympiads_specialization();

$id = required_param('id', PARAM_INT); // ID олимпиады
$context = context_system::instance();

require_login();

$PAGE->set_url(new moodle_url('/blocks/olympiads/details.php', ['id' => $id]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('details', 'block_olympiads'));
$PAGE->set_heading($SITE->fullname);

// Получаем данные олимпиады
$olympiad = $DB->get_record('olympiads', ['id' => $id], '*', MUST_EXIST);

// Проверяем, есть ли загруженное изображение
$fs = get_file_storage();
$files = $fs->get_area_files($context->id, 'block_olympiads', 'image', $id, 'itemid', false);

if (!empty($files)) {
    $file = reset($files);
    $imageurl = moodle_url::make_pluginfile_url(
        $file->get_contextid(),
        $file->get_component(),
        $file->get_filearea(),
        $file->get_itemid(),
        $file->get_filepath(),
        $file->get_filename()
    );
} else {
    // Используем заглушку, если изображение отсутствует
    $imageurl = new moodle_url('/blocks/olympiads/icons/placeholder.png');
}

// Подготавливаем данные для передачи в шаблон
$data = [

    'name' => format_string($olympiad->name),
    'startdate' => date('d.m.Y', $olympiad->startdate),
    'enddate' => date('d.m.Y', $olympiad->enddate),
    'description' => format_text($olympiad->description, FORMAT_HTML),
    'imageurl' => $imageurl->out(),
    'registerurl' => new moodle_url('/blocks/olympiads/register.php', ['id' => $id])

];

// Отображаем страницу с использованием шаблона
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('block_olympiads/details', $data);
echo $OUTPUT->footer();