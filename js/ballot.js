var $j = jQuery.noConflict();
$j(document).ready(function(){
    $j(function(){
        var rows = 1;
        $j('#addRow').click(function(){
            if ($j('#nameRows input').length > 13) {
                alert("You already have 13 rows. If you need more names print an additional ballot sheet with the remaining nmes.");
            } else {
                $j('#nameRows').append('<div class="nameRow"><input type="text" name="names['+rows+'][name]" value="" autofocus/><a href="javascript:;" class="removeRow">Remove</a>');
                $j("#nameRows input:last").focus();
                rows++;
            }
        });
        $j(document).on('click', '.removeRow', function(){
            $j(this).closest('.nameRow').remove();
            $j("#nameRows input:last").focus();
        });
    });
});
