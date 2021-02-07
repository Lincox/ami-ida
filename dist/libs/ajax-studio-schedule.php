<?php
  include_once(dirname(__DIR__) . '/app_config.php');
  include_once(APP_PATH.'wp/wp-load.php');
  $stars = array(
    '1' => '★',
    '1.5' => '★',
    '2' => '★★',
    '2.5' => '★★',
    '3' => '★★★',
    '3.5' => '★★★',
    '4' => '★★★★',
    '5' => '★★★★★',
  );
  $html = $html_popup = '';
  $lesson = array();
  $arr_dates = array();

  $post_id = $_GET['post_id'];
  $date_start = $_GET['date_start'];

  $date_start_str = strtotime($date_start);
  $date_start = date("Y/m/d", $date_start_str);
  $date_start_short = date("m/d", $date_start_str);
  for($i=0;$i<7;$i++){
    ${"nextdays_str$i"} = strtotime("+$i day", $date_start_str);
    ${"nextdays$i"} =  date("Y/m/d", ${"nextdays_str$i"});
    ${"nextdays_short$i"} = date("m/d", ${"nextdays_str$i"});
    $lesson[${"nextdays$i"}] = [];
    array_push($arr_dates, ${"nextdays$i"});
  }

  $dj = array("日","月","火","水","木","金","土");

  $html_popup .= '
    <div class="wrap_bg">
      <div class="wrap_container">
        <div class="btn_close"></div>
        <div class="box-info">
  ';

  $html .= '
    <div class="calendar">
      <div class="calendar-head">
        <span class="prev js-prev"></span>
        <span class="txt js-date">'.$date_start_short.' - '.$nextdays_short6.'</span>
        <span class="next js-next"></span>
      </div>
    </div>
    <div class="out-lst js-out-lst">
      <div class="lst-schedule">
        <ul class="lst-date">';

  for($i=0;$i<7;$i++){
    $html .= '<li class="item">'.${"nextdays_short$i"}." (".$dj[date("w", ${"nextdays_str$i"})].")".'</li>';
  }
  $html .= '
    </ul>
    <div class="active-schedule js-calendar">';
  $min_hour = 24;
  $max_hour = 0;

  $implode_arr_dates = implode("','",$arr_dates);

  $schedule_query = "SELECT REPLACE(REPLACE(meta_key, 'schedule_', ''), '_date', '') AS key_num
                     FROM $wpdb->postmeta
                     WHERE meta_key LIKE 'schedule_%_date'
                           AND meta_value IN ('$implode_arr_dates')
                           AND post_id = $post_id";

  $schedule_rp_row = $wpdb->get_results($schedule_query);

  foreach($schedule_rp_row as $key => $val) {
    $row_index = $val->key_num;
    $this_row_data = get_field('schedule', $post_id)[$row_index];

    $lesson_date = $this_row_data['date'];

    $lesson_master = $lesson_ttl = $lesson_level = $lesson_time = $lesson_instructor = $lesson_picture = $lesson_status = '';
    $lesson_master = $this_row_data['lesson_master'];
    if ($lesson_master) {
      $lesson_id = $lesson_master->ID;
      $lesson_ttl = get_the_title($lesson_id);
      $lesson_level = get_field('lesson_level', $lesson_id);
      $lesson_content = get_field('lesson_content', $lesson_id);
    }else{
      $lesson_ttl = $this_row_data['custom_lesson_title'];
      $lesson_level = $this_row_data['custom_lesson_level'];
      $lesson_content = $this_row_data['custom_lesson_content'];
    }
    $lesson_time_start = $this_row_data['time_start'];
    $lesson_time_end = $this_row_data['time_end'];
    $time_arr = explode(':', $lesson_time_start);
    $start_hour = $time_arr[0];
    if($start_hour > 24) {
      $start_hour = substr($lesson_time_start, 0, 2);
    }
    if($start_hour > 24) {
      $start_hour = substr($lesson_time_start, 0, 1);
    }
    if (intval($start_hour) < $min_hour) {
        $min_hour = intval($start_hour);
    }
    if (intval($start_hour) > $max_hour) {
        $max_hour = intval($start_hour);
    }
    $lesson_instructor = $this_row_data['instructor'];
    if (!empty($lesson[$lesson_date])) {
        array_push($lesson[$lesson_date], [$lesson_time_start, $lesson_time_end, $lesson_instructor, $lesson_ttl, $lesson_level, $lesson_content]);
    } else {
        $lesson[$lesson_date] = [[$lesson_time_start, $lesson_time_end, $lesson_instructor, $lesson_ttl, $lesson_level, $lesson_content]];
    }
  }

  //$lesson array is multidimensional array with top key is date, and inside is every lesson

  ksort($lesson);
  foreach($lesson as $key => $val){
    //usort sort $lesson_time_start in array from smallest to biggest
    usort($val, function($a, $b) {
      return intval(str_replace(':', '', $a[0])) - intval(str_replace(':', '', $b[0]));
    });
    $lesson[$key] = $val;
  }

  //$lesson array is sorted from smallest date to biggest date, and inside: smallest time start to biggest time start
  $keyIndex = array_search($date_start,array_keys($lesson));
  $lesson = array_slice($lesson, $keyIndex, $keyIndex + 7);

  foreach($lesson as $key => $value){
    $j = 0;
    $max_j = count($value);
    $html .= '<div class="col">';
    for($i=$min_hour;$i<=$max_hour;$i++){
      $lhour = explode(':', $value[$j][0]);
      $lhour = $lhour[0];
      if($lhour > 24) {
        $lhour = substr($value[$j][0], 0, 2);
      }
      if($lhour > 24) {
        $lhour = substr($value[$j][0], 0, 1);
      }
      if($lhour != $i){
        if($lhour < $i) {
          //check if time start is duplicate, if it's duplicate, go to next lesson
          while($lhour < $i && $j < $max_j) {
            $j++;
            $lhour = explode(':', $value[$j][0]);
            $lhour = $lhour[0];
            if($lhour > 24) {
              $lhour = substr($value[$j][0], 0, 2);
            }
            if($lhour > 24) {
              $lhour = substr($value[$j][0], 0, 1);
            }
          }
        }
        $html .= '<div class="lesson empty"></div>';
      }

      if($lhour == $i){
        $date = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
        $timestamp_now = strtotime($date->format('Y/m/d H:i:s'));

        $dateStr = $key.' '.$value[$j][0].':00';
        $timestamp_this = strtotime($dateStr);

        $time_dif = ($timestamp_this - $timestamp_now)/60;

        $classDisable =  ($time_dif < 60) ? ' disabled' : ' js-lesson';
        // echo nl2br('<br>----<br>');
        // var_dump($date->format('Y/m/d H:i:s'));
        // echo nl2br('<br>');
        // var_dump($dateStr);
        // echo nl2br('<br>----<br>');
        // var_dump($time_dif);
        // echo nl2br('<br>----<br>');
        // echo nl2br('<br>----<br>');
        $html .= '
          <div class="lesson'.$classDisable.'" data-popup="schedule" data-id="'.$key.'-'.$value[$j][0].' - '.$value[$j][1].'" data-y="'.$timestamp_now.'" data-z="'.$timestamp_this.'" data-x="'.$time_dif.'">
            <div class="bg">
              <p class="time" data-date="'.$key.'">'.$value[$j][0].' - '.$value[$j][1].'</p>
              <div class="pic" style="background-image: url('.$value[$j][4].');"></div>
              <p class="ttl">'.$value[$j][3].'</p>
              <p class="level">'.$stars[$value[$j][4]].'</p>
            </div>
          </div>
        ';
        $html_popup .= '
          <div class="each" data-id="'.$key.'-'.$value[$j][0].' - '.$value[$j][1].'">
            <ul class="lst-info">
              <li class="item">
                <div class="item-ttl">スタジオ</div>
                <div class="item-txt">篠崎店</div>
              </li>
              <li class="item">
                <div class="item-ttl">日時</div>
                <div class="item-txt">'.$key.' '.$value[$j][0].' - '.$value[$j][1].'</div>
              </li>
              <li class="item">
                <div class="item-ttl">レッスン強度</div>
                <div class="item-txt">'.$stars[$value[$j][4]].'</div>
              </li>
              <li class="item">
                <div class="item-ttl">レッスン内容</div>
                <div class="item-txt">'.$value[$j][5].'</div>
              </li>
            </ul>
            <a href="#anchor04" class="c-btn js-btn-box" data-lesson="'.$value[$j][3].'" data-date="'.$key.'" data-instructor="'.$value[$j][2].'" data-time="'.$value[$j][0].' - '.$value[$j][1].'"><span>体験予約する</span></a>
          </div>
        ';

        $j++;
      }
    }
    $html .= '</div>';
  }
  $html .= '</div></div></div>';
  $html_popup .= '</div></div></div>';
  $result = array();
  $result['html'] = $html;
  $result['html_popup'] = $html_popup;
  echo json_encode($result);

  //dont detele it, this is backup function for filter repeater
  // $schedule_query = "SELECT  tt.meta_id AS meta_id,
  //                                 tt.post_id AS post_id,
  //                                 tt.meta_key AS lesson_master_key,
  //                                 tt.meta_value AS lesson_master_value,
  //                                 d.meta_key AS date_key,
  //                                 d.meta_value AS date_value
  //                   FROM $wpdb->postmeta AS tt
  //                   JOIN
  //                   (SELECT t.*, REPLACE(REPLACE(t.meta_key, 'schedule_', ''), '_date', '') AS key_num
  //                   FROM $wpdb->postmeta AS t
  //                   WHERE t.meta_key LIKE 'schedule_%_date'
  //                         AND t.meta_value IN ('$implode_arr_dates')
  //                         AND t.post_id = $post_id) d
  //                   ON d.key_num = REPLACE(REPLACE(tt.meta_key, 'schedule_', ''), '_lesson_master', '')
  //                   WHERE tt.meta_key LIKE 'schedule_%_lesson_master' AND tt.post_id = $post_id";
?>