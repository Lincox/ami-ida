<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/app_config.php');
include_once(APP_PATH.'wp/wp-load.php');
$thisPageName = 'manage-application';
$page_ttl = '体験予約者管理ボード';
if(isset($_SESSION['logID']) && $_SESSION['logID']){
  include_once(APP_PATH.'csv/read_write_csv.php');
  $request_uri = $_SERVER['REQUEST_URI'];
  $uri_parts = explode("/",$request_uri);

  $studio_slug = $_GET['studio_slug'];
  $urlYM = isset($_GET['ym']) && !empty($_GET['ym']) ? $_GET['ym'] : '';
  $defaultYM = date("Y/m");

  $cur_url = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'].explode('?', $_SERVER['REQUEST_URI'], 2)[0];
  $studio_url = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'].'/'.explode('/', explode('?', $_SERVER['REQUEST_URI'], 2)[0])[1].'/'.explode('/', explode('?', $_SERVER['REQUEST_URI'], 2)[0])[2].'/';

  if($urlYM){
    $urlYMlink = $urlYM.'/';
  }else{
    $urlYMlink = '';
  }
  $csv_url = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'].'/manage/application/csv/'.$studio_slug.'/'.$urlYMlink.'data_entry.csv';
  $flagValidPage = 0;
  //get studio
  $wp_studio = new WP_Query();
  $param_studio = array(
    'post_type'=>'studio',
    'order' => 'DESC',
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
        'posts_per_page' => '-1',
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
    }else{
      $appl_query_all = new WP_Query( array(
        'post_type' => 'application',
        'posts_per_page' => '-1',
        'post_status' => 'publish',
        'meta_query' => array(
          'relation' => 'AND',
          $meta_query_studio,
        )
      ));
    }

    if ($appl_query_all->have_posts()){
      $new_csv = new CSVT();
      if($studio_id){
        $data_ex = array(array('NO',
        '申込み日時',
        'お名前',
        '体験予約日',
        '開始時間',
        'レッスン名',
        'インストラクター',
        '経由①',
        '経由②',
        '予約確認電話',
        '- 担当',
        '事前確認電話(1~2日前)',
        '- 担当',
        'ステータス',
        'キャンセル対応',
        '備考・メモ'));
      }else{
        $data_ex = array(array('NO',
        'スタジオ',
        '申込み日時',
        'お名前',
        '体験予約日',
        '開始時間',
        'レッスン名',
        'インストラクター',
        '経由①',
        '経由②',
        '予約確認電話',
        '- 担当',
        '事前確認電話(1~2日前)',
        '- 担当',
        'ステータス',
        'キャンセル対応',
        '備考・メモ'));
      }

      $i = 0;
      while ($appl_query_all->have_posts()){
        $i++;
        $appl_query_all->the_post();

        $appl_studio = get_field('app_studio')->post_title;
        $appl_date = get_field('appl_date');
        $cus_name = get_field('cus_name');
        $desired_date = get_field('desired_date');
        $desired_time = get_field('desired_time');
        $lesson_name = get_field('lesson_name');
        $instructor = get_field('instructor');
        $via02 = get_field('via02');
        $via = get_field('via');
        $thankyou_phone = get_field('thankyou_phone');
        $in_charge1 = get_field('in_charge1');
        $confirm_phone = get_field('confirm_phone');
        $in_charge2 = get_field('in_charge2');
        $status = get_field('status');
        $cancelling = get_field('cancelling');
        $memo = get_field('memo');

        if($studio_id){
          array_push($data_ex,
            array(
              "{$i} ",
              "{$appl_date} ",
              "{$cus_name} ",
              "{$desired_date}",
              "{$desired_time}",
              "{$lesson_name}",
              "{$instructor}",
              "{$via02}",
              "{$via}",
              "{$thankyou_phone}",
              "{$in_charge1}",
              "{$confirm_phone}",
              "{$in_charge2}",
              "{$status}",
              "{$cancelling}",
              "{$memo}",
            )
          );
        }else{
          array_push($data_ex,
            array(
              "{$i} ",
              "{$appl_studio} ",
              "{$appl_date} ",
              "{$cus_name} ",
              "{$desired_date}",
              "{$desired_time}",
              "{$lesson_name}",
              "{$instructor}",
              "{$via02}",
              "{$via}",
              "{$thankyou_phone}",
              "{$in_charge1}",
              "{$confirm_phone}",
              "{$in_charge2}",
              "{$status}",
              "{$cancelling}",
              "{$memo}",
            )
          );
        }
      }
      //create csv
      $ym = explode('/',$urlYM);
      $y = $ym[0];
      $m = $ym[1];
      if($urlYM) {
        $new_csv->export_csv($studio_slug,$y,$m,$data_ex);
      }else{
        $new_csv->export_csv_all($studio_slug,$data_ex);
      }
      header( "refresh:3; url=".$csv_url );
    }
  }else{
    header('Location: '.APP_URL.'manage/');
  }
}else{
  header('Location: '.APP_URL.'manage/');
}
