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
                $return['error'] = 'Login to localhost server failed';
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

    /**
     * Generate a password of x length
     * If you set $Strong to true also special chars are used
     * @param number $length
     * @param boolean $strong
     *
     * @return password
     */
    public static function passwordGenerator($length = 10, $strong = null){
      $chars = array_merge(
        range(0,9),
        range('a','z'),
        range('A','Z'),
        ($strong === null) ? array() : array_merge(
          range(chr(33),chr(38)),
          range(chr(40),chr(59)),
          range(chr(61),chr(61)),
          range(chr(63),chr(126))
        )
      );
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
}
?>
