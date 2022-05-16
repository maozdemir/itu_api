<?php
require_once "RestfulFn.php";
require_once "db.php";

use ITU_API\HTTPHelper as HTTPHelper;

$request_method = $_SERVER['REQUEST_METHOD'];
$status = 200;
$action = $_GET['action'];
$value = $_GET['value'];

$HTTPHelper = new HTTPHelper();


if ($request_method != 'GET') {
    $HTTPHelper->header_set(403);
    exit;
}
if (!isset($action)) {
    $HTTPHelper->header_set(400);
    exit;
}
if ($value == 0) {
    $HTTPHelper->header_set(400);
    exit;
}

if ($action == 'GetCourseQuota' && isset($value)) {
    echo json_encode(DatabaseMgr::course_quota_provider(($value)));
} elseif ($action == "GetBuilding" && isset($value)) {
    $return = DatabaseMgr::get_building($value);
    $return['location'] = DatabaseMgr::get_location_name(DatabaseMgr::get_building($value)['location'])['name'];
    echo json_encode($return);
} else {
    $HTTPHelper->header_set(400);
    exit;
}
