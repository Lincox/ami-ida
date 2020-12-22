<?php
$thisPageName = 'company';
include_once('../app_config.php');
include(APP_PATH.'libs/head.php');
?>
<link rel="stylesheet" href="<?php echo APP_ASSETS ?>css/page/company.min.css">
</head>
<body class="company">
  <?php include(APP_PATH.'libs/header.php');?>
  <main id="wrap">
    <div class="banner">
      <div class="banner-txt wcm">
        <img src="<?php echo APP_ASSETS; ?>img/company/banner_txt.svg" alt="company" width="237">
      </div>
    </div>
    <div class="breadcrumb wcm" id="breadcrumb">
      <li><a href="<?php echo APP_URL; ?>">
        <img src="<?php echo APP_ASSETS; ?>img/common/icon/ico_home.svg" alt="HOME" width="24">
      </a></li>
      <li><p>会社概要</p></li>
    </div>
    <div class="wcm">
      <div class="the-title">会社概要</div>
      <div class="etr inview fadeInBottom">
        <div class="etr__row">
          <p class="etr__row--th">正式名称</p>
          <p class="etr__row--td">TSCホリスティック株式会社</p>
        </div>
        <div class="etr__row">
          <p class="etr__row--th">設立</p>
          <p class="etr__row--td">1972年5月</p>
        </div>
        <div class="etr__row">
          <p class="etr__row--th">資本金</p>
          <p class="etr__row--td">20,000万円</p>
        </div>
        <div class="etr__row">
          <p class="etr__row--th">役員</p>
          <p class="etr__row--td">代表取締役会長兼社長<br>野澤克巳<br><br>取締役　岩本一也<br>取締役　樋口弘司<br>取締役　野澤竹志<br><br>監査役　野澤二三朝</p>
        </div>
        <div class="etr__row">
          <p class="etr__row--th">従業員数</p>
          <p class="etr__row--td">205名（社員151名・アルバイト54名）<br><em>2020年10月末時点</em></p>
        </div>
        <div class="etr__row">
          <p class="etr__row--th">事業内容</p>
          <p class="etr__row--td">ホットヨガスタジオ・フィットネスクラブ等の運営</p>
        </div>
        <div class="etr__row">
          <p class="etr__row--th">所在地</p>
          <p class="etr__row--td">〒140-0002<br>東京都品川区東品川4-13-14<br>グラスキューブ品川13F</p>
        </div>
        <div class="etr__row">
          <p class="etr__row--th">TEL</p>
          <div class="etr__row--td">
            <a href="tel:03-6712-8844">03-5783-7241</a>
          </div>
        </div>
        <div class="etr__row">
          <p class="etr__row--th">E-MAIL</p>
          <div class="etr__row--td">
            <a href="mailto:support@ami-ida.com">support@ami-ida.com</a>
          </div>
        </div>
        <div class="etr__row">
          <p class="etr__row--th">URL</p>
          <p class="etr__row--td">https://www.ami-ida.com</p>
        </div>
      </div>
      <div class="map inview fadeInBottom">
        <iframe width="100" height="100" frameborder="0" src="https://maps.google.com/maps?q=〒140-0002東京都品川区東品川4-13-14&amp;hl=ja&amp;output=embed" allowfullscreen></iframe>
      </div>
    </div>
  </main>
  <?php include(APP_PATH.'libs/footer.php');?> 
</body>
</html>