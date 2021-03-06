
var inputs,
    data = {};

$(document).ready(function() {
  addDatePicker();
  addSchedule();
  deleteRow();
  // addNewRowRP();
  addNewSchedule();
  yearMonthFilter();
  selectRedirect();
  // reverseListSchedule();
  validateCreate();
})
function addDatePicker(){
  var dateToday = new Date();
  var options = $.extend({},
    $.datepicker.regional["ja"], {
      changeMonth: true,
      changeYear: true,
      minDate: dateToday,
      dateFormat: 'yy/mm/dd',
    }
  );
  $('body').on('focus', '.js-datepicker', function(){
    $(this).datepicker(options)
  });
}
function addSchedule(){
  $('body').on('click', '.js-edit-row', function(){
    fields = $(this).closest('.js-row').find('input, textarea, select').not('.restrict');
    if($(this).is('.editing')){
      $(this).text('修正する');
      $(this).removeClass('editing');
      fields.prop('disabled', true);
      $('.js-form-schedule').trigger('submit');
    }else{
      $(this).text('セーブする');
      $(this).addClass('editing');
      fields.prop('disabled', false);
    }
  })

  $('.js-form-schedule').on('submit', function() {
    data = {};
    if($(this).is('.creating')){
      inputs = $(this).find('input, textarea, select');
      inputs.each(function(key, value) {
        var name = $(this).attr('name'),
            val = $(this).val();
        data[name] = val;
      })
    }else{
      inputs = $('.js-lst-schedule').find('input, textarea, select');
      inputs.each(function(key, value) {
        var name = $(this).attr('name'),
            val = $(this).val();
        if(name == 'schedule'){
          data['schedule'] = parseInt(val) - 1;
        }else{
          data[name] = val;
        }
      })

      $.ajax({
        method: 'POST',
        dataType: 'json',
        data: data
      }).success(function(data) {
        // $('.js-status').text(data.status);
      }).error(function(xhr, status, error) {
        console.log(xhr + status + error);
      })
      return false;
    }
  })
}
// function addNewRowRP(){
//   $('body').on('click', '.js-add-row', function(){
//     var size = $('.js-rp .js-row').length,
//         name_input,
//         id_input;
//     $('.js-number-rp').attr('value', size + 1);
//     $('.js-rp .js-row').eq(0).clone().appendTo('.js-rp').attr('data-row', size);

//     // Set new name for INPUT when clone
//     $('.js-row[data-row='+size+']').find('input, textarea, select').each(function() {
//         name_input = $(this).attr('name');
//         new_name_input = name_input.replace('_0_', '_' + size + '_');
//         id_input = $(this).attr('id');
//         new_id_input = id_input.replace('_0', '_' + size);
//         $(this).attr('name', new_name_input);
//         $(this).attr('id', new_id_input);
//     });


//     // Reset Non value when clone box
//     $('.js-row[data-row='+size+']').find('input, textarea, select').val('');
//     addDatePicker();
//   })
// }

function deleteRow(){
  $('body').on('click', '.js-delete-row', function(){
    var size = $('.js-rp .js-row').length + 1,
        pos = $(this).closest('.js-row').attr('data-row');
    $(this).closest('.js-row').remove();
    $('.js-val-row-delete').attr('value', pos);
    $('.js-number-rp').attr('value', size - 1);
    var i = 0;
    $('.js-rp .js-row').each(function(){
      var thisRow = $(this).attr('data-row');
      $(this).attr('data-row', i);
      // Set new name for INPUT when clone
      $(this).find('input, textarea, select').each(function() {
          name_input = $(this).attr('name');
          new_name_input = name_input.replace('_' + thisRow + '_', '_' + i + '_');
          id_input = $(this).attr('id');
          new_id_input = id_input.replace('_' + thisRow, '_' + i);
          $(this).attr('name', new_name_input);
          $(this).attr('id', new_id_input);
      });
      i++;
    })

    $('.js-new-post').each(function(){
      var thisRow = $(this).attr('data-row');
      $(this).attr('data-row', i);
      // Set new name for INPUT when clone
      $(this).find('input, textarea, select').each(function() {
          name_input = $(this).attr('name');
          new_name_input = name_input.replace('_' + thisRow + '_', '_' + i + '_');
          id_input = $(this).attr('id');
          new_id_input = id_input.replace('_' + thisRow, '_' + i);
          $(this).attr('name', new_name_input);
          $(this).attr('id', new_id_input);
      });
      i++;
    })

    setTimeout(function(){
      $('.js-form-schedule').trigger('submit');
    }, 200);
  })
}

