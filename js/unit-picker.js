var $j = jQuery.noConflict();

$j(document).ready(function(){
  $j('#troop_picked').hide();
  $j('#change_troop').click(change_troop);
  $j('#troopsearch').autocomplete({
    source: nslodge_ajax.ajaxurl + '?action=ns_get_troops_autocomplete',
    select: function( event, ui ) {
        $j("input[name=ChapterName]").val(ui.item.chapter_name);
        $j("input[name=UnitType]").val(ui.item.unit_type);
        $j("input[name=UnitNumber]").val(ui.item.unit_num);
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
    item.label = item.district_name + " - " + item.unit_type + " " + item.unit_num + city + " (" + item.SelectorName + ")";
    li = $j('<li>')
      .attr("data-value", JSON.stringify(item))
      .append(item.label)
      .appendTo(ul);
    return li;
  }
});

function change_troop(){
    $j('#troop_picked').hide();
    $j('#troop_picker').show();
    return false;
}

