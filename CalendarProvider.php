<?php
include "db.php";
error_reporting(E_ERROR | E_PARSE);
$ids = $_GET['ids'];
$id_array = explode(',', $ids);

$data = array();

foreach ($id_array as $id) {
    $course_data = DatabaseMgr::get_course($id);
    $building_array = explode(',', $course_data["building"]);
    $class_epoch_array = explode(',', $course_data["class_epoch"]);
    $class_array = explode(',', $course_data["class"]);
    $hours = array();
    if (count($class_epoch_array) > 2) {
        $class_i = 0;
        while ($class_i < count($class_epoch_array)) {
            $hours[] = $class_epoch_array[$class_i] . "," . $class_epoch_array[$class_i + 1];
            $class_i = $class_i + 2;
        }
    } else {
        $hours[] = $class_epoch_array[0] . "," . $class_epoch_array[1];
    }
    if (is_array($class_array)) {
        $location = array();
        if (count($class_array) > 1) {
            foreach ($class_array as $i => $class) {
                $location[] = $building_array[$i] . " " . $class;
            }
        } else {
            $location[] = $course_data["building"] . " " . $course_data["class"];
        }
    } else {
        $location[] = $course_data["building"] . " " . $course_data["class"];
    }
    $course_array = array(
        "course_name" => $course_data['course_name'],
        "location" => $location,
        "hours" => $hours,
    );
    $data[] = $course_array;
}

echo json_encode($data);
