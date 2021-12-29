<?php
include "db.php";
include "newfn.php";

$url = "https://www.sis.itu.edu.tr/TR/ogrenci/ders-programi/ders-programi.php?seviye=LS&derskodu=MAT";
$codes_url = "https://www.sis.itu.edu.tr/TR/ogrenci/ders-programi/ders-programi.php?seviye=LS";

$htmlc = curl_q($codes_url);
foreach (fetch_codes($htmlc) as $code) {
    $url = "https://www.sis.itu.edu.tr/TR/ogrenci/ders-programi/ders-programi.php?seviye=LS&derskodu=$code";
    $html = curl_q($url);
    foreach (courses($html) as $course) {
        if (DatabaseMgr::add_course($course)) echo "Added course";
    }
}
