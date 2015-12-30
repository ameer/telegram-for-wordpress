<?php
require_once("Notifcaster.class.php");
$html = "<p> Amir is going to <strong>home</strong> and he <em>don't</em> like this. This is a <a href='http://amir.ir'>Link</a></p>";
strip_tags($html, "<strong><em><a>");
$re = array("/<strong>(.+?)<\\/strong>/is","/<em>(.+?)<\\/em>/is", "/<a\\s+(?:[^>]*?\\s+)?href=[\"']?([^'\"]*)[\"']?.*?>(.*?)<\\/a>/is"); 
$subst = array("*$1*", "_$1_","[$2]($1)"); 
$result = preg_replace($re, $subst, $html);
echo $result;
$nt = new Notifcaster_Class();
$nt->_telegram("122877145:AAH-mTygl1FeisuOnsESbCVerRqd6-DTxD0");
$r = $nt->channel_text("@ameer_test", $result, 1);
print_r($r);
