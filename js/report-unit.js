var $j = jQuery.noConflict();

$j(document).ready(function(){
  $j('.entry-content form:first').submit(function(){
    var foo = {};
    $j(this).find('input[type=text], input[type=hidden], input[type=number], input[type=date], select').each(function(){
      foo[$j(this).attr('name')] = $j(this).val();
    });
    document.cookie = 'formData='+JSON.stringify(foo);
    document.cookie = 'electedScouts='+JSON.stringify($j(this).find('input[name=NumberElected]').val());
    document.cookie = 'currentScout='+JSON.stringify(1);
  });
  $j('#numret').keyup(calc);
  $j('#req').attr('readonly', true);
  $j('#troop_picked').hide();
  $j('#change_troop').click(change_troop);
  $j('#unit_contacted').change(function(){
      if ($j('#unit_contacted').is(':checked')) {
        $j('.ns_type_all').show();
      } else {
        $j('.ns_type_all').hide();
      }
  });
  $j('#election_type').change(function(){
      if ($j('#election_type').val() == 'election') {
          $j('.ns_type_election').show();
          $j('.ns_type_all').show();
          $j('#unit_contact_required').hide();
      }
      else if (($j('#election_type').val() == 'noone_eligible') ||
               ($j('#election_type').val() == 'non_participant')) {
          $j('.ns_type_election').hide();
          $j('#unit_contact_required').show();
          $j('input[name=ElectionDate]').val($j.datepicker.formatDate('yy-mm-dd', new Date()));
//          $j('select[name=camp]').val('Other (explain in Additional Information below)');
          $j('textarea[name=UETeamNames]').val('None');
          $j('input[name=RegActiveYouth]').val(0);
          $j('input[name=YouthPresent]').val(0);
          $j('input[name=NumberEligible]').val(0);
          $j('input[name=NumberBallotsReturned]').val(0);
          $j('input[name=NumberRequired]').val(0);
          $j('input[name=NumberElected]').val(0);
          if ($j('#election_type').val() == 'noone_eligible') {
              $j('textarea[name=AdditionalInfo]').val('No one was eligible this year.');
              $j('select[name=notification]').val('We have no candidates to notify');
          }
          else if ($j('#election_type').val() == 'non_participant') {
              $j('textarea[name=AdditionalInfo]').val('This troop does not participate in the OA.');
              $j('select[name=notification]').val('We have no candidates to notify');
          }
      }
      else if ($j('#election_type').val() == '') {
          $j('.ns_type_election').hide();
          $j('.ns_type_all').hide();
          $j('#unit_contact_required').hide();
      }
  });
  $j('#troopsearch').autocomplete({
    source: nslodge_ajax.ajaxurl + '?action=ns_get_troops_autocomplete',
    select: function( event, ui ) {
        $j("input[name=ChapterName]").val(ui.item.chapter_name);
        $j("input[name=UnitType]").val(ui.item.unit_type);
        $j("input[name=UnitNumber]").val(ui.item.unit_num);
        $j("input[name=UnitLeaderName]").val(ui.item.ul_full_name);
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
  $j('.entry-content form:first').submit(function(){
    if (parseInt($j(this).find('input[name=NumberElected]').val()) == 0) {
      $j('span[id=done] input[type=checkbox]').attr("checked",true);
    }
  });
});

function calc(){
    $j('#req').val(Math.ceil(parseInt($j(this).val(), 10) / 2));
}

function change_troop(){
    $j('#troop_picked').hide();
    $j('#troop_picker').show();
    return false;
}
