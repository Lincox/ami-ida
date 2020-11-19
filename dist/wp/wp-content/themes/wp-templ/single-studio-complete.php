<?php
session_start();
$thisPageName = 'single-studio';

if(!empty($_POST['actionFlag']) && $_POST['actionFlag'] == "send") {
  $aMailto = $aMailtoContact;
  if(count($aBccToContact)) $aBccTo = $aBccToContact;
  $from = $fromContact;
  $fromname = $fromName;
  $subject_admin = "ホームページからお問い合わせがありました";
  $subject_user = "お問い合わせありがとうございました";
  $email_head_ctm_admin = "ホームページからお問い合わせがありました。";
  $email_head_ctm_user = "この度はお問い合わせいただきまして誠にありがとうございます。
こちらは自動返信メールとなっております。
弊社にて確認した後、改めてご連絡させていただきます。

以下、お問い合わせ内容となっております。
ご確認くださいませ。";
  $email_body_footer = "
    About company
  ";

  $entry_time = gmdate("Y/m/d H:i:s",time()+9*3600);
  $entry_host = gethostbyaddr(getenv("REMOTE_ADDR"));
  $entry_ua = getenv("HTTP_USER_AGENT");

$msgBody = "■体験内容
$reg_single_ttl

■体験希望日
$reg_hopedate $reg_hopetime

■お名前
$reg_name

■お名前（ふりがな）
$reg_nameuser_furigana
";
if(isset($reg_age) && $reg_age != '') $msgBody .= "
■年齢
$reg_age
";

$msgBody .= "
■電話番号
$reg_tel

■メールアドレス
$reg_email

■アミーダを知ったきっかけ
$reg_method $reg_other_method
$reg_content";

//お問い合わせメッセージ送信
  $body_admin = "
登録日時：$entry_time
ホスト名：$entry_host
ブラウザ：$entry_ua


$email_head_ctm_admin


$msgBody


";

//お客様用メッセージ
  $body_user = "
$reg_name 様

$email_head_ctm_user

---------------------------------------------------------------

$msgBody

---------------------------------------------------------------
".$email_body_footer."
---------------------------------------------------------------";


include_once(APP_PATH.'csv/read_write_csv.php');
// csv format
// reg_single_ttl,reg_hopedate,reg_hopetime,reg_name,reg_nameuser_furigana,reg_age,reg_tel,reg_email,reg_method,reg_content

  // ▼ ▼ ▼ START Detect SPAMMER ▼ ▼ ▼ //
  try {
    $allow_send_email = 1;
    // Anti spam advanced version 3 start: Verify by google invisible reCaptcha
    if(GOOGLE_RECAPTCHA_KEY_SECRET != '') {
      $response = $_POST['g-recaptcha-response'];
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, "secret=".GOOGLE_RECAPTCHA_KEY_SECRET."&response={$response}");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $returnJson = json_decode(curl_exec ($ch));
      curl_close ($ch);
      if( !empty($returnJson->success) ) {} else throw new Exception('Protect by Google Invisible Recaptcha');
    }

    // Anti spam advanced version 3 start: Verify by google invisible reCaptcha
    if(empty($_SESSION['ses_from_step2'])) throw new Exception('Step confirm must be display');

    // check spam mail by gtime
    $gtime_step2 = $_GET['g'];
    // submit form dosen't have g
    if(!$gtime_step2) {
      throw new Exception('Miss g request');
    } else {
      $cur_time = time();
      if(strlen($cur_time)!=strlen($gtime_step2)) {
        throw new Exception('G request\'s not a time');
      } elseif( $_SESSION['ses_gtime_step2'] == $gtime_step2 && ($cur_time-$gtime_step2 < 1) ) {
        throw new Exception('Checking confirm too fast');
      }
    }

    // Anti spam advanced version 2 start: Don't send blank emails
    if(empty($reg_name) || empty($reg_email)) {
      throw new Exception('Miss reg_name or reg_email');
    }

    // Anti spam advanced version 1 start: The preg_match() is there to make sure spammers can’t abuse your server by injecting extra fields (such as CC and BCC) into the header.
    if(preg_match( "/[\r\n]/", $reg_email)) {
      throw new Exception('Email\'s not correct');
    }

    // Anti spam: the contact form start
    if($reg_url != "") {
      throw new Exception('Url request must be empty');
    }

    // Anti spam: check session complete contact
    if(!isset($_SESSION['ses_step3'])) $_SESSION['ses_step3'] = false;
    if($_SESSION['ses_step3']) {
      throw new Exception('Session step 3 must be destroy');
    }
  } catch (Exception $e) {
    $returnE = '<pre class="preanhtn">';
    $returnE .= $e->getMessage().'<br>';
    $returnE .= 'File: '.$e->getFile().' at line '.$e->getLine();
    $returnE .= '</pre>';
    $allow_send_email = 0;
    // die($returnE);
  }
  // ▲ ▲ ▲ END Detect SPAMMER ▼ ▼ ▼ //

  if($allow_send_email) {
    //////// メール送信
    mb_language("ja");
    mb_internal_encoding("UTF-8");
    $timesend = date("Y.m.d H:i",time());

    //////// お客様受け取りメール送信
    $email = new JPHPmailer();
    $email->addTo($reg_email);
    $email->setFrom($from,$fromname);
    $email->setSubject($subject_user);
    $email->setBody($body_user);

    if($email->send()) {
      $new_csv = new CSVT();
      $data_ex = array(array(
          "{$reg_single_ttl} ",
          "{$reg_hopedate} {$reg_hopetime}",
          "{$reg_name} ",
          "{$reg_nameuser_furigana} ",
          "{$reg_age} ",
          "{$reg_tel} ",
          "{$reg_email} ",
          "{$reg_method} {$reg_other_method} ",
          "{$reg_content} ",
          "{$timesend} "
      ));
      $new_csv->export_csv($data_ex);
    }
    $dataLog = "
$timesend
$reg_single_ttl
$reg_hopedate $reg_hopetime
$reg_name
$reg_nameuser_furigana
$reg_age
$reg_tel
$reg_email
$reg_method $reg_other_method
$reg_content
";
    $ret = file_put_contents(APP_PATH.'csv/log/registerData.log', $dataLog, FILE_APPEND | LOCK_EX);
    if($ret === false) {
        // die('There was an error writing this file');
    }
    else {
        // echo "$ret bytes written to file";
    }

    //////// メール送信
    $email->clearAddresses();
    for($i = 0; $i < count($aMailto); $i++) $email->addTo($aMailto[$i]);
    for($i = 0; $i < count($aBccTo); $i++) $email->addBcc($aBccTo[$i]);
    $email->setSubject($subject_admin);
    $email->setBody($body_admin);

    if($email->Send()) { /*Do you want to debug somthing?*/ }

    $_SESSION['ses_step3'] = true;
  }

  $_SESSION['statusFlag'] = 1;
  header("Location: ".get_the_permalink()."complete/");
  exit;
}

