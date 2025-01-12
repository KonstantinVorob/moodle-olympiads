<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Класс для блока «Олимпиады».
 */
class block_olympiads extends block_base {

    /**
     * Инициализация блока — задаём заголовок.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_olympiads');
    }

    /**
     * Получение содержимого блока.
     *
     * @return stdClass Содержимое блока.
     */
    public function get_content() {
        global $USER, $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';

        // Добавляем ссылку для абитуриентов, если у пользователя есть право на просмотр олимпиад
        if ($this->can_view_olympiads()) {
            $this->content->text .= $this->get_view_link() . '<br>';
        }

        // Добавляем ссылку для сотрудников приемной комиссии, если у пользователя есть право на управление олимпиадами
        if ($this->can_manage_olympiads()) {
            $this->content->text .= $this->get_manage_link() . '<br>';
        }

        return $this->content;
    }

    /**
     * Проверяет, может ли пользователь просматривать олимпиады.
     *
     * @return bool
     */
    private function can_view_olympiads() {
        return has_capability('block/olympiads:view', context_system::instance());
    }

    /**
     * Проверяет, может ли пользователь управлять олимпиадами.
     *
     * @return bool
     */
    private function can_manage_olympiads() {
        return has_capability('block/olympiads:manage', context_system::instance());
    }

    /**
     * Формирует ссылку для просмотра списка олимпиад.
     *
     * @return string HTML-ссылка.
     */
    private function get_view_link() {
        $url = new moodle_url('/blocks/olympiads/view_public.php');
        return html_writer::link($url, get_string('viewolympiads', 'block_olympiads'));
    }

    /**
     * Формирует ссылку для управления олимпиадами.
     *
     * @return string HTML-ссылка.
     */
    private function get_manage_link() {
        $url = new moodle_url('/blocks/olympiads/view.php');
        return html_writer::link($url, get_string('manageolympiads', 'block_olympiads'));
    }
}