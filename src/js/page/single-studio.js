var calendar = $('.js-calendar'),
    schedule = $('.js-schedule'),
    schedule_popup = $('.js-popup');
$(document).ready(function () {
  slider();
  faq();
  price();
  ajaxSchedule();
  popup();
  autoCompleteForm();
})

function slider(){
  $('.js-voice-slider').slick({
    speed: 10000,
    autoplay: true,
    autoplaySpeed: 0,
    cssEase: 'linear',
    slidesToShow: 2.75,
    arrows: false,
    responsive: [
      {
        breakpoint: 500,
        settings: {
          slidesToShow: 1.5,
        }
      },
    ]
  })
}

function faq(){
  $('.js-lst-faq .item:first-child').addClass('active').find('.js-ans').slideToggle();
  $('body').on('click', '.js-ques', function(){
    $(this).siblings('.js-ans').slideToggle()
    .parent().toggleClass('active');
  })
}

function price(){
  $('.js-lst-price .item:first-child').addClass('active').find('.js-price-tbl').slideToggle();
  // $('.js-lst-price .item:nth-child(2)').addClass('active').find('.js-price-tbl').slideToggle();
  $('body').on('click', '.js-price-ttl', function(){
    $(this).siblings('.js-price-tbl').slideToggle()
    .parent().toggleClass('active');
  })
}

function deleteEmptyRow(){
  console.log('haha');
  var num_lesson = $('.js-calendar .col:first-child').find('.lesson').length,
      num_col = $('.js-calendar .col').length,
      empty_rows = [],
      nth_child = '';
  for(i=1;i<=num_lesson;i++){
    j = 0;
    $('.js-calendar .col').each(function(){
      if($(this).find('.lesson:nth-child('+i+')').html() == ''){
        j++;
      }
    })
    if(j==num_col){
      empty_rows.push(i);
    }
  }
  $.each(empty_rows, function(k, v){
    nth_child += ':nth-child('+v+'),';
  })
  nth_child = nth_child.slice(0, -1);
  $('.js-calendar .col .lesson').filter(nth_child).remove();
}

function getAjax(_post_id, _date_start){
  $.ajax({
    method: 'GET',
    url: location.origin+'/stage/libs/ajax-studio-schedule.php',
    dataType: 'json',
    data: {
      post_id: _post_id,
      date_start: _date_start,
    },
  }).success(function(data) {
    schedule.html(data.html);
    schedule_popup.html(data.html_popup);
    simplebartbl();
  }).error(function (xhr, ajaxOptions, thrownError) {
    console.log(xhr.status);
    console.log(thrownError);
  }).done(function(){
    $('.js-calendar .lesson').matchHeight({byRow: false});
    deleteEmptyRow();
  })
}
function get7DaysBefore(_d){
  var days = 7; // Days you want to subtract
  var date = new Date(_d);
  var last = new Date(date.getTime() - (days * 24 * 60 * 60 * 1000));
  var day = last.getDate();
  var month = last.getMonth()+1;
  var year = last.getFullYear();
  return year+'/'+month+'/'+day;
}

function get7DaysAfter(_d){
  var days = 7; // Days you want to subtract
  var date = new Date(_d);
  var last = new Date(date.getTime() + (days * 24 * 60 * 60 * 1000));
  var day = last.getDate();
  var month = last.getMonth()+1;
  var year = last.getFullYear();
  return year+'/'+month+'/'+day;
}

function ajaxSchedule(){
  getAjax(_post_id, _date);
  $('body').on('click', '.js-prev', function(){
    _date = get7DaysBefore(_date);
    getAjax(_post_id, _date);
  })
  $('body').on('click', '.js-next', function(){
    _date = get7DaysAfter(_date);
    getAjax(_post_id, _date);
  })
}

function popup(){
  $('body').on('click', '.js-lesson', function(){
    var dataPopup = $(this).attr('data-popup'),
        dataID = $(this).attr('data-id'),
        popupEl = $('.js-popup[data-popup='+dataPopup+']');
    popupEl.addClass('active');
    popupEl.find('[data-id="'+dataID+'"]').addClass('active').siblings().removeClass('active');

    var __cur_p = $(window).scrollTop();
    var __cur_h = $("body").outerHeight();

    $("body").attr("data-position", __cur_p);
    $("body").css({
        "top": "-" + __cur_p + "px",
        "left": 0,
        "width": "100%",
        "height": __cur_h + "px",
        "position": "fixed",
        "z-index": "-1",
        "touch-action": "none",
        "overflow": "hidden",
    });
  })

  $(".js-popup").on("click", function(e){
    if (e.target !== this)
      return;
    $('.btn_close').trigger('click');
  });

  $('body').on('click', '.btn_close', function(){
    $(".js-popup").removeClass("active");
    var __cur_p = $(window).scrollTop();
    var __cur_h = $("body").outerHeight();
    var pos_position = $("body").attr("data-position");
    $("body").css({
      "touch-action": "auto",
      "overflow-x": "hidden",
      "overflow-y": "auto",
      "position": "static",
      "top": "auto",
      "height": "auto"
    });
    $("html,body").scrollTop(Math.abs(Number(pos_position)));
  })
}

function autoCompleteForm(){
  $('body').on('click', '.js-btn-box', function(e){
    $('.js-popup').removeClass('active');
    $("body").css({
      "touch-action": "auto",
      "overflow-x": "hidden",
      "overflow-y": "auto",
      "position": "static",
      "top": "auto",
      "height": "auto"
    });

    var _lesson = $(this).attr('data-lesson'),
        _date = $(this).attr('data-date'),
        _time = $(this).attr('data-time'),
        _instructor = $(this).attr('data-instructor');

    $('.js-lesson-ttl').text(_lesson);
    $('.input-lesson').val(_lesson);
    $('.js-schedule-time').text(_date +' '+ _time);
    $('.input-date').val(_date);
    $('.input-time').val(_time);
    $('.input-instructor').val(_instructor);

    //scroll
    var headerh = getHeaderHeight();
    e.preventDefault();
    var target = this.hash,
        $target = $(target);
    if ($target.length > 0) {
      $('html, body').stop().animate({
        'scrollTop': $target.offset().top - headerh
      }, 300, 'swing', function () {});
    }
  })
}

function simplebartbl(){
  new SimpleBar($('.js-out-lst')[0], { autoHide: false })
}

$('.c-news .c-news__lst--item').click( function(){
  var _id = $(this).attr('data-id');
  $.ajax({
    method: 'POST',
    url: _url+"/libs/ajax-news.php",  
    dataType: 'json',
    data: {
      id: _id,
    },
    success: function(data){
      $('.popup-new').html('');
      $('.popup-new').append(data.html).fadeIn();
      $('body').addClass('menu-open');
      $('body').css('top', -st);
      $('.btn_close_new').click( function(){
        $('.popup-new').fadeOut();
        $('body').removeClass('menu-open');
        if ($('body').hasClass('menu-open') === false) {
            rescroll = $('body').css('top').replace(/-|px/g, '');
            $('body,html').scrollTop(rescroll);
        } else {
            $('body').css('top', -st);
        }
      });
    },
  });
});