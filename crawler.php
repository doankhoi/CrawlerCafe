<meta charset="utf-8"/>

<?php
/*
 * Notes: Các link lấy tin có dạng
 * +/ Quán nổi nổi bật nhất:  http://www.quancafe.vn/Branch/MostFamous?start=2&total=6&pro= có 13 trang
 * +/ Quán mới tham gia: http://www.quancafe.vn/Branch/Newest?start=2&total=6&pro= có 1779 trang
 * +/ Quán được yêu thích nhất: http://www.quancafe.vn/Branch/MostLike?start=2&total=6&pro= có 1779 trang
 * +/ Quán được nhiều người review nhất: http://www.quancafe.vn/Branch/MostReview?start=2&total=6&pro= có 1779 trang
 */

include 'simple_html_dom.php';
include_once 'connect_database.php';
define("URL_BASE", "http://www.quancafe.vn/");

/* Hàm lấy tên hình ảnh trong một đường dẫn
 * input : $path là chuỗi có dạng "/sd_ds/image.jpg
 * outout: Trả về tên ảnh là image.jpg
 */

function getNameImage($path) {
    $len = strlen($path);
    $name = null;
    for ($i = $len - 1; $i != 0; $i--) {

        if ($path[$i] == '/') {
            $name = substr($path, $i + 1, $len - 1);
            break;
        }
    }
    return $name;
}

/* Hàm lưu hình ảnh đại diện của quán vào thư mục chỉ định trên máy localhost
 * input: $url là đường dẫn tới ảnh trên server
 *        $des Thư mục đích dùng lưu ảnh trên mấy local
 *        $type Kiểu của quán giá trị 0 là quán nổi bật:
 *           +/ Quán nổi bật: http://cdn.quancafe.vn/z_276x145/.$url
 *           +/ Quán mới tham gia, Quán được yêu thích, Quán được review nhiều có dạng: http://cdn.quancafe.vn/z_215x145/.url
 * output: Trả về void
 */

function saveImagePlace($url, $des, $type) {
    if ($type == 0) {
        if (!file_put_contents($des . getNameImage($url), file_get_contents("http://cdn.quancafe.vn/z_276x145/" . $url))) {
            echo "<br>Don't insert image of place!!!<br>";
        }
    } else {
        if (!file_put_contents($des . getNameImage($url), file_get_contents("http://cdn.quancafe.vn/z_215x145/" . $url))) {
            echo "<br>Don't insert image !!!<br>";
        }
    }
}

/*
 * Hàm lưu hình ảnh đại diện sự kiện của quán vào local 
 * input: 
 *      $url Đường dẫn hình ảnh trên server có dạng http://cdn.quancafe.vn/z_100x70//_event/2014/5/3/-S2-110520142013.jpg
 *      $des Thư mục lưu ảnh được chỉ định
 * output:
 *      Ảnh đại diện của sự kiện được lưu vào thư mục chỉ định
 */

function saveImageEvent($url, $des) {
    if (!file_put_contents($des . getNameImage($url), file_get_contents($url))) {
        echo "<br>Don't insert image of event !!!<br>";
    }
}

/*
 * Hàm xử lý cú pháp của đối tượng JSON chuyển về
 */

function hanlerSyntax($str) {
    $result = $str;
    $result = str_replace("\"[", "[", $result);
    $result = str_replace("]\"", "]", $result);
    $result = str_replace(")", ")\"", $result);
    $result = str_replace("\")", ")", $result);
    $result = str_replace("ObjectId(\"", "\"ObjectId(", $result);
    $result = str_replace("ISODate(\"", "\"ISODate(", $result);
    $result = str_replace("NumberLong(", "\"NumberLong(", $result);
    return $result;
}

/*
 * Hàm lấy thông tin chi tiết về sự kiện
 * input: $url: Đường dẫn địa chỉ của trang sự kiện
 * output: Chuỗi nội dung của sự kiện
 */

function getDescriptionEvent($url) {
    $link = URL_BASE . $url;
    $html = file_get_html($link);
    $event = $html->find('div.fl .w630', 0);
    $description = $event->outertext;
    return $description;
}

/*
 * Hàm lấy thông tin giới thiệu về quán
 * input: $url đường dẫn địa chỉ của quán
 * output: Chuỗi thông tin giới thiệu quán
 */

function getIntroduction($url) {
    $place_link = URL_BASE . $url;
    $html = file_get_html($place_link);
    $intro = $html->find('div.overview', 0)->outertext;
    return $intro;
}

/*
 * Hàm lấy thông tin các sự kiện của quán cụ thể
 * intput: 
 *          $url đường dẫn địa chỉ của trang cần lấy thông tin
 *          $name_place tên của quán cần lấy sự kiện
 * output: Thông tin được lưu vào database
 */

