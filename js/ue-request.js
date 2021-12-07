var $j = jQuery.noConflict();

$j(document).ready(function(){
  $j('#troop_picked').hide();
  $j('#change_troop').click(change_troop);
  $j('#troopsearch').autocomplete({
    source: nslodge_ajax.ajaxurl + '?action=ns_get_troops_autocomplete',
    select: function( event, ui ) {
        $j("input[name=ChapterName]").val(ui.item.chapter_name);
        $j("select[name=chapter-selector]").val(ui.item.chapter_name);
        $j("input[name=UnitType]").val(ui.item.unit_type);
        $j("input[name=UnitNumber]").val(ui.item.unit_num);
        $j("input[name=UnitDesignator]").val(ui.item.unit_desig);
        $j('#troop_result').html(ui.item.label);
        $j('#troop_picker').hide();
        $j('#troop_picked').show();
        $j('#troopsearch').val("");
        return false;
    },
  }).autocomplete("instance")._renderItem = function( ul, item ) {
    //alert(JSON.stringify(item))
    city = item.unit_city;
    if (!city) { city = "" }
    if (city.length > 2) {
        city = ' - ' + city;
    }
    desig = item.unit_desig;
    if (!desig) { desig = "" }
    else { desig = ' ' + desig.substring(0,1); }
    item.label = item.district_name + " - " + item.unit_type + " " + item.unit_num + desig + city + " (" + item.SelectorName + ")";
    li = $j('<li>')
      .attr("data-value", JSON.stringify(item))
      .append(item.label)
      .appendTo(ul);
    return li;
  }
  $j('#tell_or_schedule').change(function (){
      val = $j('input[name="tell_or_schedule"]:checked').val();
      if (val == "I need to request an election") {
          $j('#form-tell').slideUp(500);
          $j('#form-schedule').slideDown(500);
          $j('#form-both').slideDown(500);
      } else {
          $j('#form-tell').slideDown(500);
          $j('#form-schedule').slideUp(500);
          $j('#form-both').slideDown(500);
      }
  });
  $j('input[name="e-date-0"]').change(function() {
      val = $j('input[name="e-date-0"]').val();
      $j('input[name="e-date-1"]').val(val);
      $j('input[name="e-date-2"]').val(val);
      $j('input[name="e-date-3"]').val(val);
  });
  // Also run this at page load in case they reload the page with it already selected
  val = $j('input[name="tell_or_schedule"]:checked').val();
  if (val == "I need to request an election") {
      $j('#form-tell').hide();
      $j('#form-schedule').show();
      $j('#form-both').show();
  } else if (val == "I need to tell you about an already scheduled election") {
      $j('#form-tell').show();
      $j('#form-schedule').hide();
      $j('#form-both').show();
  }
});

function change_troop(){
    $j('#troop_picked').hide();
    $j('#troop_picker').show();
    return false;
}

