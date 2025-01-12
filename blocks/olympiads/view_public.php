<?php

require_once('../../config.php');
require_once('lib.php');

block_olympiads_specialization();

/**
 * Класс для отображения списка доступных олимпиад.
 */
class olympiads_view_public_handler {
    private $context;
    private $db;

    public function __construct() {
        global $DB;

        $this->context = context_system::instance();
        $this->db = $DB;

        $this->require_login();
        $this->setup_page();
    }

    /**
     * Проверка логина пользователя.
     */
    private function require_login() {
        require_login();
    }

    /**
     * Настройка страницы Moodle.
     */
    private function setup_page() {
        global $PAGE, $SITE;

        $PAGE->set_url(new moodle_url('/blocks/olympiads/view_public.php'));
        $PAGE->set_context($this->context);
        $PAGE->set_title(get_string('availableolympiads', 'block_olympiads'));
        $PAGE->set_heading($SITE->fullname);

        // Подключаем CSS для стилизации
        $PAGE->requires->css(new moodle_url('/blocks/olympiads/css/style.css'));

        // Меняем заголовок страницы
        block_olympiads_render_header_text();
    }

    /**
     * Получение списка доступных олимпиад.
     *
     * @return array Список олимпиад.
     */
    private function get_olympiads() {
        $olympiads = $this->db->get_records('olympiads');
        $data = [];
        $fs = get_file_storage();

        foreach ($olympiads as $olympiad) {
            // Проверяем наличие изображения для олимпиады
            $files = $fs->get_area_files(
                $this->context->id,
                'block_olympiads',
                'image',
                $olympiad->id,
                'itemid',
                false
            );

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
                $imageurl = new moodle_url('/blocks/olympiads/bootstrap/icons/unknown.png');
            }

            // Формируем URL для страницы с деталями олимпиады
            $detailsurl = new moodle_url('/blocks/olympiads/details.php', ['id' => $olympiad->id]);

            $data[] = [
                'name' => format_string($olympiad->name),
                'imageurl' => $imageurl->out(),
                'detailsurl' => $detailsurl->out()
            ];
        }

        return $data;
    }

    /**
     * Отображение страницы с олимпиадами.
     */
    public function display_page() {
        global $OUTPUT;

        $data = [];
        $data['olympiads'] = $this->get_olympiads();
        $data['noavailableolympiads'] = get_string('noavailableolympiads', 'block_olympiads');

        echo $OUTPUT->header();
        echo $OUTPUT->render_from_template('block_olympiads/olympiads', $data);
        echo $OUTPUT->footer();
    }
}

// Создаём обработчик и отображаем страницу
$handler = new olympiads_view_public_handler();
$handler->display_page();