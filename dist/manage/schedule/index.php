<?php
include_once('../../app_config.php');
include_once(APP_PATH.'wp/wp-load.php');
$thisPageName = 'manage-schedule';
$thisStudioID = get_the_ID();

$page_ttl = 'レッスンスケジュール登録画面';
$request_uri = $_SERVER['REQUEST_URI'];
$uri_parts = explode("/",$request_uri);

$studio_slug = $_GET['studio_slug'];
$urlYM = isset($_GET['ym']) && !empty($_GET['ym']) ? $_GET['ym'] : '';
$defaultYM = date("Y/m");

$cur_url = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'].explode('?', $_SERVER['REQUEST_URI'], 2)[0];
$studio_url = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'].'/'.explode('/', explode('?', $_SERVER['REQUEST_URI'], 2)[0])[1].'/'.explode('/', explode('?', $_SERVER['REQUEST_URI'], 2)[0])[2].'/';

$schedule_fields_wp_id = 91;
$field_key_rp_schedule = array();
foreach(acf_get_fields($schedule_fields_wp_id) as $val){
  if($val['name'] == 'schedule'){
    $field_key_rp_schedule[$val['name']] = $val['key'];
    foreach($val['sub_fields'] as $subval){
      $field_key_rp_schedule[$subval['name']] = $subval['key'];
    }
  }
}

$schedule_fields = [
  'schedule' => 'Schedule title'
];
$rp_schedule = [
  'lesson_master' => 'lesson master',
  'date' => 'schedule date',
  'time_start' => 'lesson time start',
  'time_end' => 'lesson time end',
  'instructor' => 'instructor',//radio
];

//get lesson master data
$lesson_master_arr = array();
$wp_lesson_master = new WP_Query();
$param_lesson_master = array(
  'post_type'=>'lesson_master',
  'order' => 'DESC',
  'posts_per_page' => '-1',
  // 'meta_query' => array(
  //   array(
  //     'key' => 'lesson_studio',
  //     'value' => $studio_id,
  //     'compare' => '='
  //   )
  // )
);
$wp_lesson_master->query($param_lesson_master);
if($wp_lesson_master->have_posts()){
  $i = 0;
  while($wp_lesson_master->have_posts()){
    $wp_lesson_master->the_post();
    $i++;
    $lesson_master_arr[$i] = array(
      'id' => get_the_id(),
      'ttl' => get_the_title(),
      'content' => get_field('lesson_content'),
      'level' => get_field('lesson_level'),
    );
  }
}

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
    }
  }
}

