
<meta charset="utf-8"/>
<?php

/* 
 * Truy cap database
 */
include_once 'info_database.php';
$conn = mysql_connect($SERVER, $USERNAME, $PASSWORD) or die("Cant not connected database");
mysql_select_db($DB_NAME, $conn);


/**
 * Hàm kiểm tra tên của một quán đã tồn tại trong database hay chưa
 * input: $name tên của quán
 * output: return true : đã tồn tại
 *         return false: chưa tồn tại
 */

function isAvailablePlace($code){
    $sql = "select * from places where code = '".$code."'";
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
function insertPlace($name, $phone, $latitude, $longitude, $intro, $image, $district, $street, $national, $province, $houseno, $ward, $view, $like, $code,$vote){
    $sql = "insert into places(name, phone, latitude, longitude, intro, image, district, street, national, province, houseno, ward, view, numlike, code, vote)"
            . "values('$name','$phone','$latitude','$longitude','$intro','$image','$district','$street','$national','$province','$houseno','$ward','$view','$like','$code','$vote')";
    $query = mysql_query($sql);
    if(mysql_affected_rows()== -1){
        echo "Do'nt insert table places";
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
        echo "Do'nt update field intro of places";
    }
}

/*
 * Hàm lưu thông tin một sự kiện của quán và database
 */
function insertEvent($title,$description, $image, $place_id){
    $sql = "insert into events(title, created, description, image, places_id)"
            . "values('$title',NOW(),'$description','$image','$place_id')";
    $query = mysql_query($sql);
    if(mysql_affected_rows()==-1){
        echo "Do'nt insert table events";
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

/**
 * Hàm chèn thông tin một món ăn, đồ uống của một loại nào đó nào đó
 */

function insertItem($name, $image, $price, $unit, $description, $categories_id){
    $sql = "insert into items(name, image, price, unit, description, categories_id)"
            . "values('$name','$image','$price','$unit','$description','$categories_id')";
    $query = mysql_query($sql);
    if(mysql_affected_rows()==-1){
        echo "Do'nt insert table items";
    }
}

/**
 * Hàm chèn thông tin vào bảng images lưu các ảnh trong slide image của quán
 */

function insertImage($name, $code){
    $sql = "insert into images(name, code)"
            . "values('$name','$code')";
    $query = mysql_query($sql);
    if(mysql_affected_rows()==-1){
        echo "Do'nt insert table images";
    }
    
}

/**
 * Hàm chèn thông tin vào bảng information là bảng thông tin phục vụ của quán
 */
  
function insertInfomation($timeservice, $holiday, $storage, $priceavg, $methodpay, $lang, $code){
    $sql = "insert into informations(timeservice, holiday, storage, priceavg, methodpay, lang, code)"
            . "values('$timeservice','$holiday','$storage','$priceavg','$methodpay','$lang','$code')";
    $query = mysql_query($sql);
    if(mysql_affected_rows()==-1){
        echo "Do'nt insert table informations";
    }
}

/**
 * Hàm chèn thông tin vào bảng categories chứa thông tin các loai đồ uống, đồ ăn 
 */
function insertCategory($name, $code, $places_id){
    $sql = "insert into categories(name, code, places_id)"
            . "values('$name','$code','$places_id')";
    $query = mysql_query($sql);
    if(mysql_affected_rows()==-1){
        echo "Do'nt insert table categories";
    }
}

/**
 * Hàm chèn thông tin vào bảng liên kết services_places
 */
function insertServicesPlaces($services_id, $places_id){
    $sql = "insert into services_places(services_id, places_id)"
            . "values('$services_id','$places_id')";
    $query = mysql_query($sql);
    if(mysql_affected_rows()==-1){
        echo "Do'nt insert table services_places";
    }
}

/**
 * Hàm chèn thông tin bảng liên kết places_purports
 */
function insertPlacesPurports($places_id, $purports_id){
    $sql = "insert into places_purports(places_id, purports_id)"
            . "values('$places_id','$purports_id')";
    $query = mysql_query($sql);
    if(mysql_affected_rows()==-1){
        echo "Do'nt insert table places_purports";
    }
}
?>
