<?php
include_once('../app_config.php');
include_once(APP_PATH.'wp/wp-load.php');
$page_ttl = 'レッスンダッシュボード';
$thisPageName = 'manage-top';
if(isset($_SESSION['logID']) && $_SESSION['logID']){
  $wp_studio = new WP_Query();
  $param = array(
    'post_type'=>'studio',
    'orderby'         => 'menu_order',
    'order'           => 'ASC',
    'posts_per_page' => '-1',
  );
  $wp_studio->query($param);
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
    }
  }

include(APP_PATH.'libs/head.php');
?>
<link rel="stylesheet" href="<?php echo APP_ASSETS ?>css/page/manage_common.min.css">
<link rel="stylesheet" href="<?php echo APP_ASSETS ?>css/page/manage.min.css">
</head>
<body class="manage manage-top">
  <?php include(APP_PATH.'libs/manage_header.php');?>
  <main id="wrap">
    <div class="container-1140">
      <div class="sec-redirect">
        <div class="container-1000">
          <div class="lst-redirect">
            <div class="item">
              <h3 class="ttl">体験レッスン申込み者 管理ボード</h3>
              <div class="select">
                <p class="lbl">店舗を選択</p>
                <select name="" id="" class="js-choose-studio">
                  <option value="">スタジオをお選びください</option>
                  <option value="all">All</option>
                  <?php foreach($studio_arr as $studio_key => $studio_val){?>
                    <option value="<?php echo $studio_val['slug'];?>"><?php echo $studio_val['ttl'];?></option>
                  <?php }?>
                </select>
                <a href="javascript:void(0)" data-url="<?php echo APP_URL?>manage/studio_slug/application/" class="btn js-btn">管理ボードを開く</a>
              </div>
            </div>
            <div class="item">
              <h3 class="ttl">レッスンスケジュール　登録・管理ボード</h3>
              <div class="select">
                <p class="lbl">店舗を選択</p>
                <select name="" id="" class="js-choose-studio">
                  <option value="">スタジオをお選びください</option>
                  <?php foreach($studio_arr as $studio_key => $studio_val){?>
                    <option value="<?php echo $studio_val['slug'];?>"><?php echo $studio_val['ttl'];?></option>
                  <?php }?>
                </select>
                <a href="javascript:void(0)" data-url="<?php echo APP_URL?>manage/studio_slug/schedule/" class="btn js-btn">管理ボードを開く</a>
              </div>
            </div>
            <div class="item">
              <h3 class="ttl">お知らせ 登録・管理ボード</h3>
              <div class="select">
                <p class="lbl">店舗を選択</p>
                <select name="" id="" class="js-choose-studio">
                  <option value="">スタジオをお選びください</option>
                  <option value="top">TOP</option>
                  <?php foreach($studio_arr as $studio_key => $studio_val){?>
                    <option value="<?php echo $studio_val['slug'];?>"><?php echo $studio_val['ttl'];?></option>
                  <?php }?>
                </select>
                <a href="javascript:void(0)" data-url="<?php echo APP_URL?>manage/studio_slug/news/" class="btn js-btn">管理ボードを開く</a>
              </div>
            </div>
            <div class="item item--spec">
              <h3 class="ttl">レッスン内容　登録・管理ボード</h3>
              <div class="select">
                <a href="<?php echo APP_URL;?>manage/schedule-format/" class="btn js-btn">レッスン登録画面を開く</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
  <script src="<?php echo APP_ASSETS; ?>js/common.min.js"></script>
  <script>
    $(document).ready(function() {
      changeLink();
    })

    function changeLink(){
      $('.js-choose-studio').on('change', function(){
        var el_anchor = $(this).siblings('.js-btn'),
            url = el_anchor.attr('data-url'),
            studio_slug = $(this).val();
        url = (studio_slug != '') ? url.replace('studio_slug',studio_slug) : 'javascript:void(0)';
        el_anchor.attr('href', url);
      })
    }
  </script>
</body>
</html>
<?php
  }else{
    header('Location: '.APP_URL.'manage/login/');
  }
?>