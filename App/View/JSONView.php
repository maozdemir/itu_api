<?php
namespace ITU_API;
class JSONView {
  public function render($data) {
    echo json_encode($data);
  }
}
?>