if(!empty($_SESSION['statusFlag'])) {
  unset($_SESSION['statusFlag']);
  unset($_SESSION['ses_gtime_step2']);
  unset($_SESSION['ses_from_step2']);
  unset($_SESSION['ses_step3']);
} else header('location: '.APP_URL);

include(APP_PATH."libs/head.php");
?>
<meta http-equiv="refresh" content="15; url=<?php echo APP_URL ?>">
<script type="text/javascript">
history.pushState({ page: 1 }, "title 1", "#noback");
window.onhashchange = function (event) {
  window.location.hash = "#noback";
};
</script>
<link rel="stylesheet" href="<?php echo APP_ASSETS ?>css/page/single-studio.min.css">
</head>
<body id="single-studio" class="single-studio indexThx">
  <?php include(APP_PATH."libs/header.php") ?>
  <div class="sec-thanks">
    <div class="container-750">
      <h3 class="the-title">アミーダ〇〇店<br class="sp">レッスンスケジュール</h3>
      <div class="stepImg">
        <picture>
          <source media="(max-width: 767px)" srcset="<?php echo APP_ASSETS; ?>img/common/form/img_step03SP.svg">
          <img src="<?php echo APP_ASSETS; ?>img/common/form/img_step03.svg" alt="フォームからのお問い合わせ　Step">
        </picture>
      </div>
      <div class="txt-thanks">
        <h3 class="ttl">お問い合わせ<br class="sp">ありがとうございました。</h3>
        <p class="content">入力いただいたメールアドレスに自動送信メールが送られております。<br>ご確認いただきますようお願い申し上げます。<br><br>なお、一両日経過してもメールが届かない場合には、<br class="pc">ご入力時のメールアドレスが間違っている場合がありますので、<br class="pc">まことに恐縮ですが再度のご連絡をよろしくお願いいたします。</p>
        <a href="<?php echo APP_URL;?>" class="btn-backtop">TOPに戻る</a>
      </div>
    </div>
  </div>
  <?php include(APP_PATH.'libs/footer.php') ?>
  </body>
</html>