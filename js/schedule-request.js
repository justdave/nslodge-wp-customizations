var $j = jQuery.noConflict();

$j(document).ready(function(){
  $j('#troop_picked').hide();
  $j('#change_troop').click(change_troop);
  $j('#troopsearch').autocomplete({
    source: nslodge_ajax.ajaxurl + '?action=ns_get_troops_autocomplete',
    select: function( event, ui ) {
        $j("#chapter-selector").val(ui.item.SelectorName).change();
        $j('#chapter').val(ui.item.SelectorName);
        $j("input[name=tnum]").val(ui.item.unit_num);
        $j('#troop_result').html(ui.item.label);
        $j('#troop_picker').hide();
        $j('#troop_picked').show();
        $j('#troopsearch').val("");
        return false;
    },
  }).autocomplete("instance")._renderItem = function( ul, item ) {
    //alert(JSON.stringify(item))
    city = item.unit_city;
    if (city.length > 2) {
        city = ' - ' + city;
    }
    item.label = item.district_name + " - Troop " + item.unit_num + city + " (" + item.SelectorName + ")";
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

