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



// if($_GET['debug'] == "true"){
	// echo $OUTPUT->header();
// }else{
	// header("Content-Type: text/csv");
	// header("Content-Disposition: attachment; filename=file.csv");
// }

echo $OUTPUT->header();

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

$field_id_sector = $dropdown_values_sector->id;

foreach($dropdown_values_sector_INDEXES as $dropdown_values_sector_INDEX){
	
	// CLEAN MLANG
	$new_value = $dropdown_values_sector_INDEX;
	$new_value = str_replace("{mlang en}", "",$new_value);
	$new_value = str_replace("{mlang}{mlang fr}", " / ",$new_value);
	$new_value = str_replace("{mlang}", "",$new_value);
	
	array_push($dropdown_values_sector_INDEXES_cleaned, $new_value);
}
if($_GET['debug'] == "true"){
	var_dump("DROP DOWN VALUES FOR SECTOR");
	var_dump($dropdown_values_sector_INDEXES_cleaned);
}



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

$field_id_status = $dropdown_values_status->id;

foreach($dropdown_values_status_INDEXES as $dropdown_values_status_INDEX){
	
	// CLEAN MLANG
	$new_value = $dropdown_values_status_INDEX;
	$new_value = str_replace("{mlang en}", "",$new_value);
	$new_value = str_replace("{mlang}{mlang fr}", " / ",$new_value);
	$new_value = str_replace("{mlang}", "",$new_value);
	
	array_push($dropdown_values_status_INDEXES_cleaned, $new_value);
}
if($_GET['debug'] == "true"){
	var_dump("DROP DOWN VALUES FOR STATUS");
	var_dump($dropdown_values_status_INDEXES_cleaned);
}


if($_GET['debug'] == "true"){
	echo "<hr />";echo "<hr />";echo "<hr />";
	var_dump($field_id_status);
	var_dump($field_id_sector);
	echo "<hr />";echo "<hr />";echo "<hr />";
}


// -------------
//
//
// BUILD REPORT
//
//
// -------------


echo "<table border='1'>";
echo "<tr><th>COURSE NAME</th><th>STATUS</th><th>SECTOR</th><th>COURSE URL</th><th>EDIT COURSE</th></tr>";

$courses = $DB->get_records_sql("SELECT * FROM {course}");
foreach($courses as $course){
	
	// CLEAN MLANG
	$course_fullname_clean = $course->fullname;
	$course_fullname_clean = str_replace("{mlang en}", "",$course_fullname_clean);
	$course_fullname_clean = str_replace("{mlang}{mlang fr}", " / ",$course_fullname_clean);
	$course_fullname_clean = str_replace("{mlang}", "",$course_fullname_clean);
	
	$course_field_status = $DB->get_record_sql("SELECT * FROM {customfield_data} WHERE fieldid = ".$field_id_status." AND instanceid = ".$course->id);
	$course_field_sector = $DB->get_record_sql("SELECT * FROM {customfield_data} WHERE fieldid = ".$field_id_sector." AND instanceid = ".$course->id);
	
	
	//STATUS
	$course_status_has_value = "NO STATUS";
	if($dropdown_values_status_INDEXES_cleaned[$course_field_status->intvalue]  && $course_field_status->fieldid == $field_id_status){
		$course_status_has_value = $dropdown_values_status_INDEXES_cleaned[$course_field_status->intvalue];
	}
	
	// SECTOR
	$course_sector_has_value = "NO SECTOR";
	if($dropdown_values_sector_INDEXES_cleaned[$course_field_sector->intvalue] && $course_field_sector->fieldid == $field_id_sector){
		$course_sector_has_value = $dropdown_values_sector_INDEXES_cleaned[$course_field_sector->intvalue];
	}
	
	echo "<tr>";
	echo "<td>".$course_fullname_clean ."</td><td>". $course_status_has_value ."</td><td>". $course_sector_has_value ."</td><td><a href='". $CFG->wwwroot."/course/view.php?id=".$course->id ."'>View Course</a></td><td><a href='". $CFG->wwwroot."/course/edit.php?id=".$course->id."'>Edit Course Settings</a></td>" ;	
	echo "</tr>";

	if($_GET['debug'] == "true"){
		var_dump($course_field_status);
		var_dump($course_field_sector);
		echo "<hr />";
	}
	
}

echo "</table>";

//$customfields = $DB->get_record_sql("SELECT * FROM {customfield_data} WHERE id = '".intval($_GET['id'])."'", array(1));
				


// if($_GET['debug'] == "true"){
	// echo $OUTPUT->footer();
// }

echo $OUTPUT->footer();