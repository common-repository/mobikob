<?php
if ( !defined( 'ABSPATH' ) ) exit;
    $cf7_list = apply_filters( 'sahra_contact_form_7_list', '');

    $sahra = new SahraMobikobSms(get_option("sahra_domain"),get_option("sahra_user"),get_option("sahra_pass"),get_option("sahra_input_smstitle"));
    $cevap = json_decode($sahra->sahra_GirisSorgula(get_option("sahra_domain"),get_option("sahra_user"),get_option("sahra_pass")));
    $sessionid = get_current_user_id();
    $session = new WP_User($sessionid);

    $fps_roles = new WP_Roles();
    $role_list = $fps_roles->get_names();

    $auth_roles = [];
    if (get_option('sahra_auth_roles') != ''){
        $auth_roles = explode(',', get_option('sahra_auth_roles'));
    }

    $auth_users = [];
    if (get_option('sahra_auth_users') != ''){
        $auth_users = explode(',', get_option('sahra_auth_users'));
    }

    $sahra_auth_roles_control= get_option('sahra_auth_roles_control');
    $sahra_auth_users_control= get_option('sahra_auth_users_control');

    $users = get_users();

    //yetkilendirme ile ilgili geliştirmeler 16.07.2021
    $cntrl = false;
    $cntrl2 = false;

    foreach ($session->roles as $k=>$role) {
        if(in_array($role, ['administrator'])){
            $cntrl = true;
        }
        if(in_array($role, $auth_roles)){
            $cntrl2 = true;
        }
    }
    //yetkilendirme ile ilgili geliştirmeler 16.07.2021

