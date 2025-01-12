<?php

require_once('../../config.php');
require_once('form/olympiad_form.php');
require_once('lib.php');

block_olympiads_specialization();


/**
 * Класс для обработки добавления и редактирования олимпиады.
 */
class olympiad_add_edit_handler {
    private $id;
    private $context;
    private $db;
    private $user;

    public function __construct($id) {
        global $DB, $USER;

        $this->id = $id;
        $this->context = context_system::instance();
        $this->db = $DB;
        $this->user = $USER;

        $this->require_login_and_capability();
        $this->setup_page();
    }

    /**
     * Проверка логина и прав пользователя.
     */
    private function require_login_and_capability() {
        require_login();
        require_capability('block/olympiads:manage', $this->context);
    }

    /**
     * Настройка страницы Moodle.
     */
    private function setup_page() {
        global $PAGE, $SITE;

        $PAGE->set_url(new moodle_url('/blocks/olympiads/add_edit.php', ['id' => $this->id]));
        $PAGE->set_context($this->context);
        $PAGE->set_title($this->id ? get_string('editolympiad', 'block_olympiads') : get_string('addolympiad', 'block_olympiads'));
        $PAGE->set_heading($SITE->fullname);

        // Меняем заголовок страницы
        block_olympiads_render_header_text();
    }

    /**
     * Подготовка данных олимпиады для формы.
     *
     * @return stdClass|null Данные олимпиады или null, если создаётся новая запись.
     */
    private function prepare_data() {
        if ($this->id) {
            $olympiad = $this->db->get_record('olympiads', ['id' => $this->id], '*', MUST_EXIST);

            // Подготовка описания для редактора
            $olympiad->description = ['text' => $olympiad->description, 'format' => FORMAT_HTML];

            // Подготовка черновой области для изображения
            $draftitemid = file_get_submitted_draft_itemid('image');
            file_prepare_draft_area($draftitemid, $this->context->id, 'block_olympiads', 'image', $this->id, [
                'subdirs' => false,
                'maxfiles' => 1,
                'accepted_types' => ['.png', '.jpg', '.jpeg']
            ]);
            $olympiad->image = $draftitemid;

            return $olympiad;
        }

        return null;
    }

    /**
     * Обработка отправленной формы.
     *
     * @param stdClass $data Данные, отправленные пользователем.
     */
    public function process_form($data) {
        $draftitemid = file_get_submitted_draft_itemid('image');
        $data->description = strip_tags($data->description['text']);
        $data->timemodified = time();

        if (!empty($data->id)) {
            $this->db->update_record('olympiads', $data);
        } else {
            $data->timecreated = time();
            $data->createdby = $this->user->id;
            $data->id = $this->db->insert_record('olympiads', $data);
        }

        // Сохранение изображения в файловую систему Moodle
        file_save_draft_area_files($draftitemid, $this->context->id, 'block_olympiads', 'image', $data->id, [
            'subdirs' => false,
            'maxfiles' => 1,
            'accepted_types' => ['.png', '.jpg', '.jpeg']
        ]);

        redirect(new moodle_url('/blocks/olympiads/view.php'), get_string('changessaved', 'block_olympiads'));
    }

    /**
     * Отображение формы.
     */
    public function display_form() {
        global $OUTPUT;

        $data = $this->prepare_data();
        $form = new olympiad_form(null, ['olympiad' => $data]);

        if ($form->is_cancelled()) {
            redirect(new moodle_url('/blocks/olympiads/view.php'));
        } else if ($submitted_data = $form->get_data()) {
            $this->process_form($submitted_data);
        }

        echo $OUTPUT->header();
        $form->display();
        echo $OUTPUT->footer();
    }
}

// Получаем ID олимпиады из параметров запроса
$id = optional_param('id', 0, PARAM_INT);

// Создаём обработчик добавления/редактирования и отображаем форму
$handler = new olympiad_add_edit_handler($id);
$handler->display_form();