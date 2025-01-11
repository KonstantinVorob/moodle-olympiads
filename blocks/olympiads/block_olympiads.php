<?php
defined('MOODLE_INTERNAL') || die();

class block_olympiads extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_olympiads');
    }

    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = get_string('blockcontent', 'block_olympiads');
        $this->content->footer = '';

        return $this->content;
    }
}