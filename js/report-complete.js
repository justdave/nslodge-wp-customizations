var $j = jQuery.noConflict();

$j(document).ready(function(){
  //if (document.cookie.match('(^|;)?formData=([^;]*)(;|$)') == null) {
  //  var url = window.location.href;
  //  url = url.replace(/-complete/,'');
  //  window.location.replace(url);
  //}
  var electedScouts = parseInt($j.parseJSON(
    document.cookie.match('(^|;)?electedScouts=([^;]*)(;|$)')[2]
  ));
  if (electedScouts) {
    $j('#NoCandidates').hide();
    $j('span[id=scoutsElected]').text(electedScouts);
    var adultsAllowed = Math.ceil(electedScouts / 3);
    $j('span[id=adultsAllowed]').text(adultsAllowed);
  } else {
    $j('#candidates').hide();
  }
  document.cookie = "electedScouts=;expires=Thu, 01 Jan 1970 00:00:01 GMT";
  document.cookie = "currentScout=;expires=Thu, 01 Jan 1970 00:00:01 GMT";
  document.cookie = "formData=;expires=Thu, 01 Jan 1970 00:00:01 GMT";
  document.cookie = "enteredSoFar=;expires=Thu, 01 Jan 1970 00:00:01 GMT";
});