function saveEvents($url, $name_place) {
    $html = file_get_html($url);
    $events = null;
    $events = $html->find('div[id=events] div.s315x120');

    //Kiểm tra xem quán có tồn tại sự kiện hay không
    if (isset($events)) {
        //Lấy thông tin các sự kiện của quán
        foreach ($events as $event) {

            //Lấy tên sự kiện
            $title = $event->find('div.txt', 0)->plaintext;
            
            //Kiểm tra sự kiện đã tồn tại trong
            if (!isAvailableEvent($title)) {
                //Lấy tên ảnh đại diện của sự kiện
                $url = $event->find('img', 0)->src;
                $image = getNameImage($url);

                //Lấy ảnh đại diện của sự kiện lưu trên máy local
                saveImageEvent($url, "C:/xampp/htdocs/CafeGarden/app/webroot/img/" . $image);

                //Lấy id của quán cần lưu sự kiện
                $place = getIdPlace($name_place);
                $place_id = $place['id'];

                //Lấy thông tin nội dung của sự kiện
                $link_event = $event->find('a', 0)->href;
                $description = getDescriptionEvent($link_event);

                //Chèn sự kiện vào bảng events 
                insertEvent($title, "NOW()", $description, $image, $place_id);
            }
        }
    } else {
        echo "Quán không có sự kiện";
    }
}


/*
 * Hàm lưu thông tin của địa diểm vào cơ sở dữ liệu
 * input: $url đường dẫn địa chỉ của quán
 *        $type loại quán: có thể là quán nổi bật, quán mới tham gia,
 *                         quán được yêu thích, quán được nhiều người review nhất.
 * output: Thông địa điểm được lưu vào database.
 */

function savePlaces($url, $type) {

    //Chuyển đổi và lấy về đối tượng JSON
    $content = file_get_html($url);
    $data = urldecode($content);
    $data = hanlerSyntax($data);

    //===>In ra màn hình
    echo $data;


    //Giải mã đối tượng JSON
    $json = json_decode($data);

    //Kiểm tra lỗi khi chuyển đổi đối tượng JSON
    if (json_last_error() != 0) {
        echo "<b style=\" color:red\" >Lỗi chuyển đổi đối tượng JSON </b>";
    }

    //Lấy thông tin về từ đối tượng JSON
    foreach ($json as $key => $value) {
        foreach ($value as $element) {

            //Kiểm tra nếu quán chưa tồn tại trong database thì thao tác
            if (!isAvailablePlace($element->Name)) {
                $name = $element->Name;
                $uniqueName = $element->UniqueName;
                $image = getNameImage($element->FrontImage);
                $national = $element->Address->National;
                $province = $element->Address->Province;
                $district = $element->Address->District;
                $street = $element->Address->Street;
                $houseno = $element->Address->HouseNo;
                $ward = $element->Address->Ward;
                $longtiude = $element->Map->Longitude;
                $latitude = $element->Map->Latitude;
                $intro = getIntroduction($uniqueName);

                //Chèn vào database
                insertPlace($name, "null", $latitude, $longtiude, $intro, $image, $district, $street, $national, $province, $houseno, $ward, 0, 0);
                //Lưu ảnh vào thư mục webroot
                saveImagePlace($element->FrontImage, "C:/xampp/htdocs/CafeGarden/app/webroot/img/", $type);

                //Truy cập vào trang thông tin riêng của quán để lấy thông tin về các dịch vụ và sự kiện của quán
                //Lấy thông tin các sự kiện của quán.
                saveEvents(URL_BASE . $uniqueName, $name);
                
                //Lấy thông tin các dịch vụ của quán.
//                saveServices(URL_BASE . $uniqueName, $name);
            }
        }
        break;
    }
}

/*
 * Hàm crawler thông tin các quán đối với đường dẫn url và số trang cần lấy thông tin
 * 
 * input: $type có kiểu số nguyên: 0: Ứng với lấy thông tin các quán nổi bật.
 *                                 1: Ứng với lấy thông tin quán mới tham gia.
 *                                 2: Ứng với lấy thông tin quán được yêu thích.
 *                                 3: Ứng với lấy thông tin quán được nhiều người review
 *        $num_page là số trang cần lấy thông tin bắt đầu từ trang 1
 * 
 * output: Thông tin các quán được lưu vào cơ sở dữ liệu 
 */

function crawlerPlaces($type, $num_page) {

    //Kiểm tra kiểu của quán
    if ($type == 0) {// Quán nổi bật
        for ($i = 1; $i <= $num_page; $i++) {
            $url = "http://www.quancafe.vn/Branch/MostFamous?start=" . $i . "&total=6&pro=";
            savePlaces($url, $type);
        }
    } else if ($type == 1) { //Quán mới tham gia
        for ($i = 1; $i <= $num_page; $i++) {
            $url = "http://www.quancafe.vn/Branch/Newest?start=" . $i . "&total=6&pro=";
            savePlaces($url, $type);
        }
    } else if ($type == 2) { // Quán được yêu thích nhất
        for ($i = 1; $i <= $num_page; $i++) {
            $url = "http://www.quancafe.vn/Branch/MostLike?start=" . $i . "&total=6&pro=";
            savePlaces($url, $type);
        }
    } else if ($type == 3) { // Quán được nhiều review
        for ($i = 1; $i <= $num_page; $i++) {
            $url = "http://www.quancafe.vn/Branch/MostReview?start=" . $i . "&total=6&pro=";
            savePlaces($url, $type);
        }
    } else {
        echo "Not found type places";
    }
}

?>