if($cntrl || ($cntrl2 && $sahra_auth_roles_control == 1)) {
    if( (
            ( $cntrl && in_array($session->user_login, ['admin']) )    //admin herzaman yetkilidir.
            || (in_array($session->ID, $auth_users) && $sahra_auth_users_control == 1)
        )
    || $sahra_auth_users_control == 0
         ) { //$sahra_auth_users_control 0 ise bu özellik kapalıdır ve user bazlı yetki kontrolüne gerek yoktur..
        ?>
        <br>
        <div class="container-fluid">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Sahra Eklenti Ayarları
                        <?php if ($cevap->btnkontrol == "enabled") { ?>
                            <!-- <form action="options.php" method="get" id="formLogout" name="formLogout">
                                <?php /*settings_fields( ' sahraoptionslogout' ); */ ?>
                                <?php /*do_settings_sections( 'sahraoptionslogout' ); */ ?>
                                <button class="btn btn-danger btn-sm" id="logout" name="logout" type="submit" style="float: right; margin-top: -20px">Çıkış <i class="fa fa-sign-out"></i></button>
                            </form>-->
                            <button class="btn btn-danger btn-sm" type="text" onclick="logout()"
                                    style="float: right; margin-top: -5px">Çıkış <i class="fa fa-sign-out"></i></button>
                        <?php } ?>
                    </h3>
                </div>

                <div class="panel-body">
                    <div class="col-md-6 text-left">
                        <a href="https://www.sahratelekom.com/" alt="Yeni nesil telekom operatörü" target="_blank">
                            <img src="<?php echo esc_html(plugins_url('lib/image/logo.png', dirname(__FILE__))) ?>" width="130"
                                 height="40">
                        </a>
                    </div>
                    <div class="col-md-6 text-right">
                        <div
                           
                            onclick="goto_mobikob();" 
                            class="alert alert-<?php echo esc_html($cevap->durum) ?>" id="bakiye" title="Mobikob Hesabınız için lütfen tıklayınız" style="display:inline-block; cursor: pointer;"><i class='fa fa-btc'></i>  <?php echo esc_html($cevap->mesaj) ?></div>
                    </div>

                    <form action="options.php" method="post" id="form-module" class="form-horizontal"
                          name="form-module">
                        <?php settings_fields('sahraoptions'); ?>
                        <?php do_settings_sections('sahraoptions'); ?>
                        <input type="hidden" name="sayfayi_yenile" id="sayfayi_yenile" value="0">
                        <div class="tab-pane">
                            <ul class="nav nav-tabs" id="language">
                                <li><a href="#login" data-toggle="tab"><i class="fa fa-sign-in"></i> API BİLGİLERİ</a></li>
                                <li><a href="#bulkandprivatesms" data-toggle="tab"><i class="fa fa-commenting"></i> SMS GÖNDER</a></li>
                                <li><a href="#crm" data-toggle="tab"><i class="fa fa-users"></i> CRM</a></li>
                                <li><a href="#woocommerce" data-toggle="tab"><i class="fa fa-shopping-cart"></i> WOOCOMMERCE AYARLAR</a></li>
                                <!-- <li><a href="#tf2sms" data-toggle="tab"><i class="fa fa-key"></i> Üyelik Doğrulama</a></li>
                                <li><a href="#cf7sms" data-toggle="tab"><i class="fa fa-envelope-o"></i> Contact Form7
                                        SMS</a></li> -->
                                <!-- <li><a href="#inbox" data-toggle="tab"><i class="fa fa-inbox"></i> Gelen sms</a></li>
                                <li><a href="#voip" data-toggle="tab"><i class="fa fa-phone"></i> Gelen Çağrılar</a>
                                </li> -->
                                <!-- <li><a href="#settings" data-toggle="tab"><i class="fa fa-phone"></i> Ayarlar</a></li> -->
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane" id="login">
                                    <hr>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="sahra_domain">Mobikob Alan Adı: </label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                <i class="fa fa-server" style="color: #17A2B8;"></i>
                                                </div>
                                                <input type="text" name="sahra_domain" id="sahra_domain"
                                                       placeholder="Kullanıcı Adı"
                                                       value="<?php echo esc_html(get_option("sahra_domain")) ?>" class="form-control"
                                                       />
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="sahra_user">Mobikob Kullanıcı Adı : </label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-user" style="color: #17A2B8;"></i>
                                                </div>
                                                <input type="text" name="sahra_user" id="sahra_user"
                                                       placeholder="Kullanıcı Adı"
                                                       value="<?php echo esc_html(get_option("sahra_user")) ?>" class="form-control"
                                                       onkeypress="return RestrictSpace()"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="sahra_pass">Mobikob Şifre : </label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-lock" style="color: #17A2B8;"></i>
                                                </div>
                                                <input type="password" name="sahra_pass" placeholder="Şifre"
                                                       id="sahra_pass" value="<?php echo esc_html(get_option("sahra_pass")) ?>"
                                                       class="form-control"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-2"></div>
                                        <div class="col-sm-8">
                                            <button class="btn btn-primary" id="login_save" name="login_save"
                                                    onclick="login();"> Bilgilerimi  Doğrula
                                            </button>
                                        </div>
                                    </div>
                                    <?php $sahra_all_smstitle = $sahra->getSmsBaslik();
                                    $sahra_input_smstitle = esc_html(get_option("sahra_input_smstitle")); ?>
                                    <div class="form-group">
                                        <label for="input-baslik" class="col-sm-2 control-label"
                                               style="color: <?php if (isset($sahra_input_smstitle) && !empty($sahra_input_smstitle) && $sahra_input_smstitle || $sahra_input_smstitle != 0) { ?>#2ECC71 <?php } else { ?>#E74C3C <?php } ?>;">
                                            SMS Başlığı :
                                        </label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-bullhorn" style="color: #17A2B8;"></i>
                                                </div>
                                                <select name="sahra_input_smstitle" id="sahra_input_smstitle"
                                                        class="form-control" style="height: 35px;">
                                                    <option value="0">Sms Başlığı Seçiniz</option>
                                                    <?php
                                                    if (isset($sahra_input_smstitle) && $sahra_input_smstitle != "" && is_array($sahra_all_smstitle) ) {
                                                        foreach ($sahra_all_smstitle as $title) {
                                                            if ($title != '') {
                                                                if ($title == $sahra_input_smstitle) {
                                                                    ?>
                                                                    <option value="<?php echo esc_html($title) ?>"
                                                                            selected><?php echo esc_html( $title) ?></option><?php
                                                                } else {
                                                                    ?>
                                                                    <option value="<?php echo esc_html( $title) ?>"><?php echo esc_html( $title )?></option> <?php
                                                                }
                                                            }
                                                        }
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <?php $sahra_status = esc_html(get_option("sahra_status")); ?>
                                        <?php if ($cevap->btnkontrol != "enabled") {
                                            $sahra_status = 0;
                                        } ?>
                                        <label class="col-sm-2 control-label" for="input-status"
                                               style="color: <?php if (isset($sahra_status) && !empty($sahra_status) && $sahra_status) { ?>#2ECC71<?php } else { ?>#E74C3C<?php } ?>;">Eklenti
                                            Durumu : </label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                
                                            <label class="switch">
                                                        <input name="sahra_status"
                                                               id="sahra_switch13" type="checkbox"
                                                               onchange="sahra_field_onoff(13)" value="1"
                                                               <?php if ($sahra_status == 1){ ?>checked <?php } ?> >
                                                        <span class="slider round"></span>
                                                    </label>
                                            </div>
                                            <?php if ($sahra_status) {
                                            } else { ?>
                                                <small><i> Eklenti kapalıyken programlanan sms gönderimleri iptal
                                                        olur.</i></small><?php } ?>
                                        </div>
                                    </div>
                                   
<!--                                  
                                    <div class="form-group"> 
                                                    <label class="col-sm-2 control-label" for="sahra_time_zone"
                                                    style="color: <?php if ((get_option('sahra_time_zone')) == 1) { ?>#2ECC71<?php } else { ?>#E74C3C<?php } ?>;"
                                                    >
                                                    
                                                        Timezone</label>
                                             
                                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                
                                                    <label class="switch">
                                                        <input name="sahra_time_zone" id="sahra_switch14"
                                                               type="checkbox" onchange="sahra_field_onoff(14)"
                                                               value="1"
                                                               <?php if ((get_option('sahra_time_zone')) == 1){ ?>checked <?php } ?> >
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                                <small><i>Seçildiği takdirde timezone olarak Istanbul ayarlanacaktır.(GMT+3) Seçilmez ise UTC</i></small>
                                                </div>
                                                </div> -->
                                          
                                   
                                    <div class="form-group">
                                        <div class="col-sm-2"></div>
                                        <div class="col-sm-10 text-right">
                                            <button class="btn btn-primary" id="login_save2" name="login_save2"
                                                    onclick="login();"><i class="fa fa-folder"></i> Değişiklikleri
                                                Kaydet
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane container-fluid" id="woocommerce">
                                    <hr>
                                    <div class="form-group"> <!--  sahra rehberine kayıt -->
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="col-sm-7">
                                                    <label class="control-label" for="sahra_rehber_add">
                                                        <!-- <i
                                                                class="fa fa-certificate" style="color: #E74C3C;"></i>
                                                        <i class="fa fa-certificate" style="color: #BB77AE;"></i>  -->
                                                        Yeni  yeliklerde, MOBIKOB CRM'e ekle:</label>
                                                </div>
                                                <div class="col-sm-5">
                                                    <label class="switch">
                                                        <input name="sahra_rehber_control" id="sahra_switch7"
                                                               type="checkbox" onchange="sahra_field_onoff(7)"
                                                               value="1"
                                                               <?php if ((get_option('sahra_rehber_control')) == 1){ ?>checked <?php } ?> >
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                                <p id="sahra_tags_text7" style="display: none;">
                                            </div>
                                            <!-- <div class="col-sm-9" id="sahra_field7"
                                                 style="<?php if ((get_option('sahra_rehber_control')) == 0) { ?>display:none; <?php } ?>">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-user-plus" style="color: #17A2B8;"></i>
                                                            </div>
                                                            <input name="sahra_rehber_groupname" class="form-control"
                                                                   placeholder="Eklemek istediğiniz grup ismini giriniz."
                                                                   value="<?php echo esc_html(get_option("sahra_rehber_groupname")) ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> -->
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <!-- Yeni üye olunca, belirlenen numaralara sms göndermek -->
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="col-sm-7">
                                                    <label class="control-label" for="sahra_newuser_to_admin_no">
                                                        <!-- <i
                                                                class="fa fa-certificate" style="color: #E74C3C;"></i>
                                                        <i class="fa fa-certificate" style="color: #BB77AE;"></i>  -->
                                                        Yeni
                                                        üyeliklerde, belirlenen numaralara sms gönderilsin:</label>
                                                </div>
                                                <div class="col-sm-5">
                                                    <label class="switch">
                                                        <input name="sahra_newuser_to_admin_control"
                                                               id="sahra_switch1" type="checkbox"
                                                               onchange="sahra_field_onoff(1)" value="1"
                                                               <?php if ((get_option('sahra_newuser_to_admin_control')) == 1){ ?>checked <?php } ?> >
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-9" id="sahra_field1"
                                                 style="<?php if ((get_option('sahra_newuser_to_admin_control')) == 0) { ?>display:none; <?php } ?>">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-phone" style="color: #17A2B8;"></i>
                                                            </div>
                                                            <input name="sahra_newuser_to_admin_no"
                                                                   id="sahra_newuser_to_admin_no" type="text"
                                                                   class="form-control"
                                                                   placeholder="Sms gönderilecek numaraları giriniz. Örn: 05xxXXXxxXX,05xxXXXxxXX"
                                                                   value="<?php echo  esc_html(get_option("sahra_newuser_to_admin_no")) ?>">
                                                        </div>
                                                        <p id="vars_newuser_to_admin_no"></p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-commenting" style="color: #17A2B8;"></i>
                                                            </div>
                                                            <!-- <div class="input-group-addon hoverlay"
                                                                 onclick="settingOpen('conf1')">
                                                                <a href="javascript:void(0)">
                                                                    <i class="fa fa-cogs" id="conf1_color"
                                                                       style="color: <?php if (esc_textarea(get_option("sahra_newuser_to_admin_json")) != '') {
                                                                           echo '#17A2B8';
                                                                       } else {
                                                                           echo '#2B2B2B';
                                                                       } ?>;"></i>
                                                                </a>
                                                            </div> -->
                                                            <textarea name="sahra_newuser_to_admin_text"
                                                                      id="sahra_textarea1" class="form-control"
                                                                      placeholder="Örnek : Sayın yetkili, [musteri_adi] [musteri_soyadi] kullanıcı sisteme kaydoldu. Bilgileri : tel : [musteri_telefonu] eposta: [musteri_epostasi]"><?php echo esc_textarea(get_option("sahra_newuser_to_admin_text")) ?></textarea>
                                                            <input type="hidden" id="sahra_newuser_to_admin_json"
                                                                   name="sahra_newuser_to_admin_json"
                                                                   class="form-control"
                                                                   value="<?php echo esc_html(get_option("sahra_newuser_to_admin_json")) ?>">
                                                        </div>
                                                        <p id="sahra_tags_text1" style="margin-top: 10px"><i
                                                                    class="fa fa-angle-double-right"></i>
                                                            Kullanılabilecek Değişkenler : </i></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group"> <!--  Yeni üye olunca,yeni üyeye sms  -->
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="col-sm-7">
                                                    <label class="control-label" for="sahra_newuser_to_customer_no">
                                                        <!-- <i
                                                                class="fa fa-certificate" style="color: #E74C3C;"></i>
                                                        <i class="fa fa-certificate" style="color: #BB77AE;"></i> -->
                                                         Yeni
                                                        üyeliklerde, müşteriye sms gönderilsin:</label>
                                                </div>
                                                <div class="col-sm-5">
                                                    <label class="switch">
                                                        <input name="sahra_newuser_to_customer_control"
                                                               id="sahra_switch2" type="checkbox"
                                                               onchange="sahra_field_onoff(2)" value="1"
                                                               <?php if ((get_option('sahra_newuser_to_customer_control')) == 1){ ?>checked <?php } ?> >
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-9" id="sahra_field2"
                                                 style="<?php if ((get_option('sahra_newuser_to_customer_control')) == 0) { ?>display:none; <?php } ?>">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-commenting" style="color: #17A2B8;"></i>
                                                            </div>
                                                            <!-- <div class="input-group-addon hoverlay"
                                                                 onclick="settingOpen('conf2')">
                                                                <a href="javascript:void(0)">
                                                                    <i class="fa fa-cogs" id="conf2_color"
                                                                       style="color: <?php if (esc_textarea(get_option("sahra_newuser_to_customer_json")) != '') {
                                                                           echo '#17A2B8';
                                                                       } else {
                                                                           echo '#2B2B2B';
                                                                       } ?>;"></i>
                                                                </a>
                                                            </div> -->
                                                            <textarea name="sahra_newuser_to_customer_text"
                                                                      id="sahra_textarea2" class="form-control"
                                                                      placeholder="Örnek :Sayın [musteri_adi] [musteri_soyadi], sitemize hoşgeldiniz! [musteri_telefonu] telefon numarası ve [musteri_epostasi] ile kayıt oldunuz. Keyifli Alışverişler !"><?php echo esc_textarea(get_option("sahra_newuser_to_customer_text")) ?></textarea>
                                                            <input type="hidden" id="sahra_newuser_to_customer_json"
                                                                   name="sahra_newuser_to_customer_json"
                                                                   class="form-control"
                                                                   value="<?php echo esc_html(get_option("sahra_newuser_to_customer_json")) ?>">
                                                        </div>
                                                        <p id="sahra_tags_text2" style="margin-top: 10px"><i
                                                                    class="fa fa-angle-double-right"></i>
                                                            Kullanılabilecek Değişkenler : </i></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        var settings = {
                                            'conf1': {
                                                'name': 'sahra_newuser_to_admin_json',
                                                'settings':
                                                    {
                                                        'source': 'no',
                                                        'timecondition': 'yes',
                                                        'otherAction': 'no'
                                                    }
                                            },
                                            'conf2': {
                                                'name': 'sahra_newuser_to_customer_json',
                                                'settings':
                                                    {
                                                        'source': 'no',
                                                        'timecondition': 'yes',
                                                        'otherAction': 'no'
                                                    }
                                            },
                                            'conf3': {
                                                'name': 'sahra_neworder_to_admin_json',
                                                'settings':
                                                    {
                                                        'source': 'no',
                                                        'timecondition': 'yes',
                                                        'otherAction': 'yes',
                                                        'addOrderAdminPanel' :'yes'
                                                    }
                                            },
                                            'conf4': {
                                                'name': 'sahra_neworder_to_customer_json',
                                                'settings':
                                                    {
                                                        'source': 'yes',
                                                        'timecondition': 'yes',
                                                        'otherAction': 'yes',
                                                        'addOrderAdminPanel' :'yes'
                                                    }
                                            },
                                            'conf5': {
                                                'name': 'sahra_order_refund_to_admin_json',
                                                'settings':
                                                    {
                                                        'source': 'no',
                                                        'timecondition': 'yes',
                                                        'otherAction': 'no'
                                                    }
                                            },
                                            'conf6': {
                                                'name': 'sahra_product_waitlist1_json',
                                                'settings':
                                                    {
                                                        'source': 'yes',
                                                        'timecondition': 'yes',
                                                        'otherAction': 'no'
                                                    }
                                            },
                                            'conf11': {
                                                'name': 'sahra_newnote1_to_customer_json',
                                                'settings':
                                                    {
                                                        'source': 'yes',
                                                        'timecondition': 'yes',
                                                        'otherAction': 'no'
                                                    }
                                            },
                                            'conf12': {
                                                'name': 'sahra_newnote2_to_customer_json',
                                                'settings':
                                                    {
                                                        'source': 'yes',
                                                        'timecondition': 'yes',
                                                        'otherAction': 'no'
                                                    }
                                            },
                                            <?php  if(function_exists('wc_get_order_statuses')) {
                                            $order_statuses = wc_get_order_statuses();
                                            $arraykeys = array_keys($order_statuses);
                                            foreach ($arraykeys as $item) { ?>
                                            '<?php echo esc_html($item)?>': {
                                                'name': 'sahra_order_status_text_<?php echo esc_html($item)?>_json',
                                                'settings':
                                                    {
                                                        'source': 'yes',
                                                        'timecondition': 'yes',
                                                        'otherAction': 'no'
                                                    }
                                            },
                                            <?php } }?>
                                        };

                                        var settings2 = {
                                            'source2': {
                                                'type': 'checkbox',
                                                'ids': [
                                                    '_source_billing_phone',
                                                    '_source_address_phone'
                                                ]
                                            },
                                            'source': {
                                                'type': 'text',
                                                'ids': [
                                                    '_custom_phone_key'
                                                ]
                                            },
                                            'timecondition': {
                                                'type': 'text',
                                                'ids': [
                                                    '_timecondition'
                                                ]
                                            },
                                            'otherAction': {
                                                'type': 'text',
                                                'ids': [
                                                    '_otherAction'
                                                ]
                                            },
                                            'addOrderAdminPanel' : {
                                                'type': 'checkbox',
                                                'ids': [
                                                    '_addOrderAdminPanel'
                                                ]
                                            }
                                        };

                                        // function settingOpen(conf) {
                                        //     var settingHtml = {
                                        //         'source2': '<hr><div class="col-md-12"><div class="col-md-3"><label for=""><i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="SMSin hangi telefon numarası kaynaklarına gönderileceğini belirleyebilirsiniz."></i> Gönderilecek Kaynak: </label></div><div class="col-md-7"><input type="checkbox" name="" id="' + conf + settings2["source"].ids[0] + '" class="checkbox-fix" value="off"> Fatura telefon numarasına gönder<br><input type="checkbox" name="" id="' + conf + settings2["source"].ids[1] + '" class="checkbox-fix" value="off"> Adres telefon numarasına gönder</div></div>',
                                        //         'source': '<div class="col-md-12"><div class="col-md-3"><label for=""><i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Gönderilmesini istediğiniz özel bir telefon anahtarı varsa bu anahtardaki telefon numarasına SMS gönderebilirsiniz. Örn: billing_phone, shipping_phone, musteri_tel vs. "></i> SMS gönderilecek telefon numarası anahtarı: </label></div><div class="col-md-8"><input type="text" name="" id="' + conf + settings2["source"].ids[0] + '"  class="form-control" placeholder="Gönderilmesini istediğiniz özel bir telefon anahtarı varsa bu anahtardaki telefon numarasına SMS gönderebilirsiniz. Örn: billing_phone, shipping_phone, musteri_tel vs. " value="billing_phone"></div></div>',
                                        //         'timecondition': '<hr><div class="col-md-12"><div class="col-md-3"><label for=""><i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="SMSin ne kadar zaman sonra gönderileceğini dakika cinsinden belirleyebilirsiniz. Zaman ayarlarınızı kontrol edin. Bu sayfa yüklendiğinde saat : <?php echo esc_html(date('H:i:s', current_time('timestamp')))?>"></i> Zamanla: </label></div><div class="col-md-8"><input type="number" name="" id="' + conf + settings2["timecondition"].ids[0] + '"  class="form-control" placeholder="Kaç dakika sonra gönderilsin istiyorsunuz? "></div></div>',
                                        //         'otherAction': '<hr><div class="col-md-12" style="display: none;"><div class="col-md-3"><label for=""><i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Farklı bir actionda çalıştırmak için kanca ismi girin. Gizli özelliktir."></i> Ek kanca girişi: </label></div><div class="col-md-8"><input type="text" name="" id="' + conf + settings2["otherAction"].ids[0] + '"  class="form-control" placeholder="Farklı kancada çalıştırmak için kanca ismi girin"></div></div>',
                                        //         'addOrderAdminPanel': '<hr><div class="col-md-12"><div class="col-md-3"><label for=""><i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Admin panelden eklenen siparişlerdede SMS gönderilmesini seçebilirsiniz."></i> Admin panelden eklenen siparişte de gönder: </label></div><div class="col-md-7"><input type="checkbox" name="" id="' + conf + settings2["addOrderAdminPanel"].ids[0] + '" class="checkbox-fix" value="off"> SMS Gönder</div></div>',
                                        //     };

                                        //     if (settings[conf]) {
                                        //         var setting = settings[conf];
                                        //         // var data = JSON.parse(jQuery('#'+setting.name).val());
                                        //         modalCleaner();
                                        //         jQuery('#modal-save').attr('conf', conf);

                                        //         Object.keys(settings2).forEach(function (item) {
                                        //             if (setting.settings[item] == 'yes') {  //Gönderilecek kaynak ayarı
                                        //                 jQuery('#row_' + item).html(settingHtml[item]);
                                        //             }
                                        //         });

                                        //         try {
                                        //             var data = JSON.parse(jQuery('#' + settings[conf].name).val());
                                        //         } catch (e) {
                                        //             var data = [];
                                        //         }

                                        //         Object.keys(settings2).forEach(function (item) {
                                        //             var types = settings2[item];
                                        //             Object.keys(types.ids).forEach(function (ids) {
                                        //                 var id = types.ids[ids];
                                        //                 if (types.type == 'checkbox') {
                                        //                     if (data[id] == true) {
                                        //                         jQuery('#' + conf + id).prop("checked", true);
                                        //                     } else {
                                        //                         jQuery('#' + conf + id).prop("checked", false);
                                        //                     }
                                        //                 } else {
                                        //                     if (data[id] != undefined && data[id] != '') {
                                        //                         jQuery('#' + conf + id).val(data[id]);
                                        //                     }
                                        //                 }
                                        //             });
                                        //         });
                                        //     }
                                        //     jQuery('#settingModal').modal('show');
                                        //     jQuery('[data-toggle="tooltip"]').tooltip();
                                        // }

                                        function modalCleaner() {
                                            Object.keys(settings2).forEach(function (item) {
                                                jQuery('#row_' + item).html('');
                                            });
                                        }


                                    </script>

                                    <div class="modal inmodal fade" id="settingModal" tabindex="-1" role="dialog"
                                         aria-hidden="true" style="padding-top: 20px">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"><span
                                                                aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                                                    </button>
                                                    <h4 class="modal-title">
                                                        <span id="modalTitle"><i
                                                                    class="fa fa-cogs"></i> Ek Ayarlar</span>
                                                    </h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row" id="row_source">
                                                    </div>
                                                    <div class="row" id="row_custom_phone_key">
                                                    </div>
                                                    <div class="row" id="row_timecondition">
                                                    </div>
                                                    <div class="row" id="row_addOrderAdminPanel">
                                                    </div>
                                                    <div class="row" id="row_otherAction">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-white" data-dismiss="modal"
                                                            id="modal-close">Kapat
                                                    </button>
                                                    <button type="button" class="btn btn-primary" id="modal-save"
                                                            conf="">Kaydet
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                        jQuery('#modal-save').click(function () {
                                            var conf = jQuery(this).attr('conf');
                                            var data = {};
                                            Object.keys(settings2).forEach(function (item) {
                                                var types = settings2[item];
                                                if (settings[conf].settings[item] == 'yes') {
                                                    Object.keys(types.ids).forEach(function (ids) {
                                                        var id = types.ids[ids];
                                                        if (types.type == 'checkbox') {
                                                            data[id] = jQuery('#' + conf + id).is(':checked');
                                                        } else {
                                                            data[id] = jQuery('#' + conf + id).val();
                                                        }
                                                    });
                                                }
                                            });

                                            if (JSON.stringify(data) != '') {
                                                jQuery('#' + conf + '_color').css('color', '#17A2B8')
                                            }

                                            jQuery('#' + settings[conf].name).val(JSON.stringify(data));
                                            jQuery('#settingModal').modal('hide');
                                        })

                                    </script>

<hr>
                                    <div class="form-group"> <!--  yeni sipariş geldiğinde belirlenen numaralara sms -->
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="col-sm-7">
                                                    <label class="control-label" for="sahra_neworder_to_admin_no">
                                                        <!-- <i
                                                                class="fa fa-certificate" style="color: #BB77AE;"></i> -->
                                                        Yeni siparişlerde, belirlenen numaralara sms gönderilsin:</label>
                                                </div>
                                                <div class="col-sm-5">
                                                    <label class="switch">
                                                        <input name="sahra_neworder_to_admin_control"
                                                               id="sahra_switch3" type="checkbox"
                                                               onchange="sahra_field_onoff(3)" value="1"
                                                               <?php if ((get_option('sahra_neworder_to_admin_control')) == 1){ ?>checked <?php } ?> >
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-9" id="sahra_field3"
                                                 style="<?php if ((get_option('sahra_neworder_to_admin_control')) == 0) { ?>display:none; <?php } ?>">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-phone" style="color: #17A2B8;"></i>
                                                            </div>
                                                            <input name="sahra_neworder_to_admin_no"
                                                                   id="sahra_neworder_to_admin_no" type="text"
                                                                   class="form-control"
                                                                   placeholder="Sms gönderilecek numaraları giriniz. Örn: 05xxXXXxxXX,05xxXXXxxXX"
                                                                   value="<?php echo  esc_html(get_option("sahra_neworder_to_admin_no")) ?>">
                                                        </div>
                                                        <p id="vars_neworder_to_admin_no"></p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-commenting" style="color: #17A2B8;"></i>
                                                            </div>
                                                            <!-- <div class="input-group-addon hoverlay"
                                                                 onclick="settingOpen('conf3')">
                                                                <a href="javascript:void(0)">
                                                                    <i class="fa fa-cogs" id="conf3_color"
                                                                       style="color: <?php if (esc_textarea(get_option("sahra_neworder_to_admin_json")) != '') {
                                                                           echo '#17A2B8';
                                                                       } else {
                                                                           echo '#2B2B2B';
                                                                       } ?>;"></i>
                                                                </a>
                                                            </div> -->
                                                            <textarea name="sahra_neworder_to_admin_text"
                                                                      id="sahra_textarea3" class="form-control"
                                                                      placeholder="Örnek : Sayın Yönetici, [siparis_no] no'lu bir sipariş aldınız. Ürün bilgileri : [urun_adlari]-[urun_kodlari]-[urun_adetleri]"><?php echo  esc_textarea(get_option("sahra_neworder_to_admin_text")) ?></textarea>
                                                            <input type="hidden" id="sahra_neworder_to_admin_json"
                                                                   name="sahra_neworder_to_admin_json"
                                                                   class="form-control"
                                                                   value="<?php echo esc_html(get_option("sahra_neworder_to_admin_json")) ?>">
                                                        </div>
                                                        <p id="sahra_tags_text3" style="margin-top: 10px"><i
                                                                    class="fa fa-angle-double-right"></i>
                                                            Kullanılabilecek Değişkenler : </i></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group"> <!--  Yeni sipariş olunca müşteriye sms -->
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="col-sm-7">
                                                    <label class="control-label" for="sahra_neworder_to_customer_no">
                                                        <!-- <i
                                                                class="fa fa-certificate" style="color: #BB77AE;"></i> -->
                                                        Yeni siparişlerde, Müşteriye bilgilendirme sms
                                                        gönderilsin:</label>
                                                </div>
                                                <div class="col-sm-5">
                                                    <label class="switch">
                                                        <input name="sahra_neworder_to_customer_control"
                                                               id="sahra_switch4" type="checkbox"
                                                               onchange="sahra_field_onoff(4)" value="1"
                                                               <?php if ((get_option('sahra_neworder_to_customer_control')) == 1){ ?>checked <?php } ?> >
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-9" id="sahra_field4"
                                                 style="<?php if ((get_option('sahra_neworder_to_customer_control')) == 0) { ?>display:none; <?php } ?>">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-commenting" style="color: #17A2B8;"></i>
                                                            </div>
                                                            <!-- <div class="input-group-addon hoverlay"
                                                                 onclick="settingOpen('conf4')">
                                                                <a href="javascript:void(0)">
                                                                    <i class="fa fa-cogs" id="conf4_color"
                                                                       style="color: <?php if (esc_textarea(get_option("sahra_neworder_to_customer_json")) != '') {
                                                                           echo '#17A2B8';
                                                                       } else {
                                                                           echo '#2B2B2B';
                                                                       } ?>;"></i>
                                                                </a>
                                                            </div> -->
                                                            <textarea name="sahra_neworder_to_customer_text"
                                                                      id="sahra_textarea4" class="form-control"
                                                                      placeholder="Örnek : [siparis_no]' nolu siparişiniz başarıyla oluşturulmuştur."><?php echo esc_textarea(get_option("sahra_neworder_to_customer_text")) ?></textarea>
                                                            <input type="hidden" id="sahra_neworder_to_customer_json"
                                                                   name="sahra_neworder_to_customer_json"
                                                                   class="form-control"
                                                                   value="<?php echo esc_html(get_option("sahra_neworder_to_customer_json")) ?>">
                                                        </div>
                                                        <p id="sahra_tags_text4" style="margin-top: 10px"><i
                                                                    class="fa fa-angle-double-right"></i>
                                                            Kullanılabilecek Değişkenler : </i></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                    <?php
                                    if (function_exists('wc_get_order_statuses')) {
                                        $order_statuses = wc_get_order_statuses();

                                        $actives = [];
                                        foreach ($order_statuses as $key => $order_status) {
                                            if (esc_textarea(get_option('sahra_order_status_text_' . $key)) != '') {
                                                array_push($actives, $order_status);
                                            }
                                        }
                                    }
                                    ?>
