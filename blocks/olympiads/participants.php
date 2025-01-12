<?php

require_once('../../config.php');
require_once('lib.php');

block_olympiads_specialization();

/**
 * Класс для отображения списка участников олимпиады
 */
class participants_handler {
    private $id;
    private $context;
    private $db;

    public function __construct($id) {
        global $DB;

        $this->id = $id;
        $this->context = context_system::instance();
        $this->db = $DB;

        $this->require_login_and_capability();
        $this->setup_page();
    }

    /**
     * Проверка логина и прав пользователя
     */
    private function require_login_and_capability() {
        require_login();
        require_capability('block/olympiads:manage', $this->context);
    }

    /**
     * Настройка страницы Moodle
     */
    private function setup_page() {
        global $PAGE, $SITE;

        $PAGE->set_url(new moodle_url('/blocks/olympiads/participants.php', ['id' => $this->id]));
        $PAGE->set_context($this->context);
        $PAGE->set_title(get_string('participantslist', 'block_olympiads'));
        $PAGE->set_heading($SITE->fullname);

        // Меняем заголовок страницы
        block_olympiads_render_header_text();
    }

    /**
     * Проверка существования олимпиады
     */
    private function check_olympiad_exists() {
        if (!$this->db->record_exists('olympiads', ['id' => $this->id])) {
            print_error('invalidrecord', 'error');
        }
    }

    /**
     * Получение списка участников олимпиады
     */
    private function get_participants() {
        $sql = "SELECT p.id, u.firstname, u.lastname, u.email, p.timecreated
                FROM {olympiads_participants} p
                JOIN {user} u ON p.userid = u.id
                WHERE p.olympiadid = :olympiadid
                ORDER BY p.timecreated ASC";

        return $this->db->get_records_sql($sql, ['olympiadid' => $this->id]);
    }

    /**
     * Вывод таблицы участников
     */
    public function display_participants() {
        global $OUTPUT;

        $this->check_olympiad_exists();
        $participants = $this->get_participants();

        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('participantslist', 'block_olympiads'));

        if ($participants) {
            $table = new html_table();
            $table->head = [
                get_string('fullname'),
                get_string('email'),
                get_string('registrationdate', 'block_olympiads')
            ];

            foreach ($participants as $participant) {
                $table->data[] = [
                    fullname($participant),
                    $participant->email,
                    date('d.m.Y', $participant->timecreated)
                ];
            }

            echo html_writer::table($table);
        } else {
            echo html_writer::div(get_string('noparticipants', 'block_olympiads'), 'alert alert-info');
        }

        echo $OUTPUT->footer();
    }
}

// Получаем ID олимпиады из параметров запроса
$id = required_param('id', PARAM_INT);

// Создаём обработчик участников и отображаем список участников
$handler = new participants_handler($id);
$handler->display_participants();