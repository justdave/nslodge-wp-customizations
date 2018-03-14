var $j = jQuery.noConflict();

$j(document).ready(function(){
  $j('.entry-content form:first').submit(function(){
    var foo = {};
    $j(this).find('input[type=text], input[type=number], input[type=date], select').each(function(){
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
  $j('#troopsearch').autocomplete({
    source: nslodge_ajax.ajaxurl + '?action=ns_get_troops_autocomplete',
    select: function( event, ui ) {
        $j("select[name=ChapterName]").val(ui.item.SelectorName).change();
        $j("input[name=UnitNumber]").val(ui.item.unit_num);
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
