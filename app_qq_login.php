<?php
require( 'wp-load.php' );
$user_id=$current_user->ID;
//获取设备cid
$uniapp_cid=$_POST['uniapp_cid'];
//登录类型
$leixin=$_POST['leixin'];
//微博openid
$wbopenid=$_POST['wbopenid'];
//微信头像
$headimgurl=$_POST['headimgurl'];
//微信联盟ID
$unionid=$_POST['unionid'];
//openid
$qq_openid=$_POST['openid'];
//用户昵称
$username=$_POST['nickname'];
//QQ地址
$avatar=$_POST['figureurl_qq'];
//qq-token
$access_token=$_POST['access_token'];
//微信登录判断
$uniapp_cid_user=$_GET['uniapp_cid_user'];
//处理绑定登录
$bdlogin=$_POST['name'];//绑定类型
$wx_openid=$_POST['wx_openid'];//微信的opnenid
$wx_unionid=$_POST['wx_unionid'];//微信的unionid
$qqq_openid=$_POST['qq_openid'];//微信的opnenid
$qq_unionid=$_POST['qq_unionid'];//微信的unionid
$qq_figureurl=$_POST['qq_figureurl'];//微信的unionid
$wx_avatarUrl=$_POST['wx_avatarUrl'];//微信的unionid
$profile_image_url=$_POST['profile_image_url'];//微信的unionid






/***********************用户绑定第三方登录*********************/
 //解绑微信登录
if($bdlogin=='bdweixin' && $wx_openid && $wx_unionid){
global $wpdb;
//查询微信之前是否已经绑定了
$wx_unionid_num = $wpdb -> get_var("SELECT user_id FROM $wpdb->usermeta WHERE meta_key='wechat_unionid' AND meta_value='$wx_unionid' ");
if(!$wx_unionid_num){
update_user_meta($user_id,'weixin_uid',$wx_openid);
update_user_meta($user_id,'wechat_unionid',$wx_unionid);
update_user_meta($user_id ,"wechat_avatar",$wx_avatarUrl);
update_user_meta($user_id, "avatar_type", 'weixin');

echo '{"success":"0000"}';
}else{
echo '{"success":"0001"}';
}}
 //绑定QQ登录
if($bdlogin=='bdqq' && $qqq_openid){
$url='https://graph.qq.com/oauth2.0/me?access_token='.$access_token.'&unionid=1&fmt=json';
$ch = curl_init(); // 创建一个 cURL 资源
curl_setopt($ch, CURLOPT_URL, $url); // CURLOPT_URL 目标 url 地址
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // CURLOPT_SSL_VERIFYPEER False: 终止 cURL 在服务器进行验证
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // CURLOPT_RETURNTRANSFER 返回原生的（Raw）输出
$output = curl_exec($ch);
$json_output = json_decode($output, true);
$qq_unionid = $json_output['unionid'];
global $wpdb;
//查询微信之前是否已经绑定了
$qq_unionid_num = $wpdb -> get_var("SELECT user_id FROM $wpdb->usermeta WHERE meta_key='qq_unionid' AND meta_value='$qq_unionid' ");
if(!$qq_unionid_num){
update_user_meta($user_id,'qq_openid',$qqq_openid);
update_user_meta($user_id,'qq_unionid',$qq_unionid);
update_user_meta($user_id, "qq_avatar", $qq_figureurl);
update_user_meta($user_id, "avatar_type", 'qq');

echo '{"success":"0000"}';
}else{
echo '{"success":"0001"}';
}}
//绑定weibo登录
if($bdlogin=='bdweibo' && $qqq_openid){
global $wpdb;
//查询微博
$wb_unionid_num = $wpdb -> get_var("SELECT user_id FROM $wpdb->usermeta WHERE meta_key='weibo_uid' AND meta_value='$qq_unionid' ");
if(!$wb_unionid_num){
update_user_meta($user_id,'weibo_uid',$qq_unionid);
update_user_meta($user_id, "weibo_avatar", $profile_image_url);
update_user_meta($user_id, "avatar_type", 'weibo');
echo '{"success":"0000"}';
}else{
echo '{"success":"0001"}';
}}
/**********************END*************************/




