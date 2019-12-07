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
        $j("input[name=UnitDesignator]").val(ui.item.unit_desig);
        $j("#ULName").val(ui.item.ul_full_name);
        $j("#CCName").val(ui.item.cc_full_name);
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
  $j('#switch_to_unit').click(function(){
      $j('#district_picker').hide();
      $j('#troop_picker').show();
      $j('#district_search').val("");
      $j('#troop_picked').hide();
      $j('input[name=ChapterName]').val("");
      $j('input[name=UnitType]').val("");
      $j('input[name=UnitNumber]').val("");
      $j('input[name=UnitDesignator]').val("");
      return false;
  });
  $j('#switch_to_district').click(function(){
      $j('#district_picker').show();
      $j('#troop_picker').hide();
      $j('#district_search').val("");
      $j('#troop_picked').hide();
      $j('input[name=ChapterName]').val("");
      $j('input[name=UnitType]').val("");
      $j('input[name=UnitNumber]').val("");
      $j('input[name=UnitDesignator]').val("");
      return false;
  });
  $j('#district_search').change(function(){
      unittype = 'District';
      unitnum = $j('#district_search').val();
      if (unitnum == 'PF') {
          unittype = 'Council';
          unitnum = '781';
          $j('input[name=ChapterName]').val('PFFSC Staff');
      }
      else if (unitnum == 'MCC') {
          unittype = 'Council';
          unitnum = '780';
          $j('input[name=ChapterName]').val('MCC Staff');
      }
      else {
          $j('#district_picked').show();
          var districtmap = [];
          districtmap[72] = 'A';
          districtmap[71] = 'B';
          districtmap[1] = 'C';
          districtmap[4] = 'D';
          districtmap[3] = 'E';
          $j("input[name=ChapterName]").val(districtmap[unitnum]);
         // $j("input[name=ChapterName]").val('');
      }
      displaytext = $j('#district_search').find('option:selected').html() + ' - ' + unittype + ' ' + unitnum;
      $j('input[name=UnitType]').val(unittype);
      $j('input[name=UnitNumber]').val(unitnum);
      $j('#troop_result').html(displaytext);
      $j('#district_picker').hide();
      $j('#troop_picker').hide();
      $j('#troop_picked').show();
      $j('#adult_main_form').show();
      $j('#adult_main_form').focus();
      $j('input[name="FirstName"]').focus();
  });

});

function change_troop() {
    $j('#district_picker').hide();
    $j('#troop_picker').show();
    $j('#district_search').val("");
    $j('#troop_picked').hide();
}
