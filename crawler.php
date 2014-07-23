<meta charset="utf-8"/>

<?php
/*
 * Notes: Các link lấy tin có dạng
 * +/ Quán nổi nổi bật nhất:  http://www.quancafe.vn/Branch/MostFamous?start=1&total=6&pro= có 13 trang
 * +/ Quán mới tham gia: http://www.quancafe.vn/Branch/Newest?start=2&total=6&pro= có 1779 trang
 * +/ Quán được yêu thích nhất: http://www.quancafe.vn/Branch/MostLike?start=2&total=6&pro= có 1779 trang
 * +/ Quán được nhiều người review nhất: http://www.quancafe.vn/Branch/MostReview?start=2&total=6&pro= có 1779 trang
 */

include 'simple_html_dom.php';
include_once 'connect_database.php';
define("URL_BASE", "http://www.quancafe.vn/");

/**
 * Hàm xử lý lấy mã brangId của một quán
 * input: $str là chuỗi có dạng ObjectId(531fa538a1cfc03f8c259bce)
 * output: là mã 531fa538a1cfc03f8c259bce
 */
function getBranchId($str) {
    $result = $str;
    $result1 = str_replace('ObjectId(', '', $result);
    $result2 = str_replace(')', '', $result1);
    return $result2;
}

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

/**
 *  Hàm lưu hình ảnh đại diện của quán vào thư mục chỉ định trên máy localhost
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

/**
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
        return false;
    }

    return true;
}

/**
 * Hàm lấy ảnh silde của mỗi quán lưu vào bảng images
 * input: $id : Là mã của quán
 *        $path: Thư mục lưu ảnh
 * output: ảnh silde của quán được lưu và thư mục chỉ định
 */
function saveSlideImage($id, $path) {
    $content = file_get_html("http://www.quancafe.vn/shop/GetAlbum?id=" . $id);
    //Giải mã đối tượng Json
    $json = json_decode($content);
    //Lấy thông tin về từ đối tượng JSON
    foreach ($json as $key) {
        //Tải ảnh về máy
        saveImageEvent($key->src, $path);
        //Lưu ảnh vào cơ sỏ dữ liệu
        insertImage(getNameImage($key->src), $id);
    }
}

/**
 * 
 * Hàm cắt bỏ kí tự đầu tiên của một chuỗi
 */
function getItemInfo($str) {
    $result = strstr($str, ":");
    $len = strlen($result);
    $result1 = substr($result, 1, $len - 1);
    return $result1;
}

/**
 * Hàm lấy thông tin phục vụ của quán bao gồm lượng khách chứa,
 * thời gian mở cửa, ngôn ngữ
 * input: $url: link tới trang thông tin chi tiết của quán
 *        $code: mã của quán
 * output: Thông tin phục vụ được lưu vào bảng informations
 */
function saveInformation($url, $code) {
    $html = file_get_html("http://www.quancafe.vn/" . $url);
    $info = $html->find('div[class=fl w100p t10]');
    $arr = "";
    foreach ($info as $item) {
        $arr = $arr . ";" . getItemInfo($item->plaintext);
    }
    $mang = array();
    $mang = explode(";", $arr);

    if (!isset($mang[2])) {
        $intro = $html->find('article[class=help] p');
        //Tạo lại mảng
        $arr = "";
        foreach ($intro as $key => $item) {
            //Bỏ qua trường điện thoại
            if ($key == 0)
                continue;
            $arr = $arr . ";" . $item->plaintext;
        }
        //Tạo lại mảng
        $mang = array();
        $mang = explode(";", $arr);
    }
    $timeservice = $mang[1];
    $holiday = $mang[2];
    $storage = $mang[3];
    $priceavg = $mang[4];
    $methodpay = $mang[5];
    $lang = $mang[6];
    //Lưu vào bảng informations
    insertInfomation($timeservice, $holiday, $storage, $priceavg, $methodpay, $lang, $code);
}
/**
 * Hàm xử lý cú pháp của đối tượng JSON chuyển về
 */

function hanlerSyntax($str) {
    $result = $str;
    $result1 = str_replace("\"[", "[", $result);
    $result2 = str_replace("]\"", "]", $result1);
    $result3 = str_replace(")", ")\"", $result2);
    $result4 = str_replace("\")", ")", $result3);
    $result5 = str_replace("ObjectId(\"", "\"ObjectId(", $result4);
    $result6 = str_replace("ISODate(\"", "\"ISODate(", $result5);
    $result7 = str_replace("NumberLong(", "\"NumberLong(", $result6);
    return $result7;
}

/**
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

/**
 * Hàm lấy thông tin giới thiệu về quán
 * input: $url đường dẫn địa chỉ của quán
 * output: Chuỗi thông tin giới thiệu quán
 */

function getIntroduction($url) {
    $place_link = URL_BASE . $url;
    $html = file_get_html($place_link);
    $intro = $html->find('div.overview', 0)->plaintext;
    return $intro;
}

