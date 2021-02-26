<?php
// กรณีต้องการตรวจสอบการแจ้ง error ให้เปิด 3 บรรทัดล่างนี้ให้ทำงาน กรณีไม่ ให้ comment ปิดไป
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 
// include composer autoload
require "vendor/autoload.php";
 
// ดึง config id&token bot
require_once 'bot_settings.php';
 
///////////// ส่วนของการเรียกใช้งาน class ผ่าน namespace
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
//use LINE\LINEBot\Event;
//use LINE\LINEBot\Event\BaseEvent;
//use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
use LINE\LINEBot\MessageBuilder\AudioMessageBuilder;
use LINE\LINEBot\MessageBuilder\VideoMessageBuilder;
use LINE\LINEBot\ImagemapActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder ;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\DatetimePickerTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselColumnTemplateBuilder;
 
// เชื่อมต่อกับ LINE Messaging API
$httpClient = new CurlHTTPClient(LINE_MESSAGE_ACCESS_TOKEN);
$bot = new LINEBot($httpClient, array('channelSecret' => LINE_MESSAGE_CHANNEL_SECRET));
 
// คำสั่งรอรับการส่งค่ามาของ LINE Messaging API
$content = file_get_contents('php://input');
 
// แปลงข้อความรูปแบบ JSON  ให้อยู่ในโครงสร้างตัวแปร array
$events = json_decode($content, true);
if(!is_null($events)){
    // ถ้ามีค่า สร้างตัวแปรเก็บ replyToken ไว้ใช้งาน
    $replyToken = $events['events'][0]['replyToken'];
    $typeMessage = $events['events'][0]['message']['type'];
    $userMessage = $events['events'][0]['message']['text'];
    $userMessage = strtolower($userMessage);
    switch ($typeMessage){
        case 'text':
            switch ($userMessage) {
                case "ข้อความ":
                    $textReplyMessage = "ท่านได้ส่งข้อความครับ";
                    $replyData = new TextMessageBuilder($textReplyMessage);
                    break;
                case "ภาพ":
                    $picFullSize = 'https://sv1.picz.in.th/images/2021/02/15/oQ57aW.md.jpg';
                    $picThumbnail = 'https://sv1.picz.in.th/images/2021/02/15/oQ57aW.md.jpg';
                    $replyData = new ImageMessageBuilder($picFullSize,$picThumbnail);
                    break;
                case "วีดีโอ":
                    $picThumbnail = 'https://www.i-vdo.info/v/cayhxorb1170';
                    $videoUrl = "https://www.i-vdo.info/v/cayhxorb1170";                
                    $replyData = new VideoMessageBuilder($videoUrl,$picThumbnail);
                    break;
                case "เสียง":
                    $audioUrl = "https://www.mywebsite.com/simpleaudio.mp3";
                    $replyData = new AudioMessageBuilder($audioUrl,27000);
                    break;
                case "ที่ตั้ง":
                    $placeName = "ที่ตั้งบริษัท";
                    $placeAddress = "332 333 หมู่ 5 ถ. พหลโยธิน ตำบล ลำไทร อำเภอวังน้อย จังหวัดพระนครศรีอยุธยา 13170";
                    $latitude = 14.228424142606057;
                    $longitude = 100.70143403664254;
                    $replyData = new LocationMessageBuilder($placeName, $placeAddress, $latitude ,$longitude);              
                    break;
                case "สติ๊กเกอร์":
                    $stickerID = 52002735;
                    $packageID = 11537;
                    $replyData = new StickerMessageBuilder($packageID,$stickerID);
                    break;                                                                                                               
                default:
                    $textReplyMessage = "ฉันยังไม่ค่อยเข้าใจกรุณาลองใช้คำที่กำหนด
                    - ข้อความ
                    - ภาพ
                    - วีดีโอ
                    - เสียง
                    - ที่ตั้ง
                    - สติ๊กเกอร์
                      ";
                    $replyData = new TextMessageBuilder($textReplyMessage);         
                    break;
             
                case "im":
                    $imageMapUrl = 'https://sv1.picz.in.th/images/2021/02/15/oQ57aW.md.jpg';
                    $replyData = new ImagemapMessageBuilder(
                    $imageMapUrl,'URL',new BaseSizeBuilder(699,1040),array(
                     //new ImagemapMessageActionBuilder('MSarea',new AreaBuilder(0,0,520,699)),
                     new ImagemapUriActionBuilder('http://www.google.com',new AreaBuilder(0,0,1024,699))
                        )); 
                    break;
case "test":
$imageMapUrl = 'https://sv1.picz.in.th/images/2021/02/15/oQ57aW.md.jpg';
$replyData = new ImagemapMessageBuilder($imageMapUrl,"",new BaseSizeBuilder(699,1040),array(new ImagemapUriActionBuilder('http://www.google.com',new AreaBuilder(0,0,1024,699))
)); 
break;

            }
            break;
        default:
               $stickerID = 52002744;
               $packageID = 11537;
               $replyData = new StickerMessageBuilder($packageID,$stickerID);         
            break;  
      
    }
}
 
//l ส่วนของคำสั่งตอบกลับข้อความ
$response = $bot->replyMessage($replyToken,$replyData);
if ($response->isSucceeded()) {
    echo 'Succeeded!';
    return;
}
 
// Failed
echo $response->getHTTPStatus() . ' ' . $response->getRawBody();
?>
