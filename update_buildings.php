<?php
include "db.php";
include "newfn.php";

$bld_url_tr = "https://www.sis.itu.edu.tr/TR/obs-hakkinda/bina-kodlari.php";
$bld_url_en = "https://www.sis.itu.edu.tr/EN/about-sis/building-codes.php";


$bldg_tr_html = curl_q($bld_url_tr, false);
$bldg_en_html = curl_q($bld_url_en);
$bldg_tr_html = html_entity_decode($bldg_tr_html);

preg_match("#<div class=\"content-area\">(.*?)<\/div>#si", $bldg_tr_html, $contents_tr);
$contents_tr_actual = trim(preg_replace('/\t+/', '', str_replace(PHP_EOL, '', $contents_tr[1])));
preg_match_all("#<td><strong>(.*?)</strong></td><td>(.*?)</td>#si", $contents_tr_actual, $bldg_data);

foreach ($bldg_data[1] as $key => $bldg_code) {
    $bldg_data_actual = $bldg_data[2][$key];
    preg_match('#\([\pL]+\)#siu', $bldg_data_actual, $bldg_data_location);
    $bldg_loc = str_replace(array('(', ')'), '', $bldg_data_location[0]);
    DatabaseMgr::add_location($bldg_loc);
    $location_id = DatabaseMgr::get_location($bldg_loc)['id'];
    $bldg_name = rtrim(explode('(', $bldg_data_actual)[0]);
    DatabaseMgr::add_building($location_id, $bldg_code, $bldg_name);
}
