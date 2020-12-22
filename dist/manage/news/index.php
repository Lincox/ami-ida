<?php
include_once('../../app_config.php');
include_once(APP_PATH.'wp/wp-load.php');
$page_ttl = 'お知らせ情報登録フォーム';
$thisPageName = 'manage-news';
$request_uri = $_SERVER['REQUEST_URI'];
$uri_parts = explode("/",$request_uri);

$studio_slug = $_GET['studio_slug'];
$cur_url = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'].explode('?', $_SERVER['REQUEST_URI'], 2)[0];
$studio_url = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'].'/'.explode('/', explode('?', $_SERVER['REQUEST_URI'], 2)[0])[1].'/'.explode('/', explode('?', $_SERVER['REQUEST_URI'], 2)[0])[2].'/';

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
    }else if($studio_slug == 'top'){
      $flagValidPage = 1;
      $studio_id = '';
    }
  }
}

//ajax action
if(isset($_SESSION['logID']) && $_SESSION['logID'] && $flagValidPage){
  //prepare for wp_editor
  wp_head();
  wp_enqueue_media();
  $settings = array(
    'media_buttons' => true,
    'dfw' => true,
    'textarea_name' => 'vceditor',
    "drag_drop_upload" => true
  );
  $editor_id = "vceditor";
  $settings = array(
    'wpautop' => false
  );
  if (isset($_POST) && !empty($_POST)) {
    if(isset($_POST['form_action']) && $_POST['form_action'] == 'create'){
      $new_post = wp_insert_post(
        array(
          'post_type' => 'news',
        )
      );
      wp_update_post(array(
        'ID' => $new_post,
        'post_title' => $_POST['postTitle'],
        'post_content' => $_POST['vceditor'],
        'post_status' => 'publish',
      ));
      update_field('date', $_POST['postDate'], $new_post);
      update_field('studio', $_POST['studio_id'], $new_post);
      header("Location: ".$cur_url, TRUE, 301);
    }else if(isset($_POST['delete']) && !empty($_POST['delete'])){
      wp_delete_post($_POST['delete']);
      return;
    }else{
      wp_update_post(array(
        'ID' => $_POST['post_id'],
        'post_title' => $_POST['postTitleEdit'],
      ));
      update_field('date', $_POST['postDateEdit'], $_POST['post_id']);
      return;
    }
  }

include(APP_PATH.'libs/head.php');
?>
</head>
<body class="manage schedule-format">
<?php include(APP_PATH.'libs/manage_header.php');?>
  <main id="wrap">
    <div class="container-1140">
      <div class="sec-main-post">
        <div class="sec-bg">
          <div class="container-1000">
            <div class="sec-filter">
              <div class="lst-filter">
                <div class="item item--studio">
                  <p class="item-ttl">店舗を選択</p>
                  <select name="" id="" class="js-select-redirect">
                    <?php $isSelectedTop = $studio_slug == 'top' ? ' selected' : '';?>
                    <option value="<?php echo APP_URL.'manage/top/news/'; ?>"<?php echo $isSelectedTop; ?>>TOP</option>
                    <?php foreach ($studio_arr as $studio_key => $studio_val) {
                    $isSelected = $studio_slug == $studio_val['slug'] ? ' selected' : ''; ?>
                      <option value="<?php echo APP_URL.'manage/'.$studio_val['slug'].'/news/'; ?>"<?php echo $isSelected; ?>><?php echo $studio_val['ttl']; ?></option>
                    <?php
                } ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="div-form">
              <form action="" method="POST">
                <div class="sec-field">
                  <input type="hidden" name="studio_id" id="studio_id" value="<?php echo $studio_id;?>">
                  <input type="hidden" name="form_action" id="form_action" value="create">
                  <div class="item">
                    <label for="postDate">日付</label>
                    <input type="text" name="postDate" id="postDate" class="required js-datepicker" readonly>
                  </div>
                  <div class="item">
                    <label for="postTitle">お知らせタイトル</label>
                    <input type="text" name="postTitle" id="postTitle" class="required">
                  </div>
                  <div class="item-editor">
                    <label for="postContent">お知らせの内容</label>
                    <div class="wp-editor">
                      <?php wp_editor( wpautop(''), $editor_id, $settings );?>
                    </div>
                  </div>
                </div>
                <div class="sec-btn-submit">
                  <input type="hidden" name="submitted" id="submitted" value="true">
                  <button type="submit" class="btn-add-new js-add-new">登録する</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <div class="sec-lst">
        <h3 class="ttl">登録されているお知らせ</h3>
        <ul class="lst-ttl">
          <li class="item">日付</li>
          <li class="item">お知らせのタイトル</li>
          <li class="item"></li>
        </ul>
        <?php
        $query = new WP_Query( array(
          'post_type' => 'news',
          'posts_per_page' => '-1',
          'post_status' => 'publish',
          'meta_query' => array(
            array(
              'key' => 'studio',
              'value' => $studio_id,
              'compare' => '=',
            )
          )
        ) );
        ?>
        <ul class="lst-post">
          <?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>
            <li class="item js-row">
              <input type="hidden" name="post_id" id="post_id" value="<?php echo get_the_ID();?>">
              <div class="item-content"><input type="text" name="postDateEdit" id="postDateEdit" value="<?php echo get_field('date'); ?>" class="js-datepicker" disabled></div>
              <div class="item-ttl"><input type="text" name="postTitleEdit" id="postTitleEdit" value="<?php echo get_the_title(); ?>" disabled></div>
              <div class="item-btns">
                <div class="btn-edit js-edit-row">修正する</div>
                <div class="btn-delete js-delete-row">削除する</div>
              </div>
            </li>
          <?php endwhile; endif; ?>
        </ul>
      </div>
    </div>
  </main>
  <?php wp_footer();?>
  <link href="<?php echo APP_ASSETS; ?>css/style.min.css" rel="stylesheet" media="all">
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="<?php echo APP_ASSETS ?>css/page/manage_common.min.css">
  <link rel="stylesheet" href="<?php echo APP_ASSETS ?>css/page/manage-news.min.css">
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.1/i18n/jquery-ui-i18n.min.js"></script>
  <script src="<?php echo APP_ASSETS; ?>js/page/manage-news.min.js"></script>
  </body>
</html>
<?php
    }else{
    header('Location: '.APP_URL.'manage/');
  }
?>