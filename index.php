<?php
/*
Plugin Name: MobiKoB
Plugin URI: https://wordpress.org/plugins/mobikob/
Description: MobiKoB hesabınız ile Woocommerce müşterileriniz yeni sipariş verdiğinde, yeni kayıt olan müşterileriniz olduğunda ve toplu smslerde kişiye özel ve yöneticilere sms gönderebileceğiniz bir eklentidir. Bunun yanısıra kişiye özel toplu ve özel sms gönderebilir. Yeni kayıt olan müşterileriniz MobiKoB CRM'e  ekleyebilir, siparişlerin durumları değiştiğinde kargo takip kodu gibi bilgileri müşterilerinize otomatik olarak gönderebilirsiniz. Ayrıca woocommerce sisteine kayıt olan müşterilerinizi tek tuş ile uygun olan cihaz üzerinden arayabilirsiniz.
Author: sahratelekom
Author URI: www.sahratelekom.com
Version: 1.0.3

*/

/**
 * Copyright (c) 2018 Sahratelekom Tüm hakları saklıdır.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 *
 *
 * Sahra - Yeni Nesil Telekom Operatörü - www.sahratelekom.com.
 * Sahra - Toplu SMS - Başlıklı SMS - Sabit Telefon - Sanal Santral - 0850li Numara
 * **********************************************************************
 */
    if ( !defined( 'ABSPATH' ) ) exit;

    define( 'SAHRA_MOBIKOB_PLUGIN_CLASS_PATH', dirname(__FILE__). '/includes' );
    require_once SAHRA_MOBIKOB_PLUGIN_CLASS_PATH.'/sahrasms.php';
    require_once SAHRA_MOBIKOB_PLUGIN_CLASS_PATH.'/replacefunction.php';

    // @ini_set('log_errors','On');
    // @ini_set('display_errors','off');
    // @ini_set('error_log','phperrors.log'); // path to server-writable log file

    add_action("admin_menu", "sahra_addMenu");
    function sahra_addMenu()
    {
        add_menu_page("MobiKoB - Yeni Nesil Telekom Operatörü - www.sahratelekom.com.tr", "MobiKoB",'edit_pages', "sahra-wp-plugin", "sahra_mobikob_index",plugins_url( 'lib/image/sahraicon.png', __FILE__ ));
    }
    
