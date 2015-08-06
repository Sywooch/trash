$(document).ready(function(){

    /* ie8 handing imitations of labels */
    $("input.radio_imit__input:checked").each(function(){
        $(this).next(".radio_imit__label").addClass("s-slctd");
    });
    $(".radio_imit__label").click(function(){
        $(this).addClass("s-slctd");
        var $clickedLabel = $(this);
        var $form = $clickedLabel.closest($(".req_form"));
        var $formLabels = $(".radio_imit__label", $form);

        $formLabels.removeClass("s-slctd");
        $clickedLabel.addClass("s-slctd");
    });
    /* ie8 handing imitations of labels end */
});