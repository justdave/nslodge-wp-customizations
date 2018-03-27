var $j = jQuery.noConflict();

$j(document).ready(function(){
  $j('#troop_picked').hide();
  $j('#change_troop').click(change_troop);
  $j('#troopsearch').autocomplete({
    source: nslodge_ajax.ajaxurl + '?action=ns_get_troops_autocomplete',
    select: function( event, ui ) {
        $j("#ChapterName").val(ui.item.SelectorName).change();
        $j("#UnitNumber").attr('value',ui.item.unit_num);
        // ^ this doesn't work with .val() for some reason, maybe because type="number"
        $j('#troop_result').html(ui.item.label);
        $j('#troop_picker').hide();
        $j('#troop_picked').show();
        $j('#troopsearch').val("");
        return false;
    },
  }).autocomplete("instance")._renderItem = function( ul, item ) {
    //alert(JSON.stringify(item))
    item.label = item.district_name + " - Troop " + item.unit_num + " (" + item.SelectorName + ")";
    li = $j('<li>')
      .attr("data-value", JSON.stringify(item))
      .append(item.label)
      .appendTo(ul);
    return li;
  }
  $j('#recommendation').change(function(){
      if ($j('#recommendation').val() == 'Unit Recommendation') {
          $j('#unit_unpicked').hide();
          $j('#district_picker').hide();
          $j('#district_search').val("");
          $j('#troop_picker').show();
          $j('#troop_picked').hide();
          $j('#district_picked').hide();
          $j('input[name=UnitType]').val('Troop');
      }
      else if ($j('#recommendation').val() == 'District/Council Recommendation') {
          $j('#unit_unpicked').hide();
          $j('#district_picker').show();
          $j('#district_search').val("");
          $j('#troop_picker').hide();
          $j('#troop_picked').hide();
          $j('#district_picked').hide();
      }
      else {
          $j('#unit_unpicked').show();
          $j('#district_picker').hide();
          $j('#district_search').val("");
          $j('#troop_picker').hide();
          $j('#troop_picked').hide();
          $j('#district_picked').hide();
      }
  });
  $j('#district_search').change(function(){
      unittype = 'District';
      unitnum = $j('#district_search').val();
      if (unitnum == 'PF') {
          unittype = 'Council';
          unitnum = '781';
          $j('#ChapterName').val('PFFSC Staff').change();
      }
      else if (unitnum == 'MCC') {
          unittype = 'Council';
          unitnum = '780';
          $j('#ChapterName').val('MCC Staff').change();
      }
      else {
          $j('#district_picked').show();
          $j("#ChapterName option[value='']").attr('selected',true);
      }
      displaytext = $j('#district_search').find('option:selected').html() + ' - ' + unittype + ' ' + unitnum;
      $j('#UnitType').val(unittype);
      $j('#UnitNumber').attr('value',unitnum);
      // ^ this doesn't work with .val() for some reason, maybe because type="number"
      $j('#troop_result').html(displaytext);
      $j('#district_picker').hide();
      $j('#troop_picker').hide();
      $j('#troop_picked').show();
  });


});

function change_troop(){
    $j('#recommendation').change();
    return false;
}
