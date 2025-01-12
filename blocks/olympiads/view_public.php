<?php
require_once('../../config.php');
require_once('lib.php');

block_olympiads_specialization();

$context = context_system::instance();

require_login();

$PAGE->set_url(new moodle_url('/blocks/olympiads/view_public.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('availableolympiads', 'block_olympiads'));
$PAGE->set_heading($SITE->fullname);

// Подключаем CSS для стилизации карточек
$PAGE->requires->css(new moodle_url('/blocks/olympiads/css/style.css'));

// Получаем список всех доступных олимпиад
$olympiads = $DB->get_records('olympiads');

$data = [];
$data['olympiads'] = [];

$fs = get_file_storage(); // Хранилище файлов Moodle

foreach ($olympiads as $olympiad) {
    // Проверяем, есть ли загруженное изображение для олимпиады
    $files = $fs->get_area_files($context->id, 'block_olympiads', 'image', $olympiad->id, 'itemid', false);

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
//        var_dump($imageurl->out()); die;
    } else {
        // Используем заглушку, если изображение отсутствует
        $imageurl = new moodle_url('/blocks/olympiads/bootstrap/icons/unknown.png');
    }

    // Формируем URL для перехода на страницу с деталями олимпиады
    $detailsurl = new moodle_url('/blocks/olympiads/details.php', ['id' => $olympiad->id]);

    $data['olympiads'][] = [
        'name' => format_string($olympiad->name),
        'imageurl' => $imageurl->out(),
        'detailsurl' => $detailsurl->out()
    ];
}

// Если список олимпиад пуст
$data['noavailableolympiads'] = get_string('noavailableolympiads', 'block_olympiads');

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('block_olympiads/olympiads', $data);
echo $OUTPUT->footer();