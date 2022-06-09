<?php


// globalwidgets Dashboard
// ---------

require_once("../../config.php");
global $DB;

// Security.
$context = context_system::instance();
require_login();
require_capability('moodle/site:config', $context);

// Page boilerplate stuff.
$url = new moodle_url('/local/customreports/index.php');
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$title = "ISED Custom Reports";
$PAGE->set_title($title);
$PAGE->set_heading($title);


echo $OUTPUT->header();

echo "<h2>ISED Custom Reports:</h2>";

echo "<a target='_blank' href='".$CFG->wwwroot."/local/customreports/coursesectors.php'>Course Status & Sectors</a>";


echo $OUTPUT->footer();