/**********************取得CID和移动端配色**************************/
//如果获取到设备码就执行
if($uniapp_cid_user){
//只有用户登录的情况在执行
if(is_user_logged_in()) {
global $wpdb;
//查询这个设备码之前是否已经绑定
$user_cid = $wpdb -> get_var("SELECT user_id FROM $wpdb->usermeta WHERE meta_key='uniapp_cid'AND meta_value='$uniapp_cid_user' ");
get_currentuserinfo();
//写入设备码到用户资料
delete_user_meta($user_cid, "uniapp_cid");
update_user_meta($current_user->ID , "uniapp_cid", $uniapp_cid_user);

} 
} 
$default_color = jinsom_get_option('jinsom_mobile_default_color');//默认颜色风格
if($uniapp_cid_user){
echo '{"success":"'.$default_color.'"}';
} 
/****************************END**************************/


/***********************微信登录逻辑************************/
if($leixin=='weixin' && $qq_openid && $unionid){
$text='登录类型:【'.$leixin.'】   openid:【'.$qq_openid.'】    昵称:【'.$username.'】   联盟ID'.$unionid.'   时间:'.date('Y-m-d H:i:s');
file_put_contents("login.log",$text."\r\n", FILE_APPEND);

global $wpdb;
 $weixin_unionid = $wpdb -> get_var("SELECT user_id FROM $wpdb->usermeta WHERE meta_key='wechat_unionid' AND meta_value='$unionid' ");
 //判断是否有微信
 if($weixin_unionid && $qq_openid && $unionid){
  wp_set_current_user($weixin_unionid, $user_login);
  wp_set_auth_cookie($weixin_unionid);
  do_action('wp_login', $user_login);
echo '{"success":"0000"}';
 }elseif(!$weixin_unionid && $qq_openid && $unionid){
$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );         
$userdata = array(
    'ID' => $user_id, // ID of existing user
    'user_login' => 'WX_'.time(),
    'user_pass'  =>  md5($random_password) ,// no plain password here!
    'nickname' => $username,
    'display_name' => $username
); 
//获取新创建用户的ud
$user_id = wp_insert_user( $userdata ) ;
$headimgurl = preg_replace("/^http:/i", "https:", $headimgurl);
//写入openid到自定义字段
update_user_meta($user_id ,"wechat_unionid",$unionid);
update_user_meta($user_id ,"wechat_avatar",$headimgurl);
update_user_meta($user_id, "avatar_type", 'weixin');
update_user_meta($user_id, "leixin", $leixin);
update_user_meta($user_id,'weixin_uid',$qq_openid);

//登录新注册的账号
  wp_set_current_user($user_id, $user_login);
  wp_set_auth_cookie($user_id);
  do_action('wp_login', $user_login);
  echo '{"success":"0000"}';

 }
}
/***********************end************************/

