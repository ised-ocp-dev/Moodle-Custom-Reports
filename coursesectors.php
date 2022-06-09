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
$url = new moodle_url('/local/customreports/coursesectors.php');
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$title = "Custom Reports";
$PAGE->set_title($title);
$PAGE->set_heading($title);



//echo $OUTPUT->header();

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=file.csv");

// -------------
//
//
// FIND SECTOR
//
//
// -------------
$dropdown_values_sector = $DB->get_record_sql("SELECT * FROM {customfield_field} WHERE shortname = 'sector'");
$dropdown_values_sector_INDEXES = (explode(PHP_EOL, json_decode($dropdown_values_sector->configdata)->options));
$dropdown_values_sector_INDEXES_cleaned = [];

foreach($dropdown_values_sector_INDEXES as $dropdown_values_sector_INDEX){
	
	// CLEAN MLANG
	$new_value = $dropdown_values_sector_INDEX;
	$new_value = str_replace("{mlang en}", "",$new_value);
	$new_value = str_replace("{mlang}{mlang fr}", " / ",$new_value);
	$new_value = str_replace("{mlang}", "",$new_value);
	
	array_push($dropdown_values_sector_INDEXES_cleaned, $new_value);
}
//var_dump($dropdown_values_sector_INDEXES_cleaned);



// -------------
//
//
// FIND STATUS
//
//
// -------------
$dropdown_values_status = $DB->get_record_sql("SELECT * FROM {customfield_field} WHERE shortname = 'status'");
$dropdown_values_status_INDEXES = (explode(PHP_EOL, json_decode($dropdown_values_status->configdata)->options));
$dropdown_values_status_INDEXES_cleaned = [];

foreach($dropdown_values_status_INDEXES as $dropdown_values_status_INDEX){
	
	// CLEAN MLANG
	$new_value = $dropdown_values_status_INDEX;
	$new_value = str_replace("{mlang en}", "",$new_value);
	$new_value = str_replace("{mlang}{mlang fr}", " / ",$new_value);
	$new_value = str_replace("{mlang}", "",$new_value);
	
	array_push($dropdown_values_status_INDEXES_cleaned, $new_value);
}
//var_dump($dropdown_values_status_INDEXES_cleaned);



// -------------
//
//
// BUILD REPORT
//
//
// -------------
echo "COURSE NAME, STATUS, SECTOR, COURSE URL, EDIT COURSE\n";

$courses = $DB->get_records_sql("SELECT * FROM {course}");
foreach($courses as $course){
	
	// CLEAN MLANG
	$course_fullname_clean = $course->fullname;
	$course_fullname_clean = str_replace("{mlang en}", "",$course_fullname_clean);
	$course_fullname_clean = str_replace("{mlang}{mlang fr}", " / ",$course_fullname_clean);
	$course_fullname_clean = str_replace("{mlang}", "",$course_fullname_clean);
	
	$course_fields = $DB->get_record_sql("SELECT * FROM {customfield_data} WHERE instanceid = ".$course->id);
	
	$course_selected_dropdown = $course_fields->intvalue;
	
	// SECTOR
	$course_sector_has_value = "NO SECTOR";
	if($dropdown_values_sector_INDEXES_cleaned[$course_fields->intvalue]){
		$course_sector_has_value = $dropdown_values_sector_INDEXES_cleaned[$course_fields->intvalue];
	}
	
	//STATUS
	$course_status_has_value = "NO STATUS";
	if($dropdown_values_status_INDEXES_cleaned[$course_fields->intvalue]){
		$course_status_has_value = $dropdown_values_status_INDEXES_cleaned[$course_fields->intvalue];
	}
	
	echo $course_fullname_clean .",". $course_status_has_value .",". $course_sector_has_value .",". $CFG->wwwroot."/course/view.php?id=".$course->id .",". $CFG->wwwroot."./course/edit.php?id=".$course->id ;	
	echo "\n";
	
}

//$customfields = $DB->get_record_sql("SELECT * FROM {customfield_data} WHERE id = '".intval($_GET['id'])."'", array(1));
				



//echo $OUTPUT->footer();