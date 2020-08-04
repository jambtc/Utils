<?php
/**
 * @author Sergio Casizzone
 */
class Utils {

    public function execInBackground($cmd) {
        if (substr(php_uname(), 0, 7) == "Windows"){
          pclose(popen("start /B ". $cmd, "r"));
        } else {
            $ssh = Yii::app()->phpseclib->createSSH2('localhost');
            if (!$ssh->login(Utils::getRootUser(), Utils::getRootPassword())) {
                $return['error'] = 'Login Failed';
                echo CJSON::encode($return);
                exit;
            }
            $action = $cmd . " > /dev/null &";
            $ssh->exec($action);
        }
    }

    public function getRootPassword(){
        $settings=Settings::load();
        return crypt::Decrypt($settings->sshpassword);
    }

    public function getRootUser(){
        $settings=Settings::load();
        return crypt::Decrypt($settings->sshuser);
    }


    public function strToHex($string){
        $hex = '';
        for ($i=0; $i<strlen($string); $i++){
            $ord = ord($string[$i]);
            $hexCode = dechex($ord);
            $hex .= substr('0'.$hexCode, -2);
        }
        return strToUpper($hex);
    }

    public function hexToStr($hex){
        $string='';
        for ($i=0; $i < strlen($hex)-1; $i+=2){
            $string .= chr(hexdec($hex[$i].$hex[$i+1]));
        }
        return $string;
    }


    public static function passwordGenerator($length = 10){
        $chars = array_merge(range(0,9), range('a','z'), range('A','Z'));
        shuffle($chars);
        return implode(array_slice($chars, 0, $length));
    }


    public function wrapDate($timestamp,$align = null){
        $r = '';
        $b = '</br>';

        $d = date("d/m/Y",$timestamp);
        $t = date("H:i:s",$timestamp);
        if ($align == 'center'){
            $r .= '<center>'.$d.$b.$t.'</center>';
        }else{
            $r .= $d.$b.$t;
        }
        return $r;
    }

    public function get_domain($host){
      $myhost = strtolower(trim($host));
      $count = substr_count($myhost, '.');
      if($count === 2){
        if(strlen(explode('.', $myhost)[1]) > 3) $myhost = explode('.', $myhost, 2)[1];
      } else if($count > 2){
        $myhost = self::get_domain(explode('.', $myhost, 2)[1]);
      }
      return $myhost;
    }

    /*
    * Recupera i country code in accordance with the ISO 3166-1 standard
    * e restituisce un array con la lista
    *
    * curl -L "https://datahub.io/core/country-list/r/0.json"
    */
    public function CountryDataset(){
        $url = 'https://datahub.io/core/country-list/r/0.json';

        $json = BTCPayWebRequest::request($url,[],"GET");

		$array = CJSON::decode($json);
		// echo "<pre>".print_r($json,true)."</pre>";
		// exit;

		if (isset($array))
        	foreach ($array as $field => $desc)
            	$country[$desc['Code']] = $desc['Name'];
		else
			$country = ['IT'=>'Italy'];

        return $country;
    }

    
}
?>
