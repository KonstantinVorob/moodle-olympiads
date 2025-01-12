<?php

require_once('../../config.php');
require_once('lib.php');

block_olympiads_specialization();

/**
 * Класс для отображения деталей олимпиады.
 */
class olympiad_details_handler {
    private $id;
    private $context;
    private $db;

    public function __construct($id) {
        global $DB;

        $this->id = $id;
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

        $PAGE->set_url(new moodle_url('/blocks/olympiads/details.php', ['id' => $this->id]));
        $PAGE->set_context($this->context);
        $PAGE->set_title(get_string('details', 'block_olympiads'));
        $PAGE->set_heading($SITE->fullname);

        // Меняем заголовок страницы
        block_olympiads_render_header_text();
    }

    /**
     * Получение URL изображения олимпиады.
     *
     * @return moodle_url URL изображения или заглушки.
     */
    private function get_image_url() {
        $fs = get_file_storage();
        $files = $fs->get_area_files($this->context->id, 'block_olympiads', 'image', $this->id, 'itemid', false);

        if (!empty($files)) {
            $file = reset($files);
            return moodle_url::make_pluginfile_url(
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                $file->get_itemid(),
                $file->get_filepath(),
                $file->get_filename()
            );
        }

        // Возвращаем заглушку, если изображение отсутствует
        return new moodle_url('/blocks/olympiads/icons/placeholder.png');
    }

    /**
     * Отображение страницы деталей олимпиады.
     */
    public function display_details() {
        global $OUTPUT;

        // Получаем данные олимпиады
        $olympiad = $this->db->get_record('olympiads', ['id' => $this->id], '*', MUST_EXIST);

        // Подготавливаем данные для передачи в шаблон
        $data = [
            'name' => format_string($olympiad->name),
            'startdate' => date('d.m.Y', $olympiad->startdate),
            'enddate' => date('d.m.Y', $olympiad->enddate),
            'description' => format_text($olympiad->description, FORMAT_HTML),
            'imageurl' => $this->get_image_url()->out(),
            'registerurl' => (new moodle_url('/blocks/olympiads/register.php', ['id' => $this->id]))->out()
        ];

        // Отображаем страницу с использованием шаблона
        echo $OUTPUT->header();
        echo $OUTPUT->render_from_template('block_olympiads/details', $data);
        echo $OUTPUT->footer();
    }
}

// Получаем ID олимпиады из параметров запроса
$id = required_param('id', PARAM_INT);

// Создаём обработчик и отображаем детали олимпиады
$handler = new olympiad_details_handler($id);
$handler->display_details();