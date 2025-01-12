<?php
require_once($CFG->libdir . '/formslib.php');

class olympiad_form extends moodleform {
    public function definition() {
        $mform = $this->_form;
        $olympiad = $this->_customdata['olympiad'];

        // Скрытое поле для редактирования
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // Поле для ввода названия олимпиады
        $mform->addElement('text', 'name', get_string('name', 'block_olympiads'));
        $mform->setType('name', PARAM_NOTAGS);
        $mform->addRule('name', null, 'required', null, 'client');

        // Поле для описания олимпиады
        $mform->addElement('editor', 'description', get_string('description', 'block_olympiads'), null, ['maxfiles' => 0]);
        $mform->setType('description', PARAM_RAW);
        $mform->addRule('description', get_string('required'), 'required', null, 'client');

        // Поле для загрузки изображения
        $mform->addElement('filepicker', 'image', get_string('image', 'block_olympiads'), null, [
            'accepted_types' => ['.png', '.jpg', '.jpeg']
        ]);
        $mform->addRule('image', null, 'optional', null, 'client');

        // Поля для ввода дат
        $mform->addElement('date_selector', 'startdate', get_string('startdate', 'block_olympiads'));
        $mform->addElement('date_selector', 'enddate', get_string('enddate', 'block_olympiads'));

        // Кнопки "Сохранить" и "Отмена"
        $this->add_action_buttons();

        // Если это редактирование, заполняем форму существующими данными
        if ($olympiad) {
            $this->set_data($olympiad);
        }
    }
}