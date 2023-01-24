<?php
/*
 * @copyright   Leyun internet Technology(Shanghai)Co.,Ltd
 * @license     http://www.dzzoffice.com/licenses/license.txt
 * @package     DzzOffice
 * @link        http://www.dzzoffice.com
 * @author      zyx(zyx@dzz.cc)
 */
if(!defined('IN_DZZ')) {
	exit('Access Denied');
} 
Hook::listen('email_chk',$_GET);
$navtitle=lang('Safety management').' - '.lang('myCountCenter');
Hook::listen('check_login');
$verify = C::t('user_verify')->fetch($_G['uid']);//验证信息
$do=trim($_GET['do']) ? trim($_GET['do']):'editpass';

$uid=intval($_G['uid']); 
$seccodecheck = $_G['setting']['seccodestatus'] & 4; 
$hook_qqbind_arr=array("uid"=>$uid); 
$member=C::t('user_profile')->get_userprofile_by_uid($_G['uid']); 
//$openid= C::t('user_qqconnect')->fetch_bindstatus_by_uid($uid);

if($do == 'editpass'){

    $strongpw = ($_G['setting']['strongpw']) ? json_encode($_G['setting']['strongpw']):'';
    if(isset($_GET['editpass'])){

        //验证提交是否合法，阻止外部非法提交
        chk_submitroule($type);

        //验证码
        if(!check_seccode( $_GET['seccodeverify'],$_GET['sechash'])){

            showTips(array('error'=>lang('submit_seccode_invalid')), $type);
        }
        //验证原密码
        $password0=$_GET['password0']; 
		if( md5(md5("").$member['salt'])!=$member['password']) {
			if(md5(md5($password0).$member['salt'])!=$member['password']){ 
				showTips(array('error'=>lang('password_error')), $type);
			}
		}  

        if($_GET['password'] && $_G['setting']['pwlength']) {
            if(strlen($_GET['password']) < $_G['setting']['pwlength']) {

                showTips(array('error'=>lang('profile_password_tooshort'),'pwlength'=>$_G['setting']['pwlength']), $type);
            }
        }

        //验证密码强度
        if($_GET['password'] && $_G['setting']['strongpw']) {
            $strongpw_str = array();
            if(in_array(1, $_G['setting']['strongpw']) && !preg_match("/\d+/", $_GET['password'])) {
                $strongpw_str[] = lang('strongpw_1');
            }
            if(in_array(2, $_G['setting']['strongpw']) && !preg_match("/[a-z]+/", $_GET['password'])) {
                $strongpw_str[] = lang('strongpw_2');
            }
            if(in_array(3, $_G['setting']['strongpw']) && !preg_match("/[A-Z]+/", $_GET['password'])) {
                $strongpw_str[] = lang('strongpw_3');
            }
            if(in_array(4, $_G['setting']['strongpw']) && !preg_match("/[^a-zA-z0-9]+/", $_GET['password'])) {
                $strongpw_str[] = lang('strongpw_4');
            }
            if($strongpw_str) {

                showTips(array('error'=>lang('password_weak').implode(',', $strongpw_str)), $type);

            }
        }

        if($_GET['password'] && $_GET['password'] !== $_GET['password2']) {

            showTips(array('error'=>lang('profile_passwd_notmatch')), $type);
        }
        $setarr=array();

        if($_GET['password']){

            $password = preg_match('/^\w{32}$/', $_GET['password']) ? $_GET['password'] : md5($_GET['password']);

            $password = md5($password.$member['salt']);
        }

        if($password && C::t('user')->update_password($_G['uid'],$password)){
            showTips(array('success'=>lang('update_password_success')), $type);
            exit();
        }
    }

}
elseif($do == 'login'){
$asc = isset($_GET['asc']) ? intval($_GET['asc']) : 1;
$uid =$_G['uid'];
$order = in_array($_GET['order'], array('dateline', 'count')) ? trim($_GET['order']) : 'dateline';
$page = empty($_GET['page']) ? 1 : intval($_GET['page']);
$perpage = 20;
$start = ($page - 1) * $perpage;
$sql = "1";
$gets = array('op' => 'password', 'do' => 'login', 'order' => $order, 'asc' => $asc);
$theurl = MOD_URL."&" . url_implode($gets);
$orderby = " order by $order " . ($asc ? 'DESC' : '');
$param = array('user_login');
$list = array();
	if ($count = DB::result_first("SELECT COUNT(*) FROM %t WHERE uid =$uid", $param)) {
		$list = DB::fetch_all("SELECT * FROM %t WHERE uid =$uid and $sql $orderby limit $start,$perpage", $param);
	}
$multi = multi($count, $perpage, $page, $theurl, 'pull-right');
}
elseif($do == 'changeemail'){

    $emailchange = $member['emailstatus'];

    $bindemail = isset($_GET['newemail']) ? $_GET['newemail']:'';

    if(!empty($bindemail)){

        if(C::t('user')->chk_email_by_uid($bindemail,$uid)){

            showTips(array('error'=>lang('profile_email_duplicate')),$type);
        }

        $idstring = random(6);

        $type = $_GET['returnType'];

        $confirmurl = C::t('shorturl')->getShortUrl("{$_G[siteurl]}user.php?mod=profile&op=password&do=changeemail&uid={$_G[uid]}&id=$idstring&email={$bindemail}");
        $sitename=$_G['setting']['sitename'];
        $email_bind_message = <<<EOT
      <p style="font-size:14px;color:#333; line-height:24px; margin:0;">尊敬的用户$member[username],您好！</p>
      <p style="line-height: 24px; margin: 6px 0px 0px; overflow-wrap: break-word; word-break: break-all;"><span style="color: rgb(51, 51, 51); font-size: 14px;">这封信是由 $sitename 发送的。您收到这封邮件，是由于在 $sitename 进行了Email 绑定操作，或修改 Email 绑定使用了这个邮箱地址。如果您不是 $sitename 的用户，或没有进行上述操作，请忽略这封邮件。您不需要退订或进行其他进一步的操作。</span></p>
      <p style="line-height: 24px; margin: 6px 0px 0px; overflow-wrap: break-word; word-break: break-all;"><span style="color: rgb(51, 51, 51); font-size: 14px;font-weight:bold;">邮箱绑定链接：</span></p>
      <p style="line-height: 24px; margin: 6px 0px 0px; overflow-wrap: break-word; word-break: break-all;"><span style="color: rgb(51, 51, 51); font-size: 12px;"><a href="$confirmurl" style="text-decoration-line: none; word-break: break-all; overflow-wrap: normal; color: rgb(51, 51, 51); font-size: 12px;" rel="noopener" target="_blank"><span style="color: rgb(0, 164, 255);">$confirmurl</span></a><span style="font-size: 12px; color: rgb(51, 51, 51);">,(如果上面不是链接形式，请将该地址手工粘贴到浏览器地址栏再访问)</span></p>
EOT;
        if(!sendmail("$member[username] <$bindemail>",'Email 绑定', $email_bind_message)) {

            runlog('sendmail', "$bindemail  发送失败");

            showTips(array('error'=>lang('setting_mail_send_error')),$type);

        }else{
            $updatearr = array("emailsenddate"=>$idstring.'_'.time());
            C::t('user')->update($uid,$updatearr);
            showTips(array('success'=>array('email'=>$bindemail)),$type);

        }

    }
}
elseif ($do == 'bindqq'){//绑定qq

    @session_start();

    if($_G['setting']['qq_login']!='1'){

        showTips(array('lang'=>lang('qq_login_closed'),'referer'=>$_G['siteurl']),'html');
    }

    require_once DZZ_ROOT."./user/api_qqlogin/qqConnectAPI.php";

    $inurl = $_SERVER["HTTP_REFERER"]; //来路

    $_SESSION['bindqq'] = true;

    $_SESSION['bind_ref'] = $inurl;

    $qc = new QC();

    $qc->qq_login();

}elseif ($do  == 'unbindqq') {//取消qq绑定

    if(C::t('user_qqconnect')->delete($_GET['openid'])){
        showmessage(lang('qq_unbind_success'), dreferer(), array(), array('alert' => 'right'));
    }else{
        showmessage(lang('qq_unbind_failed'), dreferer(), array(), array('alert' => 'right'));
    }
}
//三方登录未设置密码时不需要输入原密码
$showoldpassword=1; 
if( md5(md5("").$member['salt'])==$member['password']) {
	$showoldpassword=0;
} 
include template('pass_safe');
exit();