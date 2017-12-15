<?php
/**
 * Notifcaster.com — sending and recieving notifcations using Telegram bot api.
 * @author Ameer Mousavi <ameer.ir>
 * forked from Notifygram by Anton Ilzheev <ilzheev@gmail.com>
 * Attention! $method always must be started with slash " / "
 */
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
        $api_token        = null,
        $url        = 'https://tg-notifcaster.rhcloud.com/api/v1',
        $api_method = null;
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
     *
     */
    public function _telegram($bot_token)
    {
        $this->url = 'https://api.telegram.org/bot'.$bot_token;
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
        $params = array(
            'api_token'  => $this->api_token,
            'msg'        => $msg
        );
        $this->api_method = "/selfMessage";
        $response = $this->make_request($params);
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
            'text'        => strip_tags($msg)
        );
        $this->api_method = "/sendMessage";
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
     * Request Function
     *
     * @param array $params
     * @param string $file_upload
     *
     * @return string "success" || error message
     */    
    protected function make_request(array $params = array(), $file_upload = false)
    {
        if (function_exists('curl_init')) {
            $curl = curl_init($this->url.$this->api_method);
            if (PHP_MAJOR_VERSION >= 5 && PHP_MINOR_VERSION >= 5){
                curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
            } 
            if ($file_upload) {
                if (class_exists('CURLFile')) {
                    $params['photo'] = new CURLFile($params['photo']);
                } else {
                    $params = $this->curl_custom_postfields($curl, array('chat_id'  => $params['chat_id'], 'caption' => $params['caption']), array('photo' => $params['photo']));
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
        } else {
            $context = stream_context_create(array(
                'http' => array(
                    'method' => 'POST',
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'content' => $post,
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
        array_walk($body, function (&$part) use ($boundary) {
            $part = "--{$boundary}\r\n{$part}";
        });

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
}
?>