function sahra_mobikob_plugin_name_get_version() {
    $plugin_data = get_plugin_data( __FILE__ );
    $plugin_version = $plugin_data['Version'];
    return $plugin_version;
}



    function sahra_mobikob_index()
    {
        require_once ('pages/index.php');
    }

    add_action( 'admin_enqueue_scripts', 'sahra_mobikob_loadcustomadminstyle' );
    function sahra_mobikob_loadcustomadminstyle($hook)
    {
        if($hook!= 'toplevel_page_sahra-wp-plugin') {
            return;
        }
        $plugin_url = plugin_dir_url( __FILE__ );
        wp_enqueue_style( 'bootstrap',      $plugin_url . 'lib/css/bootstrap.css' );
        wp_enqueue_style( 'font-awesome',   $plugin_url . 'lib/fonts/css/font-awesome.min.css' );
        wp_enqueue_style( 'style',         $plugin_url . 'lib/css/style.css' );
        wp_enqueue_style( 'sweetalert2',    $plugin_url . 'lib/js/sweetalert2/dist/sweetalert2.css' );
        wp_enqueue_style( 'dataTables',    $plugin_url . 'lib/css/bootstrap-table.min.css' );
    }

    add_action( 'admin_enqueue_scripts','sahra_mobikob_script');
    function sahra_mobikob_script()
    {
        //bootstrap-js YAPMALIYIZ
       wp_register_script('bootstrapminjs', plugins_url('bootstrap.min.js', dirname(__FILE__).'/lib/js/1/'));
       wp_enqueue_script('bootstrapminjs');

       wp_register_script('sweet2', plugins_url('sweetalert2.all.js', dirname(__FILE__).'/lib/js/sweetalert2/dist/1/'));
       wp_enqueue_script('sweet2');

        wp_register_script('table', plugins_url('bootstrap-table.min.js', dirname(__FILE__).'/lib/js/1/'));
        wp_enqueue_script('table');
    }

    function sahra_mobikob_settings_sanitize($input){
        $replace = new SahraMobikobReplaceFunction();
        $input = $replace->sahra_spaceTrim($input);
        return $input;
    }

    add_action('admin_init', 'sahra_mobikob_options');
    function sahra_mobikob_options()
    {
        register_setting('sahraoptions', 'sahra_user', 'sahra_mobikob_settings_sanitize');
        register_setting('sahraoptions', 'sahra_pass');
        register_setting('sahraoptions', 'sahra_domain');
        register_setting('sahraoptions', 'sahra_input_smstitle');

        register_setting('sahraoptions', 'sahra_newuser_to_admin_control');
        register_setting('sahraoptions', 'sahra_newuser_to_admin_no');
        register_setting('sahraoptions', 'sahra_newuser_to_admin_text');
        register_setting('sahraoptions', 'sahra_newuser_to_customer_control');
        register_setting('sahraoptions', 'sahra_newuser_to_customer_text');

        register_setting('sahraoptions', 'sahra_neworder_to_admin_control');
        register_setting('sahraoptions', 'sahra_neworder_to_admin_no');
        register_setting('sahraoptions', 'sahra_neworder_to_admin_text');
        register_setting('sahraoptions', 'sahra_neworder_to_customer_control');
        register_setting('sahraoptions', 'sahra_neworder_to_customer_text');

        register_setting('sahraoptions', 'sahra_newnote1_to_customer_control');
        register_setting('sahraoptions', 'sahra_newnote1_to_customer_text');

        register_setting('sahraoptions', 'sahra_newnote2_to_customer_control');
        register_setting('sahraoptions', 'sahra_newnote2_to_customer_text');

        register_setting('sahraoptions', 'sahra_order_refund_to_admin_control');
        register_setting('sahraoptions', 'sahra_order_refund_to_admin_no');
        register_setting('sahraoptions', 'sahra_order_refund_to_admin_text');


        register_setting('sahraoptions', 'sahra_product_waitlist1_control');
        register_setting('sahraoptions', 'sahra_product_waitlist1_text');

        // register_setting('sahraoptions', 'sahra_time_zone');
        
        register_setting('sahraoptions', 'sahra_rehber_control');
        register_setting('sahraoptions', 'sahra_rehber_groupname');

        register_setting('sahraoptions', 'sahra_orderstatus_change_customer_control');

        register_setting('sahraoptions', 'sahra_status');
        register_setting('sahraoptions', 'sahra_trChar');

        register_setting('sahraoptions', 'sahra_order_status_query_control');
        register_setting('sahraoptions', 'sahra_order_status_query_prefix');
        register_setting('sahraoptions', 'sahra_order_status_query_text');
        register_setting('sahraoptions', 'sahra_order_status_query_error_text');
        register_setting('sahraoptions', 'sahra_order_status_query_token');
        register_setting('sahraoptions', 'sahra_order_status_query_link');

        //JSON settings
        register_setting('sahraoptions', 'sahra_newuser_to_admin_json');
        register_setting('sahraoptions', 'sahra_newuser_to_customer_json');
        register_setting('sahraoptions', 'sahra_newnote1_to_customer_json');
        register_setting('sahraoptions', 'sahra_newnote2_to_customer_json');
        register_setting('sahraoptions', 'sahra_neworder_to_admin_json');
        register_setting('sahraoptions', 'sahra_neworder_to_customer_json');
        register_setting('sahraoptions', 'sahra_order_refund_to_admin_json');
        register_setting('sahraoptions', 'sahra_product_waitlist1_json');

        //OTP SMS
        register_setting('sahraoptions', 'sahra_tf2_auth_register_control');
        register_setting('sahraoptions', 'sahra_tf2_auth_register_text');
        register_setting('sahraoptions', 'sahra_tf2_auth_register_diff');

        //otp duplicate control
        register_setting('sahraoptions', 'sahra_tf2_auth_register_phone_control');
        register_setting('sahraoptions', 'sahra_tf2_auth_register_phone_warning_text');

        //contacts meta
        register_setting('sahraoptions', 'sahra_contact_meta_key');

        //roles
        register_setting('sahraoptions', 'sahra_auth_roles');
        register_setting('sahraoptions', 'sahra_auth_users');
        register_setting('sahraoptions', 'sahra_auth_roles_control');
        register_setting('sahraoptions', 'sahra_auth_users_control');

        register_setting('sahraoptions', 'sahra_phonenumber_zero1');
        register_setting('sahraoptions', 'sahra_licence_key_to_meta');

        //ÇIKIŞ
        register_setting('sahraoptionslogout', 'sahra_user');
        register_setting('sahraoptionslogout', 'sahra_pass');

        if(function_exists('wc_get_order_statuses')) {
            $order_statuses = wc_get_order_statuses();
            $arraykeys = array_keys($order_statuses);
            foreach ($arraykeys as $arraykey) {
                register_setting('sahraoptions','sahra_order_status_text_'.$arraykey);
                register_setting('sahraoptions','sahra_order_status_text_'.$arraykey.'_json');
            }
        }

        register_setting('sahraoptions','sahra_cf7_success_customer_control');
        register_setting('sahraoptions','sahra_cf7_success_admin_control');
        register_setting('sahraoptions','sahra_cf7_contact_control');

        register_setting('sahraoptions','sahra_cf7_to_admin_no');
        $cf7_list = apply_filters( 'sahra_contact_form_7_list', '');
        foreach ($cf7_list as $item) {
            register_setting('sahraoptions','sahra_cf7_list_text_success_customer_'.$item->ID);
            register_setting('sahraoptions','sahra_cf7_list_text_success_admin_'.$item->ID);
            register_setting('sahraoptions','sahra_cf7_list_contact_'.$item->ID);
            register_setting('sahraoptions','sahra_cf7_list_contact_firstname_'.$item->ID);
            register_setting('sahraoptions','sahra_cf7_list_contact_lastname_'.$item->ID);
            register_setting('sahraoptions','sahra_cf7_list_contact_other_'.$item->ID);
            register_setting('sahraoptions','sahra_cf7_list_text_error_'.$item->ID);
        }
    }

    

    function sahra_mobikob_getCustomSetting($key, $search){
        $settings  = esc_html(get_option($key));
        if ($settings != ''){
            $jsonData = stripslashes(html_entity_decode($settings));
            $object = json_decode($jsonData, true);
            if (isset($object[$search])){
                return $object[$search];
            } else {
                return '';
            }
        }
        return $settings;
    }

    add_action( 'admin_footer', 'sahra_ajaxRequest' );
    function sahra_ajaxRequest() { ?>
        <script type="text/javascript" >
            function sendBulkandPrivateSMS(number="") {
              
                var phone = document.getElementById('private_phone').value;
                var message = document.getElementById('private_text').value;
                if (phone =="" || message =="")
                {
                    swal('Mesaj göndermek için lütfen gerekli alanları doldurun.');
                    return;
                }
                document.getElementById('sendSMS').disabled = true;
                var data = {
                    'action': 'sahra_sendsms',
                    'phone': phone,
                    'message':message
                };
                jQuery.post(ajaxurl, data, function(response) {
                   var obje = JSON.parse(response);
                  
                        if(obje.durum==1){
                            document.getElementById('private_phone').value="";
                            document.getElementById('private_text').value="";
                            swal({
                                title: "BAŞARILI!",
                                html: obje.mesaj,
                                type: 'success'
                            });
                        }
                        else{
                            swal({
                                title: "HATA! Kod : "+ obje.kod,
                                text: obje.mesaj,
                                type: 'error'
                            });
                        }
                    document.getElementById('sendSMS').disabled = false;
                });
                
            }

         


            function sahra_sendSMS_bulkTab(id="") {
                document.getElementById('bulkSMSbtn').disabled = true;
                var users = [];
                var numberstext;
                if (id!=""){
                    users = id; numberstext="numarasına";
                }
                else {
                    var table = jQuery('#table');
                    var sonuc = table.bootstrapTable('getAllSelections');
                    for (var i=0 ; i<sonuc.length ; i++)
                    {
                        users += sonuc[i][1]+",";
                    }
                    numberstext="numaralarına";
                }

                var mark='<mark class="bulkmark" onclick="varfill2(this.innerHTML);">';
                var variables = mark+'[musteri_adi]</mark> &nbsp'+mark+'[musteri_soyadi]</mark> &nbsp'+mark+'[musteri_telefonu]</mark> &nbsp'+mark+'[musteri_epostasi]</mark> &nbsp'+mark+'[kullanici_adi]</mark>&nbsp'+mark+'[tarih]</mark>&nbsp'+mark+'[saat]</mark>';
                if(users!="") {
                    swal({
                        title: "Mesaj",
                        input: 'textarea',
                        inputPlaceholder:'Mesaj İçeriğini buraya giriniz.',
                        confirmButtonText : 'Gönder',
                        cancelButtonText : 'İptal',
                        confirmButtonColor : '#3085d6',
                        cancelButtonColor : '#E74C3C',
                        width: 650,
                        showCancelButton: true,
                        showLoaderOnConfirm: true,
                        footer: '<i class="fa fa-angle-double-right"></i> Kullanabileceğiniz Değişkenler : ' + variables,
                        preConfirm: function (text) {
                            return new Promise(function (resolve, reject) {
                                if (text) {
                                    var message = text;
                                    var data = {
                                        'action': 'sahra_sendSMS_bulkTab',
                                        'users': users,
                                        'message':message
                                    };
                                    jQuery.post(ajaxurl, data, function (response) {
                                        
                                        var obje = JSON.parse(response);
                                        
                                        if (obje.durum == 1) {
                                            swal({
                                                title: "BAŞARILI!",
                                                html: 'Mesajlar başarılı bir şekilde gönderildi. Sonuçları görmek için lütfen <a href = "https://<?php echo esc_html(get_option('sahra_domain'))?>.mobikob.com/1/pages/sms" target="_blank" > tıklayınız </a> ',
                                                type: 'success'
                                            });
                                        }
                                        else{
                                            swal({
                                                title: "HATA! Kod : "+ obje.kod,
                                                text: obje.mesaj,
                                                type: 'error'
                                            });
                                        }
                                    });
                                } else {
                                    swal({
                                        title: "Mesaj içeriğini boş bıraktınız.",
                                        text: "Lütfen sms göndermek için birşeyler yazın.",
                                        type: 'error',
                                    });
                                }
                                document.getElementById('bulkSMSbtn').disabled = false;
                            })
                        }
                    })
                }
                else {
                    swal('Mesaj göndermek için müşteri seçmelisiniz.');
                    return;
                }
            }
            function sahra_MakeCall(userID="") {
          
            
            document.getElementById('sahra_call').disabled = true;
             
                var data = {
                                        'action': 'sahra_MakeCall',
                                       
                                       
                                    };
                                    
                jQuery.post(ajaxurl, data, function (response) {
                
                   
                    var obje1 = JSON.parse(response);
                   
                    if (Object.keys(obje1["result"]).length) {
                
                        var html = "<div>";
                        obje1["result"].forEach(item => {
                            
                            var agent = item.agent.toLowerCase();
                        
                        if (!agent.includes("jssip")&& !item.agent.includes('JsSIP') && !item.agent.includes('jssıp')){
                                html+=`<input type="button" style="border-radius: 6px;"  
                                        onclick="make_call_with_devices('${item.user_contact}','${userID}');" class="btn btn-primary btn-lg" value ="${item.agent}"></input> <br><br>`
                        }
                        
                        
                        } )
                    html+= "</div>";
                        swal({
                        //   title: "BAŞARILI!",
                            html :html,
                            showConfirmButton:false,
                            showCancelButton:true,
                            cancelButtonColor : '#E74C3C',
                            cancelButtonText : 'İptal',
                            
                        });
                    }else if (obje1["result"]=='error') {
                    swal({
                        title: "HATA!",
                        text:  obje1['resultmsg'],
                        type: obje1["result"]
                    });
                }
                    
                                    
                })

                
                                
                                document.getElementById('sahra_call').disabled = false;
                            
}
                   
   

            function sendSMSglobal(phone) {
                if(!jQuery.isNumeric(phone)){
                    swal({
                        title: "Uyarı!",
                        text: 'Sadece telefon numaralarına SMS gönderilebilir.',
                        type: 'error'
                    });
                    return false;
                }
                if(phone!="") {
                    swal({
                        title: "Mesaj",
                        input: 'textarea',
                        inputPlaceholder:'Mesaj İçeriğini buraya giriniz.',
                        confirmButtonText : 'Gönder',
                        cancelButtonText : 'İptal',
                        confirmButtonColor : '#3085d6',
                        cancelButtonColor : '#E74C3C',
                        width: 650,
                        showCancelButton: true,
                        showLoaderOnConfirm: true,
                        footer: 'Kayıtlı olmayan bir numaraya sms atıyorsunuz.',
                        preConfirm: function (text) {
                            return new Promise(function (resolve, reject) {
                                if (text) {
                                    var message = text;
                                    var data = {
                                        'action': 'sahra_sendsms',
                                        'phone': phone,
                                        'message':message
                                    };
                                    jQuery.post(ajaxurl, data, function (response) {
                                        var obje = JSON.parse(response);
                                        console.log(obje)
                                        if (obje.durum == 1) {
                                            swal({
                                                title: "BAŞARILI!",
                                                html: obje.mesaj,
                                                type: 'success'
                                            });
                                        }
                                        else{
                                            swal({
                                                title: "HATA! Kod : "+ obje.kod,
                                                text: obje.mesaj,
                                                type: 'error'
                                            });
                                        }
                                    });
                                } else {
                                    swal({
                                        title: "Mesaj içeriğini boş bıraktınız.",
                                        text: "Lütfen sms göndermek için birşeyler yazın.",
                                        type: 'error',
                                    });
                                }
                                document.getElementById('bulkSMSbtn').disabled = false;
                            })
                        }
                    })
                }
                else {
                    swal('Mesaj göndermek için müşteri seçmelisiniz.');
                    return;
                }
            }
            function varfill2(degisken) {
                var textarea = document.getElementsByClassName('swal2-textarea')[0];
                var start = textarea.selectionStart;
                var end = textarea.selectionEnd;
                var finText = textarea.value.substring(0, start) + degisken + textarea.value.substring(end);
                textarea.value = finText;
                textarea.focus();
                textarea.selectionEnd= end + (degisken.length);
            }



            
            function make_call_with_devices(user_contact,userID)
{
    
                                    var data = {
                                        'action': 'make_call_with_devices',
                                        'userID': userID,
                                        'contact':user_contact,
                                    };
                jQuery.post(ajaxurl, data, function (response) {
                    var obje1 = JSON.parse(response);
                
                            if (obje1['result']=='success') {
                                //resolve();
                                swal({
                                    title: "BAŞARILI!",
                                    text: obje1['resultmsg'],
                                    type: obje1["result"],
                                });
                            }
                            else
                            {
                                swal({
                                    title: "HATA!",
                                    text:  obje1['resultmsg'],
                                    type: obje1["result"],
                                });
                            }
                        })

                  
            
}

            jQuery(document).keyup(function(e) {
                if (e.keyCode == 27) {
                    hideLoadingMessage();
                    errorControl = 1;
                    xhr.abort();
                }
            });
            function add_crm_one_person(userID)
{
    showLoadingMessage('Kayıt aktarılıyor...');
                                    var data = {
                                        'action': 'add_crm_one_person',
                                        'userID': userID,
                                      
                                    };
                                    jQuery.post(ajaxurl, data, function (response) {
                    var response = JSON.parse(response);
                    if (response['result']=='success') {
                        swal({
                            title: "BAŞARILI!",
                            html: response['resultmsg'],
                            type: response["result"],
                        });
                    }
                    else
                    {
                        swal({
                            title: "HATA!",
                            text:  response['resultmsg'],
                            type: response["result"],
                        });
                    }
                    hideLoadingMessage();
                });

                  
            
}

            jQuery('#contactsMove').click(function () {
                var groupName = jQuery('#contactsBulk').val();
                if(groupName == ''){
                    swal({
                        title: "BAŞARISIZ!",
                        html: 'Aktarım için grup ismi girilmelidir.',
                        type: 'error',
                    });
                    return false;
                }
                showLoadingMessage('Kayıtlar aktarılıyor...');
                var data = {
                    'action': 'sahra_addtoGroup',
                    'copy' :1,
                    'groupName':groupName
                };
                jQuery.post(ajaxurl, data, function (response) {
                    var response = JSON.parse(response);
                    if (response['result']=='success') {
                        swal({
                            title: "BAŞARILI!",
                            html: response['resultmsg'],
                            type: response["result"],
                        });
                    }
                    else
                    {
                        swal({
                            title: "HATA!",
                            text:  response['resultmsg'],
                            type: response["result"],
                        });
                    }
                    hideLoadingMessage();
                });

            })


        </script> <?php
    }

    add_action( 'wp_ajax_sahra_addtoGroup', 'sahra_addtoGroup' );
    function sahra_addtoGroup()
    {
        $users = get_users();
        
        
        $phone_meta = 'billing_phone';
        if (get_option("sahra_contact_meta_key") != ''){
            $phone_meta = get_option("sahra_contact_meta_key");
        }
       
        $contacts = []; $tmp = [];
        foreach ($users as $result) {
            $tmp['phone'] = get_user_meta( $result->ID, $phone_meta, true );
            if ($tmp['phone'] == ''){
                continue;
            }
            $tmp['name'] =$result->first_name." ". $result->last_name ;
            $tmp['email'] = $result->billing_email;
            $tmp['crm_source'] = "woocommerce";
            $tmp['card_type'] = "person";
         
                array_push($contacts, $tmp);
          
        }
      
        $sahra = new SahraMobikobSms(get_option("sahra_domain"),get_option("sahra_user"), get_option("sahra_pass"), get_option('sahra_input_smstitle'), get_option("sahra_trChar"));
  
        $result = $sahra->addCrm($contacts);
        $add_person_length = count($result->data);
        
        if ( $add_person_length == count($contacts) ){
            $json = ['result'=>'success','resultmsg'=>'Toplam <b>'.$add_person_length.'</b> müşteri   MOBIKOB CRM eklendi.'];
        }elseif($add_person_length != 0){
            $size = count($contacts)- $add_person_length;
            $json = ['result'=>'success','resultmsg'=> 'Toplam <b>'.$add_person_length.'</b> müşteri   MOBIKOB CRM eklendi. 
            Toplam : <b>'.$size.'</b> Müşteri MOBIKOB CRM kaydı olduğundan eklenemedi'];
        }else{
            $json = ['result'=>'error','resultmsg'=> 'Herhangi bir müşteri eklenemedi lütfen destek@sahratelekom veya 03124732787 numaralı telefondan destek isteyiniz'];
        }
        echo json_encode($json);
        wp_die();
    }
    add_action( 'wp_ajax_add_crm_one_person', 'add_crm_one_person' );
    function add_crm_one_person()
    {
        $users =explode(',',rtrim(sanitize_text_field($_POST['userID']),','));
      
     
        $contacts = []; $tmp = [];
        foreach ($users as $userID){
            $user = get_user_meta( $userID, $phone_meta, true );
            $tmp['phone'] = $user["billing_phone"][0];
            if ($tmp['phone'] == ''){
                continue;
            }
            $tmp['name'] =$user["first_name"][0]." ". $user["last_name"][0] ;
            $tmp['email'] = $user["billing_email"][0];
            $tmp['crm_source'] = "woocommerce";
            $tmp['card_type'] = "person";
          

            array_push($contacts, $tmp);

        }

   
        $sahra = new SahraMobikobSms(get_option("sahra_domain"),get_option("sahra_user"), get_option("sahra_pass"), get_option('sahra_input_smstitle'), get_option("sahra_trChar"));
        
        $result = $sahra->addCrm($contacts);
        if ($result){
            $add_person_length = count($result->data);
        
            if ( $add_person_length == count($contacts) ){
                $json = ['result'=>'success','resultmsg'=>'Toplam <b>'.$add_person_length.'</b> müşteri   MOBIKOB CRM eklendi.'];
            }elseif($add_person_length != 0){
                $size = count($contacts)- $add_person_length;
                $json = ['result'=>'success','resultmsg'=> 'Toplam <b>'.$add_person_length.'</b> müşteri   MOBIKOB CRM eklendi. 
                Toplam : <b>'.$size.'</b> Müşteri MOBIKOB CRM kaydı olduğundan eklenemedi']; 
            }     
        }else{
            $json = ['result'=>'error','resultmsg'=> 'Herhangi bir müşteri eklenemedi lütfen destek@sahratelekom veya 03124732787 numaralı telefondan destek isteyiniz'];
        }
        echo json_encode($json);
        wp_die();
    }
   
    add_filter( 'sahra_contact_form_7_list', 'sahra_wpcf7_form_list' );
    function sahra_wpcf7_form_list($arg='') {
        $list = get_posts(array(
            'post_type'     => 'wpcf7_contact_form',
            'numberposts'   => -1
        ));

        return  $list;
    }




    add_action( 'wp_ajax_sahra_sendSMS_bulkTab', 'sahra_sendSMS_bulkTab');
    function sahra_sendSMS_bulkTab() {
        $phones = "";
        $json   = array();
        $phone_meta = 'billing_phone';
        if (get_option("sahra_contact_meta_key") != ''){
            $phone_meta = get_option("sahra_contact_meta_key");
        }
        if(isset($_POST['users']) && isset($_POST['message'])) {
            $users      = explode(',',rtrim(sanitize_text_field($_POST['users']),','));
           
            $replace    = new SahraMobikobReplaceFunction();
            foreach ($users as $userID) {
                $phones    .= $replace->sahra_spaceTrim(get_user_meta( $userID, $phone_meta, true )).",";
                $userinfo   = get_userdata($userID);
                $data       = array('first_name'=> $userinfo->first_name,   'last_name'=> $userinfo->last_name,
                                    'user_login'=> $userinfo->user_login,   'phone'=>get_user_meta($userID, $phone_meta, true),
                                    'user_email' =>$userinfo->user_email,   'message'=> sanitize_text_field($_POST['message']));
                $message = $replace->sahra_replace_bulksms($data);
                $message = $replace->sahra_replace_date($message);
               
            }
            
            //$phones = rtrim($phones,',');
            $sahra = new SahraMobikobSms(get_option("sahra_domain"),get_option("sahra_user"), get_option("sahra_pass"), get_option('sahra_input_smstitle'), get_option("sahra_trChar"));
            $status_code = $sahra->sendSMS( $data['phone'], $message);
           
            if($status_code==200)
            {
                $json['durum'] = '1';
                $json['mesaj'] = 'Smsler başarılı bir şekilde gönderildi.';
            }
            else{
                $json['durum'] = '0';
                $json['mesaj'] = 'Sms gönderimi başarısız. Lütfen sahra hesabınıza giriş yaptığınıza ve başlık seçtiğinize emin olun.' ;
            }
        }
        else {
            $json['durum'] = '0';
            $json['mesaj'] = 'Sms gönderimi başarısız.';
        }
        echo json_encode($json);
      
        
        wp_die();
    }

    

    add_action( 'wp_ajax_sahra_MakeCall', 'sahra_MakeCall' );
    function sahra_MakeCall(){
     
        $json = array();
       $sahra = new SahraMobikobSms(get_option("sahra_domain"),get_option("sahra_user"), get_option("sahra_pass"), get_option('sahra_input_smstitle'), get_option("sahra_trChar"));
        $result = $sahra->get_active_agent_mobikob();
        
        if($result)
        {
            $json['result'] = $result;
            $json['resultmsg'] = '';
        }
        else{
            $json['result'] = 'error';
            $json['resultmsg'] = 'MOBIKOB sistemine kayıtlı herhangi bir cihazınız bulunmamaktadır' ;
        }
    
  
    
        echo json_encode($json);
       
        wp_die();
    }
    add_action( 'wp_ajax_make_call_with_devices', 'make_call_with_devices' );
    function make_call_with_devices(){
        
       
        $json = array();
        $sahra = new SahraMobikobSms(get_option("sahra_domain"),get_option("sahra_user"), get_option("sahra_pass"), get_option('sahra_input_smstitle'), get_option("sahra_trChar"));
        $users =explode(',',rtrim(sanitize_text_field($_POST['userID']),','));
      
        foreach ($users as $userID){
            $user = get_user_meta( $userID, $phone_meta, true );
        }
       
        if ( count($user["billing_phone"])>0){
            $result = $sahra->make_call( sanitize_text_field($_POST['contact']), sanitize_text_field($user["billing_phone"][0]));
            
            if($result == 200)
            {
                
                $json['result'] = "success";
                $json['resultmsg'] = 'Arıyor';
              
            }
            else{
                $json['result'] = 'error';
                $json['resultmsg'] = 'Arama Yapılırken bir problem oluştu' ;
            }
        }else{
            $json['result'] = 'error';
            $json['resultmsg'] = 'Kullanıcının sistemde telefon numarasının olduğundan emin olunuz' ;
        }
        
      
    
     
    
    echo json_encode($json);
    wp_die();
    }

    add_action( 'wp_ajax_sahra_sendsms', 'sahra_sendsms' );
    function sahra_sendsms() {
       
       

        $json = array();
        $replace = new SahraMobikobReplaceFunction();
        if(isset($_POST['phone']) && isset($_POST['message'])){
            $sahra = new SahraMobikobSms(get_option("sahra_domain"),get_option("sahra_user"),get_option("sahra_pass"),get_option('sahra_input_smstitle'), get_option("sahra_trChar"));
            $status_code = $sahra->sendSMS(sanitize_text_field($_POST['phone']), sanitize_text_field($_POST['message']));

            if($status_code==200)
            {
                $json['durum'] = '1';
                $json['mesaj'] = 'Smsler başarılı bir şekilde gönderildi.';
            }
            else{
                $json['durum'] = '0';
                $json['mesaj'] = 'Sms gönderimi başarısız. Lütfen sahra hesabınıza giriş yaptığınıza ve başlık seçtiğinize emin olun.' ;
            }
          
        }

        else {
            $json['durum'] = '0';
            $json['mesaj'] = 'Sms gönderimi başarısız.';
        }
        echo json_encode($json);
     
        wp_die();
    
    }

    function sahra_newcustomer_control()
    {
        $newuser1       = esc_html(get_option("sahra_newuser_to_admin_control"));
        $newuser2       = esc_html(get_option("sahra_newuser_to_customer_control"));
        $newuser3       = esc_html(get_option("sahra_rehber_control"));
        $newuser4       = esc_html(get_option("sahra_tf2_auth_register_control"));
        
        $control        = 0;
        if(isset($newuser1) && !empty($newuser1) && $newuser1==1) {
            $control=1;
        }
        elseif(isset($newuser2) && !empty($newuser2) && $newuser2==1) {
            $control=2;
        }
        elseif(isset($newuser3) && !empty($newuser3) && $newuser3==1) {
            $control=3;
        }
        elseif(isset($newuser4) && !empty($newuser4) && $newuser4==1) {
            $control = 4;
        }
        return $control;
    }



    add_action( 'woocommerce_register_form', 'sahra_ajaxRegister' );
    function sahra_ajaxRegister() {
        $sahra_phonenumber_zero  = esc_html(get_option("sahra_phonenumber_zero1"));
        ?>
        <script type="text/javascript" >
            function sendtf2Code(phone) {
                var firstname = jQuery('#first_name').val();
                var lastname = jQuery('#last_name').val();
                var email = jQuery("input[name*='email']").val();

                var error = '';
                if(firstname == '') {
                    error += '> İsim girilmedi.\n';
                }
                if(lastname == '') {
                    error += '> Soyisim girilmedi.\n';
                }
                if(email == '') {
                    error += '> E-mail adresi girilmedi.\n';
                }
                if(phone == '') {
                    error += '> Telefon numarası girilmedi.\n';
                }

                <?php
                if ($sahra_phonenumber_zero==1){
                    ?>
                if(phone.slice( 0,1 ) !='0'){
                    error += '> Telefon numarası 0 ile başlamalıdır.\n';
                }
                <?php
                }
                ?>

                if (error != ''){
                    alert('Aşağıdaki hatalar alındı : \n\n'+ error);
                    return false;
                }

                var data = {
                    'action': 'sahra_sendtf2SMS',
                    'phone': phone,
                    'first_name': firstname,
                    'last_name': lastname,
                    'email':email
                };

                jQuery.post(ajaxurl, data, function(response) {
                    var endChar = response.substring(response.length-1);
                    if (endChar == '0'){
                        response = response.substring(0,(response.length-1));
                    }
                    var obje = JSON.parse(response);
                    if(obje.status == 'success'){
                        alert('Lütfen '+obje.phone+' numaralı telefonunuza gelen güvenlik kodunu giriniz.');
                        jQuery('#tf2Codealert').html('*Lütfen '+obje.phone+' numaralı telefonunuza gelen '+obje.refno+' referans numaralı güvenlik kodunu giriniz.');
                        document.getElementById("tf2Code").focus();
                        jQuery('#sendCode').prop('disabled', true);
                    } else {
                        if (obje.status == 'error' && obje.state == 1){
                            alert(obje.data + ' Lütfen site yöneticisi ile iletişime geçin.');
                        } else if(obje.status == 'error' && obje.state == 2) {
                            alert(obje.data);
                        }else if(obje.status == 'error' && obje.state == 4){
                            alert(obje.data);
                        }else if(obje.status == 'error' && obje.state == 5){
                            alert(obje.data);
                        } else {
                            alert('Bilinmeyen bir hata oluştu. GSM numarası girdiğinize emin olun. Sorunun devam etmesi halinde lütfen site yöneticisi ile iletişime geçin.');
                        }
                    }
                });
            }
            </script>
        <?php
    }

    add_action( 'wp_ajax_sahra_sendtf2SMS', 'sahra_sendtf2SMS' );
    function sahra_sendtf2SMS(){
        $authKey = rand(10000, 99999);
        $refno = substr(md5(uniqid()),0,5);
        $phone = sanitize_text_field($_POST['phone']);
        ltrim($phone, '0');
        $replace = new SahraMobikobReplaceFunction();

        $phone_control = get_option('sahra_tf2_auth_register_phone_control');
        if($phone_control == 1){
            $args = array (
                'order' => 'DESC',
                'orderby' => 'ID',
                'number'=>1,    //limit
                'meta_query' => array(
                    'relation' => 'OR',
                    array(
                        'key'     => 'billing_phone',
                        'value'   => sanitize_text_field($_POST['phone']),
                        'compare' => 'LIKE'
                    )
                )
            );
            $query = new WP_User_Query($args);
            $results = $query->get_results();
            if (is_array($results) && $results != null && isset($results[0])){  // kayıtlı numara var
                $phone_warning_text = get_option('sahra_tf2_auth_register_phone_warning_text');
                $newVar = [];
                $oldVar = [];
                array_push($oldVar, '[telefon_no]');
                array_push($oldVar, '[ad]');
                array_push($oldVar, '[soyad]');
                array_push($oldVar, '[mail]');

                $newVar['telefon_no'] = sanitize_text_field($_POST['phone']);
                $newVar['ad'] = sanitize_text_field($_POST['first_name']);
                $newVar['soyad'] = sanitize_text_field($_POST['last_name']);
                $newVar['mail'] = sanitize_text_field($_POST['email']);

                $phone_warning_text = $replace->sahra_replace_array($oldVar, $newVar, $phone_warning_text);
                echo json_encode(['status'=>'error','state'=>'5', 'data'=>$phone_warning_text]);
                die;
            } else {
            }
        }

        $sahra_status  = esc_html(get_option("sahra_status"));
        if(isset($sahra_status) && !empty($sahra_status) && $sahra_status==1 ){
            if ($phone != '' && in_array(strlen($phone),[10,11])){
                $otpregister_control = esc_html(get_option("sahra_tf2_auth_register_control"));
                if ($otpregister_control == 1){
                    $first_name = sanitize_text_field($_POST['first_name']);
                    $last_name = sanitize_text_field($_POST['last_name']);
                    $email = sanitize_text_field($_POST['email']);

                    $code = get_post_meta(1, $phone.'_2fa', true);
                   
                    
                    if ($code != ''){
                        $sendTime = get_post_meta(1, $phone.'_2fa_time', true);
                        $sendTime = strtotime($sendTime);
                        $now =  time();
                        $diff = $now - $sendTime;

                        $savedDiff  = esc_html(get_option("sahra_tf2_auth_register_diff"));
                        if ($savedDiff == '' || is_int($savedDiff)){
                            $savedDiff = 180;
                        }
                        if ($diff > $savedDiff * 60) {
                            update_post_meta( 1, $phone.'_2fa', $authKey );
                            update_post_meta( 1, $phone.'_2fa_time',  date('Y-m-d H:i:s', time()) );
                            update_post_meta( 1, $phone.'_2fa_ref',  $refno );
                        } else {
                            $savedRefNo = get_post_meta(1, $phone.'_2fa_ref', true);
                            echo json_encode(['status'=>'error','state'=>4, 'data'=>'Daha önce '.$phone.' numarasına '.$savedRefNo.' referans numaralı doğrulama kodu gönderilmiş. Lütfen gönderilmiş güvenlik kodunu girin.'.' Gönderme zamanı: '.date('d.m.Y H:i', $sendTime), 'phone'=>$phone]);
                            die;
                        }
                    } else {
                        add_post_meta(1, $phone.'_2fa', $authKey, '');
                        add_post_meta(1, $phone.'_2fa_time', date('Y-m-d H:i:s', time()));
                        add_post_meta(1, $phone.'_2fa_ref', $refno);
                    }

                    $data  = array(
                            'first_name'=> $first_name,
                            'last_name'=> $last_name,
                            'phone'=>$phone,
                            'user_email' =>$email,
                            'otpcode'=> $authKey,
                            'refno' => $refno,
                            'message'=> (get_option("sahra_tf2_auth_register_text"))
                    );
                    $message = $replace->sahra_replace_twofactorauth_text($data);
                    $message = $replace->sahra_replace_date($message);

                    $sahra = new SahraMobikobSms(get_option("sahra_domain"),get_option("sahra_user"),get_option("sahra_pass"),get_option('sahra_input_smstitle'), get_option("sahra_trChar"));

                    $result = $sahra->sendOTPSMS($phone, $message);

                    if ($result['kod'] == '00'){
                        echo json_encode(['status'=>'success', 'data'=>'Doğrulama gönderildi.', 'phone'=>$phone, 'refno'=>$refno]);
                    } else {
                        update_post_meta( 1, $phone.'_2fa_time',  '1970-01-01 12:12:12' );
                        echo json_encode(['status'=>'error', 'state'=>'1', 'data'=>'Doğrulama kodu gönderilemedi. '.$result->mesaj]);
                    }
                } else {
                    echo json_encode(['status'=>'error','state'=>'3', 'data'=>'OTP kontrol açık değil.']);
                }
            } else {
                echo json_encode(['status'=>'error','state'=>'2', 'data'=>'Telefon numarası hatalı']);
            }
        }
    }

    add_action('wp_enqueue_scripts', 'sahra_admin_scripts');
    add_action('wp_ajax_nopriv_sahra_sendtf2SMS', 'sahra_sendtf2SMS');
    function sahra_admin_scripts() {
        $sahra_status  = esc_html(get_option("sahra_status"));
        if(isset($sahra_status) && !empty($sahra_status) && $sahra_status==1 ){
            $otpregister_control = esc_html(get_option("sahra_tf2_auth_register_control"));
            if ($otpregister_control == 1){
                wp_enqueue_script( 'script', plugins_url('ajax.js', dirname(__FILE__).'/lib/js/1/'), array('jquery'), '1.0', true );
                wp_localize_script('script', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
            }
        }
    }

    add_filter( 'woocommerce_process_registration_errors', 'sahra_custom_registration_errors', 10, 1 ); //telefon numarası girilmemişse
    function sahra_custom_registration_errors( $validation_error) {
        $sahra_status  = esc_html(get_option("sahra_status"));
        $billing_phone  = sanitize_text_field($_POST['billing_phone']);
        $first_name     = sanitize_text_field($_POST['first_name']);
        $last_name      = sanitize_text_field($_POST['last_name']);
        $control        = sahra_newcustomer_control();

        $sahra_status  = esc_html(get_option("sahra_status"));
        $otpstatus = false;
        if(isset($sahra_status) && !empty($sahra_status) && $sahra_status==1 ) {
            $otpregister_control = esc_html(get_option("sahra_tf2_auth_register_control"));
            if ($otpregister_control == 1) {
                $otpstatus = true;
            }
        }

        $tf2Code = '';
        $code = '';
        if ($otpstatus){
            $tf2Code        = sanitize_text_field($_POST['tf2Code']);
            $code = get_post_meta(1, $billing_phone.'_2fa', true);
        }
        $sahra_phonenumber_zero  = esc_html(get_option("sahra_phonenumber_zero1"));
        $delete = true;
        if($code != $tf2Code && $otpstatus==true ){
            $validation_error->add('tf2Code_error', __('<strong></strong>Doğrulama kodunu yanlış girdiniz!', 'mydomain'));
            $delete = false;
        } else {
            if(isset($sahra_status) && !empty($sahra_status) && $sahra_status==1 && $control!=0)
            {
                if(isset($first_name) && empty($first_name) || trim($first_name) == ''){
                    $validation_error->add('first_name_error', __('<strong></strong>Adınızı giriniz.', 'mydomain'));
                    $delete = false;
                }
                if(isset($last_name) && empty($last_name) || trim($last_name) == ''){
                    $validation_error->add('last_name_error', __('<strong></strong>Soyadınızı giriniz.', 'mydomain'));
                    $delete = false;
                }
                if(isset($billing_phone) && empty($billing_phone) || trim($billing_phone) == ''){
                    $validation_error->add('billing_phone_error', __('<strong></strong>Telefon numaranızı giriniz.', 'mydomain'));
                    $delete = false;
                }
                if ($sahra_phonenumber_zero == 1){
                    if (substr($billing_phone, 0, 1) != 0){
                        $validation_error->add('billing_phone_error', __('<strong></strong>Telefon numaranızın başında sıfır olmalıdır.', 'mydomain'));
                        $delete = false;
                    }
                }
            }
        }
        if ($delete = true){
            delete_post_meta(1, $billing_phone.'_2fa', $code);
            delete_post_meta(1, $billing_phone.'_2fa_time');
            delete_post_meta(1, $billing_phone.'_2fa_ref');
        }

        return $validation_error;
    }

    add_action( 'woocommerce_created_customer', 'sahra_newcustomer', 10, 3 );
    function sahra_newcustomer($customer_id){

        $newuser1       = esc_html(get_option("sahra_newuser_to_admin_control"));
        $newuser2       = esc_html(get_option("sahra_newuser_to_customer_control"));
        $newuser3       = esc_html(get_option("sahra_rehber_control"));
        $control        = sahra_newcustomer_control();
        $billing_phone  = "";
        $billing_phone  = sanitize_text_field($_POST['billing_phone']);

        if (isset($_POST['first_name'])){
            $first_name= sanitize_text_field($_POST['first_name']);
        }else {
            $first_name= sanitize_text_field($_POST['billing_first_name']);
        }

        if (isset($_POST['last_name'])){
            $last_name      = sanitize_text_field($_POST['last_name']);
        }else {
            $last_name      = sanitize_text_field($_POST['billing_last_name']);
        }

        $sahra_status  = esc_html(get_option("sahra_status"));
        $replace        = new SahraMobikobReplaceFunction();


        if(isset($sahra_status) && !empty($sahra_status) && $sahra_status==1 && $control!=0){
            update_user_meta($customer_id, 'billing_phone', $billing_phone);
            update_user_meta($customer_id, 'first_name', $first_name);
            update_user_meta($customer_id, 'last_name', $last_name);

            $custom_settings_admin = sahra_mobikob_getCustomSetting('sahra_newuser_to_admin_json', '_timecondition');
            $custom_settings_customer = sahra_mobikob_getCustomSetting('sahra_newuser_to_customer_json', '_timecondition');

            /*if(empty($first_name)){
                update_user_meta($customer_id, 'first_name', sanitize_text_field($_POST['billing_first_name']), '');
            }
            if (empty($last_name)){
                update_user_meta($customer_id, 'last_name', sanitize_text_field($_POST['billing_last_name']), '');
            }*/
            $userinfo       = get_userdata($customer_id);
            if (isset($newuser1) && !empty($newuser1) && $newuser1 == 1) {   //admine mesaj
                $phone      = esc_html(get_option('sahra_newuser_to_admin_no'));
                $data       = array('first_name'=> $first_name,   'last_name'=> $last_name,
                                    'user_login'=> $userinfo->user_login,   'phone'=>$billing_phone, 'user_email' =>$userinfo->user_email,
                                    'message'=> (get_option('sahra_newuser_to_admin_text')));
                $message    = $replace->sahra_replace_newuser_to_text($data);
                $message = $replace->sahra_replace_date($message);

                sahra_sendSMS_oneToMany($phone, $message, ['startDate'=>$custom_settings_admin]);
            }
            if (isset($newuser2) && !empty($newuser2) && $newuser2 == 1) {   //müşteriye mesaj
                $data       = array('first_name'=> $first_name,   'last_name'=> $last_name,
                                    'user_login'=> $userinfo->user_login,   'phone'=>$billing_phone, 'user_email' =>$userinfo->user_email,
                                    'message'=> (get_option('sahra_newuser_to_customer_text')));
                $message    = $replace->sahra_replace_newuser_to_text($data);
                $message = $replace->sahra_replace_date($message);
                sahra_sendSMS_oneToMany($billing_phone, $message, ['startDate'=>$custom_settings_customer]);
            }
            if (isset($newuser3) && !empty($newuser3) && $newuser3 == 1) {   //rehbere kayıt
                sahra_add_contact($customer_id);
            }
        }
    }

    add_action('lmfwc_event_post_order_license_keys','sahra_new_licance', 10,1);
    function sahra_new_licance($id)
    {
        $sahra_status = esc_html(get_option("sahra_status"));
        $sahra_licence_key_to_meta  = esc_html(get_option("sahra_licence_key_to_meta"));
        if(isset($sahra_status) && !empty($sahra_status) && $sahra_status==1 && $sahra_licence_key_to_meta == 1 ) {
            $licences = [];
            foreach ($id['licenses'] as $item) {
                $licence = apply_filters('lmfwc_decrypt', $item->getLicenseKey());
                array_push($licences, $licence);
            }
            $keys = implode(' , ', $licences);

            add_post_meta($id['orderId'], '_licence_keys', $keys, '');
        }
    }

    //payment_complete ve thankyou kancalarının çalışmaması durumlarında özel kanca çalıştır(ayarlanmalı.)
    $sahra_new_order_custom_action = sahra_mobikob_getCustomSetting('sahra_neworder_to_admin_json', '_otherAction');

    if ( $sahra_new_order_custom_action != '' ){
        add_action( $sahra_new_order_custom_action, 'sahra_new_order_custom_action', 10,3 );
    } else {
        add_action( 'woocommerce_payment_complete', 'sahra_new_order_send_sms', 10,1 );   //yeni siparişte sms gönder
        add_action( 'woocommerce_thankyou', 'sahra_new_order_send_sms', 10,1 );   //yeni siparişte sms gönder
    }
    function sahra_new_order_custom_action( $order_id, $data, $order){
        sahra_new_order_send_sms($order_id);
    }

    add_action( 'wp_insert_post', 'sahra_new_order_admin_panel', 10, 1 );
    function sahra_new_order_admin_panel( $post_id ) {
        if (function_exists('wc_get_order')){
            $order = wc_get_order( $post_id );
            if (method_exists((object)$order, 'get_billing_phone') && $order->get_billing_phone() != '') {
                $custom_settings_admin = sahra_mobikob_getCustomSetting('sahra_neworder_to_admin_json', '_addOrderAdminPanel');
                $custom_settings_customer = sahra_mobikob_getCustomSetting('sahra_neworder_to_customer_json', '_addOrderAdminPanel');
                if($custom_settings_admin == 1){
                    sahra_new_order_send_sms($post_id, 1);
                }
                if($custom_settings_customer == 1){
                    sahra_new_order_send_sms($post_id, 2);
                }
            }
        }
    }

    function sahra_new_order_send_sms($order_id, $adminpanel = 0){
        if( get_post_meta( $order_id, '_new_order_sahra', true ) && $adminpanel==0 ){   // eğer daha önce bu sipariş zaten oluşmuşsa bu fonksiyon dursun
            return; // sipariş zaten alınmış.
        }
        add_post_meta($order_id, '_new_order_sahra', 'yes', '');


        if(function_exists('wc_get_order')) {
            $order2 = wc_get_order( $order_id );
            $items = $order2->get_items();
            $products_info ="";
            foreach ($items as $item) {
                $products_info .= $item->get_name()."(".$item->get_subtotal().'TLx'.$item->get_quantity()."), ";
            }
            $products_info = rtrim($products_info,' ,');
        }
        else{
            $products_info="";
        }
        $neworder1       = esc_html(get_option("sahra_neworder_to_admin_control"));
        $neworder2       = esc_html(get_option("sahra_neworder_to_customer_control"));
        $control         = sahra_neworder_control();
        $sahra_status   = esc_html(get_option("sahra_status"));
        $order           = new WC_Order( $order_id );
        $userinfo        = get_userdata($order->customer_id);
        $replace         = new SahraMobikobReplaceFunction();
        $custom_settings_admin = sahra_mobikob_getCustomSetting('sahra_neworder_to_admin_json', '_timecondition');
        $custom_settings_customer = sahra_mobikob_getCustomSetting('sahra_neworder_to_customer_json', '_timecondition');
        $custom_settings_customer_private_phone_key = sahra_mobikob_getCustomSetting('sahra_neworder_to_customer_json', '_custom_phone_key');


        if(isset($sahra_status) && !empty($sahra_status) && $sahra_status==1 && $control!=0) {
            if (isset($neworder1) && !empty($neworder1) && $neworder1 == 1 && (in_array($adminpanel, [0,1]))) {   //admine mesaj
                $phone      = esc_html(get_option('sahra_neworder_to_admin_no'));
                $username = explode('@', $order->billing_email);
                $data   = array(
                            'order_id'=>$order_id,
                            'total'=>$order->total,
                            'first_name'=> $order->billing_first_name,
                            'last_name'=> $order->billing_last_name,
                            'user_login'=> $userinfo->user_login,
                            'phone'=>$order->billing_phone,
                            'user_email' =>$order->billing_email,
                            'items' => $products_info,
                            'message'=> (get_option('sahra_neworder_to_admin_text'))
                );
                $message = $replace->sahra_replace_neworder_to_text($data);
                $message = $replace->sahra_replace_order_meta_datas($order, $message);
                $metadatas = get_post_meta($order_id);
                $message = $replace->sahra_replace_order_meta_datas2($metadatas, $message);
                $message = $replace->sahra_replace_order_add_datas($order, $message, 'data', '[data:');
                $message = $replace->sahra_replace_date($message);
                sahra_sendSMS_oneToMany($phone, $message, ['startDate'=>$custom_settings_admin]);
            }

            if (isset($neworder2) && !empty($neworder2) && $neworder2 == 1  && (in_array($adminpanel, [0,2]))) {   //müşteriye mesaj
                $order = wc_get_order( $order_id );
                $phone_key = 'billing_phone';
                if ($custom_settings_customer_private_phone_key != ''){
                    $phone_key = $custom_settings_customer_private_phone_key;
                }
                $sendsmsphone = '';
                if (isset($order->{$phone_key})){
                    $sendsmsphone = $order->{$phone_key};
                }
                if ($sendsmsphone == '' || !is_numeric($sendsmsphone)){
                    $sendsmsphone = $order->billing_phone;
                }
                $data   = array(
                            'order_id'=>$order_id,
                            'total'=>$order->total,
                            'first_name'=> $order->billing_first_name,
                            'last_name'=> $order->billing_last_name,
                            'user_login'=> $userinfo->user_login,
                            'phone'=> $sendsmsphone,
                            'user_email' =>$order->billing_email,
                            'items' => $products_info,
                            'message'=> (get_option('sahra_neworder_to_customer_text'))
                );
                $message    = $replace->sahra_replace_neworder_to_text($data);
                $message = $replace->sahra_replace_order_meta_datas($order, $message);
                $metadatas = get_post_meta($order_id);
                $message = $replace->sahra_replace_order_meta_datas2($metadatas, $message);
                $message = $replace->sahra_replace_order_add_datas($order, $message, 'data', '[data:');
                $message = $replace->sahra_replace_date($message);
                sahra_sendSMS_oneToMany($sendsmsphone, $message,['startDate'=>$custom_settings_customer]);
            }
        }
    }

                                                        //120---            cancelled---            completed
    function sahra_order_status_changed( $this_get_id, $this_status_transition_from, $this_status_transition_to, $instance ) {
        sahra_order_status_changed_sendSMS($this_get_id, 'sahra_order_status_text_wc-'.$this_status_transition_to, $this_status_transition_to);

    };
    add_action( 'woocommerce_order_status_changed', 'sahra_order_status_changed', 10, 4 );

    add_action('woocommerce_order_status_cancelled', 'sahra_order_status_cancelled');
    function sahra_order_status_cancelled($order_id)
    {
        $control         = esc_html(get_option("sahra_order_refund_to_admin_control"));
        $message         = (get_option("sahra_order_refund_to_admin_text"));
        $phones          = esc_html(get_option("sahra_order_refund_to_admin_no"));
        $sahra_status   = esc_html(get_option("sahra_status"));
        $replace         = new SahraMobikobReplaceFunction();
        if(isset($sahra_status) && !empty($sahra_status) && $sahra_status==1) {
            if (isset($control) && !empty($control) && $control == 1) {
                if (isset($message) && !empty($message)) {
                    $custom_settings_cancelled_timecondition = sahra_mobikob_getCustomSetting('sahra_order_refund_to_admin_json', '_timecondition');

                    $order           = new WC_Order( $order_id );
                    $userinfo        = get_userdata($order->customer_id);
                    $data       = array('order_id'=>$order_id, 'first_name'=> $userinfo->first_name,   'last_name'=> $userinfo->last_name,
                        'user_login'=> $userinfo->user_login,   'phone'=> $order->billing_phone, 'user_email' =>$userinfo->user_email,
                        'message'=> $message);
                    $message    = $replace->sahra_replace_order_status_changes($data);
                    $message = $replace->sahra_replace_order_meta_datas($order, $message);
                    $metadatas = get_post_meta($order_id);
                    $message = $replace->sahra_replace_order_meta_datas2($metadatas, $message);
                    $message = $replace->sahra_replace_date($message);
                    sahra_sendSMS_oneToMany($phones, $message, ['startDate'=>$custom_settings_cancelled_timecondition]);
                }
            }
        }
      
    }

    add_filter( 'woocommerce_customer_save_address', 'sahra_customereditaddres', 10, 2 );  //fatura adresi değişip telefon numnarası girildiyse rehbere ekler
    function sahra_customereditaddres($user_id){
        sahra_add_contact($user_id);
    }

    function sahra_sendSMS_oneToMany($phone, $message, $settings=[])
    {
        $replace = new SahraMobikobReplaceFunction();
        $json = array();
        if(isset($phone) && isset($message) && !empty($phone) && !empty($message)){
            $sahra = new SahraMobikobSms(get_option("sahra_domain"),get_option("sahra_user"), get_option("sahra_pass"), get_option("sahra_input_smstitle"), get_option("sahra_trChar"));
            if (isset($settings['startDate']) && $settings['startDate'] != '' && is_numeric(intval($settings['startDate']))){
                $sahra->setStartDate(date('Y-m-d H:i', current_time( 'timestamp' ) + ($settings['startDate']*60)));
            }
            $json = $sahra->sendSMS($replace->sahra_spaceTrim($phone), $message);
        }
        else {
            $json['durum'] = '0';
            $json['mesaj'] = 'Sms gönderimi başarısız.';
        }
        return json_encode($json);
    }

    function sahra_neworder_control()
    {
        $neworder1       = esc_html(get_option("sahra_neworder_to_admin_control"));
        $neworder2       = esc_html(get_option("sahra_neworder_to_customer_control"));
        $control         = 0;
        if(isset($neworder1) && !empty($neworder1) && $neworder1==1) {
            $control=1;
        }
        elseif(isset($neworder2) && !empty($neworder2) && $neworder2==1) {
            $control=2;
        }
        return $control;
    }

    function sahra_add_contact($customer_id)
    {
        $grupcontrol    = esc_html(get_option('sahra_rehber_control'));
      
        $userinfo       = get_userdata($customer_id);
        $contacts = []; $tmp = [];
        if(isset($grupcontrol) && !empty($grupcontrol) && $grupcontrol==1) {
            $sahra = new SahraMobikobSms(get_option("sahra_domain"),get_option("sahra_user"), get_option("sahra_pass"));
         
        
            $tmp['phone'] = $userinfo->billing_phone;
          
             $tmp['name'] = $userinfo->first_name." ".$userinfo->last_name;
             $tmp['email'] = $userinfo->user_email;
             $tmp['crm_source'] = "woocommerce";
             $tmp['card_type'] = "person";
         
             array_push($contacts, $tmp);
          
        
             $sahra->addCrm($contacts);
        }
    }

    function sahra_order_status_changed_sendSMS($order_id, $text, $this_status_transition_to)
    {
        $control         = esc_html(get_option("sahra_orderstatus_change_customer_control"));
        $message         = (get_option($text));
        $sahra_status   = esc_html(get_option("sahra_status"));
        $replace         = new SahraMobikobReplaceFunction();


        if(isset($sahra_status) && !empty($sahra_status) && $sahra_status==1) {
            if (isset($control) && !empty($control) && $control == 1) {
                if (isset($message) && !empty($message)) {
                    $order           = new WC_Order( $order_id );
                    $userinfo        = get_userdata($order->customer_id);
                    $trackingCode = '';
                    $trackingCompany = '';
                    foreach ($order->meta_data as $meta_datum) {
                        if ($meta_datum->key == 'kargo_takip_no'){
                            $trackingCode = $meta_datum->value;
                        }
                        if ($meta_datum->key == 'kargo_firmasi'){
                            $trackingCompany = $meta_datum->value;
                        }
                    }
                    if ((isset($userinfo->user_login) && $userinfo->user_login != '')){
                        $user_login = $userinfo->user_login;
                    } else {
                        $user_login = $order->shipping_first_name.$order->shipping_last_name;
                    }


                    $custom_settings_changed_timecondition = sahra_mobikob_getCustomSetting('sahra_order_status_text_wc-'.$this_status_transition_to.'_json', '_timecondition');
                    $custom_settings_customer_private_phone_key = sahra_mobikob_getCustomSetting('sahra_order_status_text_wc-'.$this_status_transition_to.'_json', '_custom_phone_key');

                    $phone_key = 'billing_phone';
                    if ($custom_settings_customer_private_phone_key != ''){
                        $phone_key = $custom_settings_customer_private_phone_key;
                    }
                    $sendsmsphone='';
                    if (isset($order->{$phone_key})){
                        $sendsmsphone = $order->{$phone_key};
                    }
                    if ($sendsmsphone == '' || !is_numeric($sendsmsphone)){
                        $sendsmsphone = $order->billing_phone;
                    }

                    $data       = array('order_id'=>$order_id, 'first_name'=> $order->shipping_first_name,   'last_name'=> $order->shipping_last_name,
                        'user_login'=> $user_login,   'phone'=> $sendsmsphone, 'user_email' =>$order->billing_email,
                        'message'=> $message, 'trackingCompany'=>$replace->sahra_replace_shipping_company($trackingCompany), 'trackingCode'=>$trackingCode);
                    $message    = $replace->sahra_replace_order_status_changes($data);
                    $message = $replace->sahra_replace_order_meta_datas($order, $message);
                    $metadatas = get_post_meta($order_id);
                    $message = $replace->sahra_replace_order_meta_datas2($metadatas, $message);
                    $message = $replace->sahra_replace_order_add_datas($order, $message, 'data', '[data:');
                    $message = $replace->sahra_replace_date($message);
                    sahra_sendSMS_oneToMany($sendsmsphone, $message, ['startDate'=>$custom_settings_changed_timecondition]);
                }
            }
        }
    }

    add_action( 'woocommerce_new_order_note_data', 'sahra_new_order_note_data', 10, 2 );
    function sahra_new_order_note_data( $args, $args2 ) {
      

        $order_id = $args['comment_post_ID'];
        $type = $args2['is_customer_note'];

        $note = $args['comment_content'];

        $status_note1 = esc_html(get_option("sahra_newnote1_to_customer_control"));
        $status_note2 = esc_html(get_option("sahra_newnote2_to_customer_control"));

        if (!empty($status_note1) && $status_note1==1 && $type == 0){   //özel sms
            $customermessage = esc_html(get_option("sahra_newnote1_to_customer_text"));
            $options = 'sahra_newnote1_to_customer_json';
            sahra_new_order_note_sendSMS($order_id, $type, $note, $customermessage, $options);
        }

        if (!empty($status_note2) && $status_note2==1 && $type == 1){ //müşteriye sms
            $customermessage = esc_html(get_option("sahra_newnote2_to_customer_text"));
            $options = 'sahra_newnote2_to_customer_json';
            sahra_new_order_note_sendSMS($order_id, $type, $note, $customermessage, $options);
        }
        return $args;
    }


function sahra_new_order_note_sendSMS($order_id, $note_type, $note, $customermessage, $optionskey)
    {
        $sahra_status   = esc_html(get_option("sahra_status"));
        $replace         = new SahraMobikobReplaceFunction();
        if(isset($sahra_status) && !empty($sahra_status) && $sahra_status==1) {
            if (isset($customermessage) && !empty($customermessage)) {
                $order           = new WC_Order( $order_id );
                $userinfo        = get_userdata($order->customer_id);

                if ((isset($userinfo->user_login) && $userinfo->user_login != '')){
                    $user_login = $userinfo->user_login;
                } else {
                    $user_login = $order->shipping_first_name.$order->shipping_last_name;
                }

                $custom_settings_changed_timecondition = sahra_mobikob_getCustomSetting($optionskey, '_timecondition');
                $custom_settings_customer_private_phone_key = sahra_mobikob_getCustomSetting($optionskey, '_custom_phone_key');

                $phone_key = 'billing_phone';
                if ($custom_settings_customer_private_phone_key != ''){
                    $phone_key = $custom_settings_customer_private_phone_key;
                }
                $sendsmsphone='';
                if (isset($order->{$phone_key})){
                    $sendsmsphone = $order->{$phone_key};
                }
                if ($sendsmsphone == '' || !is_numeric($sendsmsphone)){
                    $sendsmsphone = $order->billing_phone;
                }

                $data       = array('order_id'=>$order_id, 'first_name'=> $order->billing_first_name,   'last_name'=> $order->billing_last_name,
                    'user_login'=> $user_login,   'phone'=> $sendsmsphone, 'user_email' =>$order->billing_email, 'note'=> $note,
                    'message'=> $customermessage, 'total'=>$order->total);
                $message    = $replace->sahra_replace_add_note($data);
                $message = $replace->sahra_replace_date($message);
                sahra_sendSMS_oneToMany($sendsmsphone, $message, ['startDate'=>$custom_settings_changed_timecondition]);
            }
        }
    }

 



    /*
     * Normal ürünlerin stok değişimini dinleyerek stoğa girmesi durumunda waitlist listesine SMS gönderir.
     */
    function sahra_product_set_stock( $product ) {
        sahra_set_stock_trigger($product);
    };
    add_action( 'woocommerce_product_set_stock', 'sahra_product_set_stock', 10, 1 );

    /*
     * Varyasyonlu ürünlerin stok değişimini dinleyerek stoğa girmesi durumunda waitlist listesine SMS gönderir.
     */
    function sahra_product_set_stock_variation($product){
        sahra_set_stock_trigger($product);
    }
    add_action( 'woocommerce_variation_set_stock', 'sahra_product_set_stock_variation', 10, 1 );

    /*
     * Stok değişikliğini dinleyerek bekleme listesine sms gönderimini tetikleyecek fonksiyon
     */
    function sahra_set_stock_trigger($product)
    {
        $productId  = $product->get_data()['id'];
       // $limit = wc_get_low_stock_amount( $product );
       // $new_stock = $product->get_changes()['stock_quantity'];
       // $old_stock = $product->get_data()['stock_quantity'];

        if (isset($product->get_changes()['stock_status']) && $product->get_changes()['stock_status']=='instock'){
            if (class_exists('Pie_WCWL_Waitlist')) {
                $waitlist = new Pie_WCWL_Waitlist($product);
                $users = $waitlist->waitlist;
                foreach ($users as $customerId => $date) {
                    sahra_waitlist_send_sms($customerId, $productId);
                }
            }
        }
    }

    /*
     * waitlist listesine mail gönderildiğinde SMS de gönderir.
     */
    function sahra_waitlist_push($customerId, $productId)
    {
        sahra_waitlist_send_sms($customerId, $productId);
    }
    add_action( 'wcwl_mailout_send_email', 'sahra_waitlist_push', 10, 2 );

    /*
     * Bekleme listelerine SMS gönderimi sağlayan fonksiyondur.
     */
    function sahra_waitlist_send_sms($customerId,$productId)
    {
        $control         = esc_html(get_option("sahra_product_waitlist1_control"));
        $message         = esc_html(get_option("sahra_product_waitlist1_text"));;
        $sahra_status   = esc_html(get_option("sahra_status"));
        $replace         = new SahraMobikobReplaceFunction();
        $product =  wc_get_product($productId);
        if(isset($sahra_status) && !empty($sahra_status) && $sahra_status==1) {
            if (isset($control) && !empty($control) && $control == 1) {
                if (isset($message) && !empty($message)) {
                    $customer = new WC_Customer($customerId);
                    $product =  wc_get_product($productId);


                    $custom_settings_changed_timecondition = sahra_mobikob_getCustomSetting('sahra_product_waitlist1_json', '_timecondition');
                    $custom_settings_customer_private_phone_key = sahra_mobikob_getCustomSetting('sahra_product_waitlist1_json', '_custom_phone_key');

                    $phone_key = 'billing_phone';
                    if ($custom_settings_customer_private_phone_key != ''){
                        $phone_key = $custom_settings_customer_private_phone_key;
                    }

                    $sendsmsphone='';
                    if ($customer->{$phone_key} != ''){
                        $sendsmsphone = $customer->{$phone_key};
                    }

                    if ($sendsmsphone == '' || !is_numeric($sendsmsphone)){
                        $sendsmsphone = $customer->billing_phone;
                    }

                    $newVar = [];
                    $oldVar = [];
                    array_push($oldVar, '[musteri_adi]');
                    array_push($oldVar, '[musteri_soyadi]');
                    array_push($oldVar, '[musteri_telefonu]');
                    array_push($oldVar, '[musteri_epostasi]');
                    array_push($oldVar, '[kullanici_adi]');
                    array_push($oldVar, '[urun_kodu]');
                    array_push($oldVar, '[urun_adi]');
                    array_push($oldVar, '[stok_miktari]');

                    $newVar['musteri_adi'] = sanitize_text_field($customer->first_name);
                    $newVar['musteri_soyadi'] = sanitize_text_field($customer->last_name);
                    $newVar['musteri_telefonu'] = sanitize_text_field($customer->billing_phone);
                    $newVar['musteri_epostasi'] = sanitize_text_field($customer->email);
                    $newVar['kullanici_adi'] = sanitize_text_field($customer->display_name);
                    $newVar['urun_kodu'] = sanitize_text_field($product->sku);
                    $newVar['urun_adi'] = sanitize_text_field($product->name);
                    $newVar['stok_miktari'] = sanitize_text_field($product->stock);

                    $message = $replace->sahra_replace_array($oldVar, $newVar, $message);

                    $message = $replace->sahra_meta_data_replace($customer->data, $message, '[meta_user:');
                    $message = $replace->sahra_meta_data_replace($product->get_data(), $message, '[meta_product:');
                    $message = $replace->sahra_replace_date($message);
                    sahra_sendSMS_oneToMany($sendsmsphone, $message, ['startDate'=>$custom_settings_changed_timecondition]);
                }
            }
        }
    }

  

    /*
     * WC API için gerekli işlemler
     */
    add_filter( 'woocommerce_rest_api_get_rest_namespaces', 'woo_sahra_custom_api' );
    function woo_sahra_custom_api( $controllers ) {
        $controllers['wc/v3']['custom'] = 'WC_REST_Custom_Controller';
        $controllers['wc/v3']['test'] = 'WC_REST_Custom_Controller';

        return $controllers;
    }

    require_once 'wc-sahra-api.php';

