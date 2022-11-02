<?php
if(!defined('IN_DZZ')) {
	exit('Access Denied');
}
@set_time_limit(300);
function sendmail($toemail, $subject, $message, $from = '') {
	global $_G;
	if(!is_array($_G['setting']['mail'])) {
		$_G['setting']['mail'] = dunserialize($_G['setting']['mail']);
	}
	$_G['setting']['mail']['server'] = $_G['setting']['mail']['port'] = $_G['setting']['mail']['auth'] = $_G['setting']['mail']['from'] = $_G['setting']['mail']['auth_username'] = $_G['setting']['mail']['auth_password'] = '';
	if($_G['setting']['mail']['mailsend'] != 1) {
		$smtpnum = count($_G['setting']['mail']['smtp']);
		if($smtpnum) {
			$rid = rand(0, $smtpnum-1);
			$smtp = $_G['setting']['mail']['smtp'][$rid];
			$_G['setting']['mail']['server'] = $smtp['server'];
			$_G['setting']['mail']['port'] = $smtp['port'];
			$_G['setting']['mail']['auth'] = $smtp['auth'] ? 1 : 0;
			$_G['setting']['mail']['from'] = $smtp['from'];
			$_G['setting']['mail']['auth_username'] = $smtp['auth_username'];
			$_G['setting']['mail']['auth_password'] = $smtp['auth_password'];
		}
	}
	$message = preg_replace("/href\=\"(?!(http|https)\:\/\/)(.+?)\"/i", 'href="'.$_G['siteurl'].'\\1"', $message);
  $sitename=$_G['setting']['sitename'];
  $sitelogo=IO::getFileUri('attach::'.$_G['setting']['sitelogo']);
$message = <<<EOT
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=$_G[charset]">
    <title>$subject</title>
  </head>
  <body>
    <div style="box-sizing:border-box;text-align:center;min-width:320px;border:1px solid #ebeef5; background-color:#f2f6fc; border-radius: .5rem;margin:auto; padding:20px 0 30px; font-family:'helvetica neue',PingFangSC-Light,arial,'hiragino sans gb','microsoft yahei ui','microsoft yahei',simsun,sans-serif">
        <table style="width:100%;font-weight:300;margin-bottom:10px;border-collapse:collapse">
          <tbody>
            <tr style="font-weight:300">
              <td style="width:3%;"></td>
              <td>
                <div>
                  <a href="$_G[siteurl]" rel="noopener" target="_blank">
                    <img border="0" src="$sitelogo">
                  </a>
                </div>
                <p style="height:2px;background-color: #00a4ff;border: 0;font-size:0;padding:0;width:100%;margin-top:20px;"></p>
                <div style="background-color:#fff; padding:23px 0 20px;border-radius:0 0 .5rem .5rem;box-shadow: 0px 1px 1px 0px rgba(122, 55, 55, 0.2);text-align:left;">
                  <table style="width:100%;font-weight:300;margin-bottom:10px;border-collapse:collapse;text-align:left;">
                    <tbody>
                      <tr style="font-weight:300">
                        <td style="width:3.2%;max-width:30px;"></td><td style="text-align:left;"><h1 style="font-weight:bold;font-size:20px; line-height:36px; margin:0 0 16px;">$subject</h1>
                        $message
                        <p style="line-height: 24px; margin: 6px 0px 0px; overflow-wrap: break-word; word-break: break-all;"><span style="color: rgb(51, 51, 51); font-size: 14px;"><br>
                          </span></p>
                        <dl style="font-size: 14px; color: rgb(51, 51, 51); line-height: 18px;"></dl>
                        <dl style="font-size: 14px; color: rgb(51, 51, 51); line-height: 18px;">
                          <dd style="margin: 0px 0px 6px; padding: 0px; font-size: 12px; line-height: 22px;">
                            <p style="font-size: 14px; line-height: 26px; word-wrap: break-word; word-break: break-all; margin-top: 32px;"><span style="color: rgb(0, 0, 0);">此致 <br>
                              </span>
                              <strong><span style="color: rgb(0, 0, 0);">$sitename 团队</span></strong>
                            </p>
                          </dd>
                        </dl>
                        </td>
                        <td style="width:3.2%;max-width:30px;"></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div style="text-align:center; font-size:12px; line-height:18px; color:#999">
                  <table style="width:100%;font-weight:300;margin-bottom:10px;border-collapse:collapse">
                      <tbody>
                          <tr style="font-weight:300">
                              <td style="width:3.2%;max-width:30px;"></td>
                              <td>
                                  <p style="text-align:center; margin:20px auto 14px auto;font-size:12px;color:#999;">此为系统邮件，请勿回复</p>
																	<p style="margin:auto;font-size:12px;color:#999;text-align:center;line-height:22px;">
                                  $sitename <a href="$_G[siteurl]" target="_blank">$_G[siteurl]</a>
                                  </p>
                                  <p style="margin:auto;font-size:12px;color:#999;text-align:center;line-height:22px;">
                                  Copyright &copy;2012-2022 <a href="http://www.dzzoffice.com/" target="_blank">DzzOffice</a>  All Rights Reserved
                                    <br>
                                  </p>
                            </td>
                            <td style="width:3.2%;max-width:30px;"></td>
                        </tr>
                    </tbody>
                  </table>
                </div>
              </td>
              <td style="width:3%;max-width:30px;"></td>
            </tr>
          </tbody>
        </table>
    </div>
  </body>
</html>
EOT;

	$maildelimiter = $_G['setting']['mail']['maildelimiter'] == 1 ? "\r\n" : ($_G['setting']['mail']['maildelimiter'] == 2 ? "\r" : "\n");
	$mailusername = isset($_G['setting']['mail']['mailusername']) ? $_G['setting']['mail']['mailusername'] : 1;
	$_G['setting']['mail']['port'] = $_G['setting']['mail']['port'] ? $_G['setting']['mail']['port'] : 25;
	$_G['setting']['mail']['mailsend'] = $_G['setting']['mail']['mailsend'] ? $_G['setting']['mail']['mailsend'] : 1;

	if($_G['setting']['mail']['mailsend'] == 3) {
		$email_from = empty($from) ? $_G['setting']['adminemail'] : $from;
	} else {
		$email_from = $from == '' ? '=?'.CHARSET.'?B?'.base64_encode($_G['setting']['sitename'])."?= <".$_G['setting']['adminemail'].">" : (preg_match('/^(.+?) \<(.+?)\>$/',$from, $mats) ? '=?'.CHARSET.'?B?'.base64_encode($mats[1])."?= <$mats[2]>" : $from);
	}

	$email_to = preg_match('/^(.+?) \<(.+?)\>$/',$toemail, $mats) ? ($mailusername ? '=?'.CHARSET.'?B?'.base64_encode($mats[1])."?= <$mats[2]>" : $mats[2]) : $toemail;

	$email_subject = '=?'.CHARSET.'?B?'.base64_encode(preg_replace("/[\r|\n]/", '', ''.$_G['setting']['sitename'].' '.$subject)).'?=';
	
	$email_message = chunk_split(base64_encode(str_replace("\n", "\r\n", str_replace("\r", "\n", str_replace("\r\n", "\n", str_replace("\n\r", "\r", $message))))));
	$host = $_SERVER['HTTP_HOST'];
	$version = $_G['setting']['version'];
	$headers = "From: $email_from{$maildelimiter}X-Priority: 3{$maildelimiter}X-Mailer: $host $version {$maildelimiter}MIME-Version: 1.0{$maildelimiter}Content-type: text/html; charset=".CHARSET."{$maildelimiter}Content-Transfer-Encoding: base64{$maildelimiter}";
	if($_G['setting']['mail']['mailsend'] == 1) {
		if(function_exists('mail') && @mail($email_to, $email_subject, $email_message, $headers)) {
			return true;
		}
		return false;

	} elseif($_G['setting']['mail']['mailsend'] == 2) {

		if(!$fp = fsocketopen($_G['setting']['mail']['server'], $_G['setting']['mail']['port'], $errno, $errstr, 30)) {
			runlog('SMTP', "({$_G[setting][mail][server]}:{$_G[setting][mail][port]}) CONNECT - Unable to connect to the SMTP server", 0);
			return false;
		}
		
		stream_set_blocking($fp, true);

		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != '220') {
			runlog('SMTP', "{$_G[setting][mail][server]}:{$_G[setting][mail][port]} CONNECT - $lastmessage", 0);
			return false;
		}

		fputs($fp, ($_G['setting']['mail']['auth'] ? 'EHLO' : 'HELO')." uchome\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 220 && substr($lastmessage, 0, 3) != 250) {
			runlog('SMTP', "({$_G[setting][mail][server]}:{$_G[setting][mail][port]}) HELO/EHLO - $lastmessage", 0);
			return false;
		}

		while(1) {
			if(substr($lastmessage, 3, 1) != '-' || empty($lastmessage)) {
				break;
			}
			$lastmessage = fgets($fp, 512);
		}

		if($_G['setting']['mail']['auth']) {
			fputs($fp, "AUTH LOGIN\r\n");
			$lastmessage = fgets($fp, 512);
			if(substr($lastmessage, 0, 3) != 334) {
				runlog('SMTP', "({$_G[setting][mail][server]}:{$_G[setting][mail][port]}) AUTH LOGIN - $lastmessage", 0);
				return false;
			}

			fputs($fp, base64_encode($_G['setting']['mail']['auth_username'])."\r\n");
			$lastmessage = fgets($fp, 512);
			if(substr($lastmessage, 0, 3) != 334) {
				runlog('SMTP', "({$_G[setting][mail][server]}:{$_G[setting][mail][port]}) USERNAME - $lastmessage", 0);
				return false;
			}

			fputs($fp, base64_encode($_G['setting']['mail']['auth_password'])."\r\n");
			$lastmessage = fgets($fp, 512);
			if(substr($lastmessage, 0, 3) != 235) {
				runlog('SMTP', "({$_G[setting][mail][server]}:{$_G[setting][mail][port]}) PASSWORD - $lastmessage", 0);
				return false;
			}

			$email_from = $_G['setting']['mail']['from'];
		}

		fputs($fp, "MAIL FROM: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $email_from).">\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 250) {
			fputs($fp, "MAIL FROM: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $email_from).">\r\n");
			$lastmessage = fgets($fp, 512);
			if(substr($lastmessage, 0, 3) != 250) {
				runlog('SMTP', "({$_G[setting][mail][server]}:{$_G[setting][mail][port]}) MAIL FROM - $lastmessage", 0);
				return false;
			}
		}

		fputs($fp, "RCPT TO: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $toemail).">\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 250) {
			fputs($fp, "RCPT TO: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $toemail).">\r\n");
			$lastmessage = fgets($fp, 512);
			runlog('SMTP', "({$_G[setting][mail][server]}:{$_G[setting][mail][port]}) RCPT TO - $lastmessage", 0);
			return false;
		}

		fputs($fp, "DATA\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 354) {
			runlog('SMTP', "({$_G[setting][mail][server]}:{$_G[setting][mail][port]}) DATA - $lastmessage", 0);
			return false;
		}

		$timeoffset = $_G['setting']['timeoffset'];
		if(function_exists('date_default_timezone_set')) {
			@date_default_timezone_set('Etc/GMT'.($timeoffset > 0 ? '-' : '+').(abs($timeoffset)));
		}

		$headers .= 'Message-ID: <'.date('YmdHs').'.'.substr(md5($email_message.microtime()), 0, 6).rand(100000, 999999).'@'.$_SERVER['HTTP_HOST'].">{$maildelimiter}";
		fputs($fp, "Date: ".date('r')."\r\n");
		fputs($fp, "To: ".$email_to."\r\n");
		fputs($fp, "Subject: ".$email_subject."\r\n");
		fputs($fp, $headers."\r\n");
		fputs($fp, "\r\n\r\n");
		fputs($fp, "$email_message\r\n.\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 250) {
			runlog('SMTP', "({$_G[setting][mail][server]}:{$_G[setting][mail][port]}) END - $lastmessage", 0);
		}
		fputs($fp, "QUIT\r\n");

		return true;

	} elseif($_G['setting']['mail']['mailsend'] == 3) {

		ini_set('SMTP', $_G['setting']['mail']['server']);
		ini_set('smtp_port', $_G['setting']['mail']['port']);
		ini_set('sendmail_from', $email_from);

		if(function_exists('mail') && @mail($email_to, $email_subject, $email_message, $headers)) {
			return true;
		}
		return false;
	}
}

function sendmail_cron($toemail, $subject, $message) {
	global $_G;
	$toemail = addslashes($toemail);

	$value = C::t('mailcron')->fetch_all_by_email($toemail, 0, 1);
	$value = $value[0];
	if($value) {
		$cid = $value['cid'];
	} else {
		$cid = C::t('mailcron')->insert(array('email' => $toemail), true);
	}
	$message = preg_replace("/href\=\"(?!(http|https)\:\/\/)(.+?)\"/i", 'href="'.$_G['siteurl'].'\\1"', $message);
	$setarr = array(
		'cid' => $cid,
		'subject' => $subject,
		'message' => $message,
		'dateline' => $_G['timestamp']
	);
	C::t('mailqueue')->insert($setarr);

	return true;
}

function sendmail_touser($touid, $subject, $message, $mailtype='') {
	global $_G;

	if(empty($_G['setting']['sendmailday'])) return false;

	require_once libfile('function/user','','user');
	$tospace = getuserbyuid($touid);
	if(empty($tospace['email'])) return false;

	//space_merge($tospace, 'field_home');
	space_merge($tospace, 'status');

	$acceptemail = $tospace['acceptemail'];
	if(!empty($acceptemail[$mailtype]) && $_G['timestamp'] - $tospace['lastvisit'] > $_G['setting']['sendmailday']*86400) {
		if(empty($tospace['lastsendmail'])) {
			$tospace['lastsendmail'] = $_G['timestamp'];
		}
		$sendtime = $tospace['lastsendmail'] + $acceptemail['frequency'];

		$value = C::t('mailcron')->fetch_all_by_touid($touid, 0, 1);
		$value = $value[0];
		if($value) {
			$cid = $value['cid'];
			if($value['sendtime'] < $sendtime) $sendtime = $value['sendtime'];
			C::t('mailcron')->update($cid, array('email' => $tospace['email'], 'sendtime' => $sendtime));
		} else {
			$cid = C::t('mailcron')->insert(array(
				'touid' => $touid,
				'email' => $tospace['email'],
				'sendtime' => $sendtime,
			), true);
		}
		$message = preg_replace("/href\=\"(?!(http|https)\:\/\/)(.+?)\"/i", 'href="'.$_G['siteurl'].'\\1"', $message);
		$setarr = array(
			'cid' => $cid,
			'subject' => $subject,
			'message' => $message,
			'dateline' => $_G['timestamp']
		);
		C::t('mailqueue')->insert($setarr);
		return true;
	}
	return false;
}

?>
