<?php
  include_once(dirname(__DIR__) . '/app_config.php');
  include_once(APP_PATH.'wp/wp-load.php');
  $stars = array(
    1 => '★',
    2 => '★★',
    3 => '★★★',
    4 => '★★★★',
    5 => '★★★★★',
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
    <div class="out-lst">
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

  while( have_rows('schedule', $post_id) ){
    the_row();
    $lesson_date = get_sub_field('schedule_date');
    if (in_array($lesson_date, $arr_dates)){
      $lesson[$lesson_date] = [];
      while( have_rows('lesson', $post_id) ){
        the_row();
        $lesson_master = $lesson_ttl = $lesson_level = $lesson_time = $lesson_instructor = $lesson_picture = $lesson_status = '';
        $lesson_master = get_sub_field('lesson_master');
        if($lesson_master){
          $lesson_ttl = $lesson_master->post_title;
          $lesson_level = get_field('lesson_level', $lesson_master->ID);
        }
        $lesson_picture = get_sub_field('lesson_picture');
        $lesson_picture = $lesson_picture['url'];
        $lesson_time = get_sub_field('lesson_time');
        $lesson_status = get_sub_field('lesson_status');
        $time_arr = explode(':', $lesson_time);
        $start_hour = $time_arr[0];
        if(intval($start_hour) < $min_hour){
          $min_hour = intval($start_hour);
        }
        if(intval($start_hour) > $max_hour){
          $max_hour = intval($start_hour);
        }
        $lesson_instructor = get_sub_field('lesson_instructor');
        if(!empty($lesson[$lesson_date])){
          array_push($lesson[$lesson_date], [$lesson_time, $lesson_instructor, $lesson_ttl, $lesson_level, $lesson_picture, $lesson_status]);
        }else{
          $lesson[$lesson_date] = [[$lesson_time, $lesson_instructor, $lesson_ttl, $lesson_level, $lesson_picture, $lesson_status]];
        }
      }
    }
  }

  $keyIndex = array_search($date_start,array_keys($lesson));
  $lesson = array_slice($lesson, $keyIndex, $keyIndex + 7);
  foreach($lesson as $key => $value){
    $j = 0;
    $classDisable = ($value[$j][5] && $value[$j][5] == 'unavailable') ? ' disabled' : '';
    $html .= '<div class="col">';
    for($i=$min_hour;$i<=$max_hour;$i++){
      $lhour = explode(':', $value[$j][0]);
      $lhour = $lhour[0];
      if($lhour != $i){
        $html .= '<div class="lesson empty"></div>';
      }else{
        $html .= '
          <div class="lesson js-lesson'.$classDisable.'" data-popup="schedule" data-id="'.$key.'-'.$value[$j][0].'">
            <div class="bg">
              <p class="time" data-date="'.$key.'">'.$value[$j][0].'</p>
              <div class="pic" style="background-image: url('.$value[$j][4].');"></div>
              <p class="ttl">'.$value[$j][2].'</p>
              <p class="level">'.$stars[$value[$j][3]].'</p>
            </div>
          </div>
        ';
        $html_popup .= '
          <div class="each" data-id="'.$key.'-'.$value[$j][0].'">
            <div class="img" style="background-image: url('.$value[$j][4].');"></div>
            <ul class="lst-info">
              <li class="item">
                <div class="item-ttl">スタジオ</div>
                <div class="item-txt">篠崎店</div>
              </li>
              <li class="item">
                <div class="item-ttl">日時</div>
                <div class="item-txt">'.$key.' '.$value[$j][0].'</div>
              </li>
              <li class="item">
                <div class="item-ttl">インストラクター</div>
                <div class="item-txt">'.$value[$j][1].'</div>
              </li>
              <li class="item">
                <div class="item-ttl">難易度</div>
                <div class="item-txt">'.$stars[$value[$j][3]].'</div>
              </li>
              <li class="item">
                <div class="item-ttl">内容</div>
                <div class="item-txt">リラックスレッスン内容</div>
              </li>
            </ul>
            <a href="#anchor04" class="btn-box js-btn-box" data-lesson="'.$value[$j][2].'" data-date="'.$key.'" data-time="'.$value[$j][0].'"><span>体験する</span></a>
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
?>