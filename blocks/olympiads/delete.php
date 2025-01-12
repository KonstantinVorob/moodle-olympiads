<?php

require_once('../../config.php');
require_once('lib.php');

block_olympiads_specialization();

/**
 * Класс для удаления олимпиады.
 */
class olympiad_delete_handler {
    private $id;
    private $context;
    private $db;

    public function __construct($id) {
        global $DB;

        $this->id = $id;
        $this->context = context_system::instance();
        $this->db = $DB;

        $this->require_login_and_capability();
    }

    /**
     * Проверка логина и прав пользователя.
     */
    private function require_login_and_capability() {
        require_login();
        require_capability('block/olympiads:manage', $this->context);
    }

    /**
     * Проверка существования олимпиады.
     */
    private function check_olympiad_exists() {
        if (!$this->db->record_exists('olympiads', ['id' => $this->id])) {
            print_error('invalidrecord', 'error');
        }
    }

    /**
     * Удаление олимпиады.
     */
    public function delete_olympiad() {
        $this->check_olympiad_exists();

        // Удаляем олимпиаду
        $this->db->delete_records('olympiads', ['id' => $this->id]);

        // Перенаправляем обратно на список олимпиад с уведомлением
        redirect(new moodle_url('/blocks/olympiads/view.php'), get_string('recorddeleted', 'block_olympiads'));
    }
}

// Получаем ID олимпиады из параметров запроса
$id = required_param('id', PARAM_INT);

// Создаём обработчик удаления и выполняем удаление
$handler = new olympiad_delete_handler($id);
$handler->delete_olympiad();