/***********************QQ登录逻辑************************/
if($leixin=='qq' && $qq_openid){
$url='https://graph.qq.com/oauth2.0/me?access_token='.$access_token.'&unionid=1&fmt=json';
$ch = curl_init(); // 创建一个 cURL 资源
curl_setopt($ch, CURLOPT_URL, $url); // CURLOPT_URL 目标 url 地址
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // CURLOPT_SSL_VERIFYPEER False: 终止 cURL 在服务器进行验证
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // CURLOPT_RETURNTRANSFER 返回原生的（Raw）输出
$output = curl_exec($ch);
$json_output = json_decode($output, true);
$qq_unionid = $json_output['unionid'];
global $wpdb;
    $user_qq = $wpdb -> get_var("SELECT user_id FROM $wpdb->usermeta WHERE meta_key='qq_unionid' AND meta_value='$qq_unionid' ");
$text='登录类型:【'.$leixin.'】   openid:【'.$qq_openid.'】   昵称:【'.$username.'】   联盟ID:'.$qq_unionid.'   时间:'.date('Y-m-d H:i:s');
file_put_contents("login.log",$text."\r\n", FILE_APPEND);
//如果用户没注册,就注册
        if (!$user_qq && $qq_unionid && $qq_openid) {
            $nickname = str_replace(' ', '', $username);//去掉空格
            $nickname = jinsom_filter_emoji($nickname);//过滤emoji
            $name_max = jinsom_get_option('jinsom_reg_name_max');
            $nickname = mb_substr($nickname, 0, $name_max, 'utf-8');
            if (jinsom_nickname_exists($nickname)) {
                $nickname = $nickname.'_'.rand(0, 999);
            }
            $login_name = wp_create_nonce($qq_openid);
            $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
            $userdata = array(
                'user_login' => 'qq_'.time(),
                'user_pass' => $random_password,
                'nickname' => $nickname
            );
            $user_id = wp_insert_user($userdata);
            $avatar = preg_replace("/^http:/i", "https:", $avatar);
            wp_signon(array("user_login"=> $login_name, "user_password"=> $random_password), is_ssl());
            update_user_meta($user_id, "qq_openid", $qq_openid);
            update_user_meta($user_id, "access_token", $access_token);
            update_user_meta($user_id, "qq_unionid", $qq_unionid);
            update_user_meta($user_id, "qq_avatar", $avatar);
            update_user_meta($user_id, "avatar_type", 'qq');
             update_user_meta($user_id, "leixin", $leixin);
            $user = get_user_by('id', $user_id);
            wp_set_current_user($user_id, $user -> user_login);
            wp_set_auth_cookie($user_id, true);
            do_action('wp_login', $user -> user_login);
             $data_arr['msg']='qq注册成功';
          echo '{"success":"0000"}';

        } 


       elseif($user_qq && $qq_unionid && $qq_openid) {
        //使用QQ登录
  wp_set_current_user($user_qq, $user_login);
  wp_set_auth_cookie($user_qq);
  do_action('wp_login', $user_login);
            $data_arr['msg']='qq登录成功';
        echo '{"success":"0000"}';
        }
      }
/***********************END************************/




/***********************微博登录逻辑************************/
if($leixin=='weibo' && $qq_openid){
global $wpdb;
    $user_weibo = $wpdb -> get_var("SELECT user_id FROM $wpdb->usermeta WHERE meta_key='weibo_uid' AND meta_value='$qq_openid'");
$text='登录类型:【'.$leixin.'】   openid:【'.$qq_openid.'】   昵称:【'.$username.'】   登录用户:'.$user_weibo.'   时间:'.date('Y-m-d H:i:s');
file_put_contents("login.log",$text."\r\n", FILE_APPEND);

        if (!$user_weibo && $qq_openid) {
            $nickname = str_replace(' ', '', $username);//去掉空格
            $nickname = jinsom_filter_emoji($nickname);//过滤emoji
            $name_max = jinsom_get_option('jinsom_reg_name_max');
            $nickname = mb_substr($nickname, 0, $name_max, 'utf-8');
            if (jinsom_nickname_exists($nickname)) {
                $nickname = $nickname.'_'.rand(0, 999);
            }
            $login_name = wp_create_nonce($user_weibo);
            $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
            $userdata = array(
                'user_login' => 'wb_'.time(),
                'user_pass' => $random_password,
                'nickname' => $nickname
            );
            $user_id = wp_insert_user($userdata);
            wp_signon(array("user_login"=> $login_name, "user_password"=> $random_password), is_ssl());
             $avatar = preg_replace("/^http:/i", "https:", $avatar);
            update_user_meta($user_id, "weibo_uid", $qq_openid);
            update_user_meta($user_id, "access_token", $access_token);
            update_user_meta($user_id, "weibo_avatar", $avatar);
            update_user_meta($user_id, "avatar_type", 'weibo');
             update_user_meta($user_id, "leixin", $leixin);
            $user = get_user_by('id', $user_id);
            wp_set_current_user($user_id, $user -> user_login);
            wp_set_auth_cookie($user_id, true);
            do_action('wp_login', $user -> user_login);
             $data_arr['msg']='wb注册成功';
             echo '{"success":"0000"}';

        }elseif($user_weibo && $qq_openid){
   
        //使用QQ登录
  wp_set_current_user($user_weibo, $user_login);
  wp_set_auth_cookie($user_weibo);
  do_action('wp_login', $user_login);
 $data_arr['msg']='qq登录成功';
echo '{"success":"0000"}';
        }


header('content-type:application/json');
}
/***********************END************************/
