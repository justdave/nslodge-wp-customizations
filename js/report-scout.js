var $j = jQuery.noConflict();

$j(document).ready(function(){
  var ff = $j('.entry-content form:first');
  var cookiematch;
  if (ff.length) {
    if (document.cookie.match('(^|;)?formData=([^;]*)(;|$)') == null) {
      var url = window.location.href;
      url = url.replace(/-scoutentry/,'');
      window.location.replace(url);
    }
    cookiematch = document.cookie.match('(^|;)?formData=([^;]*)(;|$)');
    if (cookiematch) {
      var data = $j.parseJSON(cookiematch[2]);
      var fieldList = ['ElectionDate','ChapterName','UnitType','UnitNumber','SubmitterName','SubmitterType','NumberElected'];
      var arrayLength = fieldList.length;
      for (var i = 0; i < arrayLength; i++) {
        var name = fieldList[i];
        $j('input[name='+name+'], select[name='+name+']').val(data[name]);
      }
    }
    cookiematch = document.cookie.match('(^|;)?electedScouts=([^;]*)(;|$)');
    if (cookiematch) {
        var electedScouts = $j.parseJSON(cookiematch[2]);
        $j('span[id=electedScouts]').text(electedScouts);
    }
    cookiematch = document.cookie.match('(^|;)?enteredSoFar=([^;]*)(;|$)');
    if (cookiematch) {
      var enteredSoFar = $j.parseJSON(cookiematch[2]);
      var i = 0;
      while (i < enteredSoFar.length) {
        enteredSoFar[i] = document.createElement( 'a' ).appendChild(document.createTextNode( enteredSoFar[i] ) ).parentNode.innerHTML;
        i++;
      }
      var scoutlist = enteredSoFar.join("<br>");
      $j('div[id=scoutlist]').html("<b>Entered so far:</b><br>" + scoutlist);
    }
    cookiematch = document.cookie.match('(^|;)?currentScout=([^;]*)(;|$)');
    if (cookiematch) {
        var currentScout = $j.parseJSON(cookiematch[2]);
        $j('span[id=currentScout]').text(parseInt(currentScout));
        if (parseInt(currentScout)==parseInt(electedScouts)) {
            $j('span[id=done] input[type=checkbox]').attr("checked",true);
            $j('.entry-content input[type=submit]').val("Submit Last Scout");
        };
    }
  }
  $j('.entry-content form:first').submit(function(){
      document.cookie = "currentScout=" + JSON.stringify(parseInt($j("#currentScout").text()) + 1);
      var cookiematch = document.cookie.match('(^|;)?enteredSoFar=([^;]*)(;|$)');
      var enteredSoFar = new Array();
      if (cookiematch) {
          enteredSoFar = $j.parseJSON(cookiematch[2]);
      }
      var scoutName = $j('input[name=FirstName]').val() + " " +
                      $j('input[name=MiddleName]').val() + " " +
                      $j('input[name=LastName]').val() + " " +
                      $j('input[name=Suffix]').val();
      enteredSoFar.push(scoutName);
      document.cookie = "enteredSoFar=" + JSON.stringify(enteredSoFar);
  });
  $j('#DateOfBirth').change(function(){
    var age = getAge($j('#DateOfBirth').val());
    $j('#scout_age').text("(Age: " + age + ")");
    $j('#scout_age').css('color', '');
    if (age > 20) {
        $j('#scout_age').text("(Age: " + age + ") - too old!");
        $j('#scout_age').css('color', 'red');
    }
  });
});

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
