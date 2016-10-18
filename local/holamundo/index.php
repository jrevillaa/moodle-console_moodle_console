
<?php

require_once(/var/www/html/Atypax_console"/config.php");

global $DB, $CFG, $PAGE, $OUTPUT;
require_once($CFG->libdir."/adminlib.php");

require_login();
require_capability("moodle/site:config", context_system::instance());
admin_externalpage_setup("generateDummy", "", null);
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_heading($SITE->fullname);
$main_url = new moodle_url("/local/holamundo/index.php");
$PAGE->set_url($main_url);
$title = get_string("titlesite","local_holamundo");
$PAGE->set_title($title);
$PAGE->set_heading($title);
print $OUTPUT->header();

echo html_writer::tag("h2","Creando Cursos..");


print $OUTPUT->footer();