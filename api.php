<?php
include_once('wp-load.php');
$xcx_code=$_GET['xcx_code'];//小程序传递code
$xcx_text=$_GET['xcx_text'];//小程序类型
$user_id=$current_user->ID;

if($xcx_text=='小程序code码'){
$WeChat_xcx=get_user_meta($user_id,"WeChat-xcx_openid",true);
if(!$WeChat_xcx){
    $cstqurl='https://api.weixin.qq.com/sns/jscode2session?appid=d5e774d8502823c76341ae705137d30a&js_code='.$xcx_code.'&grant_type=authorization_code';
    $cstqdata = file_get_contents($cstqurl);
    $cstqdata = json_decode($cstqdata,true);
$unionid=$cstqdata['unionid'];
global $wpdb;
//查询这个设备码之前是否已经绑定
$user_xcx = $wpdb -> get_var("SELECT user_id FROM wp_usermeta WHERE meta_key='wechat_unionid' AND meta_value='$unionid'");
get_currentuserinfo();

echo'{"openid":"'.$cstqdata['openid'].'","user_id":"'.$user_xcx.'","unionid":"'.$unionid.'","测试21":"'.$current_user->ID.'"}';
if($user_xcx){
update_user_meta($user_xcx,"WeChat-xcx_openid",$cstqdata['openid']);
}
}

}
