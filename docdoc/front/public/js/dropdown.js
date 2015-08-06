
/* Citydropdown script */

function initDropDowns() {

    $('.b-dropdown').each( function( i ) {
        var $el = $(this);
        //var jsDropdownData = $.parseJSON($el.children('.b-dropdown_data').text());

        // buildDropdown(jsDropdownData, $el );

    });


    $('.b-dropdown_list .b-dropdown_item').click( function (){

            var $clickedItem = $(this),
                $wrapper = $clickedItem.closest('.b-dropdown'),
                $currentItem = $(".b-dropdown_item__current", $wrapper );
            $currentItemText = $(".b-dropdown_item__text", $wrapper );

            //$currentItem.html($clickedItem.html());
            $currentItemText.html($clickedItem.text());
            $('.b-dropdown_item.s-current', $wrapper ).removeClass('s-current');
            $clickedItem.addClass('s-current');

            $('.b-dropdown_list', $wrapper ).hide();
            $wrapper.removeClass("s-open");

        }
    );

    $(".b-dropdown_form").each( function() {
        var $wrapper = $(this).closest('.b-dropdown');
        $('.b-dropdown_list .b-dropdown_item', $wrapper).click( function(){
            var $clickedItem = $(this);
            var $clickedItemValue = $clickedItem.attr("data-cityid");
            $(".b-dropdown_input", $wrapper).val( $clickedItemValue );
            $(".b-dropdown_form", $wrapper).submit();
        });
    });

    $('body').unbind('click.dropdown').bind( 'click.dropdown', function(evt) {
        $('.b-dropdown_list').hide();
        $(".b-dropdown").removeClass("s-open");
    });

    $('.b-dropdown_item__current').click( function (evt){
            evt.stopPropagation();
            var $wrapper = $(this).closest('.b-dropdown');
            var $dropdownList = $('.b-dropdown_list', $wrapper );

            if (($dropdownList).is(":visible")) {
                $dropdownList.hide();
                $wrapper.removeClass("s-open");
            }
            else {
                $wrapper.addClass("s-open");
                $dropdownList.show();
            }

        }
    );

    // select by hashtag
    //if ( window.location.hash != '' ) {
    //    $('.b-dropdown_item').filter(function(){
    //        return $(this).data('anything')==window.location.hash.replace('#','');
    //    }).click();
    //}
}


function buildDropdown(data, $domElement ) {
    var $liTemplate = $('.b-dropdown_list__li-template .b-dropdown_item', $domElement ),
        $ul = $('.b-dropdown_list', $domElement ),
        $newLi = null,
        first = true,
        $currentItem = $(".b-dropdown_item__text", $domElement );

    for (var i in data) {
        if( typeof( data[i]['cityname'] ) == 'undefined' ) {
            continue;
        }
        var cityname = data[i].cityname,

            $newLi = $liTemplate.clone();
        //$newLi.data("anything", data[i].anything);
        //$newLi.html(data[i].cityname);
        $newLi.attr("data-cityid", data[i].cityid);
        $newLi.html(data[i].cityname);

        //placing first item as default
        if ( first ) {
            $currentItem.html( $newLi.html() );
            $newLi.addClass('s-current');
        }

        $newLi.appendTo($ul);

        first = false;
    }
}


/* Citydropdown end */