var $j = jQuery.noConflict();
$j(document).ready(function(){
  $j('.hide').hide();
  $j("#choices").change(function(){
    $j('.hide').slideUp();
    $j('#'+this.value).slideDown();
    if (this.value == "none") {
        $j('#chaptermap').css("background-image", "url(/images/chaptermap2018-thumb.png)");
        $j("#chaptermaplink").attr("href", "/images/chaptermap2018.png");
    } else {
        $j('#chaptermap').css("background-image", "url(/images/chaptermap2018-" + this.value + "-thumb.png)");
        $j("#chaptermaplink").attr("href", "/images/chaptermap2018-" + this.value + ".png");
    }
  });
});
