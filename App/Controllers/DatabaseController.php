<?php
namespace ITU_API;
include "Model/DatabaseModel.php";
class DatabaseController {
    private $model;

    public function __construct() {
        $this->model = new DatabaseModel();
    }

    public function getCourseQuota($value) {
        return $this->model->course_quota_provider($value);
    }

    public function getBuilding($value) {
        $return = $this->model->get_building($value);
        $return['location'] = $this->model->get_location_name($this->model->get_building($value)['location'])['name'];
        return $return;
    }
}
?>