/**
 * Hàm lấy thông tin các sự kiện của quán cụ thể
 * intput: 
 *          $url đường dẫn địa chỉ của trang cần lấy thông tin
 *          $name_place tên của quán cần lấy sự kiện
 * output: Thông tin được lưu vào database
 */

function saveEvents($url, $places_id) {
    $html = file_get_html($url);
    $events = $html->find('div[id=events] div[class=s315x120 fl]');

    //Kiểm tra xem quán có tồn tại sự kiện hay không
    if ($events == null) {
        echo "Quán không có sự kiện";
    } else {
        //Lấy thông tin các sự kiện của quán
        foreach ($events as $event) {

            //Lấy tên sự kiện
            $title = $event->find('div.txt', 0)->plaintext;

            //Kiểm tra sự kiện đã tồn tại trong database chưa
            if (!isAvailableEvent($title)) {
                //Lấy tên ảnh đại diện của sự kiện
                $url = $event->find('img', 0)->src;
                $image = getNameImage($url);

                //Lấy ảnh đại diện của sự kiện lưu trên máy local 
                saveImageEvent($url, "C:/xampp/htdocs/CrawlerCafe/temp/EventImage/");
                 
                //Lấy thông tin nội dung của sự kiện
                $link_event = $event->find('a', 0)->href;
                $description = getDescriptionEvent($link_event);

                //Chèn sự kiện vào bảng events 
                insertEvent($title, $description, $image, $places_id);
            }
        }
    }
}
    /**
     * Hàm lưu thông tin mỗi quán cung cấp các dịch vụ nào vào bảng services_places và purpots_places
     * Có tổng 46 mục ứng với mỗi quán, trong đó từ 0->20 là mã các dịch vụ quán cung cấp, từ 21->45 là 
     * các mục đích mà quán phù hợp có thể cung cấp.
     * 
     * input : $url: Link tới địa chỉ trang lưu thông ti của quán.
     *         $name: Tên quán dùng lấy id của quán
     * ouput: Thông tin được lưu và hai bảng services_places và places_purports
     */
    function saveServicesPurports($url, $places_id) {

        $html = file_get_html(URL_BASE . $url);
        $ser = $html->find('div[class=services]');
        foreach ($ser as $key => $item) {
            //Kiểm tra nếu $key <= 20 thì lưu vào bảng services_places ngược lại lưu vào bảng places_purports
            if ($item->find('span[class=checkBox_checked]')) {
                if ($key <= 20) {
                    insertServicesPlaces($key, $places_id);
                } else {
                    insertPlacesPurports($places_id, $key);
                }
            }
        }
    }

    /**
     * Hàm lấy về tập ảnh slide của từng quán
     */
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
        $data1 = hanlerSyntax($data);

        //===>In ra màn hình
//    echo $data1;
        //Giải mã đối tượng JSON
        $json = json_decode($data1);

        //Kiểm tra lỗi khi chuyển đổi đối tượng JSON
        if (json_last_error() != 0) {
            echo "<b style=\" color:red\" >Lỗi chuyển đổi đối tượng JSON </b>";
        }

        //Lấy thông tin về từ đối tượng JSON
        foreach ($json as $key => $value) {
            foreach ($value as $element) {

                //Kiểm tra nếu quán chưa tồn tại trong database thì thao tác
                if (!isAvailablePlace(getBranchId($element->_id))) {
                    $id = getBranchId($element->_id);
                    $name = $element->Name;
                    $uniqueName = $element->UniqueName;
                    $phone = $element->Phones;
                    $image = getNameImage($element->FrontImage);
                    $national = $element->Address->National;
                    $province = $element->Address->Province;
                    $district = $element->Address->District;
                    $street = $element->Address->Street;
                    $houseno = $element->Address->HouseNo;
                    $ward = $element->Address->Ward;
                    $longtiude = $element->Map->Longitude;
                    $latitude = $element->Map->Latitude;
                    $vote = $element->VoteNumber;
                    $intro = getIntroduction($uniqueName);

                    //Chèn vào database
                    insertPlace($name, $phone, $latitude, $longtiude, $intro, $image, $district, $street, $national, $province, $houseno, $ward, 0, 0, $id, $vote);
                    //Lưu ảnh vào thư mục webroot
                    saveImagePlace($element->FrontImage, "C:/xampp/htdocs/CrawlerCafe/temp/FrontImage/", $type);

                    //Truy cập vào trang thông tin riêng của quán để lấy thông tin về các dịch vụ và sự kiện của quán
                    //Lấy thông tin dịch vụ và mục đích
                    saveServicesPurports($uniqueName, $id);

                    //Lấy thông tin các sự kiện của quán.
//                    saveEvents(URL_BASE . $uniqueName,$id);

                    //Lấy tập ảnh slide của quán
                    saveSlideImage($id, "C:/xampp/htdocs/CrawlerCafe/temp/SlideImage/");

                    //Lấy thông tin phục vụ của quán
                    saveInformation($uniqueName, $id);
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

    crawlerPlaces(0, 13);
    crawlerPlaces(1, 1779);
    crawlerPlaces(2, 1779);
    crawlerPlaces(3, 1779);
    ?>

