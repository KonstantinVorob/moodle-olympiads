<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Обработка запросов на вывод файлов из плагина block_olympiads.
 *
 * @param stdClass $course Курс (не используется)
 * @param stdClass $cm Элемент курса (не используется)
 * @param context $context Контекст, из которого запрашивается файл
 * @param string $filearea Область файлов (в нашем случае 'image')
 * @param array $args Дополнительные аргументы (ID олимпиады и путь к файлу)
 * @param bool $forcedownload Флаг принудительного скачивания файла (не используется)
 * @param array $options Дополнительные параметры (не используются)
 * @return void
 */
function block_olympiads_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    global $USER;

    if ($context->contextlevel != CONTEXT_SYSTEM) {
        return false;
    }

    if ($filearea !== 'image') {
        return false;
    }

    $itemid = array_shift($args);
    $filepath = '/';
    $filename = array_pop($args);

    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'block_olympiads', 'image', $itemid, $filepath, $filename);

    if (!$file || $file->is_directory()) {
        return false;
    }

    send_stored_file($file, 0, 0, $forcedownload, $options);
}

/**
 * Специализация блока — вызывается перед рендерингом страницы.
 */
function block_olympiads_specialization() {
    global $PAGE, $OUTPUT;

    // Подключение CSS
    $PAGE->requires->css(new moodle_url('/blocks/olympiads/bootstrap/css/bootstrap.min.css'));
    $PAGE->requires->css(new moodle_url('/blocks/olympiads/css/styles.css'));


    // Подключение JS
    $PAGE->requires->js(new moodle_url('/blocks/olympiads/bootstrap/js/jquery.min.js'), true);
    $PAGE->requires->js(new moodle_url('/blocks/olympiads/bootstrap/js/bootstrap.bundle.min.js'), true);
}