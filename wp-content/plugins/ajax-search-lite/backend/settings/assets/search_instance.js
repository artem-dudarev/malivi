jQuery(function ($) {
    $('.tabs a[tabid=1]').click(function () {
        $('.tabs a[tabid=101]').click();
    });

    $('.tabs a[tabid=4]').click(function () {
        $('.tabs a[tabid=401]').click();
    });

    $('.tabs a').on('click', function(){
        $('#sett_tabid').val($(this).attr('tabid'));
        location.hash = $(this).attr('tabid');
    });

    // Remove the # from the hash, as different browsers may or may not include it
    var hash = location.hash.replace('#','');

    if(hash != ''){
        hash = parseInt(hash);
        $('.tabs a[tabid=' + Math.floor( hash / 100 ) + ']').click();
        $('.tabs a[tabid=' + hash + ']').click();
    } else {
        $('.tabs a[tabid=1]').click();
    }

    $('input[name="search_all_cf"]').change(function(){
        if ($(this).val() == 1)
            $('input[name="customfields"]').parent().addClass('disabled');
        else
            $('input[name="customfields"]').parent().removeClass('disabled');
    });
    $('input[name="search_all_cf"]').change();

    $('input[name="redirectonclick"] + .wpdreamsYesNoInner').click(function(){
        if ($(this).prev().val() == 0)
            $('select[name="redirect_click_to"]').parent().addClass('disabled');
        else
            $('select[name="redirect_click_to"]').parent().removeClass('disabled');
    });
    $('input[name="redirect_on_enter"] + .wpdreamsYesNoInner').click(function(){
        if ($(this).prev().val() == 0)
            $('select[name="redirect_enter_to"]').parent().addClass('disabled');
        else
            $('select[name="redirect_enter_to"]').parent().removeClass('disabled');
    });

    if ( $('input[name="redirectonclick"]').val() == 0 )
        $('select[name="redirect_click_to"]').parent().addClass('disabled');
    else
        $('select[name="redirect_click_to"]').parent().removeClass('disabled');

    if ( $('input[name="redirect_on_enter"]').val() == 0 )
        $('select[name="redirect_enter_to"]').parent().addClass('disabled');
    else
        $('select[name="redirect_enter_to"]').parent().removeClass('disabled');
});