<?php

class ChirpSeo_Auth
{
  private $key = false;

  function __construct($content = "")
  {
    $API  = new PerchAPI(1.0, 'chirp_seo');

    $Settings = $API->get('Settings');
    $key = $Settings->get('chirp_seo_license_key')->val();
    $this->key = $key;
  }

  public function check() {
    if (!$this->key) {
      echo '<div class="notification notification-warning">' . PerchLang::get("Enter your Chirp license key in %s Settings %s", '<a href="' . PERCH_LOGINPATH . '/core/settings">', '</a>') . '</div>';

      exit;
    } else if (!$this->activate()) {
      echo '<div class="notification notification-warning">' . PerchLang::get("Sorry, your Chirp license key isn't valid for this domain. Log into your %sChirp account%s and add the following as your live, staging or testing domain: %s", '<a href="https://grabachirp.com/account"></a>', '</a>', '<code>'.PerchUtil::html($_SERVER['SERVER_NAME']).'</code>') . '</div>';

      exit;
    }

  }

  private function activate()
  {
      /*
          Any attempt to circumvent activation invalidates your license.
      */
      $Perch  = PerchAdmin::fetch();

      if (PerchSession::get("chirp_seo_activated")) {
        return PerchSession::get("chirp_seo_activated");
      } else {
        $host = 'grabachirp.com';
        $path = '/activate/';
        $url = 'https://' . $host . $path;

        $data = array();
        $data['key']     = $this->key;
        $data['host']    = $_SERVER['SERVER_NAME'];
        $content = http_build_query($data);

        $result = false;
        $use_curl = false;
        if (function_exists('curl_init')) $use_curl = true;

        if ($use_curl) {
            PerchUtil::debug('Activating via CURL');
            $ch 	= curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_TIMEOUT, 10);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
      $result = curl_exec($ch);
      PerchUtil::debug($result);
      $http_status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
      if ($http_status!=200) {
          $result = false;
          PerchUtil::debug('Not HTTP 200: '.$http_status);
      }
      curl_close($ch);
        }else{
            if (function_exists('fsockopen')) {
                PerchUtil::debug('Activating via sockets');
                $fp = fsockopen($host, 80, $errno, $errstr, 10);
                if ($fp) {
                    $out = "POST $path HTTP/1.1\r\n";
                    $out .= "Host: $host\r\n";
                    $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
                    $out .= "Content-Length: " . strlen($content) . "\r\n";
                    $out .= "Connection: Close\r\n\r\n";
                    $out .= $content. "\r\n";

                    fwrite($fp, $out);
                    stream_set_timeout($fp, 10);
                    while (!feof($fp)) {
                        $result .=  fgets($fp, 128);
                    }
                    fclose($fp);
                }

                if ($result!='') {
                    $parts = preg_split('/[\n\r]{4}/', $result);
                    if (is_array($parts)) {
                        $result = $parts[1];
                    }
                }
            }
        }

        if ($result) {
            $json = PerchUtil::json_safe_decode($result);
            if (is_object($json) && $json->result == 'SUCCESS') {
                PerchUtil::debug($json);
                PerchUtil::debug('Chirp Activation: success');
                PerchSession::set("chirp_seo_activated", true);
                return true;
            }else{
                PerchUtil::debug('Chirp Activation: failed');
                PerchSession::set("chirp_seo_activated", false);
                return false;
            }
        }

        return true;
      }
  }
}
