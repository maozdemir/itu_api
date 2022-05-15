<?php
header('Content-type: text/html; charset=utf-8');


date_default_timezone_set('Europe/Istanbul');
setlocale(LC_TIME, 'trk.UTF-8');




class DatabaseMgr
{
    /**
     * Database connection
     * @return mysqli $mysqli_connection
     */

    static private function dbconnection()
    {
        //$mysqli = new mysqli("localhost", "root", "", "itu_api");
        $mysqli = new mysqli("localhost", "root", "", "itu_database");
        if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }
        return $mysqli;
    }

    /**
     * Add new course to database
     * @param Course $course
     * @return boolean $success
     */

    static public function add_course($course)
    {

        $connection = DatabaseMgr::dbconnection();
        if (count($course["class_epoch"], 1) > 2) {
            $new_class_epoch = array();
            foreach ($course["class_epoch"] as $class_epoch) {
                $new_class_epoch[] = implode(",", $class_epoch);
            }
            var_dump($new_class_epoch);
            $course["class_epoch"] = implode(",", $new_class_epoch);
        } else {
            $course["class_epoch"] = implode(",", $course["class_epoch"]);
        }

        if (is_array($course["class"])) {
            if (count($course["class"], 1) > 1) {
                $course["class"] = implode(",", $course["class"]);
            } else {
                $course["class"] = $course["class"][0];
            }
        }
        if (is_array($course["building"])) {
            if (count($course["building"], 1) > 1) {
                $course["building"] = implode(",", $course["building"]);
            } else {
                $course["building"] = $course["building"][0];
            }
        }

        var_dump($course["class_epoch"]);
        var_dump($course["class"]);
        foreach ($course as $column => $value) {
            $cols[] = $column;
            $vals[] = mysqli_real_escape_string($connection, $value);
        }
        $colnames = "`" . implode("`, `", $cols) . "`";
        $colvals = "'" . implode("', '", $vals) . "'";

        if ($connection->query("INSERT INTO `courses` ($colnames) VALUES ($colvals)")) {
            return true;
        } else {
            printf("HATA: %s\n", $connection->error);
            return false;
        }
    }

    /**
     * Get course data
     * @param string $id
     * @return array
     */
    static public function get_course($id)
    {

        $db = DatabaseMgr::dbconnection();
        if ($result = $db->query("SELECT * FROM `courses` WHERE crn = $id")) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    return $row;
                }
            } else {
                return "ERR";
            }
        } else {
            printf("ERR: %s\n", $db->error);
            return "ERR";
        }
    }

    /**
     * Get course data for RESTful API
     * @param int $id
     * @return array
     */
    static public function course_quota_provider($id)
    {
        $course_data = DatabaseMgr::get_course($id);
        $course_data_proc = array(
            "crn" => $course_data['crn'],
            "quota" => $course_data['quota'],
            "enrolled" => $course_data['enrolled']
        );
        return $course_data_proc;
    }

    /**
     * Get location data from name
     * @param int $location
     * @return array
     */

    static public function get_location($location)
    {

        $db = DatabaseMgr::dbconnection();
        if ($result = $db->query("SELECT * FROM `locations` WHERE `name` = '$location'")) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    return $row;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Get location name from location id
     *
     * @param int $location_id
     * @return boolean|array Returns false if location id is not found, otherwise returns location name
     */

    static public function get_location_name($location_id)
    {

        $db = DatabaseMgr::dbconnection();
        if ($result = $db->query("SELECT * FROM `locations` WHERE `id` = '$location_id'")) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    return $row;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Add new location to database.
     * @param int $location Location name
     * @return boolean $success
     */
    static public function add_location($location)
    {
        $connection = DatabaseMgr::dbconnection();
        if ($result = $connection->query("SELECT * FROM `locations` WHERE `name` = '$location'")) {
            if ($result->num_rows > 0) {
                return false;
            } else {
                if ($location != '') {
                    if ($connection->query("INSERT INTO `locations` (`name`) VALUES ('$location')")) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        } else {
            return false;
        }
    }

    /**
     * Add new building to database.
     * @param int $location Location ID
     * @param string $code Building Code
     * @param string $name_tr Building Name (Turkish)
     * @return boolean $success
     */

    static public function add_building($location, $code, $name_tr)
    {
        $connection = DatabaseMgr::dbconnection();
        if ($result = $connection->query("SELECT * FROM `buildings` WHERE `code` = '$code'")) {
            if ($result->num_rows > 0) {
                return false;
            } else {
                if ($location != '') {
                    if ($connection->query("INSERT INTO `buildings` (`code`,`name_tr`,`location`) VALUES ('$code','$name_tr','$location')")) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        } else {
            return false;
        }
    }

    /**
     * Returns building data by building ID. If building ID is not found, returns false.
     * @param int $building_id
     * @return array $building_data
     */

    static public function get_building($building_id)
    {

        $db = DatabaseMgr::dbconnection();
        if ($result = $db->query("SELECT * FROM `buildings` WHERE `id` = '$building_id'")) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    return $row;
                }
            } else {
                return false;
            }
        } else {
            printf("ERR: %s\n", $db->error);
            return false;
        }
    }
}
