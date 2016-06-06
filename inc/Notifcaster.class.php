<?php
/**
 * Sending messages to Telegram channels and groups using bot API.
 * Also you can send notifications to your Telegram accounts using Notifcaster API. (Useful for websites notifications)
 * Forked from Notifygram by Anton Ilzheev <ilzheev@gmail.com>
 * Attention! $method always must be started with slash " / "
 * @category Telegram Bot API
 * @package Telegram for WordPress
 * @author Ameer Mousavi <me@ameer.ir>
 * @copyright 2015-2016 Ameer Mousavi
 * @license GPLv3 or later.
 * @version 1.6
 * @link http://notifcaster.com
 */

// Remove below line if you want using this file outside of WordPress
if ( ! defined( 'ABSPATH' ) ) exit;

if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);

    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}
if (PHP_VERSION_ID < 50207) {
    define('PHP_MAJOR_VERSION',   $version[0]);
    define('PHP_MINOR_VERSION',   $version[1]);
    define('PHP_RELEASE_VERSION', $version[2]);
}
class Notifcaster_Class
{
    protected
        $api_token  = null,
        $url        = 'https://tg-notifcaster.rhcloud.com/api/v1',
        $api_method = null,
        $parse_mode = null;
    public
        $web_preview = 0;
    /**
    * Notifcaster API constructor
    * @param string $api_token
    * @param string $url
    */
    public function Notifcaster($api_token, $url = 'https://tg-notifcaster.rhcloud.com/api/v1')
    {
        $this->api_token = $api_token;
        $this->url = $url;
    }
    /**
     * Telegram API constructor
     *
     * @param string $bot_token
     * @param string $parse_mode - default 
     *
     */
    public function _telegram($bot_token, $parse_mode = null, $web_preview = 0)
    {
        $this->url = 'https://api.telegram.org/bot'.$bot_token;
        $this->parse_mode = $parse_mode;
        $this->web_preview = $web_preview;
    }
    /**
     * Send Notification to user
     *
     * @param string $msg
     *
     * @return string
     */
    public function notify($msg = 'NULL')
    {
        $msg = strip_tags($msg);
        //The below condition should be go inside make_request
        if (isset($msg)) {
            if(mb_strlen($msg) > 4096){
              $splitted_text = $this->str_split_unicode($msg, 4096);  
          }
        }
        $this->api_method = "/selfMessage";
        if (isset($splitted_text)) {
            foreach ($splitted_text as $text_part) {
                $params = array(
                    'api_token'  => $this->api_token,
                    'msg'        => $text_part
                    );
                $response = $this->make_request($params);
            }
        } else {
            $params = array(
                'api_token'  => $this->api_token,
                'msg'        => $msg
                );
           $response = $this->make_request($params); 
        }
        return $response;
    }
    /**
     * Get bot info from Telegram
     *
     * @return JSON
     */
    public function get_bot()
    {
        $params = array();
        $this->api_method = "/getMe";
        $response = $this->make_request($params);
        return $response;
    }
    /**
     *  Get the number of members in a chat.
     *  @param  string $chat_id Unique identifier for the target chat or username of the target supergroup or channel (in the format @channelusername)
     *
     *  @return JSON
     */
    public function get_members_count($chat_id)
    {
        if($chat_id == null || $chat_id == ''){
            return;
        }
        $params = array(
            'chat_id' => $chat_id
            );
        $this->api_method = "/getChatMembersCount";
        $response = $this->make_request($params);
        return $response;
    }
    /**
     * Send text message to channel
     *
     * @param string $chat_id
     * @param string $msg
     *
     * @return string
     */
    public function channel_text($chat_id , $msg)
    {
        $params = array(
            'chat_id'  => $chat_id,
            'text'        => $this->prepare_text($msg),
            'parse_mode' => $this->parse_mode,
            'disable_web_page_preview' => $this->web_preview
        );
        $this->api_method = "/sendMessage";
        $response = $this->make_request($params);
        return $response;
    }
    /**
     * Send photo message to channel
     * @deprecated deprecated since version 1.6
     */
    public function channel_photo($chat_id , $caption , $photo)
    {
        $params = array(
            'chat_id'  => $chat_id,
            'caption'  => $caption,
            'photo'    => $photo
        );
        $this->api_method = "/sendPhoto";
        $file_upload = true;
        $response = $this->make_request($params, $file_upload);
        return $response;
    }
    /**
     * Send file to channel based on its format
     *
     * @param string $chat_id
     * @param string $caption
     * @param string $file relative path to file
     *
     * @return string
     */
    public function channel_file($chat_id , $caption , $file, $file_format)
    {
        switch ($file_format) {
            case 'image':
                $method = 'photo';
                break;
            case 'mp3':
                $method = 'audio';
                break;
            case 'mp4':
                $method = 'video';
                break;
            default:
                $method = 'document';
                break;
        }
        $params = array(
            'chat_id'  => $chat_id,
            'caption'  => $caption,
            $method    => $file
        );
        $this->api_method = "/send".$method;
        $file_upload = true;
        $file_param = $method;
        $response = $this->make_request($params, $file_upload, $file_param);
        return $response;
    }

