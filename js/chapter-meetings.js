var $j = jQuery.noConflict();
$j(document).ready(function(){
  $j('.hide').hide();
  $j("#choices").change(function(){
    $j('.hide').slideUp();
    $j('#'+this.value).slideDown()
  });
});
