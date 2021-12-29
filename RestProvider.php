<?php
require_once "RestfulFn.php";
require_once "db.php";

$request_method = $_SERVER['REQUEST_METHOD'];
$status = 200;
$action = $_GET['action'];
$value = $_GET['value'];

if ($request_method != 'GET') {
    header_set(403);
    exit;
}
if (!isset($action)) {
    header_set(400);
    exit;
}
if ($value == 0) {
    header_set(400);
    exit;
}

if ($action == 'GetCourseQuota' && isset($value)) {
    echo json_encode(DatabaseMgr::course_quota_provider(($value)));
} elseif ($action == "GetBuilding" && isset($value)) {
    $return = DatabaseMgr::get_building($value);
    $return['location'] = DatabaseMgr::get_location_name(DatabaseMgr::get_building($value)['location'])['name'];
    echo json_encode($return);
} else {
    header_set(400);
    exit;
}