//ajax action
if(isset($_SESSION['logID']) && $_SESSION['logID']){
  if($flagValidPage) {
    if (isset($_POST) && !empty($_POST)) {

      $parsed_json = json_decode(key($_POST));
      if ($parsed_json) {
        $alter_type = $parsed_json->alter_type;

        if ($alter_type === "modify") {
          foreach($parsed_json as $key => $value) {
            if (substr($key, 0, 8) === "schedule")
              update_field($key, $value, $studio_id);
          }
          header('Content-type: application/json');
          echo json_encode($reponse['status'] = 'success');

        } elseif ($alter_type === "delete") {
          delete_row('schedule', $parsed_json->course_index + 1, $studio_id);

          header('Content-type: application/json');
          echo json_encode($reponse['status'] = 'success');
        } else {}
        return;
      }

      if(isset($_POST['is_create']) && $_POST['is_create'] == 1){
        $_POST['schedule_delete'] = '';
      }
      foreach ($_POST as $key => $value) {
        switch ($key) {
          case 'schedule':
            update_post_meta($studio_id, $key, $value);
            update_post_meta($studio_id, '_'.$key, $field_key_rp_schedule[$key]);
            break;
          case 'is_create':
            break;
          case 'schedule_delete':
            if ($value != '') {
              delete_row('schedule', $value+1, $studio_id);
            }
            break;
          default:
            update_field($key, $value, $studio_id);
            break;
        }
      }
      if(!isset($_POST['is_create']) || empty($_POST['is_create'])){
        return;
      }else{
        header("Location: ".$cur_url, TRUE, 301);
      }
    }

include(APP_PATH.'libs/head.php');
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="<?php echo APP_ASSETS ?>css/page/manage_common.min.css">
<link rel="stylesheet" href="<?php echo APP_ASSETS ?>css/page/manage-schedule.min.css">

<body class="manage manage-schedule">
  <?php include(APP_PATH.'libs/manage_header.php');?>
  <main id="wrap">
    <div class="container-1140">
      <div class="sec-main-post">
        <div class="sec-sub-bg">
          <div class="container-1000">
            <div class="sec-filter">
              <div class="lst-filter">
                <div class="item item--studio">
                  <p class="item-ttl">店舗を選択</p>
                  <select name="" id="" class="js-select-redirect">
                    <?php foreach ($studio_arr as $studio_key => $studio_val) {
                    $isSelected = $studio_slug == $studio_val['slug'] ? ' selected' : ''; ?>
                    <option value="<?php echo APP_URL.'manage/'.$studio_val['slug'].'/schedule/'; ?>" <?php echo
                      $isSelected; ?>>
                      <?php echo $studio_val['ttl']; ?>
                    </option>
                    <?php } ?>
                  </select>
                </div>
                <div class="item item--date">
                  <p class="item-ttl">月を選択</p>
                  <select name="" id="" class="js-select-redirect">
                    <option value="">月を選択してください</option>
                    <?php for ($i=1;$i<=12;$i++) {
                      $ym = date("Y").'/'.$i;
                      $isSelected = ($urlYM == $ym) ? ' selected' : ''; ?>
                    <option value="<?php echo $cur_url.'?ym='.$ym?>" <?php echo $isSelected; ?>>
                      <?php echo $ym;?>
                    </option>
                    <?php } ?>
                    <?php for ($i=1;$i<=12;$i++) {
                      $ym = date('Y', strtotime('+1 year')).'/'.$i;
                      $isSelected = ($urlYM == $ym) ? ' selected' : ''; ?>
                    <option value="<?php echo $cur_url.'?ym='.$ym?>" <?php echo $isSelected; ?>>
                      <?php echo $ym;?>
                    </option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
        <form id="form-schedule" class="js-form-schedule" method="POST">
          <div class="sec-sub-bg01">
            <div class="container-1000">
              <div class="sec-create js-new-post">
                <input type="hidden" name="is_create" id="is_create" value="1">
                <div class="item">
                  <h3 class="item-ttl">レッスンタイトル</h3>
                  <div class="item-field item-field--inse">
                    <input type="text" name="schedule_0_custom_lesson_title" id="schedule_0_custom_lesson_title"
                      class="js-custom-title">
                    <select name="schedule_0_lesson_master" id="schedule_0_lesson_master" class="js-create-select">
                      <option value="">レッスンを選択してください</option>
                      <?php foreach($lesson_master_arr as $lesson_master_key => $lesson_master_val){?>
                      <option value="<?php echo $lesson_master_val['id'];?>"
                        data-id="<?php echo $lesson_master_val['id'];?>"
                        data-ttl="<?php echo $lesson_master_val['ttl'];?>"
                        data-content="<?php echo strip_tags($lesson_master_val['content']);?>"
                        data-level="<?php echo $lesson_master_val['level'];?>">
                        <?php echo $lesson_master_val['ttl'];?>
                      </option>
                      <?php }?>
                    </select>
                  </div>
                </div>
                <div class="item">
                  <h3 class="item-ttl item-ttl--nopad">レッスン内容<span class="note">※自動反映</span></h3>
                  <div class="item-field">
                    <textarea name="schedule_0_custom_lesson_content" id="schedule_0_custom_lesson_content"
                      class="txt textarea js-lesson-content"></textarea>
                  </div>
                </div>
                <div class="item">
                  <h3 class="item-ttl item-ttl--nopad">レッスン強度<span class="note">※自動反映</span></h3>
                  <div class="item-field">
                    <select name="schedule_0_custom_lesson_level" id="schedule_0_custom_lesson_level"
                      class="txt txt-small-field js-lesson-level">
                      <option value="1">★ 1</option>
                      <option value="1.5">★ 1.5</option>
                      <option value="2">★ 2</option>
                      <option value="2.5">★ 2.5</option>
                      <option value="3">★ 3</option>
                      <option value="3.5">★ 3.5</option>
                      <option value="4">★ 4</option>
                      <option value="5">★ 5</option>
                    </select>
                  </div>
                </div>
                <div class="item">
                  <h3 class="item-ttl">レッスンの日程</h3>
                  <div class="item-field">
                    <input type="text" id="schedule_0_date" name="schedule_0_date"
                      class="js-datepicker ip-datepicker restrict" placeholder="日付を選択してください" readonly>
                  </div>
                </div>
                <div class="item">
                  <h3 class="item-ttl">レッスンの時間</h3>
                  <div class="item-field">
                    <div class="half">
                      <select name="schedule_0_time_start" id="schedule_0_time_start" class="js-start-time">
                        <?php for ($i = 6; $i <= 22; $i++){
                          for ($j = 0; $j <= 45; $j+=15){
                            if($i < 10 && $j < 10){
                              $each15 = '0'.$i.':0'.$j;
                            }else if($i < 10 && $j >= 10){
                              $each15 = '0'.$i.':'.$j;
                            }else if($i >= 10 && $j < 10){
                              $each15 = $i.':0'.$j;
                            }else{
                              $each15 = $i.':'.$j;
                            }
                            echo '<option value="'.$each15.'">'.$each15.'</option>';
                          }
                        }?>
                      </select>
                    </div>
                    <div class="half">
                      <select name="schedule_0_time_end" id="schedule_0_time_end" class="js-end-time">
                        <?php for ($i = 6; $i <= 22; $i++){
                          for ($j = 0; $j <= 45; $j+=15){
                            if($i < 10 && $j < 10){
                              $each15 = '0'.$i.':0'.$j;
                            }else if($i < 10 && $j >= 10){
                              $each15 = '0'.$i.':'.$j;
                            }else if($i >= 10 && $j < 10){
                              $each15 = $i.':0'.$j;
                            }else{
                              $each15 = $i.':'.$j;
                            }
                            echo '<option value="'.$each15.'">'.$each15.'</option>';
                          }
                        }?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="item item--last">
                  <h3 class="item-ttl">担当インストラクター名</h3>
                  <div class="item-field">
                    <input type="text" id="schedule_0_instructor" name="schedule_0_instructor"
                      class="schedule_0_instructor">
                  </div>
                </div>
                <p class="btn-add-new js-add-new">この内容でスケジュールを登録する</p>
                <p class="txt-status js-status">
                  <?php if (isset($_POST) && !empty($_POST)) {echo '登録が完了しました！';}?>
                </p>
              </div>
            </div>
          </div>
          <h3 class="the-title container-1080">登録されているレッスン</h3>
          <div class="sec-schedule" id="anchor03">
            <div class="container-1080">
              <div class="schedule js-schedule"></div>
            </div>
          </div>
          <div class="sec-lst-schedule js-lst-schedule">
            <h3 class="ttl">登録されているレッスン</h3>
            <ul class="lst-ttl">
              <li class="item">レッスンタイトル</li>
              <li class="item">日程</li>
              <li class="item">開始時間</li>
              <li class="item">終了時間</li>
              <li class="item">担当インストラクター</li>
              <li class="item"></li>
            </ul>
            <?php foreach($schedule_fields as $key=> $value){?>
            <input type="hidden" id="schedule_delete" name="schedule_delete" value="" class="js-val-row-delete">
            <div class="js-rp rp">
              <!-- Sorted schedules -->
              <?php
              $lessons = get_field('schedule', $studio_id);
              $original_indexes = range(0, count($lessons) - 1, 1);

              $keys = array();
              foreach($lessons as $index => $value)
                $keys[$index] = strtotime($value['date'].' '.$value['time_start']);
              array_multisort($keys, SORT_DESC, $lessons, $original_indexes);
              ?>

              <?php foreach ($lessons as $index => $value) { ?>
              <div class="row js-row" data-row="<?php $index ?>">
                <div class="inside">
                  <select name="<?php echo 'schedule_'.$original_indexes[$index].'_lesson_master'?>" id="<?php echo 'schedule_'.$original_indexes[$index].'_lesson_master'?>" class="restrict" disabled>
                    <?php if($value['custom_lesson_title']){ ?>
                    <option value="">
                      <?php echo $value['custom_lesson_title'];?>
                    </option>
                    <?php }?>
                    <?php foreach($lesson_master_arr as $lesson_master_key => $lesson_master_val){
                          $selected = (get_sub_field($index, $studio_id)->ID) == $lesson_master_val['id'] ? ' selected' : '';
                          ?>
                    <option value="<?php echo $lesson_master_val['id'];?>" <?php echo $selected;?> data-id="
                      <?php echo $lesson_master_val['id'];?>" data-ttl="
                      <?php echo $lesson_master_val['ttl'];?>" data-content="
                      <?php echo $lesson_master_val['content'];?>" data-level="
                      <?php echo $lesson_master_val['level'];?>">
                      <?php echo $lesson_master_val['ttl'];?>
                    </option>
                    <?php }?>
                  </select>
                  <input type="text" id="<?php echo 'schedule'.'_'.$original_indexes[$index].'_'.'date';?>"
                    name="<?php echo 'schedule'.'_'.$original_indexes[$index].'_'.'date';?>" class="js-datepicker datepicker restrict"
                    value="<?php echo $value['date'];?>" disabled>
                  <input type="text" name="<?php echo 'schedule'.'_'.$original_indexes[$index].'_'.'time_start';?>"
                    id="<?php echo 'schedule'.'_'.$original_indexes[$index].'_'.'time_start';?>" value="<?php echo $value['time_start'];?>"
                    disabled>
                  <input type="text" name="<?php echo 'schedule'.'_'.$original_indexes[$index].'_'.'time_end';?>"
                    id="<?php echo 'schedule'.'_'.$original_indexes[$index].'_'.'time_end';?>" value="<?php echo $value['time_end'];?>"
                    disabled>
                  <input type="text" name="<?php echo 'schedule'.'_'.$original_indexes[$index].'_'.'instructor';?>"
                    id="<?php echo 'schedule'.'_'.$original_indexes[$index].'_'.'instructor';?>" value="<?php echo $value['instructor'];?>"
                    disabled>
                  <div class="btn-edit js-edit-row">修正する</div>
                  <div class="btn-delete delete-rp js-delete-row">削除する</div>
                </div>
              </div>
              <?php } ?>
              <input type="hidden" id="schedule" name="schedule" value="<?php echo $index;?>" class="js-number-rp">
<!--
              <?php $i = 0;
                if (have_rows($key, $studio_id)) {
                  while (have_rows($key, $studio_id)) {
                    the_row();
              ?>
              <div class="row js-row" data-row="<?php echo $i; ?>">
                <div class="inside">
                  <?php foreach($rp_schedule as $rp_key=>$rp_value){
                      switch($rp_key){
                        case 'lesson_master':
                      ?>
                  <select name="<?php echo $key.'_'.$i.'_'.$rp_key?>" id="<?php echo $key.'_'.$i.'_'.$rp_key?>"
                    class="restrict" disabled>
                    <?php if(!get_sub_field($rp_key, $studio_id)->ID){?>
                    <option value="">
                      <?php echo get_sub_field('custom_lesson_title', $studio_id)?>
                    </option>
                    <?php }?>
                    <?php foreach($lesson_master_arr as $lesson_master_key => $lesson_master_val){
                          $selected = (get_sub_field($rp_key, $studio_id)->ID) == $lesson_master_val['id'] ? ' selected' : '';
                          ?>
                    <option value="<?php echo $lesson_master_val['id'];?>" <?php echo $selected;?> data-id="
                      <?php echo $lesson_master_val['id'];?>" data-ttl="
                      <?php echo $lesson_master_val['ttl'];?>" data-content="
                      <?php echo $lesson_master_val['content'];?>" data-level="
                      <?php echo $lesson_master_val['level'];?>">
                      <?php echo $lesson_master_val['ttl'];?>
                    </option>
                    <?php }?>
                  </select>
                  <?php
                          break;
                        case 'date':
                      ?>
                  <input type="text" id="<?php echo $key.'_'.$i.'_'.$rp_key?>"
                    name="<?php echo $key.'_'.$i.'_'.$rp_key?>" class="js-datepicker datepicker restrict"
                    value="<?php echo get_sub_field($rp_key, $studio_id);?>" disabled>
                  <?php
                          break;
                        default:
                      ?>
                  <input type="text" name="<?php echo $key.'_'.$i.'_'.$rp_key?>"
                    id="<?php echo $key.'_'.$i.'_'.$rp_key?>" value="<?php echo get_sub_field($rp_key, $studio_id);?>"
                    disabled>
                  <?php
                          break;
                      ?>
                  <?php }
                    }?>
                  <div class="btn-edit js-edit-row">修正する</div>
                  <div class="btn-delete delete-rp js-delete-row">削除する</div>
                </div>
              </div>
              <?php
                    $i++;
                  }
                }
              ?>
            </div>
            <input type="hidden" id="<?php echo $key;?>" name="<?php echo $key;?>" value="<?php echo $i;?>"
              class="js-number-rp">
            <?php }?>
-->
          </div>
        </form>
      </div>
    </div>
    <!-- end new 06/12/2020 !-->

    <form id="form-popup" class="js-form-schedule-popup" method="POST">
      <div class="sec-schedule-popup js-popup" data-popup="schedule">
        <div class="wrap_bg">
          <div class="wrap_container">
            <div class="btn_close"></div>
            <div class="box-info">
              <div class="sec-lst-schedule">
                <div class="rp">
                  <div class="row js-row">
                    <div class="inside inside-popup">
                      <div class="grInput">
                        <div class="grInput__row">
                          <p class="grInput__row--ttl">レッスンタイトル</p>
                          <div class="grInput__row--input">
                            <input name="_lesson_master" class="restrict name" id="_popup_course_type" value="" disabled>
                          </div>
                        </div>
                        <div class="grInput__row">
                          <p class="grInput__row--ttl">日程</p>
                          <div class="grInput__row--input">
                            <input type="text" class="js-datepicker datepicker restrict" id="_popup_course_date" value=""
                            disabled>
                          </div>
                        </div>
                        <div class="grInput__row">
                          <p class="grInput__row--ttl">開始時間</p>
                          <div class="grInput__row--input">
                            <input type="text" id="_popup_course_time_start" value="" disabled>
                          </div>
                        </div>
                        <div class="grInput__row">
                          <p class="grInput__row--ttl">終了時間</p>
                          <div class="grInput__row--input">
                            <input type="text" id="_popup_course_time_end" value="" disabled>
                          </div>
                        </div>
                        <div class="grInput__row">
                          <p class="grInput__row--ttl">担当インストラクター</p>
                          <div class="grInput__row--input">
                            <input type="text" id="_popup_course_instructor" value="" disabled>
                          </div>
                        </div>
                      </div>
                      <div class="grBtn">
                        <div class="btn-edit js-edit-row-popup">修正する</div>
                        <div class="btn-delete delete-rp js-delete-row-popup">削除する</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>

  </main>

  <script src="<?php echo APP_ASSETS; ?>js/common.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.1/i18n/jquery-ui-i18n.min.js"></script>
  <script src="<?php echo APP_ASSETS; ?>js/page/manage-schedule-notgulp.min.js"></script>
  <script src="<?php echo APP_ASSETS; ?>js/lib/simplebar.min.js"></script>

  <script>
    var calendar = $(".js-calendar"),
      schedule = $(".js-schedule"),
      schedule_popup = $(".js-popup");

    var _date = '<?php echo date("Y/m/d");?>'
    var _post_id = '<?php echo $studio_id;?>';

    var start_time_backup = "";

    <?php
    $i = 0;
    $start_times = (array) null;
    $end_times = (array) null;
    $instructors = (array) null;
    $dates = (array) null;

    if (have_rows("schedule", $studio_id)) {
      while (have_rows("schedule", $studio_id)) {
        the_row();

        $start_time = get_sub_field("time_start", $studio_id);
        $end_time = get_sub_field("time_end", $studio_id);
        $instructor = get_sub_field("instructor", $studio_id);
        $date = get_sub_field("date", $studio_id);

        array_push($start_times, $start_time);
        array_push($end_times, $end_time);
        array_push($instructors, $instructor);
        array_push($dates, $date);
      }

      $js_start_times = json_encode($start_times);
      $js_end_times = json_encode($end_times);
      $js_instructors = json_encode($instructors);
      $js_dates = json_encode($dates);

      echo "let __start_times = ".$js_start_times.";\n";
      echo "\tlet __end_times = ".$js_end_times.";\n";
      echo "\tlet __instructors = ".$js_instructors.";\n";
      echo "\tlet __dates = ".$js_dates.";\n";
    }
    ?>

    function deleteEmptyRow() {
      var t = $(".js-calendar .col:first-child").find(".lesson").length,
        e = $(".js-calendar .col").length,
        o = [],
        s = "";
      for (i = 1; i <= t; i++) j = 0, $(".js-calendar .col").each(function () {
        "" == $(this).find(".lesson:nth-child(" + i + ")").html() && j++
      }), j == e && o.push(i);
      $.each(o, function (t, e) {
        s += ":nth-child(" + e + "),"
      }), s = s.slice(0, -1), $(".js-calendar .col .lesson").filter(s).remove()
    }

    function get7DaysBefore(t) {
      var e = new Date(t),
        o = new Date(e.getTime() - 6048e5),
        s = o.getDate(),
        a = o.getMonth() + 1;
      return o.getFullYear() + "/" + a + "/" + s
    }

    function get7DaysAfter(t) {
      var e = new Date(t),
        o = new Date(e.getTime() + 6048e5),
        s = o.getDate(),
        a = o.getMonth() + 1;
      return o.getFullYear() + "/" + a + "/" + s
    }

    function simplebartbl() {
      new SimpleBar($(".js-out-lst")[0], {
        autoHide: !1
      })
    }

    function getAjax(t, e) {
      $.ajax({
        method: "GET",
        url: location.origin + "/libs/ajax-studio-schedule.php",
        dataType: "json",
        data: {
          post_id: t,
          date_start: e
        }
      }).success(function (t) {
        schedule.html(t.html), simplebartbl()
      }).error(function (t, e, o) {
        console.log(t.status), console.log(o)
      }).done(function () {
        $(".js-calendar .lesson").matchHeight({
          byRow: !1
        }), deleteEmptyRow()
      })
    }

    function ajaxSchedule() {
      getAjax(_post_id, _date), $("body").on("click", ".js-prev", function () {
        _date = get7DaysBefore(_date), getAjax(_post_id, _date)
      }), $("body").on("click", ".js-next", function () {
        _date = get7DaysAfter(_date), getAjax(_post_id, _date)
      })
    }

    function popup() {
      $("body").on("click", ".js-lesson", function () {
        let course_type = $(this).children().find(".ttl")[0].innerHTML;

        let course_info = $(this).attr("data-id").split("-");
        let date = course_info[0].trim();
        let start_time = course_info[1].trim();
        let end_time = course_info[2].trim();
        let instructor = "";

        __dates.some((__date, index) => {
          if (__date === date && __start_times[index] === start_time) {
            instructor = __instructors[index];
            return true;
          }
          return false;
        });

        $("#_popup_course_type").val(course_type);
        $("#_popup_course_date").val(date);
        $("#_popup_course_time_start").val(start_time);
        $("#_popup_course_time_end").val(end_time);
        $("#_popup_course_instructor").val(instructor);

        var t = $(this).attr("data-popup"),
          e = $(this).attr("data-id"),
          o = $(".js-popup[data-popup=" + t + "]");
        o.addClass("active"), o.find('[data-id="' + e + '"]').addClass("active").siblings().removeClass("active");
        var s = $(window).scrollTop(),
          a = $("body").outerHeight();
        $("body").attr("data-position", s), $("body").css({
          top: "-" + s + "px",
          left: 0,
          width: "100%",
          height: a + "px",
          position: "fixed",
          "z-index": "-1",
          "touch-action": "none",
          overflow: "hidden"
        })

        $(".js-popup").on("click", function (t) {
          t.target === this && $(".btn_close").trigger("click")
        }), $("body").on("click", ".btn_close", function () {
          $(".js-popup").removeClass("active");
          $(window).scrollTop(), $("body").outerHeight();
          var t = $("body").attr("data-position");
          $("body").css({
            "touch-action": "auto",
            "overflow-x": "hidden",
            "overflow-y": "auto",
            position: "static",
            top: "auto",
            height: "auto"
          }), $("html,body").scrollTop(Math.abs(Number(t)))
        })
      })
    }

    function autoCompleteForm() {
      $("body").on("click", ".js-btn-box", function (t) {
        $(".js-popup").removeClass("active"), $("body").css({
          "touch-action": "auto",
          "overflow-x": "hidden",
          "overflow-y": "auto",
          position: "static",
          top: "auto",
          height: "auto"
        });
      });
    }

    function popup_btn_edit_add_handlers() {

      $("body").on("click", ".js-edit-row-popup", function () {
        if (!$(this).is(".editing")) {
          start_time_backup = $("#_popup_course_time_start").val();
        }
        fields = $(this)
          .closest(".js-row")
          .find("input, textarea, select")
          .not(".restrict");
        if ($(this).is(".editing")) {
          $(this).text("修正する");
          $(this).removeClass("editing");
          fields.prop("disabled", true);
          $(".js-form-schedule-popup").trigger("submit");
        } else {
          $(this).text("セーブする");
          $(this).addClass("editing");
          fields.prop("disabled", false);
        }
      });

      $(".js-form-schedule-popup").on("submit", function () {
        let course_type = $("#_popup_course_type").val();
        let course_date = $("#_popup_course_date").val();
        let course_start_time = $("#_popup_course_time_start").val();
        let course_end_time = $("#_popup_course_time_end").val();
        let course_instructor = $("#_popup_course_instructor").val();

        let selected_course_index = null;
        __dates.some((__date, index) => {
          if (__date === course_date && __start_times[index] === start_time_backup) {
            selected_course_index = index;
            return true;
          }
          return false;
        });

        if (selected_course_index == null)
          return false;

        let schedule_changes = {};
        schedule_changes[`schedule_${selected_course_index}_lession_master`] = course_type;
        schedule_changes[`schedule_${selected_course_index}_date`] = course_date;
        schedule_changes[`schedule_${selected_course_index}_time_start`] = course_start_time;
        schedule_changes[`schedule_${selected_course_index}_time_end`] = course_end_time;
        schedule_changes[`schedule_${selected_course_index}_instructor`] = course_instructor;
        schedule_changes["alter_type"] = "modify";

        $.ajax({
          method: "POST",
          dataType: "json",
          data: JSON.stringify(schedule_changes),
        })
          .success(function (data) {
            $(`#schedule_${selected_course_index}_time_start`).val(course_start_time);
            $(`#schedule_${selected_course_index}_time_end`).val(course_end_time);
            $(`#schedule_${selected_course_index}_instructor`).val(course_instructor);

            $(".btn_close").trigger("click");
            getAjax(_post_id, _date);
          })
          .error(function (xhr, status, error) {
            console.log(xhr + status + error);
          });
        window.location.reload(true);
        location.reload(true);
        oobject.reload(force_reload);
        return false;
      });
    }

    function popup_btn_delete_add_handlers() {
      $(".js-delete-row-popup").on("click", () => {
        let course_date = $("#_popup_course_date").val();
        let course_start_time = $("#_popup_course_time_start").val();

        let selected_course_index = null;
        __dates.some((__date, index) => {
          if (__date === course_date && __start_times[index] === course_start_time) {
            selected_course_index = index;
            return true;
          }
          return false;
        });

        if (selected_course_index == null)
          return false;

        let delete_info = {
          "course_index": selected_course_index,
          "alter_type": "delete"
        };

        $.ajax({
          method: "POST",
          dataType: "json",
          data: JSON.stringify(delete_info)
        })
          .success((data) => {
            $(`.js-row[data-row=${selected_course_index}]`).remove();
            $(".btn_close").trigger("click");
            getAjax(_post_id, _date);
          })
          .error((xhr, status, error) => { console.log(status, error); return false });
        window.location.reload(true);
        location.reload(true);
        oobject.reload(force_reload);
        return true;
      });
    }

    $(document).ready(() => {
      popup();
      ajaxSchedule();
      autoCompleteForm();
      popup_btn_edit_add_handlers();
      popup_btn_delete_add_handlers();
    });
  </script>

</body>

</html>
<?php
    }else {
      header('Location: '.APP_URL.'manage/');
    }
  }else{
    header('Location: '.APP_URL.'manage/');
  }
?>