<?php
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('Europe/Istanbul');

$url = "https://www.sis.itu.edu.tr/TR/ogrenci/ders-programi/ders-programi.php?seviye=LS&derskodu=INS";
$codes_url = "https://www.sis.itu.edu.tr/TR/ogrenci/ders-programi/ders-programi.php?seviye=LS";

function curl_q($callback, $iconv = true)
{
    $cookie = fopen("cook.txt", "w+");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $callback);
    curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.81 Safari/537.36");
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
    $return = curl_exec($ch);
    curl_close($ch);
    if ($iconv) $return = iconv('ISO-8859-9', 'UTF-8', $return);
    return $return;
}

/*####################################################################################
 #####################################################################################
 ################################## TABLE STRUCTURE ##################################
 #####################################################################################
 #####################################################################################
 ____________________________________________________________________________________
 | CRN | CODE | TITLE | INS | BLDG | DAY | TIME | ROOM | CAPACITY | ENROLLED | REST |
 ''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
 #####################################################################################
 #####################################################################################
 ################################## TIME FORMATTING ##################################
 #####################################################################################
 #####################################################################################
 ###################				978307200 + time				##################
 #####################################################################################
 #####################################################################################*/

$term_start_epoch = 1633294800;
$term_end_epoch = 1642193999;

function class_day_to_epoch($days_raw, $hours_raw)
{
    $term_start_epoch = 1633294800;
    $dta = array_filter(explode(" <br>", strip_tags($days_raw, " <br>")), 'strlen');
    $hta = array_filter(explode("<br>", strip_tags($hours_raw, " <br>")), 'strlen');
    $pool = array(
        "Pazartesi"    => 0,
        "Salı"        => 1,
        "Çarşamba"    => 2,
        "Perşembe"    => 3,
        "Cuma"        => 4
    );
    $return_data = array();
    foreach ($dta as $i => $days) {
        $day = trim($days);
        if ($day != "----") {
            $day_to_nr = $pool[$day];
            $day_nr_to_epoch = $day_to_nr * 24 * 60 * 60;
            $hours = trim($hta[$i]);
            $hours_arr = explode("/", $hours);
            $start_hr = mb_substr($hours_arr[0], 0, 2);
            $start_mn = mb_substr($hours_arr[0], 2, 2);
            $end_hr = mb_substr($hours_arr[1], 0, 2);
            $end_mn = mb_substr($hours_arr[1], 2, 2);
            $start_hr_epoch = $start_hr * 60 * 60;
            $end_hr_epoch = $end_hr * 60 * 60;
            $start_mn_epoch = $start_mn * 60;
            $end_mn_epoch = $end_mn * 60;
            $start_epoch = $start_hr_epoch + $start_mn_epoch + $day_nr_to_epoch + $term_start_epoch;
            $end_epoch = $end_hr_epoch + $end_mn_epoch + $day_nr_to_epoch + $term_start_epoch;
            $return_data[] = array("start_epoch" => $start_epoch, "end_epoch" => $end_epoch);
        } else {
            $return_data[] = array("start_epoch" => 0, "end_epoch" => 0);
        }
    }
    return $return_data;
}

function courses($payload)
{
    preg_match("/<table class=\"table table-bordered table-striped table-hover table-responsive\" >(.*?)<\/table>/si", $payload, $courses_table_preg);
    $courses_table = $courses_table_preg[1];
    preg_match_all("/<tr>(.*?)<\/tr>/si", $courses_table, $courses_preg);
    $courses = $courses_preg[1];
    $dersler[] = array();
    foreach ($courses as $course) {
        preg_match_all("/<td>(.*?)<\/td>/si", $course, $course_data_preg);
        $course_data = $course_data_preg[1];
        $crn = $course_data[0];
        $course_code = strip_tags($course_data[1]);
        $course_name = $course_data[2];
        $is_online = ($course_data[3] == "" ? false : true);
        $instructor = $course_data[4];
        $building = ($is_online ? "Çevrimiçi" : str_split(strip_tags($course_data[5]), 3));
        $days_raw = $course_data[6];
        $hours_raw = $course_data[7];
        $class = ($is_online ? "Çevrimiçi" : explode(" <br>", $course_data[8]));
        array_pop($class);
        $capacity = $course_data[9];
        $enrolled = $course_data[10];
        $reservation = $course_data[11];
        $restriction = strip_tags($course_data[12]);
        $prereq = strip_tags($course_data[13]);
        $class_restrictions = $course_data[14];
        $dersler[] = array(
            "crn" => $crn,
            "course_code" => $course_code,
            "course_name" => $course_name,
            "is_online" => $is_online,
            "instructor" => $instructor,
            "building" => $building,
            "class_epoch" => class_day_to_epoch($days_raw, $hours_raw),
            "class" => $class,
            "capacity" => $capacity,
            "enrolled" => $enrolled,
            "reservation" => $reservation,
            "restriction" => $restriction,
            "prereqs" => $prereq,
            "class_restrictions" => $class_restrictions,
        );
    }
    return $dersler;
}
function fetch_codes($payload)
{
    $arr = array();
    preg_match_all("#option  value\=\"(.*?)\"#si", $payload, $codes_preg);
    foreach ($codes_preg[1] as $i => $codes) {
        $arr[$i] = $codes;
    }
    return $arr;
}
$html = curl_q($url);
$htmlc = curl_q($codes_url);

//var_dump(fetch_codes($htmlc));
//var_dump(courses($html));
//var_dump(fetch_codes($htmlc));
