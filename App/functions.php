<?php
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('Europe/Istanbul');

$url = "http://www.sis.itu.edu.tr/tr/ders_programlari/LSprogramlar/prg.php?fb=AKM";
$codes_url = "http://www.sis.itu.edu.tr/tr/ders_programlari/LSprogramlar/prg.php";

function curl_q($callback)
{
	$cookie = fopen("cook.txt", "w+");
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $callback);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$return = curl_exec($ch);
	curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
	curl_close($ch);
	$return = iconv('ISO-8859-9', 'UTF-8', $return);
	return $return;
}

function tr_day_to_num($str)
{
	$pool = array(
		"Pazartesi"	=> 1,
		"Salı"		=> 2,
		"Çarşamba"	=> 3,
		"Perşembe"	=> 4,
		"Cuma"		=> 5
	);
	return $pool[$str];
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


function deretardified_courses($payload)
{
	$arr = array();
	preg_match("#<table  class=dersprg>(.*?)</table>#si", $payload, $matches);
	$table = $matches[0];
	preg_match_all("#<tr(.*?)>(.*?)</tr>#si", $table, $rows);
	unset($rows[2][0]);
	unset($rows[2][1]);
	$rows[2] = array_values($rows[2]);
	foreach ($rows[2] as $row) {
		preg_match_all("#<td(.*?)>(.*?)</td>#si", $row, $columns);
		preg_match("#subj=(.*?)\&numb=(.*?)\"#si", $columns[2][1], $classinfo);
		$dta = array_filter(explode(" <br>", strip_tags($columns[2][5], " <br>")), 'strlen');
		$hta = array_filter(explode("<br>", strip_tags($columns[2][6], " <br>")), 'strlen');
		$day_array = array();
		$hr_array = array();
		foreach ($dta as $i => $d) {
			# TODO: Insert each day as a different course to the table, this will make the timetable creation easier.
			# Since this procedure will create duplicated CRN's, specific identifier COLUMN is required on the table.
			# 01.01.2001 - MONDAY. Changing the DD of the date will give appropriate and usable Unix timestamp to use
			# on timetable creation.
			$day_array[] = tr_day_to_num(trim($d));
			$hours_span_arr = explode("/", $hta[$i]);
			$start_hr = mb_substr($hours_span_arr[0], 0, 2);
			$start_mn = mb_substr($hours_span_arr[0], 2, 2);
			$start_time = mktime($start_hr, $start_mn, 0, 1, 1, 2001);
			$end_hr = mb_substr($hours_span_arr[1], 0, 2);
			$end_mn = mb_substr($hours_span_arr[1], 2, 2);
			$end_time = mktime($end_hr, $end_mn, 0, 1, 1, 2001);
			$hr_array[tr_day_to_num(trim($d))]["start"] = $start_time;
			$hr_array[tr_day_to_num(trim($d))]["end"] = $end_time;
		}
		$arr[] = array(
			"crn"	=> $columns[2][0],
			"subj"	=> @$classinfo[1],
			"no"	=> @$classinfo[2],
			"name"	=> strip_tags($columns[2][2], "<br>"),
			"inst"	=> strip_tags($columns[2][3], "<br>"),
			"bldg"	=> strip_tags($columns[2][4], "<br>"),
			"day"	=> $day_array,
			"hrs"	=> $hr_array,
			"room"	=> strip_tags($columns[2][7], "<br>"),
			"cpc"	=> strip_tags($columns[2][8], "<br>"),
			"enr"	=> strip_tags($columns[2][9], "<br>"),
			"rez"	=> strip_tags($columns[2][10], "<br>"),
			"rest"	=> strip_tags($columns[2][11], "<br>"),
			"onsa"	=> strip_tags($columns[2][12], "<br>"),
		);
	}
	return $arr;
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

var_dump(deretardified_courses($html));
var_dump(fetch_codes($htmlc));