function addNewSchedule(){
  var stars = ['★ 1', '★ 1.5', '★★ 2', '★★ 2.5', '★★★ 3', '★★★ 3.5', '★★★★ 4', '★★★★★ 5'];
  $('.js-create-select').on('change', function(){
    var option = $('option:selected', this),
        data_id = option.attr('data-id'),
        data_ttl = option.attr('data-ttl'),
        data_content = option.attr('data-content'),
        data_level = option.attr('data-level'),

        el_content = $('.js-lesson-content'),
        el_level = $('.js-lesson-level');
    el_content.html('').append($.parseHTML(data_content));
    el_level.find('option[value='+data_level+']').prop('selected', true);
  })

  var size = $('.js-rp .js-row').length,
      name_input,
      id_input;
  $('.js-number-rp').attr('value', size + 1);
  $('.js-new-post').attr('data-row', size);

  $('.js-new-post').find('input, textarea, select').each(function() {
      name_input = $(this).attr('name');
      new_name_input = name_input.replace('_0_', '_' + size + '_');
      id_input = $(this).attr('id');
      new_id_input = id_input.replace('_0', '_' + size);
      $(this).attr('name', new_name_input);
      $(this).attr('id', new_id_input);
  });

  $('body').on('click', '.js-add-new', function(){
    $('.js-form-schedule').addClass('creating');
    setTimeout(function(){
      $('.js-form-schedule').trigger('submit');
    }, 200);
  })
}

function yearMonthFilter(){
  var ym = getUrlParameter('ym');
  if(ym !== undefined){
    console.log(ym);
    $('.js-rp .js-row').find('.js-datepicker').each(function(){
      var thisDate = $(this).val(),
          date = new Date(thisDate),
          day = date.getDate(),
          month = date.getMonth()+1,
          year = date.getFullYear(),
          thisYM = year+'/'+month;
      if(ym != thisYM){
        $(this).closest('.js-row').hide();
      }
    });
  }
}

function selectRedirect(){
  $('.js-select-redirect').on('change', function () {
    var url = $(this).val();
    if (url) {
      window.location = url;
    }
    return false;
  });
}

// function reverseListSchedule(){
//   var list = $('.js-rp');
//   var listItems = list.children('.js-row');
//   list.append(listItems.get().reverse());
// }

var getUrlParameter = function getUrlParameter(sParam) {
  var sPageURL = window.location.search.substring(1),
      sURLVariables = sPageURL.split('&'),
      sParameterName,
      i;

  for (i = 0; i < sURLVariables.length; i++) {
    sParameterName = sURLVariables[i].split('=');
    if (sParameterName[0] === sParam) {
      return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
    }
  }
};


function validateCreate(){
  $('.js-start-time').on('change', function(){
    var selected = $(this)[0].selectedIndex;
    if($('.js-end-time').val() == '' || $('.js-end-time')[0].selectedIndex <= selected){
      $('.js-end-time option:nth-child('+(selected+2)+')').prop('selected', 'true');
    }
    $('.js-end-time option').each(function(){
      if($(this).index() <= selected){
        $(this).prop('disabled', 'true');
      }else{
        $(this).removeAttr('disabled');
      }
    })
  })
}