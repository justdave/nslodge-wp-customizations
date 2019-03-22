var $j = jQuery.noConflict();

$j(document).ready(function(){
  $j('#troop_picked').hide();
  $j('#change_troop').click(change_troop);
  $j('#nominate_leader').change( function() {
      if (this.checked) {
          $j('#adult_main_form').show();
          $j('#adult_main_form').focus();
          $j('input[name="FirstName"]').focus();
          $j('input[name=CurrentPosition][value="Assistant Scoutmaster"]').parent().parent().hide();
          $j('input[name=CurrentPosition][value="Associate Adviser"]').parent().parent().hide();
          $j('input[name=CurrentPosition][value="Mate"]').parent().parent().hide();
          $j('input[name=CurrentPosition][value="Committee Chairman"]').parent().parent().hide();
          $j('input[name=CurrentPosition][value="Secretary"]').parent().parent().hide();
          $j('input[name=CurrentPosition][value="Treasurer"]').parent().parent().hide();
          $j('input[name=CurrentPosition][value="Committee Member"]').parent().parent().hide();
          $j('input[name=CurrentPosition][value="Other (specify in comments)"]').parent().parent().hide();
      } else {
          $j('#adult_main_form').hide();
      }
  });
  $j('input[name=CurrentPosition]').change( function() {
      position = $j('input[name=CurrentPosition]:checked').val();
      if (position == 'Scoutmaster' || position == 'Crew Adviser' || position == 'Skipper') {
          $j('#unit_leader_chosen').show();
      } else {
          $j('#unit_leader_chosen').hide();
      }
  });
  $j('#troopsearch').autocomplete({
    source: nslodge_ajax.ajaxurl + '?action=ns_get_troops_autocomplete',
    select: function( event, ui ) {
        $j("input[name=ChapterName]").val(ui.item.chapter_name);
        $j("input[name=UnitType]").val(ui.item.unit_type);
        $j("input[name=UnitNumber]").val(ui.item.unit_num);
        $j("#ULName").val(ui.item.ul_full_name);
        $j("#CCName").val(ui.item.cc_full_name);
        $j('#troop_result').html(ui.item.label);
        $j('#troop_picker').hide();
        $j('#troop_picked').show();
        $j('#troopsearch').val("");
        $j('#adult_unit_loading').show();
        $j.ajax({
            url : nslodge_ajax.ajaxurl,
            type : 'get',
            data : {
                action : 'ns_get_unit_candidate_meta',
                chapter : ui.item.chapter_name,
                unit_type : ui.item.unit_type,
                unit_num : ui.item.unit_num
            },
            success : function( response ) {
                num_allowed = 0;
                if (response.num_candidates > 0) {
                    num_allowed = Math.ceil(response.num_candidates / 3);
                }
                num_nominations = response.num_nominations - response.leader_nominated;
                adults_remaining = num_allowed - num_nominations;
                election_date = response.election_date;
                if (null == election_date) { election_date = "No election report submitted" }
                $j('#adult_unit_loading').hide();
                $j('#adult_unit_info').show();
                $j('#election_date').text(election_date);
                $j('#youth_elected').text(response.num_candidates.toString());
                $j('#adults_allowed').text(num_allowed.toString());
                $j('#adults_nominated').text(num_nominations.toString());
                $j('#adults_remaining').text(adults_remaining.toString());
                if (response.num_candidates == 0) {
                    $j('#report_submitted_no_youth').show();
                } else if (adults_remaining > 0) {
                    $j('#adult_main_form').show();
                    $j('#adult_unit_info').focus();
                    $j('input[name="FirstName"]').focus();
                } else {
                    $j('#exceeded_nominations').show();
                    $j('#nominate_leader').prop('checked', false);
                }
            }

        })
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
  $j('#DateOfBirth').change(function(){
    var age = getAge($j('#DateOfBirth').val());
    $j('#scout_age').text("(Age: " + age + ")");
  });
  $j('#recommendation').change(function(){
      if ($j('#recommendation').val() == 'Unit Recommendation') {
          $j('#unit_unpicked').hide();
          $j('#district_picker').hide();
          $j('#troop_picker').show();
      }
      else if ($j('#recommendation').val() == 'District/Council Recommendation') {
          $j('#unit_unpicked').hide();
          $j('#district_picker').show();
          $j('#troop_picker').hide();
      }
      else {
          $j('#unit_unpicked').show();
          $j('#district_picker').hide();
          $j('#troop_picker').hide();
      }
      $j('#district_search').val("");
      $j('#troop_picked').hide();
      $j('#adult_main_form').hide();
      $j('#adult_unit_loading').hide();
      $j('#adult_unit_info').hide();
      $j('#report_submitted_no_youth').hide();
      $j('#exceeded_nominations').hide();
      $j('input[name=ChapterName]').val("");
      $j('input[name=UnitType]').val("");
      $j('input[name=UnitNumber]').val("");
      $j('input[name=CurrentPosition][value="Assistant Scoutmaster"]').parent().parent().show();
      $j('input[name=CurrentPosition][value="Associate Adviser"]').parent().parent().show();
      $j('input[name=CurrentPosition][value="Mate"]').parent().parent().show();
      $j('input[name=CurrentPosition][value="Committee Chairman"]').parent().parent().show();
      $j('input[name=CurrentPosition][value="Secretary"]').parent().parent().show();
      $j('input[name=CurrentPosition][value="Treasurer"]').parent().parent().show();
      $j('input[name=CurrentPosition][value="Committee Member"]').parent().parent().show();
      $j('input[name=CurrentPosition][value="Other (specify in comments)"]').parent().parent().show();
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
          $j("input[name=ChapterName]").val('ABCDE'.substring(unitnum-1,unitnum));
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

function change_troop(){
    $j('#recommendation').change();
    return false;
}

function getAge(dateString) {
    var today = new Date();
    var birthDate = new Date(dateString);
    var age = today.getFullYear() - birthDate.getFullYear();
    var m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
}
