<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/app_config.php');
include_once(APP_PATH.'wp/wp-load.php');

$paged = isset($_GET['paged']) && $_GET['paged'] ? $_GET['paged'] : 1;
$thisPageName = 'manage-application';
$page_ttl = '体験予約者管理ボード';
if(isset($_SESSION['logID']) && $_SESSION['logID']){
  $request_uri = $_SERVER['REQUEST_URI'];
  $uri_parts = explode("/",$request_uri);

  $studio_slug = $_GET['studio_slug'];
  $urlYM = isset($_GET['ym']) && !empty($_GET['ym']) ? $_GET['ym'] : '';
  $defaultYM = date("Y/m");

  $cur_url = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'].explode('?', $_SERVER['REQUEST_URI'], 2)[0];
  $studio_url = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'].'/'.explode('/', explode('?', $_SERVER['REQUEST_URI'], 2)[0])[1].'/'.explode('/', explode('?', $_SERVER['REQUEST_URI'], 2)[0])[2].'/';

  $csv_url = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'].explode('?', $_SERVER['REQUEST_URI'], 2)[0].'csv.php?'.explode('?', $_SERVER['REQUEST_URI'], 2)[1];

  $flagValidPage = 0;
  //get studio
  $wp_studio = new WP_Query();
  $param_studio = array(
    'post_type'=>'studio',
    'orderby'         => 'menu_order',
    'order'           => 'ASC',
    'posts_per_page' => '-1',
  );
  $wp_studio->query($param_studio);
  if($wp_studio->have_posts()){
    $i = 0;
    while($wp_studio->have_posts()){
      $wp_studio->the_post();
      $i++;
      $studio_arr[$i] = array(
        'id' => get_the_id(),
        'ttl' => get_the_title(),
        'slug' => $post->post_name,
      );
      if($studio_slug == $post->post_name){
        $flagValidPage = 1;
        $studio_id = get_the_id();
      }else if($studio_slug == 'all'){
        $flagValidPage = 1;
        $studio_id = '';
      }
    }
  }
  $cur_date = date('Y/m/d');
  if($flagValidPage) {
    if(isset($_POST)){
      if(isset($_POST['studio_email'])){
        update_field('studio_email', $_POST['studio_email'], $studio_id);
        header("Location: ".$cur_url, TRUE, 301);
      }else if(!empty($_POST['field_key'])){
        update_field($_POST['field_key'], $_POST['field_value'], $_POST['post_id']);
        return;
      }
    }
    include(APP_PATH.'libs/head.php');
?>
<link rel="stylesheet" href="<?php echo APP_ASSETS ?>css/lib/simplebar.css">
<link rel="stylesheet" href="<?php echo APP_ASSETS ?>css/page/manage_common.min.css">
<link rel="stylesheet" href="<?php echo APP_ASSETS ?>css/page/manage_application.min.css">
</head>
<body class="manage application">
  <?php include(APP_PATH.'libs/manage_header.php'); ?>
  <main id="wrap">
    <div class="container-1140">
      <div class="sec-bg">
        <div class="container-1000">
          <div class="sec-filter">
            <div class="lst-filter">
              <div class="item item--studio">
                <p class="item-ttl">店舗を選択</p>
                <select name="" id="" class="js-select-redirect">
                  <?php $isSelectedAll = $studio_slug == 'all' ? ' selected' : '';?>
                  <option value="<?php echo APP_URL.'manage/all/application/'; ?>"<?php echo $isSelectedAll; ?>>All</option>
                  <?php foreach ($studio_arr as $studio_key => $studio_val) {
                  $isSelected = $studio_slug == $studio_val['slug'] ? ' selected' : ''; ?>
                    <option value="<?php echo APP_URL.'manage/'.$studio_val['slug'].'/application/'; ?>"<?php echo $isSelected; ?>><?php echo $studio_val['ttl']; ?></option>
                  <?php
              } ?>
                </select>
              </div>
              <div class="item item--date">
                <p class="item-ttl">月を選択</p>
                <select name="" id="" class="js-select-redirect">
                  <option value="<?php echo $cur_url; ?>">すべて</option>
                  <?php for ($i=1;$i<=12;$i++) {
                    $ym = date("Y").'/'.$i;
                    $isSelected = ($urlYM == $ym) ? ' selected' : ''; ?>
                    <option value="<?php echo $cur_url.'?ym='.date("Y").'/'.$i?>"<?php echo $isSelected; ?>><?php echo date("Y").'/'.$i?></option>
                  <?php
              } ?>
                </select>
              </div>
            </div>
          </div>
          <form class="sec-email" method="POST">
            <input type="email" id="studio_email" name="studio_email" placeholder="xxxx@email.com" value="<?php echo get_field('studio_email', $studio_id) ?:'';?>">
            <button type="submit">メールを更新する</button>
          </form>
          <?php
            $meta_query_studio = '';
            if($studio_id) {
              $meta_query_studio = array(
                'key' => 'app_studio',
                'value' => $studio_id,
                'compare' => '=',
              );
            }
            if($urlYM) {
              $appl_query_all = new WP_Query( array(
                'post_type' => 'application',
                'posts_per_page' => '30',
                'paged' => $paged,
                'post_status' => 'publish',
                'meta_query' => array(
                  'relation' => 'AND',
                  $meta_query_studio,
                  array(
                    'key' => 'desired_date',
                    'value' => date("Y/m/d", strtotime($urlYM.'/1')),
                    'compare' => '>=',
                    'type' => 'DATE',
                  ),
                  array(
                    'key' => 'desired_date',
                    'value' => date("Y/m/t", strtotime($urlYM.'/1')),
                    'compare' => '<=',
                    'type' => 'DATE',
                  )
                )
              ));
              $appl_query_condition = new WP_Query( array(
                'fields' => 'ids',
                'post_type' => 'application',
                'posts_per_page' => '-1',
                'post_status' => 'publish',
                'meta_query' => array(
                  'relation' => 'AND',
                  $meta_query_studio,
                  array(
                    'key' => 'desired_date',
                    'value' => date("Y/m/d"),
                    'compare' => '>=',
                    'type' => 'DATE',
                  ),
                  array(
                    'key' => 'desired_date',
                    'value' => date("Y/m/d", strtotime($urlYM.'/1')),
                    'compare' => '>=',
                    'type' => 'DATE',
                  ),
                  array(
                    'key' => 'desired_date',
                    'value' => date("Y/m/t", strtotime($urlYM.'/1')),
                    'compare' => '<=',
                    'type' => 'DATE',
                  )
                )
              ));
            }else{
              $appl_query_all = new WP_Query( array(
                'post_type' => 'application',
                'posts_per_page' => '30',
                'paged' => $paged,
                'post_status' => 'publish',
                'meta_query' => array(
                  'relation' => 'AND',
                  $meta_query_studio,
                )
              ));
              $appl_query_condition = new WP_Query( array(
                'fields' => 'ids',
                'post_type' => 'application',
                'posts_per_page' => '-1',
                'post_status' => 'publish',
                'meta_query' => array(
                  'relation' => 'AND',
                  $meta_query_studio,
                  array(
                    'key' => 'desired_date',
                    'value' => date("Y/m/d"),
                    'compare' => '>=',
                    'type' => 'DATE',
                  ),
                )
              ));
            }
            $total_appl = $appl_query_all->found_posts;
            $condition_appl = $appl_query_condition->found_posts;
          ?>
          <div class="sec-num-application">
            <h3 class="ttl">体験予約状況</h3>
            <div class="boxes">
              <div class="box">
                <div class="box-bg">
                  <h4 class="box-ttl">体験レッスン申込み数 </h4>
                  <p class="box-num">
                    <span class="num"><?php echo $total_appl;?></span>
                    <span class="unit">人</span>
                  </p>
                </div>
                <span class="note">※今月の体験申込み総数</span>
              </div>
              <div class="box">
                <div class="box-bg">
                  <h4 class="box-ttl">体験レッスン予定人数 </h4>
                  <p class="box-num">
                    <span class="num"><?php echo $condition_appl;?></span>
                    <span class="unit">人</span>
                  </p>
                </div>
                <span class="note">※今月の来店予定者数</span>
              </div>
            </div>
          </div>
          <?php
            $appl_fields_form = array(
              'appl_date',
              'cus_name',
              'desired_date',
              'desired_time',
              'lesson_name',
              'instructor',
            );
            $appl_fields_edit = array(
              'via02',
              'via',
              'thankyou_phone',
              'in_charge1',
              'confirm_phone',
              'in_charge2',
              'status',
              'cancelling',
              'memo'
            );
            if ( $appl_query_all->have_posts() ) :
            $i = 0;?>
          <div class="sec-data">
            <h3 class="ttl">体験予約者 管理ボード</h3>
            <div class="tbl-outside js-tbl-outside">
              <div class="tbl-data">
                <div class="row">
                  <div class="th">NO</div>
                  <?php if(!$studio_id) {?>
                  <div class="th">スタジオ</div>
                  <?php } ?>
                  <div class="th">申込み日時</div>
                  <div class="th">お名前</div>
                  <div class="th">体験予約日</div>
                  <div class="th">開始時間</div>
                  <div class="th">レッスン名</div>
                  <div class="th">インストラクター</div>
                  <div class="th">経由①</div>
                  <div class="th">経由②</div>
                  <div class="th">予約確認電話</div>
                  <div class="th">- 担当</div>
                  <div class="th">事前確認電話(1~2日前)</div>
                  <div class="th">- 担当</div>
                  <div class="th">ステータス</div>
                  <div class="th">追客対応</div>
                  <div class="th">備考・メモ</div>
                </div>
                <?php while ( $appl_query_all->have_posts() ) :
                  $i++;
                  $appl_query_all->the_post();
                ?>
                <div class="row js-row" data-post-id="<?php echo get_the_ID();?>">
                  <div class="td"><p class="txt"><?php echo (($paged-1)*30 + $i);?></p></div>
                  <?php if(!$studio_id) {?>
                  <div class="td"><p class="txt"><?php echo get_field('app_studio')->post_title;?></p></div>
                  <?php } ?>
                  <?php foreach($appl_fields_form as $val){?>
                    <div class="td"><p class="txt"><?php echo get_field($val);?></p></div>
                  <?php }?>
                  <?php foreach($appl_fields_edit as $val){ ?>
                    <div class="td td--edit">
                  <?php
                      switch($val){
                        case 'via02':
                          $via02_arr = array('WEB', '電話', '来店');
                          $via02_val = get_field($val);
                  ?>

                      <span class="temp-val js-temp-val"></span>
                      <select name="<?php echo $val;?>" id="<?php echo $val;?>">
                        <option value="">選択する</option>
                        <?php foreach($via02_arr as $vval){
                          $selected = $via02_val == $vval ? ' selected' : '';
                        ?>
                          <option value="<?php echo $vval;?>"<?php echo $selected;?>><?php echo $vval;?></option>
                        <?php }?>
                      </select>
                  <?php
                          break;
                        case 'via':
                          $via_arr = array('Google検索', 'Yahoo検索', 'LINE', 'Instagram', 'Twitter', 'YouTube', 'ホットペッパービューティー', '楽天ビューティー', 'フィットサーチ', '駅広告', '店舗前広告', 'チラシ（新聞折込）', 'チラシ（ポスティング）', 'ハガキ', 'ラジオ', '雑誌・情報誌・フリーペッパー', '紹介', '再入会', 'その他' );
                          $via_val = get_field($val);
                  ?>

                      <span class="temp-val js-temp-val"></span>
                      <select name="<?php echo $val;?>" id="<?php echo $val;?>">
                        <option value="">選択する</option>
                        <?php foreach($via_arr as $vval){
                          $selected = $via_val == $vval ? ' selected' : '';
                        ?>
                          <option value="<?php echo $vval;?>"<?php echo $selected;?>><?php echo $vval;?></option>
                        <?php }?>
                      </select>
                  <?php
                          break;
                        case 'thankyou_phone':
                        case 'confirm_phone':
                        case 'cancelling':
                          $phone_arr = array('〇 完了', '× 不通');
                          $phone_val = get_field($val);
                  ?>
                    <span class="temp-val js-temp-val"></span>
                    <select name="<?php echo $val;?>" id="<?php echo $val;?>">
                      <option value="">※未入力</option>
                      <?php foreach($phone_arr as $pval){
                        $selected = $phone_val == $pval ? ' selected' : '';
                      ?>
                        <option value="<?php echo $pval;?>"<?php echo $selected;?>><?php echo $pval;?></option>
                      <?php }?>
                    </select>
                  <?php
                          break;
                        case 'status':
                          $status_arr = array('入会', '検討中', '未入会', '追客対応', '不通');
                          $status_val = get_field($val);
                  ?>
                    <span class="temp-val js-temp-val"></span>
                    <select name="<?php echo $val;?>" id="<?php echo $val;?>">
                      <option value="">選択する</option>
                      <?php foreach($status_arr as $sval){
                        $selected = $status_val == $sval ? ' selected' : '';
                      ?>
                        <option value="<?php echo $sval;?>"<?php echo $selected;?>><?php echo $sval;?></option>
                      <?php }?>
                    </select>
                  <?php
                          break;
                        case 'memo':
                  ?>
                    <span class="temp-val js-temp-val"></span>
                    <input name="<?php echo $val;?>" id="<?php echo $val;?>" value="<?php echo get_field($val);?>">
                  <?php
                          break;
                        default:
                  ?>
                    <span class="temp-val js-temp-val"></span>
                    <input type="text" id="<?php echo $val;?>" name="<?php echo $val;?>" value="<?php echo get_field($val);?>">
                  <?php }?>
                  </div>
                <?php }?>
                </div>
                <?php endwhile;?>
              </div>
            </div>

            <div class="pageNavi">
              <?php if (function_exists('wp_pagenavi')) { echo wp_pagenavi(array( 'query' => $appl_query_all )); } ?>
            </div>
            <a href="<?php echo $csv_url;?>" class="btn-download" target="_blank">csvをダウンロード</a>
          </div>
          <?php wp_reset_postdata(); endif;?>
        </div>
      </div>
    </div>
  </main>
  <script src="<?php echo APP_ASSETS; ?>js/common.min.js"></script>
  <script src="<?php echo APP_ASSETS; ?>js/lib/simplebar.min.js"></script>
  <script src="<?php echo APP_ASSETS; ?>js/page/manage-application.min.js"></script>
  <script>
    $(document).ready(function(){
      autoWidthTable();
    })
    function autoWidthTable(){
      $('.js-tbl-outside').find('input, textarea').each(function(){
        var thisVal = $(this).val(),
            span = $(this).siblings('.js-temp-val');
        span.text(thisVal);
        $(this).width(span.width() + 20);
        $(this).on('input', function(){
          thisVal = $(this).val();
          span.text(thisVal);
          $(this).width(span.width() + 20);
        })
      })

      $('.js-tbl-outside').find('select').each(function(){
        var thisVal = $(this).find('option:selected').text(),
            span = $(this).siblings('.js-temp-val');
        span.text(thisVal);
        $(this).width(span.width() + 10);
        $(this).on('change', function(){
          thisVal = $(this).find('option:selected').text();
          span.text(thisVal);
          $(this).width(span.width() + 10);
        })
      })
    }
  </script>
</body>
</html>
<?php
  }else{
    header('Location: '.APP_URL.'manage/');
  }
}else{
  header('Location: '.APP_URL.'manage/');
}
?>