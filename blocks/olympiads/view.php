<?php

require_once('../../config.php');
require_once('lib.php');

block_olympiads_specialization();

/**
 * Класс для отображения списка олимпиад.
 */
class olympiads_view_handler {
    private $context;
    private $db;

    public function __construct() {
        global $DB;

        $this->context = context_system::instance();
        $this->db = $DB;

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

        $PAGE->set_url(new moodle_url('/blocks/olympiads/view.php'));
        $PAGE->set_context($this->context);
        $PAGE->set_title(get_string('viewolympiads', 'block_olympiads'));
        $PAGE->set_heading($SITE->fullname);

        // Меняем заголовок страницы
        block_olympiads_render_header_text();
    }

    /**
     * Получение списка олимпиад из базы данных.
     *
     * @return array Список олимпиад.
     */
    private function get_olympiads() {
        return $this->db->get_records('olympiads');
    }

    /**
     * Подсчёт количества участников для каждой олимпиады.
     *
     * @return array Массив с количеством участников для каждой олимпиады.
     */
    private function get_registration_counts() {
        $registrations = $this->db->get_records_sql('
            SELECT olympiadid, COUNT(id) AS count
            FROM {olympiads_participants}
            GROUP BY olympiadid
        ');

        $counts = [];
        foreach ($registrations as $registration) {
            $counts[$registration->olympiadid] = $registration->count;
        }
        return $counts;
    }

    /**
     * Отображение страницы со списком олимпиад.
     */
    public function display_page() {
        global $OUTPUT;

        $olympiads = $this->get_olympiads();
        $registration_counts = $this->get_registration_counts();

        echo $OUTPUT->header();

        // Кнопка для создания новой олимпиады
        $addurl = new moodle_url('/blocks/olympiads/add_edit.php');
        echo html_writer::link($addurl, get_string('addolympiad', 'block_olympiads'), ['class' => 'btn btn-success mb-3']);

        echo $OUTPUT->heading(get_string('olympiadslist', 'block_olympiads'));

        if ($olympiads) {
            $table = new html_table();
            $table->head = [
                get_string('name', 'block_olympiads'),
                get_string('startdate', 'block_olympiads'),
                get_string('enddate', 'block_olympiads'),
                get_string('description', 'block_olympiads'),
                get_string('registrations', 'block_olympiads'),
                get_string('actions', 'block_olympiads')
            ];

            foreach ($olympiads as $olympiad) {
                $editurl = new moodle_url('/blocks/olympiads/add_edit.php', ['id' => $olympiad->id]);
                $deleteurl = new moodle_url('/blocks/olympiads/delete.php', ['id' => $olympiad->id]);
                $participantsurl = new moodle_url('/blocks/olympiads/participants.php', ['id' => $olympiad->id]);

                $count = isset($registration_counts[$olympiad->id]) ? $registration_counts[$olympiad->id] : 0;

                // Формируем ссылки с иконками для кнопок действий
                $editicon = html_writer::empty_tag('img', [
                    'src' => new moodle_url('/blocks/olympiads/bootstrap/icons/pencil.svg'),
                    'alt' => get_string('edit', 'block_olympiads'),
                    'class' => 'icon'
                ]);

                $deleteicon = html_writer::empty_tag('img', [
                    'src' => new moodle_url('/blocks/olympiads/bootstrap/icons/trash.svg'),
                    'alt' => get_string('delete', 'block_olympiads'),
                    'class' => 'icon'
                ]);

                $table->data[] = [
                    format_string($olympiad->name),
                    date('d.m.Y', $olympiad->startdate),
                    date('d.m.Y', $olympiad->enddate),
                    shorten_text(format_text($olympiad->description, FORMAT_HTML), 100),
                    $count,
                    html_writer::link($editurl, $editicon, ['class' => 'btn']) . ' ' .
                    html_writer::link($deleteurl, $deleteicon, ['class' => 'btn']) . ' ' .
                    html_writer::link($participantsurl, get_string('viewparticipants', 'block_olympiads'), ['class' => 'btn btn-info'])
                ];
            }

            echo html_writer::table($table);
        } else {
            echo html_writer::div(get_string('noolympiads', 'block_olympiads'), 'alert alert-info');
        }

        echo $OUTPUT->footer();
    }
}

// Создаём обработчик и отображаем страницу
$handler = new olympiads_view_handler();
$handler->display_page();