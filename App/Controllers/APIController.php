<?php
namespace ITU_API;

include "View/JSONView.php";
include "Controllers/DatabaseController.php";
class APIController {
  private $model;
  private $view;
  private $request_method;
  private $action;
  private $value;
  
  public function __construct($request_method, $action, $value) {
    $this->request_method = $request_method;
    $this->action = $action;
    $this->value = $value;
    $this->model = new DatabaseModel();
    $this->view = new JSONView();
  }

  public function displayData() {
    if ($this->request_method != 'GET' || !isset($this->action) || !isset($this->value) || $this->action == '' || $this->value == '' || $this->value == 0) {
      header('HTTP/1.0 403 Forbidden');
      exit;
    }

    if ($this->action == 'GetCourseQuota') {
      $data = $this->model->course_quota_provider($this->value);
      $this->view->render($data);
    } elseif ($this->action == "GetBuilding") {
      $data = $this->model->get_building($this->value);
      $data['location'] = $this->model->get_location_name($this->model->get_building($this->value)['location'])['name'];
      $this->view->render($data);
    } else {
      header('HTTP/1.0 400 Bad Request');
      exit;
    }
  }
}

?>