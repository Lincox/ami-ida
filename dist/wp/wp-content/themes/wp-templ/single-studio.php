<?php
$thisPageName = 'single-studio';
$thisStudioID = get_the_ID();
$pslug = $post->post_name;
if(!empty($_POST['actionFlag'])) {
  include_once('single-studio-confirm.php');
  exit();
} elseif(!empty(get_query_var('actionFlag'))) {
  if(get_query_var('actionFlag') == 'complete') {
    include_once('single-studio-complete.php');
    exit();
  } else header('location: '.get_the_permalink());
}
// var_dump(get_field('schedule'));
$this_studio_lesson_master = array();
if(get_field('schedule')){
  foreach(get_field('schedule') as $k=>$v){
    if(isset($v['lesson_master']->ID) && $v['lesson_master']->ID && !in_array($v['lesson_master']->ID, $this_studio_lesson_master)){
      array_push($this_studio_lesson_master, $v['lesson_master']->ID);
    }
  }
}
include(APP_PATH.'libs/head.php');
?>
<link rel="stylesheet" href="<?php echo APP_ASSETS ?>css/lib/simplebar.css">
<link rel="stylesheet" href="<?php echo APP_ASSETS ?>css/style.min.css">
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<link rel="stylesheet" href="<?php echo APP_ASSETS ?>css/form/validationEngine.jquery.css">
<link rel="stylesheet" href="<?php echo APP_ASSETS ?>css/page/single-studio.min.css">
</head>
<body id="single-studio" class="single-studio">
<?php include(APP_PATH.'libs/header.php'); ?>
<?php include(APP_PATH.'libs/loading.php'); ?>
<div id="wrap">
  <?php if (have_posts()) :
    while (have_posts()) : the_post();
    $fields = get_fields();
  ?>
  <main>
    <div class="visual">
      <div class="visual__bg" data-parallax='{"y": -70, "smoothness": 10}'></div>
     <!--  <video muted="" autoplay="" playsinline="" loop><source src="<?php //echo APP_ASSETS; ?>img/studio/mainvisual.mp4"></video> -->
      <div class="visual">
        <div class="visual__scroll">
          <a href="#intro" class="visual__scroll--btn"><span>scroll</span></a>
        </div>
        <div class="visual__txt">
          <picture>
            <source srcset="<?php echo APP_ASSETS; ?>img/common/visual_txt_sp.svg" media="(max-width: 767px)">
            <img src="<?php echo APP_ASSETS; ?>img/common/visual_txt.svg" alt="Amiida">
          </picture>
        </div>
      </div>
    </div>
    <div class="breadcrumb wcm" id="breadcrumb">
      <li><a href="<?php echo APP_URL; ?>">
        <img src="<?php echo APP_ASSETS; ?>img/common/icon/ico_home.svg" alt="HOME" width="24">
      </a></li>
      <li><a href="<?php echo APP_URL; ?>studio/">店舗ページ</a></li>
      <li><p><?php echo get_the_title(); ?></p></li>
    </div>
    <?php
      $wp_news = new WP_Query();
      $param_news = array(
        'post_type'=>'news',
        'order' => 'DESC',
        'showposts' => 3,
        'meta_key' => 'date',
        'orderby' => 'meta_value',
        'order' => 'DESC',
        'meta_query' => array(
          array(
            'key' => 'studio',
            'value' => $thisStudioID,
            'compare' => '='
          )
        )
      );
      $wp_news->query($param_news);
      if($wp_news->have_posts()){
    ?>
    <div class="c-news inview fadeInBottom">
      <div class="wcm">
        <p class="c-news__ttl">NEWS</p>
        <div class="c-news__lst">
        <?php while($wp_news->have_posts()){
          $wp_news->the_post();?>
            <div data-id="<?php echo get_the_ID(); ?>" class="c-news__lst--item">
              <p class="date"><?php echo date('Y/m/d', strtotime(get_field('date')));?></p>
              <p class="cat"><em>お知らせ</em></p>
              <p class="ttl"><em><?php echo get_the_title();?></em></p>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
    <?php } wp_reset_postdata();?>
    <div class="sec-intro inview fadeInBottom">
      <div class="container-1080">
        <h3 class="the-title">12月限定！<br><?php echo get_the_title(); ?>店の入会特典!</h3>
        <div class="gr">
          <div class="inside">
            <ul class="lst-intro">
              <li class="item">
                <div class="ttl">入会金+登録料</div>
                <div class="circle">
                  <div class="txt">
                    <p class="txt01">通常10,000円</p>
                    <p class="price">
                      <span class="number">0</span>
                      <span class="unit">円</span>
                    </p>
                  </div>
                </div>
              </li>
              <li class="item">
                <p class="ttl">月額会費</p>
                <div class="circle">
                  <div class="txt">
                    <p class="txt01">12月＆1月分</p>
                    <p class="txt02">半額</p>
                  </div>
                </div>
              </li>
            </ul>
            <div class="gr03">
              <p class="ttl"><em>＼</em> 更に！入会者全員にプレゼント <em>／</em></p>
              <div class="gr03__lst">
                <div class="gr03__lst--item">
                  <p class="ttl">水素水ボトル</p>
                  <div class="circle">
                    <img src="<?php echo APP_ASSETS; ?>img/studio/circle01.jpg" alt="">
                  </div>
                  <p class="txt">※水素水飲み放題<br>2ヶ月分無料</p>
                </div>
                <div class="gr03__lst--item">
                  <p class="ttl">ヨガラグ</p>
                  <div class="circle">
                    <img src="<?php echo APP_ASSETS; ?>img/studio/circle02.jpg" alt="">
                  </div>
                  <p class="txt">※販売価格：3,900円</p>
                </div>
                <div class="gr03__lst--item">
                  <p class="ttl">マットストラップ</p>
                  <div class="circle">
                    <img src="<?php echo APP_ASSETS; ?>img/studio/circle03.jpg" alt="">
                  </div>
                  <p class="txt">※販売価格：900円</p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg">
            <img class="lazy img" data-src="<?php echo APP_ASSETS; ?>img/studio/img_yoga.jpg" alt="">
          </div>
        </div>
      </div>
    </div>
    <div class="sec-btns inview fadeInBottom">
      <div class="container-900">
        <a href="https://www.helloweb.jp/Admission/User/652911/Introduction.aspx" class="btn"><span>体験なしで入会される方</span></a>
        <a href="#anchor04" class="btn"><span>体験レッスンへのご参加はこちら</span></a>
      </div>
    </div>
    <div class="sec-charge" id="anchor01">
      <div class="container-900">
        <h3 class="the-title inview fadeInBottom">料金プラン</h3>
        <ul class="lst-price js-lst-price inview fadeInBottom">
        <!-- 12/22リリース時：非掲載
          <li class="item">
            <h4 class="item-ttl js-price-ttl">
              <span class="txt01">料金プラン</span>
            </h4>
            <div class="tbl js-price-tbl">
              <div class="row">
                <p class="th">月額会費</p>
                <div class="td">
                  <p class="td01">19,800円 </p>
                  <p class="td02">※通い放題&amp;溶岩浴利用も可能</p>
                </div>
              </div>
            </div>
          </li> -->
          <li class="item">
            <h4 class="item-ttl js-price-ttl">
              <span class="txt01">オプションサービス</span>
            </h4>
            <div class="tbl js-price-tbl">
              <div class="row">
                <p class="th">水素水</p>
                <div class="td">
                  <p class="td01">1,000円/月</p>
                  <p class="td02">水素水飲み放題。専用ボトルにてご利用可能</p>
                </div>
              </div>
              <div class="row">
                <p class="th">タオル</p>
                <div class="td">
                  <p class="td01">1,000円/月</p>
                  <p class="td02"> 1日1回、バスタオル1枚とフェイスタオル1枚がご利用可能
                </div>用可能</p>
              </div>
              <div class="row">
                <p class="th">マットお預かり</p>
                <div class="td">
                  <p class="td01">1,000円/月</p>
                  <p class="td02">ヨガマットお預かり</p>
                </div>
              </div>
            </div>
          </li>
          <li class="item">
            <h4 class="item-ttl js-price-ttl">
              <span class="txt01">購入できるもの(※一部店舗)</span>
            </h4>
            <div class="tbl js-price-tbl">
              <div class="row">
                <p class="th">アミーダオリジナル溶岩ヨガマット</p>
                <div class="td">
                  <p class="td01">12,000円</p>
                  <p class="td02">(サイズ 174cm×61cm×5cm)</p>
                </div>
              </div>
              <div class="row">
                <p class="th">ラグ</p>
                <div class="td">
                  <p class="td01">3,900円</p>
                </div>
              </div>
              <div class="row">
                <p class="th">マットトラップ</p>
                <div class="td">
                  <p class="td01">900円</p>
                </div>
              </div>
              <div class="row">
                <p class="th">水素水ボトル</p>
                <div class="td">
                  <p class="td01">800円</p>
                </div>
              </div>
              <div class="row">
                <p class="th">アミーダセット</p>
                <div class="td">
                  <p class="td01">15,800円</p>
                  <p class="td02">アミーダオリジナル溶岩ヨガマット＋ラグ＋マットストラップ</p>
                </div>
              </div>
              <div class="row">
                <p class="th">アミーダウェアー(上)</p>
                <div class="td">
                  <p class="td01">6,800円</p>
                </div>
              </div>
              <div class="row">
                <p class="th">アミーダウェアー(下)</p>
                <div class="td">
                  <p class="td01">6,800円</p>
                </div>
              </div>              
              <div class="row">
                <p class="th">アミーダウェアー(セット)</p>
                <div class="td">
                  <p class="td01">12,000円</p>
                </div>
              </div>              
            </div>
          </li>
          <li class="item">
            <h4 class="item-ttl js-price-ttl">
              <span class="txt01">レンタルできるもの</span>
              <span class="txt02">(※一部店舗)</span>
            </h4>
            <div class="tbl js-price-tbl">
              <div class="row">
                <p class="th">ウェアー（上下）</p>
                <div class="td">
                  <p class="td01">750円/日</p>
                </div>
              </div>
              <div class="row">
                <p class="th">タオルセット <br>(フェイスタオル・バスタオル)</p>
                <div class="td">
                  <p class="td01">350円/日</p>
                </div>
              </div>
              <div class="row">
                <p class="th">ヨガマット</p>
                <div class="td">
                  <p class="td01">300円/日</p>
                </div>
              </div>
            </div>
          </li>
        </ul>
        <p class="script">※体験料以外の表示価格は全て税抜きとなります。水素水のみ消費税8％</p>
      </div>
    </div>
    <div class="sec-voice">
      <div class="slider js-voice-slider">
        <?php for($i=1;$i<9;$i++){ ?>
          <div class="item inview fadeInBottom" style="background-image: url(<?php echo APP_ASSETS; ?>img/top/<?php echo $i+1; ?>.jpg);"></div>
        <?php } ?>
      </div>
      <h3 class="the-title">アミーダの<br class="sp">溶岩ホットヨガを<br>選ぶお客様のお声</h3>
      <div class="container-1080">
        <?php include(APP_PATH.'libs/voice.php'); ?>
      </div>
      <div class="sec-btns inview fadeInBottom">
        <div class="container-900">
          <a href="https://www.helloweb.jp/Admission/User/652911/Introduction.aspx" class="btn"><span>体験なしで入会される方</span></a>
          <a href="#anchor04" class="btn"><span>体験レッスンへのご参加はこちら</span></a>
        </div>
      </div>
    </div>
    <div class="feature1">
      <div class="wcm">
        <h3 class="the-title inview fadeInBottom">アミーダ<?php echo get_the_title(); ?>店が選ばれる理由</h3>
        <div class="feature1__lst">
          <div class="feature1__lst--item inview fadeInBottom">
            <div class="img lazy" data-bg="url(<?php echo APP_ASSETS; ?>img/studio/feature1.jpg)"></div>
            <div class="info">
              <div class="inner">
                <p class="ttl">効果を実感できる<br>溶岩ホットヨガスタジオ　</p>
                <p class="txt">溶岩石の遠赤外線で、身体の芯から自然に温めるので、呼吸もしやすく身体への負担も少ないです。 <br>天然の溶岩石で温めるスタジオで、ミネラルとマイナスイオン、サラサラした大量の汗が噴き出す感覚を是非体感してください。 </p>
              </div>
            </div>
          </div>
          <div class="feature1__lst--item inview fadeInBottom">
            <div class="img lazy" data-bg="url(<?php echo APP_ASSETS; ?>img/studio/feature2.jpg)"></div>
            <div class="info">
              <div class="inner">
                <p class="ttl">初体験でも安心の<br>パーソナル型サポート　</p>
                <p class="txt">初心者の方や、身体の固い方、個人のレベルに合わせた様々なプログラムを用意しております。<br>また、一人一人のお悩みに適した「パーソナル型サポート」を実施しておりますのでお気軽にご来店ください。</p>
              </div>
            </div>
          </div>
          <div class="feature1__lst--item inview fadeInBottom">
            <div class="img lazy" data-bg="url(<?php echo APP_ASSETS; ?>img/studio/feature3.jpg)"></div>
            <div class="info">
              <div class="inner">
                <p class="ttl">レベルの高いインストラクター　</p>
                <p class="txt">お客様にご満足いただける良質なプログラムをご提供するために、アミーダのインストラクターは難関な試験を合格した上でプログラムを担当させて頂いております。<br>経験豊富なインストラクターが、サポートさせていただきますのでご安心ください。</p>
              </div>
            </div>
          </div>
          <div class="feature1__lst--item inview fadeInBottom">
            <div class="img lazy" data-bg="url(<?php echo APP_ASSETS; ?>img/studio/feature4.jpg)"></div>
            <div class="info">
              <div class="inner">
                <p class="ttl">通いやすい女性専用スタジオ　</p>
                <p class="txt">お仕事帰りや、隙間時間にご利用頂けるよう、パウダールームを併設しております。<br>貸出しグッズ＆お預けサービスもあり、女性専用スタジオなので安心してご利用いただけます。</p>
              </div>
            </div>
          </div>
          <div class="feature1__lst--item inview fadeInBottom">
            <div class="img lazy" data-bg="url(<?php echo APP_ASSETS; ?>img/studio/feature5.jpg)"></div>
            <div class="info">
              <div class="inner">
                <p class="ttl">予定が合わせやすい<bR>豊富なレッスンスケジュール　</p>
                <p class="txt">自分の目的に合わせて、様々なレベル感のレッスンの中から、好きなレッスンに参加していただけます。<br>好きなタイミングで、好きなペースで、無理なく通って頂けます。</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="sec-schedule" id="anchor03">
      <div class="container-1080">
        <h3 class="the-title inview fadeInBottom">アミーダ<?php echo get_the_title(); ?>店<br class="pc">レッスンスケジュール</h3>
        <div class="the-txt"><em>＜ オススメのレッスン準備 ＞</em><br>スタジオはレッスンの15分前から入ることができます。<br class="pc">レッスン前の15分間で、溶岩浴で体を温め、呼吸を整え、心を整えることで、45分のレッスンを集中して受けることができ、効果をより実感いただけます。</div>
        <div class="schedule js-schedule inview fadeInBottom"></div>
      </div>
    </div>
    <?php if ($this_studio_lesson_master){ ?>
    <div class="sec-lesson">
      <div class="container-900">
        <div class="the-title inview fadeInBottom">アミーダ<?php echo get_the_title(); ?>店<br class="pc">レッスン内容</div>
        <div class="etr">
          <?php foreach ($this_studio_lesson_master as $each) :
            $img = get_field('lesson_image', $each)['url'];
            if(empty($img)){ $img = APP_ASSETS.'img/common/other/nophoto.jpg';}
          ?>
          <div class="etr__item">
            <p class="etr__item--ttl"><?php echo get_the_title($each);?></p>
            <div class="js-hide">
              <div class="etr__item--cont">
                <div class="image">
                  <div class="img lazy" data-bg="url(<?php echo $img; ?>)"></div>
                </div>
                <div class="info">
                  <p class="txt"><?php echo get_field('lesson_content', $each);?></p>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach;?>
        </div>
      </div>
    </div>
    <?php } ?>
    <div class="sec-form" id="anchor04">
      <div class="container-750">
        <h3 class="the-title inview fadeInBottom">アミーダ<?php echo get_the_title(); ?>店<br class="pc">体験レッスン申込みフォーム</h3>
        <form method="post" class="studioform inview fadeInBottom" id="studioform" action="confirm/?g=<?php echo time() ?>" name="studioform">
          <div class="stepImg">
            <picture>
              <source media="(max-width: 767px)" srcset="<?php echo APP_ASSETS; ?>img/common/form/img_step01SP.svg">
              <img src="<?php echo APP_ASSETS; ?>img/common/form/img_step01.svg" alt="フォームからのお問い合わせ　Step">
            </picture>
          </div>
          <p class="hid_url">Leave this empty: <input type="text" name="url"></p><!-- Anti spam part1: the contact form -->
          <table class="tableContact">
            <tr>
              <th>体験内容</th>
              <td>
                <input type="hidden" name="single_ttl" id="single_ttl" class="input-lesson" value="">
                <input type="hidden" name="studio_slug" id="studio_slug" value="<?php echo $pslug;?>">
                <input type="hidden" name="studio_id" id="studio_id" value="<?php echo $thisStudioID;?>">
                <input type="hidden" name="instructor" id="instructor" class="input-instructor" value="">
                <input placeholder="例) 初心者レッスン" type="text" name="single_ttl" id="single_ttl" class="input-lesson validate[required]" value="" readonly>
                <span class="note">※レッスンスケジュールから、体験するレッスンを選択してください</span>
              </td>
            </tr>
            <tr>
              <th>体験希望日</th>
              <td>
                <p class="half">
                  <input placeholder="例) 2020/10/10" type="text" name="hopedate" id="hopedate" class="validate[required] input-date" value="" readonly>
                </p>
                <p class="half">
                  <input placeholder="例) 10:00 - 10:45" type="text" name="hopetime" id="hopetime" class="validate[required] input-time" value="" readonly>
                </p>
                <span class="note">※レッスンスケジュールから、体験するレッスンを選択してください</span>
              </td>
            </tr>
            <tr>
              <th>お名前</th>
              <td><input placeholder="例)田中 花子" type="text" name="nameuser" id="nameuser" class="validate[required]"></td>
            </tr>
            <tr>
              <th>お名前（ふりがな）</th>
              <td><input placeholder="例：たなか はなこ" type="text" name="nameuser_furigana" id="nameuser_furigana" class="validate[required,custom[furigana]]"></td>
            </tr>
            <tr>
              <th class="norequire">年齢</th>
              <td>
                <p class="half fullsp">
                  <select name="age" id="age">
                    <option value="">選択してください</option>
                    <?php for($i=10;$i<=120;$i++){?>
                      <option value="<?php echo $i;?>"><?php echo $i;?></option>
                    <?php }?>
                  </select>
                </p>
              </td>
            </tr>
            <tr>
              <th>電話番号</th>
              <td><input placeholder="例) 03-1234-5678" type="tel" name="tel" id="tel" class="validate[required,custom[phone]]"></td>
            </tr>
            <tr>
              <th>メールアドレス</th>
              <td><input placeholder="例：abc@efg.jp（半角英数字）" type="email" name="email" id="email" class="validate[required,custom[email]]"></td>
            </tr>
            <tr>
              <th>メールアドレス(確認)</th>
              <td>
                <input placeholder="例：abc@efg.jp（半角英数字）" type="email"  name="cemail" id="cemail" value="" class="validate[equals[email]]">
                <p class="row">
                  <textarea name="content" id="content" placeholder="ご質問内容をご記入ください"></textarea>
                </p>
              </td>
            </tr>
          </table>
          <p class="btn-row">
            <button id="btnConfirm" class="btn-confirm"><span>体験申込み 確認画面へ</span></button>
            <input type="hidden" name="actionFlag" value="confirm">
          </p>
        </form>
      </div>
    </div>
    <div class="stuff">
      <div class="wcm">
        <h3 class="the-title inview fadeInBottom">溶岩ヨガ体験レッスンに必要な持ち物リスト</h3>
        <div class="stuff__lst">
          <div class="stuff__lst--item">
            <div class="info">
              <ul>
                <li>運動しやすいウェア</li>
                <li>着替え用の下着</li>
                <li>水分補給用のお水<br class="sp">（1リットル程度）</li>
                <li>汗拭き用のフェイスタオル</li>
                <li>シャワー用のバスタオル</li>
                <li>身分証明書</li>
              </ul>
            </div>
            <div class="img">
              <div class="img1 lazy" data-bg="url(<?php echo APP_ASSETS; ?>img/studio/stuff2.jpg)"></div>
              <div class="img2 lazy" data-bg="url(<?php echo APP_ASSETS; ?>img/studio/stuff1.jpg)"></div>
            </div>
          </div>
        </div>            
      </div>
    </div>
    <div class="sec-access" id="anchor02">
      <div class="container-1080">
        <h3 class="the-title inview fadeInBottom">アミーダ<?php echo get_the_title(); ?>店へのアクセス</h3>
        <div class="access">
          <div class="map inview fadeInBottom">
            <iframe width="100" height="100" frameborder="0" src="https://maps.google.com/maps?q=<?php echo $fields['access_zipcode'].$fields['access_address01']; ?>&amp;hl=ja&amp;output=embed" allowfullscreen></iframe>
          </div>
          <div class="info inview fadeInBottom">
            <div class="inside">
              <div class="row">
                <div class="th">所在地</div>
                <div class="td"><?php echo $fields['access_zipcode'].'<br>'.$fields['access_address01']; ?><?php  if(!empty($fields['access_address02'])){ echo '<br>'.$fields['access_address02'];} ?></div>
              </div>
              <?php if(!empty($fields['access_tel'])){ ?>
              <div class="row">
                <div class="th">TEL</div>
                <div class="td"><?php echo $fields['access_tel']; ?></div>
              </div>
              <?php } if(!empty($fields['access_fax'])){ ?>
              <div class="row">
                <div class="th">FAX</div>
                <div class="td"><?php echo $fields['access_fax']; ?></div>
              </div>
              <?php }  if(!empty($fields['access_station'])){ ?>
              <div class="row">
                <div class="th">最寄駅/アクセス</div>
                <div class="td"><?php echo $fields['access_station']; ?></div>
              </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="faq">
      <?php include(APP_PATH.'libs/faq.php'); ?>
    </div>
    <?php if(!empty($fields['access_instagram'])){ ?>
    <div class="sns wcm">
      <h3 class="the-title">SNS</h3>
      <div class="grBtn">
        <a target="_blank" href="https://www.instagram.com/<?php echo $fields['access_instagram']; ?>/?hl=ja" class="grBtn__item ins"><p>Instagram</p></a>
      </div>
    </div>
    <?php } ?>
    <div class="popup popup-new">
    </div>
  </main>
<?php endwhile;endif; ?>
</div>
<div class="sec-schedule-popup js-popup" data-popup="schedule"></div>
<?php include(APP_PATH.'libs/footer.php'); ?>
<script src="<?php echo APP_ASSETS; ?>js/lib/simplebar.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script src="<?php echo APP_ASSETS; ?>js/form/jquery.validationEngine.js"></script>
<script src="<?php echo APP_ASSETS; ?>js/form/languages/jquery.validationEngine-ja.js"></script>
<script>
  var _date = '<?php echo date("Y/m/d");?>',
      _url = '<?php echo APP_URL; ?>';
      _post_id = '<?php echo $thisStudioID?>';
</script>
<script src="<?php echo APP_ASSETS ?>js/page/single-studio.min.js"></script>
<script>
  $(document).ready(function () {
    $("#studioform").validationEngine({
      promptPosition: "topLeft",
      scrollOffset: ($('.header').outerHeight() + 5),
    });
  });
  $('.sec-lesson .etr__item--ttl').click( function(){
    $(this).parent().toggleClass('active');
    if ($(this).parent().hasClass('active') === true) {
      $(this).next().slideDown();
    } else {
      $(this).next().slideUp();
    }
  })
</script>
</body>
</html>