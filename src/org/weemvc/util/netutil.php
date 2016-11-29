<?php
namespace org\weemvc\util;

class NetUtil {
  //http://wezfurlong.org/blog/2006/nov/http-post-from-php-without-curl/
  static public function request($url, $params = null, $usePost = false){
    $cparams = array(
      //NOTICE: no matter to use http or https, always write http here
      'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => $usePost ? 'POST' : 'GET',
        'ignore_errors' => true
      )
    );
    if (isset($params)){
      if(is_array($params)){
        $params = http_build_query($params);
      }
      $cparams['http']['content'] = $params;
    }
    $context = stream_context_create($cparams);
    $fp = fopen($url, 'rb', false, $context);
    if($fp){
      // If you're trying to troubleshoot problems, try uncommenting the next two lines;
      // it will show you the HTTP response headers across all the redirects:
      // $meta = stream_get_meta_data($fp);
      // var_dump($meta['wrapper_data']);
      $result = stream_get_contents($fp);
    }else{
      $result = false;
    }
    if (!$result) {
      throw new Exception("{$url} failed");
    }
    return $result;
  }

  static public function curlRequest($url, $params = null, $type = 'GET', $useJson = false){
    $result = null;
    if(is_callable('curl_init')){
      // Setup cURL
      $ch = curl_init();
      // The site we'll be sending the data to.
      curl_setopt($ch, CURLOPT_URL, $url);
      // http://php.net/manual/en/function.curl-setopt.php
      // GET POST DELETE CONNECT ...
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
      // Tell cURL that we want to send a POST request.
      // curl_setopt($ch, CURLOPT_POST, 1);
      // Attach our POST data.
      if (isset($params)){
        if($useJson){
          curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
          $params = json_encode($params);
        }else{
          if(is_array($params)){
            $params = http_build_query($params);
          }
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
      }
      // Tell cURL that we want to receive the response that the site
      // gives us after it receives our request.
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      // Finally, send the request.
      $result = curl_exec($ch);
      // Close the cURL session
      curl_close($ch);
    }
    return $result;
  }

}