    /**
     * Request Function
     *
     * @param array $params
     * @param string $file_upload
     *
     * @return string "success" || error message
     */    
    protected function make_request(array $params = array(), $file_upload = false, $file_param = '')
    {
        $default_params = $params;
        if (!empty($params)) {
            if (isset($params['caption'])) {
                if (mb_strlen($params['caption']) > 200){
                    $params['caption'] = $this->str_split_unicode($params['caption'], 200);
                    $params['caption'] = $params['caption'][0];
                }
            }
            if (isset($params['text'])) {
                if(mb_strlen($params['text']) > 4096){
                  $splitted_text = $this->str_split_unicode($params['text'], 4096);  
                }
            }
        }
        if (function_exists('curl_init')) {
            $curl = curl_init($this->url.$this->api_method);
            if (PHP_MAJOR_VERSION >= 5 && PHP_MINOR_VERSION >= 5){
                curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
            }
            if ($file_upload) {
                if (class_exists('CURLFile')) {
                    $params[$file_param] = new CURLFile($params[$file_param]);
                } else {
                    $params = $this->curl_custom_postfields($curl, array('chat_id'  => $params['chat_id'], 'caption' => $params['caption']), array($file_param => $params[$file_param]));
                }
            } else {
                $params = http_build_query($params);
            }
            curl_setopt_array($curl, array(
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $params
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            // if text
            if (isset($splitted_text)) {
                $curl = curl_init($this->url."/sendMessage");
                curl_setopt_array($curl, array(
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_POST => 1
                    ));
                foreach ($splitted_text as $text_part) {
                    $params = array(
                        'chat_id'  => $default_params['chat_id'],
                        'text'     => $this->prepare_text($text_part),
                        'parse_mode' => $this->parse_mode,
                        'disable_web_page_preview' => $this->web_preview
                        );
                    $params = http_build_query($params);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                    $response = curl_exec($curl);
                }
                curl_close($curl);
            }
                              
        } else {
            $context = stream_context_create(array(
                'http' => array(
                    'method' => 'POST',
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'content' => $params,
                    'timeout' => 10,
                ),
            ));
            $response = file_get_contents($this->url.$this->api_method, false, $context);
        }
        return $this->response = json_decode($response, true);
    }

    /**
    * Helpers
    */

    /**
    * For safe multipart POST request for PHP5.3 ~ PHP 5.4.
    * @author https://twitter.com/mpyw
    * @param resource $ch cURL resource
    * @param array $assoc "name => value"
    * @param array $files "name => path"
    * @return string
    */
    protected function curl_custom_postfields($ch, array $assoc = array(), array $files = array()) {

    // invalid characters for "name" and "filename"
        static $disallow = array("\0", "\"", "\r", "\n");

    // initialize body
        $body = array();

    // build normal parameters
        foreach ($assoc as $k => $v) {
            $k = str_replace($disallow, "_", $k);
            $body[] = implode("\r\n", array(
                "Content-Disposition: form-data; name=\"{$k}\"",
                "",
                filter_var($v), 
                ));
        }

    // build file parameters
        foreach ($files as $k => $v) {
            switch (true) {
                case false === $v = realpath(filter_var($v)):
                case !is_file($v):
                case !is_readable($v):
                continue; // or return false, throw new InvalidArgumentException
            }
            $data = file_get_contents($v);
            $v = call_user_func("end", explode(DIRECTORY_SEPARATOR, $v));
            list($k, $v) = str_replace($disallow, "_", array($k, $v));
            $body[] = implode("\r\n", array(
                "Content-Disposition: form-data; name=\"{$k}\"; filename=\"{$v}\"",
                "Content-Type: application/octet-stream",
                "",
                $data,
                ));
        }

    // generate safe boundary 
        do {
            $boundary = "---------------------" . md5(mt_rand() . microtime());
        } while (preg_grep("/{$boundary}/", $body));

    // add boundary for each parameters
        foreach ($body as &$part) {
			$part = "--{$boundary}\r\n{$part}"; unset($part);
		}

    // add final boundary
        $body[] = "--{$boundary}--";
        $body[] = "";

    // set options
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Expect: 100-continue",
                "Content-Type: multipart/form-data; boundary={$boundary}", // change Content-Type
                )
            );
        return implode("\r\n", $body);
    }