<hr>
                                    <div class="form-group">
                                          <!-- Sİpariş durumları değiştiğinde müşteriye sms gönderilsin. -->
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="col-sm-7">
                                                    <label class="control-label" for="sahra_neworder_to_admin_no">
                                                        <!-- <i
                                                                class="fa fa-certificate" style="color: #BB77AE;"></i>
                                                        <i class="fa fa-certificate" style="color: #34495E;"></i>  -->
                                                        Ürünün
                                                        sipariş durumu değiştiğinde müşteriye sms gönderilsin:</label>
                                                </div>
                                                <div class="col-sm-5">
                                                    <label class="switch">
                                                        <input name="sahra_orderstatus_change_customer_control"
                                                               id="sahra_switch5" type="checkbox"
                                                               onchange="sahra_field_onoff(5)" value="1"
                                                               <?php if ((get_option('sahra_orderstatus_change_customer_control')) == 1){ ?>checked <?php } ?>>
                                                               
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-9" id="sahra_field5"
                                                 style="<?php if ((get_option('sahra_orderstatus_change_customer_control')) == 0) { ?>display:none; <?php } ?>">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="input-group">

                                                            <?php if (isset($actives[0])) {
                                                                ?>
                                                                <div class="input-group-addon" data-toggle="tooltip"
                                                                     data-placement="right" data-html="true"
                                                                     title="<b style='color: #2ECC71'>Aktif durumlar : </b><hr> <?php echo esc_html( implode('<br>', $actives) )?>">
                                                                    <i class="fa fa-check-square"
                                                                       style="color: #2ECC71;"></i>
                                                                </div>
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <div class="input-group-addon" data-toggle="tooltip"
                                                                     data-placement="right" data-html="true"
                                                                     title="Hiçbir durum aktifleştirilmemiş.">
                                                                    <i class="fa fa-times-circle"
                                                                       style="color: #E74C3C;"></i>
                                                                </div>
                                                                <?php
                                                            } ?>


                                                            <!-- <div class="input-group-addon">
                                                                <i class="fa fa-truck" style="color: #17A2B8;"></i>
                                                            </div> -->
                                                            <!-- <div class="input-group-addon hoverlay"
                                                                 id="settings-btn-changed" onclick="">
                                                                <a href="javascript:void(0)">
                                                                    <i class="fa fa-cogs" id="setting-btn_color"
                                                                       style="color:#2B2B2B;"></i>
                                                                </a>
                                                            </div> -->

                                                            <select id="order_status"
                                                                    onchange="order_status_change(this.value)"
                                                                    class="form-control" style="height: 35px">
                                                                <option value="" selected>Sipariş Durumu Seçiniz
                                                                </option>
                                                            
                                                                <?php if (function_exists('wc_get_order_statuses')) {
                                                                    $order_statuses = wc_get_order_statuses();
                                                                    $arraykeys = array_keys($order_statuses);
                                                                    foreach ($arraykeys as $item) { ?>
                                                                        <option value="<?php echo  esc_html($item) ?>"><?php echo  esc_html($order_statuses[$item]) ?></option>
                                                                    <?php }
                                                                } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span id="activeStatus" data=""></span>
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <?php if (isset($arraykeys)) {
                                                            foreach ($arraykeys as $item) {
                                                                $order_status_text = array($item => 'sahra_order_status_text_' . $item);
                                                                ?>
                                                                <textarea style="display: none;"
                                                                          name="sahra_order_status_text_<?php echo  esc_html($item) ?>"
                                                                          id="sahra_order_status_text_<?php echo esc_html( $item ) ?>"
                                                                          class="form-control order_status_text"
                                                                          placeholder="Örnek : Sayın [musteri_adi] [musteri_soyadi], [siparis_no] numaralı siparişinizin kargo durumu ... olarak değiştirilmiştir. "><?php echo  esc_textarea(get_option($order_status_text[$item])); ?></textarea>
                                                                <input type="hidden"
                                                                       id="sahra_order_status_text_<?php echo esc_html($item) ?>_json"
                                                                       name="sahra_order_status_text_<?php echo esc_html( $item )?>_json"
                                                                       class="form-control"
                                                                       value="<?php echo  esc_html(get_option("sahra_order_status_text_" . $item . "_json")) ?>">
                                                            <?php }
                                                        } ?>
                                                        <p id="sahra_tags_text5" style="margin-top: 10px"><i
                                                                    class="fa fa-angle-double-right"></i>
                                                            Kullanılabilecek Değişkenler : </i>
                                                            <mark onclick="varfill('sahra_order_status_text_'+jQuery('#activeStatus').attr('data'), 'siparis_no')">
                                                                [siparis_no]
                                                            </mark>&nbsp;
                                                            <mark onclick="varfill('sahra_order_status_text_'+jQuery('#activeStatus').attr('data'), 'musteri_adi')">
                                                                [musteri_adi]
                                                            </mark>&nbsp;
                                                            <mark onclick="varfill('sahra_order_status_text_'+jQuery('#activeStatus').attr('data'), 'musteri_soyadi')">
                                                                [musteri_soyadi]
                                                            </mark>&nbsp;
                                                            <mark onclick="varfill('sahra_order_status_text_'+jQuery('#activeStatus').attr('data'), 'musteri_telefonu')">
                                                                [musteri_telefonu]
                                                            </mark>&nbsp;
                                                            <mark onclick="varfill('sahra_order_status_text_'+jQuery('#activeStatus').attr('data'), 'musteri_epostasi')">
                                                                [musteri_epostasi]
                                                            </mark>&nbsp;
                                                            <mark onclick="varfill('sahra_order_status_text_'+jQuery('#activeStatus').attr('data'), 'kullanici_adi')">
                                                                [kullanici_adi]
                                                            </mark>&nbsp;
                                                            <mark onclick="varfill('sahra_order_status_text_'+jQuery('#activeStatus').attr('data'), 'tarih')">
                                                                [tarih]
                                                            </mark>&nbsp;
                                                            <mark onclick="varfill('sahra_order_status_text_'+jQuery('#activeStatus').attr('data'), 'saat')">
                                                                [saat]
                                                            </mark>&nbsp;
                                                            <mark onclick="varfill('sahra_order_status_text_'+jQuery('#activeStatus').attr('data'), 'kargo_firmasi')"> 
                                                                [kargo_firmasi]
                                                            </mark>&nbsp;
                                                            <mark onclick="varfill('sahra_order_status_text_'+jQuery('#activeStatus').attr('data'), 'takip_kodu')">
                                                                [takip_kodu]
                                                            </mark>&nbsp;
                                                            <!-- <i class="fa fa-certificate" style="color: #681947;"></i> -->
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="form-group">  <!--Yeni özel not eklenince müşteriye sms --> 
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="col-sm-7">
                                                  <label class="control-label" for="">
                                                      <!-- <i
                                                                 class="fa fa-certificate" style="color: #BB77AE;"></i>  -->
                                                        Siparişe yeni Özel not eklendiğinde müşteriye sms gönderilsin:</label>
                                                </div>
                                                <div class="col-sm-5">
                                                    <label class="switch">
                                                        <input name="sahra_newnote1_to_customer_control"
                                                               id="sahra_switch11" type="checkbox"
                                                               onchange="sahra_field_onoff(11)" value="1"
                                                               <?php if ((get_option('sahra_newnote1_to_customer_control')) == 1){ ?>checked <?php } ?> >
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-9" id="sahra_field11"
                                                 style="<?php if ((get_option('sahra_newnote1_to_customer_control')) == 0) { ?>display:none; <?php } ?>">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-commenting" style="color: #17A2B8;"></i>
                                                            </div>
                                                            <!-- <div class="input-group-addon hoverlay"
                                                                 onclick="settingOpen('conf11')">
                                                                <a href="javascript:void(0)">
                                                                    <i class="fa fa-cogs" id="conf11_color"
                                                                       style="color: <?php if (esc_textarea(get_option("sahra_newnote1_to_customer_json")) != '') {
                                                                           echo '#17A2B8';
                                                                       } else {
                                                                           echo '#2B2B2B';
                                                                       } ?>;"></i>
                                                                </a>
                                                            </div> -->
                                                            <textarea name="sahra_newnote1_to_customer_text"
                                                                      id="sahra_textarea11" class="form-control"
                                                                      placeholder="Örnek : [siparis_no]' nolu siparişinize yeni not eklendi : [not]"><?php echo  esc_textarea(get_option("sahra_newnote1_to_customer_text")) ?></textarea>
                                                            <input type="hidden" id="sahra_newnote1_to_customer_json"
                                                                   name="sahra_newnote1_to_customer_json"
                                                                   class="form-control"
                                                                   value="<?php echo esc_html(get_option("sahra_newnote1_to_customer_json")) ?>">
                                                        </div>
                                                        <p id="sahra_tags_text11" style="margin-top: 10px"><i
                                                                    class="fa fa-angle-double-right"></i>
                                                            Kullanılabilecek Değişkenler : </i></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <div class="form-group"> <!--  Yeni özel not eklenince müşteriye sms -->
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="col-sm-7">
                                                    <label class="control-label" for="">
                                                        <!-- <i
                                                                class="fa fa-certificate" style="color: #BB77AE;"></i> -->
                                                        Siparişe yeni Müşteriye not eklendiğinde müşteriye sms gönderilsin:</label>
                                                </div>
                                                <div class="col-sm-5">
                                                    <label class="switch">
                                                        <input name="sahra_newnote2_to_customer_control"
                                                               id="sahra_switch12" type="checkbox"
                                                               onchange="sahra_field_onoff(12)" value="1"
                                                               <?php if ((get_option('sahra_newnote2_to_customer_control')) == 1){ ?>checked <?php } ?> >
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-9" id="sahra_field12"
                                                 style="<?php if ((get_option('sahra_newnote2_to_customer_control')) == 0) { ?>display:none; <?php } ?>">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-commenting" style="color: #17A2B8;"></i>
                                                            </div>
                                                            <!-- <div class="input-group-addon hoverlay"
                                                                 onclick="settingOpen('conf12')">
                                                                <a href="javascript:void(0)">
                                                                    <i class="fa fa-cogs" id="conf12_color"
                                                                       style="color: <?php if (esc_textarea(get_option("sahra_newnote2_to_customer_json")) != '') {
                                                                           echo '#17A2B8';
                                                                       } else {
                                                                           echo '#2B2B2B';
                                                                       } ?>;"></i>
                                                                </a>
                                                            </div> -->
                                                            <textarea name="sahra_newnote2_to_customer_text"
                                                                      id="sahra_textarea12" class="form-control"
                                                                      placeholder="Örnek : [siparis_no]' nolu siparişinize yeni not eklendi : [not]"><?php echo  esc_textarea(get_option("sahra_newnote2_to_customer_text")) ?></textarea>
                                                            <input type="hidden" id="sahra_newnote2_to_customer_json"
                                                                   name="sahra_newnote2_to_customer_json"
                                                                   class="form-control"
                                                                   value="<?php echo  esc_html(get_option("sahra_newnote2_to_customer_json")) ?>">
                                                        </div>
                                                        <p id="sahra_tags_text12" style="margin-top: 10px"><i
                                                                    class="fa fa-angle-double-right"></i>
                                                            Kullanılabilecek Değişkenler : </i></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                    <hr>

                                    <div class="form-group">
                                        <!--  Sipariş iptal edildiğinde belirlediğim numaralı sms ile bilgilendir -->
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="col-sm-7">
                                                    <label class="control-label" for="sahra_neworder_to_admin_no">
                                                        <!-- <i
                                                                class="fa fa-certificate" style="color: #BB77AE;"></i> -->
                                                        Sipariş iptal edildiğinde belirlediğim numaralı sms ile
                                                        bilgilendir:</label>
                                                </div>
                                                <div class="col-sm-5">
                                                    <label class="switch">
                                                        <input name="sahra_order_refund_to_admin_control"
                                                               id="sahra_switch6" type="checkbox"
                                                               onchange="sahra_field_onoff(6)" value="1"
                                                               <?php if ((get_option('sahra_order_refund_to_admin_control')) == 1){ ?>checked <?php } ?> >
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-9" id="sahra_field6"
                                                 style="<?php if ((get_option('sahra_order_refund_to_admin_control')) == 0) { ?>display:none; <?php } ?>">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-phone" style="color: #17A2B8;"></i>
                                                            </div>
                                                            <input name="sahra_order_refund_to_admin_no"
                                                                   id="sahra_order_refund_to_admin_no" type="text"
                                                                   class="form-control"
                                                                   placeholder="Sms gönderilecek numaraları giriniz. Örn: 05xxXXXxxXX,05xxXXXxxXX"
                                                                   value="<?php echo  esc_html(get_option("sahra_order_refund_to_admin_no")) ?>">
                                                        </div>
                                                        <p id="sahra_order_refund_to_admin_no"></p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-commenting" style="color: #17A2B8;"></i>
                                                            </div>
                                                            <!-- <div class="input-group-addon hoverlay"
                                                                 onclick="settingOpen('conf5')">
                                                                <a href="javascript:void(0)">
                                                                    <i class="fa fa-cogs" id="conf5_color"
                                                                       style="color: <?php if (esc_textarea(get_option("sahra_order_refund_to_admin_json")) != '') {
                                                                           echo '#17A2B8';
                                                                       } else {
                                                                           echo '#2B2B2B';
                                                                       } ?>;"></i>
                                                                </a>
                                                            </div> -->
                                                            <textarea name="sahra_order_refund_to_admin_text"
                                                                      id="sahra_textarea6" class="form-control"
                                                                      placeholder="Sayın yönetici, [musteri_adi][musteri_soyadi] kullanıcısı, [urun] ürününü '[iade_nedeni]' nedeninden dolayı iptal etmiştir."><?php echo  esc_html(get_option("sahra_order_refund_to_admin_text")) ?></textarea>
                                                            <input type="hidden" id="sahra_order_refund_to_admin_json"
                                                                   name="sahra_order_refund_to_admin_json"
                                                                   class="form-control"
                                                                   value="<?php echo  esc_html(get_option("sahra_order_refund_to_admin_json")) ?>">
                                                        </div>
                                                        <p id="sahra_tags_text6" style="margin-top: 10px"><i
                                                                    class="fa fa-angle-double-right"></i>
                                                            Kullanılabilecek Değişkenler : </i></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <!--  ürün stoğa girdiğinde bekleme listesindekilere sms gönder-->
                                         <div class="row"> 
                                            <div class="col-sm-3">
                                                <div class="col-sm-7">
                                                    <label class="control-label" for="sahra_neworder_to_admin_no">
                                                       <!-- <i class="fa fa-certificate" style="color: #BB77AE;"></i> 
                                                        <i class="fa fa-certificate" style="color: #F79500;"></i> --> 
                                                        Ürün stoğa girdiğinde bekleme listesindekilere sms gönder(Wc Waitlist): </label>
                                               </div> 
                                                <div class="col-sm-5">
                                                    <label class="switch">
                                                        <input name="sahra_product_waitlist1_control"
                                                               id="sahra_switch8" type="checkbox"
                                                               onchange="sahra_field_onoff(8)" value="1"
                                                               <?php if ((get_option('sahra_product_waitlist1_control')) == 1){ ?>checked <?php } ?> >
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-9" id="sahra_field8"
                                                 style="<?php if ((get_option('sahra_product_waitlist1_control')) == 0) { ?>display:none; <?php } ?>">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-commenting" style="color: #17A2B8;"></i>
                                                            </div>
                                                            <!-- <div class="input-group-addon hoverlay"
                                                                 onclick="settingOpen('conf6')">
                                                                <a href="javascript:void(0)">
                                                                    <i class="fa fa-cogs" id="conf6_color"
                                                                       style="color: <?php if (esc_textarea(get_option("sahra_product_waitlist1_json")) != '') {
                                                                           echo '#17A2B8';
                                                                       } else {
                                                                           echo '#2B2B2B';
                                                                       } ?>;"></i>
                                                                </a>
                                                            </div> -->
                                                             <textarea name="sahra_product_waitlist1_text" 
                                                                      id="sahra_textarea8" class="form-control"
                                                                      placeholder="Sayın [musteri_adi][musteri_soyadi], [urun_adi] ürünü tekrar stoğa girmiştir. Bilginize."><?php echo  esc_html(get_option("sahra_product_waitlist1_text")) ?></textarea>
                                                            <input type="hidden" id="sahra_product_waitlist1_json"
                                                                   name="sahra_product_waitlist1_json"
                                                                   class="form-control"
                                                                   value="<?php echo  esc_html(get_option("sahra_product_waitlist1_json")) ?>">
                                                        </div>
                                                        <p id="sahra_tags_text8" style="margin-top: 10px"><i
                                                                    class="fa fa-angle-double-right"></i>
                                                            Kullanılabilecek Değişkenler : </i></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 

                                    <hr>

                                    <div class="form-group">
                                        <div class="col-sm-2"></div>
                                        <div class="col-sm-10 text-right">
                                            <button class="btn btn-primary" id="login_save3" name="login_save3"
                                                    onclick="login();"><i class="fa fa-folder"></i> Değişiklikleri
                                                Kaydet
                                            </button>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <p>

                                            <!-- <i class="fa fa-bolt" style="color: #00B49C;"></i> Yeni Özellik : SMSlere otomatik geçmiş,güncel veya gelecek tarih ve saat ekleyebilirsiniz. :
                                            Güncel tarih <b>[tarih]</b> formatındadır. <b>[tarih+5]</b> 5 gün sonranın tarihini verir .<b>[tarih-5]</b> 5 gün öncenin tarihini verir. Gün cinsinden ekleme/çıkarma yapılmalıdır. Format g.a.Y şeklindedir.<br>
                                            Güncel saat <b>[saat]</b> formatındadır.  <b>[saat+60]</b> 1 saat sonrasının saatini verir. <b>[saat-60]</b> 1 saat öncesinin saatini verir. Dakika cinsinden ekleme/çıkarma yapılmalıdır. Format : S:d:sn şeklindedir.

                                            <br>
                                            <i class="fa fa-bolt" style="color: #00B49C;"></i> Yeni Özellik : Sipariş
                                            SMSlerinde, sipariş meta anahtarlarını kullanabilirsiniz. Kullanım şekli :
                                            <b>[meta:metakeyiniz]</b> formatındadır. metakeyiniz yazan yere sipariş meta
                                            anahtarını girebilirsiniz.
                                            <br>
                                            <i class="fa fa-bolt" style="color: #00B49C;"></i> Yeni Özellik : <i
                                                    class="fa fa-cogs"></i> simgesi bulunan ayarlarda sunulan ek
                                            konfigürasyonları yapabilirsiniz.<br>

                                        <hr>

                                        <i class="fa fa-certificate" style="color: #E74C3C;"></i> Aktif olduğunda, hesap
                                        oluşturma sayfasına Ad,Soyad ve Telefon numarası kısmını ekler. Kayıt için
                                        zorunlu olur.
                                        <br> -->
                                        <i class="fa fa-star" ></i>
                                      
                                         Bu özellikler
                                        woocommerce e-ticaret eklentisi yüklü ve etkin olduğunda çalışır.
                                        <br>
                                        <i class="fa fa-star" ></i> Beklemede özelliğinde 
                                        yeni sipariş verildiğinde sms gönderilir.
                                        <br>
                                        <i class="fa fa-star"></i> <a href="https://docs.woocommerce.com/document/woocommerce-waitlist/" target="_blank">WooCommerce Bekleme Listesi (WooCommerce Waitlist)</a> eklentisi yüklü olduğunda ve ürün düzenleme sayfasında <b>"send instock email"</b> özelliği çalıştırıldığında, bekleme listesindeki kullanıcıların numaralarına gönderilir.
                                        <br>

                                        <i class="fa fa-star" ></i> Kargo firması ve takip
                                        kodu sadece Kargo Takip(<a
                                                href="https://wordpress.org/plugins/kargo-takip/" target="_blank">https://wordpress.org/plugins/kargo-takip/</a>)
                                        eklentisinin yüklü ve bilgilerin doldurulmuş olması gerekmektedir.
                                        <br> 
                                        <!-- <i class="fa fa-lightbulb-o" style="color: #D35400;"></i> Satır atlamak için
                                        <strong>\n</strong> kullanabilirsiniz.<br> -->

                                        </p>

                                    </div>
                                </div>

                                <div class="tab-pane container-fluid" id="tf2sms">
                                    <hr>
                                    <div class="form-group">
                                        <!--  Sipariş iptal edildiğinde belirlediğim numaralı sms ile bilgilendir -->
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="col-sm-7">
                                                    <label class="control-label" for="sahra_neworder_to_admin_no">
                                                        <i class="fa fa-certificate" style="color: #3498DB;"></i>
                                                        <i class="fa fa-certificate" style="color: #BB77AE;"></i>
                                                        <i class="fa fa-certificate" style="color: #E74C3C;"></i> Yeni
                                                        üye olurken <b style="color: #E74C3C;" data-toggle="tooltip"
                                                                       data-placement="top"
                                                                       title="OTP SMS paketinden ücretlendirilir. OTP SMS paketizin olduğuna emin olun.">OTP
                                                            SMS</b> ile doğrulama yap :</label>
                                                </div>
                                                <div class="col-sm-5">
                                                    <label class="switch">
                                                        <input name="sahra_tf2_auth_register_control"
                                                               id="sahra_switch9" type="checkbox"
                                                               onchange="sahra_field_onoff(9)" value="1"
                                                               <?php if ((get_option('sahra_tf2_auth_register_control')) == 1){ ?>checked <?php } ?> >
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-9" id="sahra_field9"
                                                 style="<?php if ((get_option('sahra_tf2_auth_register_control')) == 0) { ?>display:none; <?php } ?>">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-commenting" style="color: #17A2B8;"></i>
                                                            </div>
                                                            <textarea name="sahra_tf2_auth_register_text"
                                                                      id="sahra_textarea9" class="form-control"
                                                                      placeholder="Tek seferlik doğrulama kodunuz : [kod]
*OTP SMS tek boy gönderilebilir.
*Metin taslağı 140 karakter ile sınırlandırılmıştır."
                                                                      maxlength="140"><?php echo  esc_html(get_option("sahra_tf2_auth_register_text")) ?></textarea>
                                                        </div>
                                                        <p id="sahra_tags_text9" style="margin-top: 10px"><i
                                                                    class="fa fa-angle-double-right"></i>
                                                            Kullanılabilecek Değişkenler : </i></p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-2">
                                                        <p style="margin-top: 5px;">Kod geçerlilik süresi(dk):</p>
                                                    </div>
                                                    <div class="col-sm-10">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-clock-o" style="color: #17A2B8;"></i>
                                                            </div>
                                                            <input name="sahra_tf2_auth_register_diff"
                                                                   class="form-control"
                                                                   placeholder="Kod geçerlilik süresi (dk.) örn: 120"
                                                                   value="<?php echo  esc_html(get_option("sahra_tf2_auth_register_diff")) ?>">
                                                        </div>
                                                        <p>Not : Bu süre boyunca aynı numaraya tekrar kod göndermek
                                                            istense bile gönderilmeyecektir. Süreyi dakika olarak
                                                            yazınız. (varsayılan olarak 180dk. )</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <!--  Sipariş iptal edildiğinde belirlediğim numaralı sms ile bilgilendir -->
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="col-sm-7">
                                                    <label class="control-label" for="">
                                                        <i class="fa fa-certificate" style="color: #3498DB;"></i>
                                                        <i class="fa fa-certificate" style="color: #BB77AE;"></i>
                                                        <i class="fa fa-certificate" style="color: #2ECC71;"></i>
                                                        Kayıtlı telefon numarası ile yeni üyeliği engelle :</label>
                                                </div>
                                                <div class="col-sm-5">
                                                    <label class="switch">
                                                        <input name="sahra_tf2_auth_register_phone_control"
                                                               id="sahra_switch10" type="checkbox"
                                                               onchange="sahra_field_onoff(10)" value="1"
                                                               <?php if ((get_option('sahra_tf2_auth_register_phone_control')) == 1){ ?>checked <?php } ?> >
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-9" id="sahra_field10"
                                                 style="<?php if ((get_option('sahra_tf2_auth_register_phone_control')) == 0) { ?>display:none; <?php } ?>">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-exclamation-circle"
                                                                   style="color: #17A2B8;"></i>
                                                            </div>
                                                            <textarea name="sahra_tf2_auth_register_phone_warning_text"
                                                                      id="sahra_textarea10" class="form-control"
                                                                      placeholder="[telefon_no] numarası ile zaten üyeliğiniz mevcut."><?php echo esc_html(get_option("sahra_tf2_auth_register_phone_warning_text")) ?></textarea>
                                                        </div>
                                                        <p id="sahra_tags_text10" style="margin-top: 10px"><i
                                                                    class="fa fa-angle-double-right"></i>
                                                            Kullanılabilecek Değişkenler : </i></p> <span
                                                                style="opacity: 0.7;">(Uyarı metnidir. Bu seçenekte SMS gönderilmez.) </span>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-2"></div>
                                        <div class="col-sm-10 text-right">
                                            <button class="btn btn-primary" id="login_save4" name="login_save4"
                                                    onclick="login();"><i class="fa fa-folder"></i> Değişiklikleri
                                                Kaydet
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <p>
                                        <h4><i class="fa fa-certificate" style="color: #3498DB;"></i> BU ÖZELLİĞİ TEST
                                            ETTİKTEN SONRA KULLANMAYA BAŞLAYIN.</h4>
                                        <br>
                                        <h4><i class="fa fa-certificate" style="color: #E74C3C;"></i> Bu özelliği
                                            kullanabilmeniz için OTP SMS paketinizin olması gereklidir.
                                            https://www.sahra.com.tr/webportal/ adresinden paket satın alabilirsiniz.
                                        </h4>
                                        <br>
                                        <h4><i class="fa fa-certificate" style="color: #BB77AE;"></i> Bu özellikler
                                            woocommerce e-ticaret eklentisi yüklü ve etkin olduğunda çalışır.
                                            Woocommerce kayıt olma sayfasında geçerlidir.</h4>
                                        <br>
                                        <h4><i class="fa fa-certificate" style="color: #2ECC71;"></i> Bu seçenekteki
                                            metin uyarı olarak gösterilecektir. SMS gönderimi için değildir.</h4>
                                        <br>

                                        <h5>
                                            OTP SMS kuralları için : <a
                                                    href="https://www.sahra.com.tr/dokuman/#otp-sms" target="_blank">https://www.sahra.com.tr/dokuman/#otp-sms</a>
                                            adresini ziyaret edebilirsiniz.
                                        </h5>
                                        <br>
                                        Satır atlamak için <strong>\n</strong> kullanabilirsiniz.

                                    </div>
                                </div>
                                <?php include 'contactform7.php' ?>
                                <div class="tab-pane container-fluid" id="bulkandprivatesms">
                                    <hr>
                                    <div class="row">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" for="sahra_user">Telefon no
                                                : </label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-phone" style="color: #17A2B8;"></i>
                                                    </div>
                                                    <input type="text" name="private_phone" id="private_phone"
                                                           placeholder="Birden fazla numaraya sms göndermek isterseniz aralarına virgül (,) koyarak yazınız."
                                                           class="form-control"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" for="private_text">Mesaj: </label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-commenting" style="color: #17A2B8;"></i>
                                                    </div>
                                                    <textarea type="text" name="private_text" id="private_text" cols="5"
                                                              placeholder="Göndermek istediğiniz mesajı yazınız."
                                                              class="form-control"/></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-right">
                                            <button type="button" class="btn btn-success" onclick="sendBulkandPrivateSMS()"
                                                    name="sendSMS" id="sendSMS"><i class="fa fa-paper-plane"></i> SMS
                                                Gönder
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane container-fluid" id="crm">

                                    <hr>

                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table data-pagination="true" id="table" name="table"
                                                   class="table table-bordered table-striped dataTable no-footer"
                                                   data-search="true" data-search-align="left"
                                                   data-pagination-v-align="bottom"
                                                   data-click-to-select="true"
                                                   data-toggle="table"
                                                   data-page-list="[10, 25, 50, 100, 150, 200]">
                                                   <a role="button" href="javascript:void(0)" class="btn btn-primary ladda-button "  style="float: right;"
                                                                   id="contactsMove"><i class="fa fa-exchange"></i>
                                                                    Tümünü MOBIKOB CRM'e Aktar</a>
                                                <a href="javascript:void(0)"  role="button" class="btn btn-success pr-2"
                                                   style="float: right;" onclick="sahra_sendSMS_bulkTab('')"><i
                                                            class="fa fa-paper-plane"></i> SMS Gönder</a>
                                                <thead>
                                                <tr>
                                                    <th data-checkbox="true"></th>
                                                    <th data-visible="false">userid</th>
                                                    <th>Kullanıcı adı</th>
                                                    <th>İsim</th>
                                                    <th><span>E-posta</span></th>
                                                    <th scope="col" id="phone" class="manage-column column-phone">
                                                        Telefon
                                                    </th>
                                                    <th>İşlemler</th>
                                                </tr>
                                                </thead>
                                                <tbody id="the-list" data-wp-lists="list:user">
                                                <?php
                                                $key2 = 0;
                                                foreach ($users as $key => $user) {
                                                    $data = get_user_meta($user->ID, $sahra_contact_meta_key, true);
                                                    if (isset($data["billing_phone"][0]) && !empty($data["billing_phone"][0])) {
                                                        if ($key2 > 1000) {
                                                            break;
                                                        }
                                                        $key2++;
                                                        ?>
                                                        <tr id="user-<?php echo esc_html( $user->ID )?>">
                                                            <td></td>
                                                            <td><?php echo esc_html( $user->ID )?></td>
                                                            <td class="username column-username has-row-actions column-primary"
                                                                data-colname="Kullanıcı adı">
                                                                <strong><a href="user-edit.php?user_id=<?php echo esc_html( $user->ID) ?>"
                                                                           target="_blank"><?php echo esc_html( $user->display_name) ?></a></strong><br>
                                                            </td>
                                                            <td class="name column-name"
                                                                data-colname="İsim"><?php
                                                                 $user->first_name . " " . $user->last_name;
                                                                
                                                                    
                                                            ?></td>
                                                            <td class="email column-email" data-colname="E-posta"><a
                                                                        href="mailto:<?php echo esc_html( $user->user_email) ?>"><?php echo esc_html( $user->user_email )?></a>
                                                            </td>
                                                            <td class="role column-phone"
                                                                data-colname="Phone"><?php echo esc_html(get_user_meta($user->ID, $sahra_contact_meta_key, true)["billing_phone"][0]) ?>
                                                             
                                                            </td>
                                                            <td>
                                                            <div>
                                                        <!-- <span class="view"> -->
                                                            <a class="btn btn-success ladda-button" role="button" href="javascript:void(0);" title="SMS Gönder"
                                                               onclick="sahra_sendSMS_bulkTab(<?php echo esc_html( $user->ID )?>)"
                                                               id="bulkSMSbtn"><i class="fa fa-paper-plane"></i></a>
                                                        <!-- </span> -->
                                                        <!-- <span class="view"> -->
                                                        <a class="btn btn-info ladda-button" role="button" id="sahra_call" title="Arama Yap"  onclick="sahra_MakeCall(<?php echo esc_html( $user->ID )?>)" ><i class="fa fa-phone"></i></a>

                                                        <a class="btn btn-primary ladda-button" role="button" id="sahra_call" title="MOBIKOB CRM'e Aktar" onclick="add_crm_one_person(<?php echo esc_html( $user->ID )?>)" ><i class="fa fa-exchange"></i></a>
                                                           
                                                        <!-- </span> -->
                                                                </div>



                                                            </td>
                                                        </tr>
                                                    <?php }
                                                } ?>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <?php include 'settings.php' ?>

                            </div>
                        </div>
                    </form>
                  
                </div>
            </div>
        </div>
        <div id="loadingmessage"
             style="display:none;margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 999999; opacity: 0.8;">
            <div style="color: white; position: absolute; top: 50%; left: 50%;transform: translate(-50%, -50%); display: inline-block;">
                <div class="text-center">
                    <i class="fa fa-spinner fa-spin" style="font-size:24px"></i>
                    <br>
                    <span style="color: white;" id="loadMesage">Aktarılıyor, bekleyin...</span>
                </div>
            </div>
        </div>
        <script>
            // setTimeout(function(){
            //     jQuery('[data-toggle="tooltip"]').tooltip();
            //     jQuery("#language a:first").tab("show");

            // },2000);
            jQuery('[data-toggle="tooltip"]').tooltip();
            
            function showLoadingMessage(message) {
                jQuery('#loadMesage').html(message);
                jQuery('#loadingmessage').show();
            }

            function hideLoadingMessage() {
                jQuery('#loadMesage').html('');
                jQuery('#loadingmessage').hide();
            }
     
            function RestrictSpace() {
                if (event.keyCode == 32) {
                    return false;
                }
            }

            jQuery("#language a:first").tab("show");
            function login() {
                jQuery('#sayfayi_yenile').val(1);
            }

            function logout() {
                swal({
                    title: 'Emin misiniz?',
                    text: "Çıkış yapılacak, onaylıyor musunuz?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Evet',
                    cancelButtonText: 'Hayır',
                    buttonsStyling: true,
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        jQuery('#sahra_domain').val('');
                        jQuery('#sahra_user').val('');
                        jQuery('#sahra_pass').val('');
                        jQuery('#input-status').val(0);
                        jQuery('#login_save').click();

                    }
                })
            }

            function cf7_form_change(id, tip, activestatus) {
                jQuery('.cf7_list_text_success_' + tip).hide('slow');
                jQuery('#sahra_cf7_list_text_success_' + tip + '_' + id).show('slow');
                jQuery('#sahra_cf7_list_tags_success_' + tip + '_' + id).show('slow');
                jQuery('#activeStatus_cf7_'+tip).attr('data', id);
            }

            function cf7_form_change2(id, tip, activestatus) {
                jQuery('.cf7_list_' + tip).hide('slow');
                jQuery('#sahra_cf7_list_' + tip + '_' + id).show('slow');
                jQuery('#sahra_cf7_list_'+tip+'_other_' + '_' + id).show('slow');
                jQuery('#' + activestatus).attr('data', id);
                jQuery('#activeStatus_cf7_other_'+tip).attr('data', id);
            }
            function goto_mobikob() {
var url = "https://<?php echo esc_html(get_option('sahra_domain'))?>.mobikob.com"

window.open(url , "_blank")
}


            function order_status_change(id) {
                jQuery('.order_status_text').hide('slow');
                jQuery('#sahra_order_status_text_' + id).show('slow');
                jQuery('#activeStatus').attr('data', id);
                jQuery('#settings-btn-changed').attr('onclick', "settingOpen('" + id + "')");

                if (jQuery('#sahra_order_status_text_' + id + '_json').val() != '') {
                    jQuery('#setting-btn_color').css('color', '#17A2B8');
                } else {
                    jQuery('#setting-btn_color').css('color', '#2B2B2B');
                }
            }

            function sahra_field_onoff(id) {
                var switchstatus = document.getElementById('sahra_switch' + id).checked;
                var field = document.getElementById('sahra_field' + id);
                if (switchstatus) {
                    jQuery('#sahra_field' + id).show('fast')
                }
                else {
                    jQuery('#sahra_field' + id).hide('fast');
                }
            }

            function sahra_field_onoff_custom(id) {
                var switchstatus = document.getElementById('switch_' + id).checked;
                if (switchstatus) {
                    jQuery('#field_' + id).show('fast');
                } else {
                    jQuery('#field_' + id).hide('fast');
                }
            }

            var field1 = ['musteri_adi', 'musteri_soyadi', 'musteri_telefonu', 'musteri_epostasi', 'kullanici_adi', 'tarih', 'saat'];
            var field2 = ['musteri_adi', 'musteri_soyadi', 'musteri_telefonu', 'musteri_epostasi', 'kullanici_adi', 'tarih', 'saat'];
            var field3 = ['siparis_no', 'toplam_tutar', 'musteri_adi', 'musteri_soyadi', 'musteri_telefonu', 'musteri_epostasi', 'kullanici_adi', 'urun_bilgileri', 'tarih', 'saat'];
            var field4 = ['siparis_no', 'toplam_tutar', 'musteri_adi', 'musteri_soyadi', 'musteri_telefonu', 'musteri_epostasi', 'kullanici_adi', 'urun_bilgileri', 'tarih', 'saat'];
            var field5 = ['siparis_no', 'musteri_adi', 'musteri_soyadi', 'aciklama'];
            var field6 = ['siparis_no', 'musteri_adi', 'musteri_soyadi', 'musteri_telefonu', 'musteri_epostasi', 'kullanici_adi', 'tarih', 'saat'];
            var field7 = [''];
            var field8 = ['musteri_adi', 'musteri_soyadi', 'musteri_telefonu', 'musteri_epostasi', 'kullanici_adi', 'urun_kodu', 'urun_adi', 'stok_miktari', 'tarih', 'saat'];
            var field9 = ['kod', 'telefon_no', 'ad', 'soyad', 'mail', 'referans_no', 'tarih', 'saat'];
            var field10 = ['telefon_no', 'ad', 'soyad', 'mail', 'tarih', 'saat'];
            var field11 = ['siparis_no', 'not', 'musteri_adi', 'musteri_soyadi', 'musteri_telefonu', 'musteri_epostasi', 'kullanici_adi', 'siparis_toplamtutar', 'tarih', 'saat'  ];
            var field12 = ['siparis_no', 'not', 'musteri_adi', 'musteri_soyadi', 'musteri_telefonu', 'musteri_epostasi', 'kullanici_adi', 'siparis_toplamtutar', 'tarih', 'saat'  ];
            for (var x = 1; x <= 12; x++) {
                if (x != 5) {   //değişkeni olmayan idler
                    var field = window['field' + x];
                    for (var i = 0; i < field.length; i++) {
                        var textarea = document.getElementById('sahra_tags_text' + x);
                        var mark = '<mark onclick="varfill(' + "'sahra_textarea" + x + "','" + field[i] + "');" + '">[' + field[i] + ']</mark> ';
                        if (textarea && textarea.innerHTML) {
                            textarea.innerHTML += mark;
                        } else {
                            textarea.innerHTML = mark
                        }
                    }
                }
            }

            function varfill(input, degisken) {
                var textarea = document.getElementById(input);
                if (jQuery('#' + input).is(":visible")) {
                    var start = textarea.selectionStart;
                    var end = textarea.selectionEnd;
                    var finText = textarea.value.substring(0, start) + '[' + degisken + ']' + textarea.value.substring(end);
                    textarea.value = finText;
                    textarea.focus();
                    textarea.selectionEnd = end + (degisken.length + 2);
                }
            }
        </script>

        <?php
    } else {
        ?>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-danger">
                        <h1>Sahra eklentisine sadece yetki verilen kullanıcılar erişebilir. </h1>
                    </div>
                    <div class="alert alert-info">
                        <h2><strong><?php print $session->display_name. ' <small>('.$session->user_login.')</small>' ?></strong> kullanıcısı için, erişimi olan bir yönetici hesabı ile giriş yapıp; Sahra eklentisi > Ayarlar sekmesinden izin verebilirsiniz.</h2>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}//login kontrol
else
{
    $text = ['Administrator(Yönetici)'];
    if($sahra_auth_roles_control == 1){
        foreach ($auth_roles as $auth_role) {
            array_push($text, $role_list[$auth_role]);
        }
    }
    ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-danger">
                    <h1>Sahra eklentisine sadece <?php print implode($text, ', '); ?> rollerine sahip kullanıcılar erişebilir. </h1>
                </div>
                <div class="alert alert-info">
                    <h2><b><?php print $role_list[$session->roles[0]] ?></b> rolüne sahip bu kullanıcı için, Yönetici hesabı ile giriş yapıp; Sahra eklentisi > Ayarlar sekmesinden izin verebilirsiniz.</h2>
                </div>
            </div>
        </div>
    </div>
<?php

}
?>