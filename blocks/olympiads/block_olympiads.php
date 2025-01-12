<?php
defined('MOODLE_INTERNAL') || die();

class block_olympiads extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_olympiads');
    }

    public function get_content() {
        global $USER, $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';

        // Проверяем, есть ли у пользователя право на просмотр олимпиад
        if (has_capability('block/olympiads:view', context_system::instance())) {
            $url = new moodle_url('/blocks/olympiads/view_public.php');
            $this->content->text .= html_writer::link($url, get_string('viewolympiads', 'block_olympiads')) . '<br>';
        }

        // Проверяем, есть ли у пользователя право на управление олимпиадами
        if (has_capability('block/olympiads:manage', context_system::instance())) {
            $url = new moodle_url('/blocks/olympiads/view.php');
            $this->content->text .= html_writer::link($url, get_string('manageolympiads', 'block_olympiads')) . '<br>';
        }

        return $this->content;
    }

}