    /**
    * Convert HTML tags to Telegram markdown format
    * @param string $html content with HTML tags
    * @param boolean $b <strong> to *bold text*
    * @param boolean $i <em> to _italic text_
    * @param boolean $u <a> to [text](url)
    * @return string
    */
    public function markdown ($html, $b = 0, $i = 0, $u = 0) {
        $allowed_tags = "";
        $re = array();
        $subst = array();
        if ($b){
            $allowed_tags .= "<strong>";
            array_push($re, "/<strong>(.+?)<\\/strong>/uis");
            array_push($subst, "*$1*");
        }
        if ($i){
            $allowed_tags .= "<em>";
            array_push($re, "/<em>(.+?)<\\/em>/uis");
            array_push($subst, "_$1_");
        }
        if ($u){
            $allowed_tags .= "<a>";
            array_push($re, "/<a\\s+(?:[^>]*?\\s+)?href=[\"']?([^'\"]*)[\"']?.*?>(.*?)<\\/a>/uis");
            array_push($subst, "[$2]($1)");
        }
        array_push($re, "/[\*](.+)?(\[.+\]\(.+\))(.+)?[\*]/ui", "/[_](.+)?(\[.+\]\(.+\))(.+)?[_]/ui");
        array_push($subst, "$2", "$2");
        $html = strip_tags($html, $allowed_tags);
        $result = preg_replace($re, $subst, $html);
        return $result;
    }
    /**
    * Split the Unicode string into characters
    * @param string $str  The input string
    * @param integer $l Maximum length of the chunk
    * @author http://nl1.php.net/manual/en/function.str-split.php#107658
    */
    public function str_split_unicode($str, $l = 0) {
        if ($l > 0) {
            $ret = array();
            $len = mb_strlen($str, "UTF-8");
            for ($i = 0; $i < $len; $i += $l) {
                $ret[] = mb_substr($str, $i, $l, "UTF-8");
            }
            return $ret;
        }
        return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
    }
    /**
     * Prepare text to compatible with Telegram format.
     * @param string $str
     * @return string
     */
    public function prepare_text($str){
            $str = stripslashes($str);
        if (strtolower($this->parse_mode) == "markdown") {
            $str = $this->markdown($str, 1, 1, 1);
        } elseif (strtolower($this->parse_mode) == "html") {
            $excluded_tags = "<b><strong><em><i><a><code><pre>";
            // Remove nested tags. The priority is <a> tags. For example if a link nested is between <strong> or <em> tags, then these tags will removed.
            $re = array();
            $subst = array();
            array_push($re, "/(<em>)(.+)?(<a\s+(?:[^>]*?\s+)?href=[\"']?([^'\"]*)[\"']?.*?>(.*?)<\/a>)(.+)?(<\/em>)/ui", "/(<strong>)(.+)?(<a\s+(?:[^>]*?\s+)?href=[\"']?([^'\"]*)[\"']?.*?>(.*?)<\/a>)(.+)?(<\/strong>)/ui");
            array_push($subst, "$3", "$3");
            $str = preg_replace($re, $subst, $str);
            $str = strip_tags($str, $excluded_tags);
        }
        return $str;
    }

    /**
     * Get file info and send it to Telegram using the correct method
     */
}
?>