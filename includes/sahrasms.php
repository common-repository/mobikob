<?php

class SahraMobikobSms
{
    private $domain;
    private $username;
    private $password;
    private $title;
    private $language = '';
    private $startDate;


    public function __construct($domain,$username,$password,$title='', $TR_Char='') {     //WP USE
        $this->username = trim($username);
        $this->password = $password;
        $this->title = $title;
        $this->domain = $domain;

        $this->language = '';
        if ($TR_Char == 1){
            $this->language = "dil='TR'";
        }
    }

    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }
    public function getStartDate()
    {
        if ($this->startDate == ''){
            return '';
        }
        return date('dmYHi', strtotime($this->startDate));
    }

    public function sendSMS($phone,$mesaj){       //WP USE
        $phones = [];
       
        $exp = explode(',',$phone);
        foreach ($exp as $phone){
           if($phone!=''){
               $phones[] = "90".substr($phone,-10) ;
           }
        }
     
        $api_url     = "https://$this->domain.mobikob.com/sms/bulk/api/";
        $api_user     = $this->username; // user@mail (profilinizden öğrenebilirsiniz)
        $api_pass     = $this->password; // Mobikob parolanız
        $head         =  $this->title; // onaylanmış başlıklarınızdan biri
        $to           =$phone; // 905000000000 formatında
        $msg          = $mesaj; // göndermek istediğiniz mesaj 
        $msgs = [];
        foreach ($phones as $phone){
            if($phone!=''){
                array_push($msgs , array( 'to'=> $phone,
                'msg'      => $msg));
               
            }
         }

        
        $payload = array(
            'api_user' => $api_user,
            'api_pass' => $api_pass,
            'head' => $head ,
            'messages' => $msgs,);
       
        $args = array(
                'body'=>json_encode($payload),
                'headers' => array(
                'Content-Type'=>'application/json',
                'Authorization' => 'Basic '. base64_encode($this->username.':'.$this->password)
                )
            );
         
            $response = wp_remote_post( $api_url, $args );
          
            return wp_remote_retrieve_response_code( $response );
       
        
       
    }

    public function addCrm($data)
    {
        $api_url ="https://$this->domain.mobikob.com/crm/crm_customer/bulk/api/?ignore_error=true";
       

        $args = array(
            'body'=>json_encode($data),
            'headers' => array(
            'Content-Type'=>'application/json',
            'Authorization' => 'Basic '. base64_encode($this->username.':'.$this->password)
            )
        );
     
        $response = wp_remote_post( $api_url, $args );
      
        return json_decode(wp_remote_retrieve_body( $response ));
    
    }

    /*
     * otp sms gönderimi
     */
    public function sendOTPSMS($phone, $message){
     
    }





    public function getSmsBaslik(){     //WP USE
        $url_did = "https://$this->domain.mobikob.com/pbx/trunk/api/?columns=did";
        $result_did = $this->getRequestResult($url_did);
        $url_head = "https://$this->domain.mobikob.com/sms/head/api/?columns=head&head_status=True";
        $result_head = $this->getRequestResult($url_head);
        $result_from =[];

        if ($result_head){
            $arrhead = $result_head;
            foreach($arrhead as $key => $value) {
                foreach($value as $val){
                    array_push($result_from,$val);
                };
            }
        }
        if ( $result_did){
            $arrdid = $result_did;
          
            foreach($arrdid as $key => $value) {
                foreach($value as $val){
                    array_push($result_from,$value->did);
                }
               
            }
        }
     
        return $result_from;
     
    }
    public function getRequestResult($url){


        $args = array(
            'headers' => array(
            'Content-Type'=>'application/json',
            'Authorization' => 'Basic '. base64_encode($this->username.':'.$this->password)
            )
        );
     
        $response = wp_remote_get( $url, $args );
        return json_decode(wp_remote_retrieve_body( $response ));
       
    }




    public function sahra_GetKredi($domain,$user,$pass) {     //WP USE
        $response = wp_remote_get("https://$domain.mobikob.com/sms/balance/?api_user=$user&api_pass=$pass" );
        $body = json_decode(wp_remote_retrieve_body( $response ));
        if($body->balance) {
            if($body->balance > 10)
                $tip = 'SMS';
            else
                $tip = 'Kredi';
            if ($body->balance) {
                $result = array('giris'=>'success', 'durum' => 'success', 'mesaj'=>'', 'kredi' =>$body->balance, 'tipmsj' => ' SMS Bakiye');
            } else {
                $result = array('giris'=>'success', 'durum' => 'warning', 'mesaj'=>' Bakiye Satın Al',  'kredi' => $body->balance,  'tipmsj' => ' Kalan '.$tip);
            }
        }
        else{
            $result = array('giris'=>'error', 'durum'=>'error', 'mesaj'=>'Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin olmadığını gösterir.');
        }
        return $result;
      
    }

  

    public function sahra_GirisSorgula($domain,$user,$pass)    //WP USE
    {
        $response = $this->sahra_GetKredi($domain,$user,$pass);

        if($response['giris']=='success') {     //Giriş Başarılı
            if($response['durum']=='success') {  //kredi varsa
                $result = array('durum' => 'info',
                    'mesaj' => $response['tipmsj']." : ".$response['kredi'],
                    'btnkontrol'=>'enabled',
                    'href'=>'' );
            }
            
        }
        else { // giriş başarısız
            if(empty($domain) ){
                $message = " Domain alanı boş.";
            }
            elseif(empty($user) && !empty($pass))
                $message = " Kullanıcı adı alanı boş.";
            elseif (empty($pass) && !empty($user))
                $message = " Şifre alanı boş.";
            elseif (empty($user) && empty($pass))
                $message = " Kullanıcı adı & şifre boş.";
            else {
                $message = " Kullanıcı adı veya şifreniz hatalı.";
            }
            $result = array('durum' => 'danger',
                'icon' => 'fa-exclamation-triangle',
                'mesaj' => $message,
                'btnkontrol' => 'disabled',
                'href' => '');
        }
        return json_encode($result);
    }

   

    public function get_active_agent_mobikob(){
        
        $api_url  = "https://$this->domain.mobikob.com/authentications/user/me/agent/active/fetch/";
    
        $result = $this->getRequestResult($api_url);
        return $result;
    }


    public function make_call($contact,$phone){
        $api_url ="https://$this->domain.mobikob.com/fs_management/xmlrpc/originate/user_contact/$contact/outbound_number/$phone/bridge/";
        $args = array(
            'headers' => array(
            'Content-Type'=>'application/json',
            'Authorization' => 'Basic '. base64_encode($this->username.':'.$this->password)
            )
        );
     
        $response = wp_remote_get( $api_url, $args );
        return wp_remote_retrieve_response_code( $response );
     
    }


}

