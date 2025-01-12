<?php

require_once('../../config.php');
require_once('lib.php');

block_olympiads_specialization();

class register_handler {
    private $id;
    private $context;
    private $user;
    private $db;

    public function __construct($id) {
        global $USER, $DB;

        $this->id = $id;
        $this->context = context_system::instance();
        $this->user = $USER;
        $this->db = $DB;

        $this->require_login();
        $this->setup_page();
    }

    // Метод для выполнения регистрации
    public function register() {
        // Проверяем, существует ли олимпиада
        if (!$this->db->record_exists('olympiads', ['id' => $this->id])) {
            print_error('invalidrecord', 'error');
        }

        // Проверяем, уже записан ли пользователь на эту олимпиаду
        if ($this->db->record_exists('olympiads_participants', ['olympiadid' => $this->id, 'userid' => $this->user->id])) {
            redirect(new moodle_url('/blocks/olympiads/view_public.php'), get_string('alreadyregistered', 'block_olympiads'));
        }

        // Записываем пользователя на олимпиаду
        $record = new stdClass();
        $record->olympiadid = $this->id;
        $record->userid = $this->user->id;
        $record->timecreated = time();
        $this->db->insert_record('olympiads_participants', $record);

        // Перенаправляем с уведомлением об успешной записи
        redirect(new moodle_url('/blocks/olympiads/view_public.php'), get_string('registrationsuccess', 'block_olympiads'));
    }

    // Метод для настройки страницы
    private function setup_page() {
        global $PAGE, $SITE;

        $PAGE->set_url(new moodle_url('/blocks/olympiads/register.php', ['id' => $this->id]));
        $PAGE->set_context($this->context);
        $PAGE->set_title(get_string('registerolympiad', 'block_olympiads'));
        $PAGE->set_heading($SITE->fullname);
    }

    // Метод для обязательной проверки логина
    private function require_login() {
        require_login();
    }
}

// Получаем ID олимпиады из параметров запроса
$id = required_param('id', PARAM_INT);

// Создаём обработчик регистрации и выполняем регистрацию
$handler = new register_handler($id);
$handler->register();