<?php

/* 
 * Truy cap database
 */
include_once 'info_database.php';
$conn = mysql_connect($SERVER, $USERNAME, $PASSWORD) or die("Cant not connected database");
mysql_select_db($DB_NAME, $conn);


/*
 * Hàm kiểm tra tên của một quán đã tồn tại trong database hay chưa
 * input: $name tên của quán
 * output: return true : đã tồn tại
 *         return false: chưa tồn tại
 */

function isAvailablePlace($name){
    $sql = "select * from places where name = \"".$name."\"";
    $query = mysql_query($sql);
    if(mysql_num_rows($query)==0){
        return false;
    }
    
    return true;
}

/*
 * Hàm kiểm tra một sự kiện có trong cơ sở dữ liệu hay chưa
 * input: $title tên của sự kiện cần tìm
 * output: return true: Nếu tồn tại
 *         return false: Nếu chưa tồn tại
 */
function isAvailableEvent($title){
    $sql = "select *from events where title ='".$title."'";
    $query = mysql_query($sql);
    if(mysql_num_rows($query)==0){
        return false;
    }
    return true;
}

/*
 * Hàm lưu thông tin của của một quán vào database
 */
function insertPlace($name, $phone, $latitude, $longtitude, $intro, $image, $district, $street, $national, $province, $houseno, $ward, $view, $like){
    $sql = "insert into places(name, phone, latitude, longtitude, intro, image, district, street, national, province, houseno, ward, view, numlike)"
            . "values('$name','$phone','$latitude','$longtitude','$intro','$image','$district','$street','$national','$province','$houseno','$ward','$view','$like')";
    $query = mysql_query($sql);
    if(mysql_affected_rows()== -1){
        echo "Do'nt insert places";
        return false;
    }
    return true;
}
    
/*
 * Hàm cập nhật thông tin giới thiệu quán
 */
function updateIntro($intro, $name){
    $sql = "update places set intro = '".$intro."' where name ='".$name."'";
    $query = mysql_query($sql);
    if(mysql_affected_rows()== -1){
        echo "Do'nt insert places";
    }
}

/*
 * Hàm lưu thông tin một sự kiện của quán và database
 */
function insertEvent($title, $created, $description, $image, $place_id){
    $sql = "insert into events(title, time, created, description, image, place_id)"
            . "values('$title','$created','$description','$image','$place_id')";
    $query = mysql_query($sql);
    if(mysql_affected_rows()==-1){
        echo "Do'nt insert events";
    }
}

/*
 * Hàm lấy id của quán có tên chỉ định
 * input: $name tên của quán cần lấy id
 * output: return id của quán
 */
function getIdPlace($name){
    $sql = "select * from  places where name ='".$name."'";
    $query = mysql_query($sql);
    if(mysql_num_rows($query)==0){
        return null;
    }else{
        $result = mysql_fetch_array($query);
        return $result;
    }
}

?>
