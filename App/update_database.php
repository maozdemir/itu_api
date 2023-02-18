<?php
require_once "db.php";
require_once "newfn.php";
require_once "HTTPHelper.php";

use ITU_API\HTTPHelper as HTTPHelper;

$HTTPHelper = new HTTPHelper();

$url = "https://www.sis.itu.edu.tr/TR/ogrenci/ders-programi/ders-programi.php?seviye=LS&derskodu=MAT";
$codes_url = "https://www.sis.itu.edu.tr/TR/ogrenci/ders-programi/ders-programi.php?seviye=LS";

$htmlc = $HTTPHelper->curl_q($codes_url);
foreach (fetch_codes($htmlc) as $code) {
    $url = "https://www.sis.itu.edu.tr/TR/ogrenci/ders-programi/ders-programi.php?seviye=LS&derskodu=$code";
    $html = $HTTPHelper->curl_q($url);
    foreach (courses($html) as $course) {
        if (DatabaseMgr::add_course($course)) echo "Added course";
    }
}
