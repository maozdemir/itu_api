<?php

$mysqli = new mysqli("localhost", "root", "", "sched");
if ($mysqli->connect_errno) echo "EROR";
