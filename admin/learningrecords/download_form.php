<?php //$Id$

require_once($CFG->libdir.'/formslib.php');

class download_form extends moodleform {

    function definition() {
        $mform =& $this->_form;
        //TODO use lang string for button text
        $mform->addElement('submit', 'downloadbutton', 'Export');

    }
}

