Array.remove = function (array, from, to) {
    var rest = array.slice((to || from) + 1 || array.length);
    array.length = from < 0 ? array.length + from : from;
    return array.push.apply(array, rest);
};

$classNameForPopup = "";

var waitForFinalEvent = (function () {
    var timers = {};

    return function (callback, ms, uniqueId) {
        if (!uniqueId) {
            uniqueId = "Don't call this twice without a uniqueId";
        }
        if (timers[uniqueId]) {
            clearTimeout(timers[uniqueId]);
        }
        timers[uniqueId] = setTimeout(callback, ms);
    };
})();

function toUpTwo(text) {
    var p = text.indexOf(' ');
    if (p > 2) {
        return text.charAt(0).toUpperCase() + text.substr(1, p) + text.charAt(p + 1).toUpperCase() + text.substr(p + 2, text.length);
    } else {
        return toUpOne(text);
    }
};
function toUpOne(text) {
    return text.charAt(0).toUpperCase() + text.substr(1);
};

function update_new_appointment() {
    setInterval(function () {
        var master_id = $('.appointment_master_id').attr('master');
        url = window.homeUrl + "index.php?r=masters/updateNewAppointment";
        $.ajax({
            type: "POST",
            url: url,
            data: {master_id: master_id},
            success: function (data) {

                var old_c = $('.appointment_new_count').text();


                if (old_c < data) {
                    $('.appointment_new_count').text(data);
                    if ($('.appointment_status').attr('status') == 'new') {

                        url_html = window.homeUrl + "index.php?r=masters/getNewAppointment";
                        $.ajax({
                            type: "POST",
                            url: url_html,
                            data: {master_id: master_id},
                            success: function (datah) {
                                if (datah) {
                                    $('#prof-appointment-grid table tbody').prepend(datah);

                                    $('span.empty').hide();

                                    $(".popup-note, #YMapsID").on("click", ".popup-close", function () {
                                        $(this).closest(".popup-note").fadeOut(300);
                                    });

                                    $("*").on("click", ".item", function () {
                                        var parentSelect = $(this).closest(".form-inp").find(".form-select-over");
                                        var idxPopupSelect = parentSelect.data("select-popup-id");
                                        var curSelTxt = $(this).text();
                                        var curSelVal = $(this).data("value");
                                        $("#" + idxPopupSelect + " span.act").removeClass("act");
                                        $("#inp-" + idxPopupSelect).val(curSelVal);
                                        $("#inp-" + idxPopupSelect).trigger('change');
                                        $(this).addClass("act");
                                        $("#" + idxPopupSelect).hide();
                                        $("#cur-" + idxPopupSelect).text(curSelTxt).change();
                                        $("#cur-" + idxPopupSelect).parents(".form-inp").removeClass("form-inp-act");
                                        flagPopupSelectOpen = true;
                                    });

                                }
                            }
                        });

                    }

                }

            }
        });
    }, 3000);
}

/**
 * Выполняет автоперезагрузку при поиске
 *
 * @param {object} obj объект по которому идет клик
 *
 * @return void
 */
function autoReload(obj) {
    $(obj).closest("form").submit();
}


$(function () {

    $.ajaxSetup({cache: false});

    $(".crop-image").fancybox({
        type: 'ajax',
        scrolling: false
    });


    // Переключатель регионов в шапке сайта
    $(".change-region").on("click", function () {
        $(".change-region-container").show();
        $(".change-region-container-overlay").show();
        return false;
    });
    $(".change-region-container-overlay").on("click", function () {
        $(".change-region-container").hide();
        $(".change-region-container-overlay").hide();
        return false;
    });

    $(".col-right").on("change", "#inp-select-popup-service-type", function () {
        autoReload(this);
    });
    $(".col-right").on("change", "#inp-select-popup-service-subtype", function () {
        autoReload(this);
    });
    $(".col-right").on("change", "#inp-check_f_home", function () {
        autoReload(this);
    });
    $(".col-right").on("change", "districtMoscow", function () {
        autoReload(this);
    });
    $(".col-right").on("change", "#inp-select-popup-city", function () {
        autoReload(this);
    });

    $("*").on("click", ".lk-edit-works .work-item .close", function () {
        $(this).parent().remove();
        if (!$('.works-uploaded .work-item').length) {
            $('.prof-btn_next').hide();
        }
    });

    $("*").on("keyup", "#LfMaster_fullName, #LfAppointment_name", function () {
        $(this).val(toUpTwo($(this).val()));
    });
    $("*").on("keyup", "#LfMaster_name, #LfMaster_surname, #LfMaster_add_street, #LfSalon_name, #LfSalon_add_street, #LfAppointment_address", function () {
        $(this).val(toUpOne($(this).val()));
    });

    $(".form-inp_check-landing").click(function () {
        if ($('#i-check_contract').hasClass('checked')) {
            $('.button-unavailable-landing').show();
            $('.button-blue').css('opacity', '0.5');
        } else {
            $('.button-unavailable-landing').hide();
            $('.button-blue').css('opacity', '1');
        }
    });

    $('.popup-appointment-lk .popup-close, .overlay-lk').on('click', function () {
        var url = window.homeUrl + "index.php?r=masters/plus2000";
        var master_id = $('.popup-appointment-lk').attr('master-id');
        $.ajax({
            type: "POST",
            url: url,
            data: {master_id: master_id},
            success: function (data) {
                if (data) {
                    $('.lk-balance-value').text(data);
                    $('.popup-appointment-lk').remove();

                }
            }
        });
    });

    $(".form-inp_check-lk").click(function () {
        if ($('.popup-appointment-lk .png-lk').hasClass('checked')) {
            $('.popup-appointment-lk .button-container .button-unavailable').show();
        } else {
            $('.popup-appointment-lk .button-container .button-unavailable').hide();
        }
    });

    $('.contract-link').on('click', function () {
        $('.contract-window-overlay').show();
        $('.contract-window').show();
        return false;
    });

    $('.contract-window-overlay, .contract-window .close').on('click', function () {
        $('.contract-window-overlay').hide();
        $('.contract-window').hide();
    });

    if ($(".prof-appointment-tab_ico-1").length) {
        update_new_appointment();
    }

    $('.show_pay').on('click', function () {
        $('.pay').slideToggle(300, function () {
            $(".pay_sum").focus();
        });
    });

    $('.pay_redirect').on('click', function () {
        if ($('.pay_sum').val()) {
            window.location.href = window.homeUrl + "lk/topUp?amount=" + $('.pay_sum').val();
        }
    });


    $("#form-footer_btn, #main-search-sbmt, #filter-right-sbmt, #gallery-search-sbmt, .map-btn-search, #remind-sbmt").click(function () {
        $(this).closest("form").submit();
    });

    $("#main-map-angle").hover(
        function () {
            $(this).stop(true, true).animate({width: 214, height: 276}, 600);
            $(this).find(".txt").stop(true, true).animate({left: 28, top: 106}, 600);
        },
        function () {
            $(this).animate({width: 155, height: 200}, 600);
            $(this).find(".txt").animate({left: 23, top: 68}, 600);
        }
    );

    $(".gal-item").hover(
        function () {
            $(this).addClass("gal-item_act");
            $("#wrap").addClass("content-wrap_act");
        },
        function () {
            $(this).removeClass("gal-item_act");
            $("#wrap").removeClass("content-wrap_act");
        }
    );

    $(".tbl-cost_btn_all span").click(function () {
        var $this = $(this);
        var container = $this.parent().siblings('.card-prices-container');

        var toggle = function () {
            $("#tbl-price-" + $this.data("price-id")).slideToggle(600, "easeInOutExpo", function () {
                $this.html($.trim($this.text()) == "весь прайс-лист" ? "свернуть<i class='arr'></i>" : "весь прайс-лист<i class='arr'></i>");
                $this.toggleClass("act");
            });
        };

        if (container.data('isLoaded') == true) {
            toggle();
        } else {
            $.get(homeUrl + 'ajax/getPricesList', {
                id: $this.data('priceId'),
                type: $this.data('type'),
                service_id: $this.data('service'),
                spec_id: $this.data('spec')
            }, function (data) {
                container.replaceWith($(data).data('isLoaded', true));
                toggle();
            });
        }
    });

    $(".det-left_txt_f_l").click(function () {
        $(".det-left_txt_f").slideToggle(700, "easeInOutExpo", function () {
            $(".det-left_txt_f_l").text($(".det-left_txt_f_l").text() == "подробнее" ? "свернуть" : "подробнее");
        });
        return false;
    });

    $(".det-left_txt_s_l").click(function () {
        $(".det-left_txt_s").slideToggle(700, "easeInOutExpo", function () {
            $(".det-left_txt_s_l").text($(".det-left_txt_s_l").text() == "больше" ? "свернуть" : "больше");
        });
        return false;
    });

    $(".det-left_txt_sp_l a").click(function () {
        $this = $(this);
        $("#det-left_txt_sp-" + $this.data("spec-id")).slideToggle(700, "easeInOutExpo", function () {
            $this.html($this.text() == "больше" ? "свернуть" : "больше");
            $this.toggleClass("act");
        });
        return false;
    });

    $(".search-seo_open").click(function () {
        $(".search-seo_switch_txt").show();
        $(this).hide();
        return false;
    });
    $(".search-seo_close").click(function () {
        $(".search-seo_switch_txt").hide();
        $(".search-seo_open").show();
        return false;
    });

    $(".det-works_switch_open").click(function () {
        var $this = this;
        var container = $("#det-works_full");
        if (container.children().length > 0) {
            toggleMasterWorks($this);
        } else {
            $($this).addClass('b-loader-loading');
            var id = $(this).data('master-id');
            $.get(homeUrl + 'masters/moreWorks/' + id + '/', function (data) {
                container.html(data);
                toggleMasterWorks($this);
                initPhotos();
                initCardLikes();
                $($this).removeClass('b-loader-loading');
            }, 'html');
        }

        return false;
    });

    function toggleMasterWorks(elem) {
        var $this = $(elem);
        var container = $("#det-works_full");
        if (container.hasClass("det-works_full_close")) {
            container.slideDown(700, "easeInOutExpo", function () {
                $(this).removeClass("det-works_full_close");
                $(".det-works_full_link").removeClass("det-works_full_link");
                $this.toggleClass("det-works_full_link");
            });
        } else {
            container.slideUp(700, "easeInOutExpo", function () {
                $(this).addClass("det-works_full_close");
                $(".det-works_full_link").removeClass("det-works_full_link");
                $this.toggleClass("det-works_full_link");
            });
        }
    }

    /** comment form star **/
    var starSelect = function (idx, class_container) {
        var i = 0;
        $("." + class_container + " .det-com_form_rating span").each(function () {
            if (idx >= i)
                $(this).addClass("act");
            i++;
        });
    }

    $(".det-com_form_rating span").click(function () {
        idxStarSelect = $(this).index();
        var class_container = $(this).parent().parent().parent().attr('class');
        $("." + class_container + " .det-com_form_rating span").removeClass("act");
        starSelect(idxStarSelect, class_container);
        $("#LfOpinion_" + class_container).val(idxStarSelect + 1);
    });

    $(".det-com_form_rating span").hover(
        function () {
            var idxStar = $(this).index();
            var class_container = $(this).parent().parent().parent().attr('class');
            starSelect(idxStar, class_container);
        },
        function () {
            var container = $(this).parent().parent().parent();
            var class_container = container.attr('class');
            var value = container.find('input').val();
            $("." + class_container + " .det-com_form_rating span").removeClass("act");
            if (value != null)
                starSelect(value - 1, class_container);
        }
    );

    $('.stars_c input').each(function () {
        var val = $(this).val();
        var class_container = $(this).parent().attr('class');
        if (val) {
            starSelect(val - 1, class_container);
        }
    });

    /** comment form star **/

    $(".det-com_form input.tel").mask("+7 (999) 999 99 99", {placeholder: " "});

    $('.det-com_form_success_window_container').on('click', function () {
        $(this).animate({opacity: 0}, 1000).hide();
    });

    $('.is_useful .yes a').on('click', function () {
        var id = $(this).attr('opinion');
        url = window.homeUrl + "index.php?r=masters/opinionAjax";
        var span = $(this).parent().find('span');
        $.ajax({
            type: "POST",
            url: url,
            data: {opinion_id: id, yes: 1},
            success: function (data) {
                if (data) {
                    span.text(data);
                }
            }
        });
        return false;
    });
    $('.is_useful .no a').on('click', function () {
        var id = $(this).attr('opinion');
        url = window.homeUrl + "index.php?r=masters/opinionAjax";
        var span = $(this).parent().find('span');
        $.ajax({
            type: "POST",
            url: url,
            data: {opinion_id: id, no: 1},
            success: function (data) {
                if (data) {
                    span.text(data);
                }
            }
        });
        return false;
    });

    $('.stars_container .stars').poshytip({
        alignX: 'left'
    });

    $('.det-com_form_rating span').poshytip({
        alignX: 'left'
    });

    $('.is_useful .is_more').poshytip({
        alignX: 'left'
    });

    $('.det-com_form_number a').poshytip({
        alignX: 'left'
    });

    $('.det-com_form_number a').on('click', function () {
        return false;
    });

    $(".det-com_form_btn .button").click(function () {
        $(this).closest("form").submit();
    });

    /*** profile ***/
    $(".prof-iphoto .form-inp_check:eq(0)").click(function () {
        $(".prof-iphoto_file .ava").removeClass("female");
    });
    $(".prof-iphoto .form-inp_check:eq(1)").click(function () {
        $(".prof-iphoto_file .ava").addClass("female");
    });

    $(".prof-informer .close").click(function () {
        $(".prof-informer").fadeOut(400);
    });

    $("#departure_checkbox .form-inp_check:eq(0)").click(function () {
        $("#departure_block").hide();
    });
    $("#departure_checkbox .form-inp_check:eq(1)").click(function () {
        $("#departure_block").show();
    });

    $("#prof-switch_type_pass input").change(function () {
        var inpPass = $('#prof-pass');
        var inpPassRepeat = $('#prof-pass-repeat');
        if ($(this)./*find("input").*/attr("checked")) {
            html_1 = '<input type="text" name="' + inpPass.attr('name') + '" value="' + inpPass.attr('value') + '" id="' + inpPass.attr('id') + '" />';
            html_2 = '<input type="text" name="' + inpPassRepeat.attr('name') + '" value="' + inpPassRepeat.attr('value') + '" id="' + inpPassRepeat.attr('id') + '" />';
        }
        else {
            html_1 = '<input type="password" name="' + inpPass.attr('name') + '" value="' + inpPass.attr('value') + '" id="' + inpPass.attr('id') + '" />';
            html_2 = '<input type="password" name="' + inpPassRepeat.attr('name') + '" value="' + inpPassRepeat.attr('value') + '" id="' + inpPassRepeat.attr('id') + '" />';
        }
        inpPass.after(html_1).remove();
        inpPassRepeat.after(html_2).remove();
    });

    $(".prof-price_edit_val .form-inp_check input").change(function () {
        var e = $(this).closest(".prof-price_edit_i").find(".prof-price_edit_cost");
        if ($(this)./*find("input").*/attr("checked")) {
            e.addClass("price-inp-show");
            e.find('input[type="text"]').focus();
        }
        else
            e.removeClass("price-inp-show");
    })

    $(".prof-price-select").click(function () {
        $(this).next().stop().slideToggle(600);
        $(this).toggleClass("price-edit-open");
    })

    $(".prof-btn_next .button").click(function () {
        if ($(this).hasClass('lk-edit-works-button')) {
            var isErrors = false;
            $(".works-uploaded .form-inp").each(function () {
                if (!$(this).find("input").val()) {
                    $(this).addClass("error");
                    isErrors = true;
                }
            });

            if (isErrors) {
                alert("Не на всех фотографиях работ выбраны \"Раздел\" и \"Подраздел\"!");
                return false;
            }
        }
        $(this).closest("form").submit();
    });

    $(".prof-photo_add, .prof-photo_imgs_wrap").hover(
        function () {
            $(this).find(".prof-photo_add_over").stop().animate({bottom: 0}, 400);
        },
        function () {
            $(this).find(".prof-photo_add_over").stop().animate({bottom: -40}, 400);
        }
    );

    var insertDistrict = function () {
        var inpCh = $(this)/*.find("input")*/;
        var dataId = $(this).closest('.form-inp_check').data("check-id");
        if (inpCh.attr("checked")) {
            $("#prof-departureDistrictIds_list").append("<span id='sel-" + dataId + "'>" + $(this).closest('.form-inp_check').text() + "<i data-depdistrids='" + dataId + "'></i></span>");
        } else {
            $("#prof-departureDistrictIds_list #sel-" + dataId).remove();
        }
    };

    $("#select-popup-departureDistrictIds .form-inp_check input").each(insertDistrict).change(insertDistrict);

    $("#prof-departureDistrictIds_list").on("click", "i", function () {
        var depIdDel = $(this).data("depdistrids");
        $("#i-check_" + depIdDel).removeClass("checked");
        $("#inp-check_" + depIdDel).attr("checked", false);
        $(this).parent().remove();
    });
    /*** profile ***/

    /*** style select ***/
    var flagPopupSelectOpen = true;
    var api = '';

    /**
     * Приводит в действие выпадающий список
     *
     * @param {object }$obj объект-список
     */
    function setformSelectOver($obj) {
        api = '';
        if ($obj.closest(".form-inp").hasClass('no-items')) return;
        var idxPopupSelect = $obj.data("select-popup-id");
        if (idxPopupSelect) {
            $(".form-select-popup").hide();
            $(".form-inp-act").removeClass("form-inp-act");
            $obj.parents(".form-inp").addClass("form-inp-act");
            $("#" + idxPopupSelect).show();
            flagPopupSelectOpen = false;
            $("#wrap").addClass("content-wrap_act");
            if (api)
                api.destroy();
            api = $("#" + idxPopupSelect + " .form-select-popup-long").jScrollPane({verticalGutter: 7}).data('jsp');
        }
    }

    $("body").on("click", ".form-select-over", function () {
        setformSelectOver($(this));
    });

    $(".form-inp").on("mousewheel", ".jspContainer", function (event) {
        event.preventDefault();
    });

    $(document).on("click", ".form-select-popup .item, #popup .item, #educations .item, .popup-note .item", function () {
        var parentSelect = $(this).closest(".form-inp").find(".form-select-over");
        var idxPopupSelect = parentSelect.data("select-popup-id");
        var curSelTxt = $(this).text();
        var curSelVal = $(this).data("value");
        $("#" + idxPopupSelect + " span.act").removeClass("act");
        $("#inp-" + idxPopupSelect).val(curSelVal);
        $("#inp-" + idxPopupSelect).trigger('change');
        $(this).addClass("act");
        $("#" + idxPopupSelect).hide();
        $("#cur-" + idxPopupSelect).text(curSelTxt).change();
        $("#cur-" + idxPopupSelect).parents(".form-inp").removeClass("form-inp-act");
        flagPopupSelectOpen = true;
    });

    $(".form-inp, #popup, #educations").on("mouseenter", ".form-select-popup", function () {
        flagPopupSelectOpen = false;
    });
    $(".form-inp, #popup, #educations").on("mouseleave", ".form-select-popup", function () {
        flagPopupSelectOpen = true;
    });

    $(document).click(function () {
        if (flagPopupSelectOpen) {
            $(".form-select-popup").hide();
            $(".form-inp-act").removeClass("form-inp-act");
            $("#wrap").removeClass("content-wrap_act");
        }
    });

    $(".content-wrap, #popup").on("click", ".form-inp", function () {
        $(this).removeClass("error");
    })
    /*** style select ***/

    /** placeholder **/
    $(document).on('click', '.form-placeholder', function () {
        $(this).hide();
        var input = $(this).parent().find("input");
        input
            .off('blur.placeholder')
            .on('blur.placeholder', function () {
                if ($(this).val().length == 0)
                    $(this).parent().find(".form-placeholder").show();
            })
            .focus();
    });

    $(".form-placeholder-city").click(function () {
        $(this).hide();
        setformSelectOver($(".city-selector .form-select-over"));
    });
    /** placeholder **/

    /** style checkbox and radio **/
    $("input[type=checkbox], input[type=radio]").each(function () {
        if ($(this).attr("checked")) {
            var idCheck = $(this).closest(".form-inp_check").data("check-id");
            $("#i-check_" + idCheck).addClass("checked");
        }
    })

    $(".content-wrap, #popup, #master-is-free").on("click", ".form-inp_check", function () {
        if (!$(this).hasClass("form-inp_radio")) {
            var idCheck = $(this).data("check-id");
            if ($("#inp-check_" + idCheck).attr("checked")) {
                $("#inp-check_" + idCheck).removeAttr("checked");
                $("#i-check_" + idCheck).removeClass("checked");
            } else {
                $("#inp-check_" + idCheck).attr("checked", "checked");
                $("#i-check_" + idCheck).addClass("checked");
            }
            $("#inp-check_" + idCheck).trigger('change');
        }
    });

    var checkedRadioInp = function (el) {
        var idCheck = el.data("check-id");
        if (!$("#inp-check_" + idCheck).attr("checked")) {
            var parGroup = el.closest(".form-group_radio");
            parGroup.find("input:checked").attr("checked", false);
            parGroup.find(".checked").removeClass("checked");
            $("#inp-check_" + idCheck).attr("checked", true).trigger('change');
            $("#i-check_" + idCheck).addClass("checked");
        }
    }

    $(".form-inp_radio").click(function () {
        checkedRadioInp($(this));
    });

    $("*").on("click", ".form-inp_radio", function () {
        checkedRadioInp($(this));
    });
    /** style checkbox and radio **/


    $("input[name='LfMaster[phone_cell]'], input[name='LfSalon[phone]']").mask("+7 (999) 999 99 99", {placeholder: " "});

    initPhotos();

    // fancybox слайдер
    $('.fancybox').fancybox();

    /*** likes ***/
    $('.gal-like').click(function (e) {
        e.preventDefault();
        var $this = $(this);
        $.ajax({
            url: homeUrl + 'like/' + $this.data('work-id') + '/',
            type: 'POST',
            dataType: 'json'
        }).done(function (data) {
            $this.find('.gal-like_num').text(data.likes);
        });
    });
    /*** likes ***/


    /*** spec and service selectors ***/
    var handleSpecChange = function (dom) {
        dom = $(dom);
        var specId = dom.find('input[type="hidden"]').val(),
            serviceSelector = dom.siblings('.service-selector'),
            serviceTitle = serviceSelector.find('.form-select'),
            serviceInput = serviceSelector.find('input[type="hidden"]'),
            serviceWrapper = serviceTitle.closest('.form-inp');

        serviceId = serviceInput.val(),
            serviceReset = (!!serviceId),
            visibleItemCount = 0;

        items = serviceSelector.find('.item'),
            empty = items.length ? items.first() : null;
        if (empty !== null && empty.data('value')) empty = null;

        items.addClass('item-hidden');
        if (specId && serviceTree[specId]) {
            if (empty) empty.removeClass('item-hidden');
            for (var i = 0; i < serviceTree[specId].length; i++) {
                if (serviceTree[specId][i] == serviceId) serviceReset = false;
                serviceSelector.find('.item[data-value="' + serviceTree[specId][i] + '"]').removeClass('item-hidden');
                visibleItemCount++;
            }
        }

        if (serviceReset) {
            if (empty) {
                serviceTitle.html(empty.html());
                serviceInput.val(empty.data('value'));
            }
            else {
                serviceTitle.html('');
                serviceInput.val(null);
            }
        }

        if (visibleItemCount) {
            serviceWrapper.removeClass('no-items');
        }
        else {
            serviceWrapper.addClass('no-items');
        }
    };

    $("form, #popup").on('change', '.spec-selector input[type="hidden"]', function () {
        handleSpecChange($(this).closest('.spec-selector'));
    });

    $('body').bind('specs', function () {
        $('.spec-selector').each(function () {
            handleSpecChange(this);
        });
    });
    $('body').trigger('specs');
    /*** spec and service selectors ***/

    $("*").on("click", ".btn-appointment-pretty", function () {
        $('.pp_overlay').remove();
        $('.pp_pic_holder').remove();
    });

    /** popups **/
    $(document).on("click", ".btn-appointment", function () {
        $("#overlay").show();
        var $this = $(this);

        var gaType = "sign_up";
        if ($this.data('gatype')) {
            gaType = $this.data('gatype');
        }

        var url = homeUrl +
            'popup/appointment/' +
            gaType +
            "/" +
            ($this.data('full') ? 'full/' : 'short/');

        if ($this.data('salon-id')) {
            url += 'salon/' + $this.data('salon-id') + '/' + ($this.data('id') || 'any') + '/';
        }
        else if ($this.data('id')) {
            url += 'master/' + $this.data('id') + '/';
        }

        if ($this.data('spec-id')) {
            url += $this.data('spec-id') + '/';
        }
        if ($this.data('service-id')) {
            url += $this.data('service-id') + '/';
        }
        $.get(url, function (data) {
            $("#popup").html(data);
            showPopup("popup-appointment");
        });
        return false;
    });

    $("#overlay").click(function () {
        closePopup();
    });

    $("#popup").on("click", ".popup-close", function () {
        closePopup();
    });

    var closePopup = function () {
        $("#popup").fadeOut(300, function () {
            $("#overlay").hide();
            $("#popup").removeClass($classNameForPopup);
            $("#popup").html("");
        });
    }

    $(".det-left_c, .search-res_item, #col-right-map, .det-right").on('click', '.abuse-link', function (e) {
        $('.popup-abuse').html('');
        var $this = $(this),
            $wrapper = $this.parent().find('.popup-abuse');

        e.preventDefault();

        if ($this.data('loading')) return;

        $this.data('loading', true);
        $.get(homeUrl + "popup/abuse/" + ($this.data("salon-id") ? 'salon/' + $this.data('salon-id') : 'master/' + $this.data('id')) + "/", function (data) {
            $wrapper.html(data).show();
        }).always(function () {
            $this.data('loading', false);
        });

        return false;
    });

    $("#prof-appointment-grid").on('click', '.apply-button', function (e) {
        $('.popup-apply').html('');
        var $this = $(this),
            $wrapper = $this.parent().find('.popup-apply');

        e.preventDefault();

        if ($this.data('loading')) return;

        $this.data('loading', true);
        $.get(window.homeUrl + "popup/applyAppointment/" + $this.data('status') + "/" + $this.data('id') + "/", function (data) {
            $wrapper.html(data).show();
        }).always(function () {
            $this.data('loading', false);
        });

        return false;
    });

    $("#prof-appointment-grid").on('click', '.cancel-button', function (e) {
        $('.popup-cancel').html('');
        var $this = $(this),
            $wrapper = $this.parent().find('.popup-cancel');

        e.preventDefault();

        if ($this.data('loading')) return;

        $this.data('loading', true);
        $.get(window.homeUrl + "popup/cancelAppointment/" + $this.data('status') + "/" + $this.data('id') + "/", function (data) {
            $wrapper.html(data).show();
        }).always(function () {
            $this.data('loading', false);
        });

        return false;
    });

    $(".popup-note, #YMapsID").on("click", ".popup-close", function () {
        $(this).closest(".popup-note").fadeOut(300);
    });
    /** popups **/

    /** landing form **/
    handleSelector = function () {

        switch ($('input:checked[name="selector"]').val()) {
            case 'master':
                $('.button-unavailable-landing').hide();
                $('.landing-contract').show();
                if ($('#i-check_contract').hasClass('checked')) {
                    $('.button-unavailable-landing').hide();
                    $('.button-blue').css('opacity', '1');
                } else {
                    $('.button-unavailable-landing').show();
                    $('.button-blue').css('opacity', '0.5');
                }
                $('.salon').hide();
                $('.master').show();
                $('.reg-auth_panel').show();
                break;
            case 'salon':
                $('.button-blue').css('opacity', '1');
                $('.button-unavailable-landing').hide();
                $('.landing-contract').hide();
                $('.master').hide();
                $('.salon').show();
                $('.reg-auth_panel').hide();
                break;
        }
    };

    $('input[name="selector"]').change(handleSelector);
    handleSelector();
    $('#form-register').show();
    /** landing form **/

    /** profile address **/
    handleCity = function () {

        switch ($('input:checked[name="City"]').val()) {
            case '0':
                $('#city').hide();
                $('#metro').show();
                $('#district').show();
                $('#departure_block').show();
                $("#cur-select-popup-city_id").text("Москва");

                break;
            case '1':
                $('#metro').hide();
                $('#district').hide();
                $('#departure_block').hide();
                $('#city').show();
                $("#cur-select-popup-underground_station_id").text("");
                if ($("#cur-select-popup-city_id").text() == "Москва") $("#cur-select-popup-city_id").text("Балашиха");

                break;
        }
    };

    $('input[name="City"]').change(handleCity);
    handleCity();
    $('#master-address-form-personal').show();
    $('#salon-address-form-personal').show();
    /** profile address **/

    /** search form **/
    handleSearch = function () {

        switch ($('input:checked[name="City_"]').val()) {
            case '0':
                $('.city-selector').hide();
                $('.head-city').hide();
                $('#select-metro').show();
                $('#select-area').show();
                $('.head-metro').show();
                $('.head-distr').show();
                $('#inp-select-popup-city').val('1');
                break;
            case '1':
                $('.head-city').show();
                $('.city-selector').show();
                $('#select-metro').hide();
                $('#select-area').hide();
                $('.head-metro').hide();
                $('.head-distr').hide();
                if ($('#inp-select-popup-city').val() == '1') $('#inp-select-popup-city').val('2');
                break;
        }
    };

    $('input[name="City_"]').change(handleSearch);
    handleSearch();
    $('#search-filter').show();
    $('#search-filter').show();
    /** search form **/

    var selMetro = $("#select-metro");
    var metroOutput = $("#selected-metro_popup");
    var selArea = $("#select-area");
    var areaOutput = $("#selected-areas_popup");

    /** metro **/

    selMetro.click(function () {
        $("#overlay").show();
        $.get(
            homeUrl + "popup/map/",
            function (data) {
                var stations = {},
                    splitIds = $('#stations').val() ? $('#stations').val().split(',') : [], i;

                for (i = 0; i < splitIds.length; i++) {
                    stations[splitIds[i]] = true;
                }

                $("#popup")
                    .html(data)
                    .find('#map_stations div')
                    .each(function () {
                        if (stations[$(this).data('idline')]) {
                            $(this).addClass('act');
                        }
                    });
                $('#map_stations').trigger('station-light');
                showPopup("popup-metro");
            }
        );
    });

    $("#popup").on("click", "#map-submit", function () {
        var ids = [], title = [];

        $("#map_stations div")
            .each(function () {
                if (!$(this).hasClass('act')) return;

                ids.push($(this).data('idline'));
                title.push($(this).attr('title'));
            });

        $("#stations").val(ids.join(","));

        if (title.length) {
            metroOutput.find("div").html(title.join(", ")).parent().removeClass("metro-no-value");
            selMetro.find(".form-select").html(title.join(", ")).addClass("metro-selected");
            $("#areaMoscow").val('');
            $("#districtMoscow").val('');
            selArea.find(".form-select").html("Любой район");
            areaOutput.addClass("areas-no-value");
        } else {
            metroOutput.find("div").html("").parent().addClass("metro-no-value");
            selMetro.find(".form-select").html("Любое метро").removeClass("metro-selected");
        }

        closePopup();
        autoReload("#stations");
    });

    selMetro.hover(
        function () {
            if (!metroOutput.hasClass("metro-no-value"))
                metroOutput.stop(true, true).fadeIn(300);
            selMetro.css("z-index", "2");
        },
        function () {
            metroOutput.stop(true, true).fadeOut(300, function () {
                selMetro.css("z-index", "1");
            });
        }
    );
    /** metro **/


    /** areas moscow **/

    selArea.click(function () {
        $("#overlay").show();
        $.get(
            homeUrl + "popup/areas/",
            function (data) {
                $("#popup").html(data);
                showPopup("popup-areasMoscow");
            }
        );
    });

    $("#popup").on("click", "#area-submit", function () {
        var ids = [];
        title = [];
        $(".district-link").each(function () {
            if ($(this).hasClass("selected")) {
                ids.push($(this).data("id"));
                title.push($(this).html());
            }
        });
        $("#districtMoscow").val(ids.join(','));

        if (ids.length != 0) {
            $("#areaMoscow").val($("#area").val());
            selArea.find(".form-select").html(title.join(", "));
            areaOutput.removeClass("areas-no-value");
            $("#stations").val('');
            metroOutput.find("div").html("").parent().addClass("metro-no-value");
            selMetro.find(".form-select").html("Любое метро");
        } else {
            $("#areaMoscow").val('');
            selArea.find(".form-select").html("Любой район");
            areaOutput.addClass("areas-no-value");
        }
        areaOutput.find("div").html(title.join(", "))
        closePopup();
        autoReload("#districtMoscow");
    });

    selArea.hover(
        function () {
            if (!areaOutput.hasClass("areas-no-value"))
                areaOutput.stop(true, true).fadeIn(300);
            selArea.css("z-index", "2");
        },
        function () {
            areaOutput.stop(true, true).fadeOut(300, function () {
                selArea.css("z-index", "1");
            });
        }
    );
    /** areas moscow **/

    /** rating popup **/
    $(".show-rating-popup").mouseenter(function () {
        $(this).parent().find(".popup-rating").stop(true, true).fadeIn(300);
    });

    $(".popup-rating").mouseleave(function () {
        $(this).stop(true, true).fadeOut(300);
    });

    $(".price-card-container").mouseenter(function () {
        $(".popup-price-card").hide();
        $(this).find(".popup-rating").stop(true, true).fadeIn(300);
    });

    $(".price-card-container").mouseleave(function () {
        $(this).find(".popup-rating").stop(true, true).fadeOut(300);
    });
    /** rating popup **/

    $("#LfMaster_photo, #LfSalon_logo, #LfSalonPhoto_image").bind({
        change: function () {
            if ($.browser.msie) {
                if (parseInt($.browser.version) <= 9)
                    $('#load-img_name').show().text($(this).val().replace(/.*(\/|\\)/, ""));
            }
            displayFiles(this.files);
        }
    });

    /* education */
    var educationIndex = 0;

    function getEducationTemplate() {
        educationIndex++;
        var template = $('#education-template').html();
        return $(template.replace(/INDEX/g, educationIndex));
    }

    function appendEducationTemplate(template) {
        $('#educations').append(template);
    }

    function appendEducation(education) {
        var template = getEducationTemplate();
        for (var prop in education) {
            template.find('.education-' + prop).val(education[prop]);
        }
        template.find('.form-select').html(education.graduation_year);
        template.find('.item[data-value="' + education.graduation_year + '"]').addClass('act');
        appendEducationTemplate(template);
    }

    function addEmptyEducation() {
        appendEducationTemplate(getEducationTemplate());
    };

    function applyEducations(educations) {
        for (var i = 0; i < educations.length; i++) {
            appendEducation(educations[i]);
        }

        if (!educations.length) addEmptyEducation();
    }

    $('#add-education').click(function (e) {
        e.preventDefault();
        addEmptyEducation();
    });
    $('#educations').on('click', '.delete-education', function (e) {
        e.preventDefault();
        $(this).parent().remove();
    });

    if ($('#educations').length) applyEducations(window.educations || []);

    /* education*/

    /* search model switch */

    $('.another-entity-count, .current-entity-count').each(function () {
        var $this = $(this);

        $this.hide();
        $.ajax({
            type: 'GET',
            url: $this.data('url'),
            dataType: 'json'
        }).done(function (data) {
            var html = '';
            if ($this.hasClass('another-entity-count')) {
                html = 'и ';
            }
            html += '<span>' + data.count + ' ' + data.word + '</span>';

            if ($this.hasClass('current-entity-count')) {
                $this.siblings('em').text(caseForNumber(data.count, ['Найден', 'Найдено', 'Найдено']));
            }

            $this.html(html).show();
        });
    });

    /* search model switch */

    /* salon tabs */
    $(".salon-tabs a").click(function () {
        if ($(this).hasClass("act")) return false;
        hashTab = $(this).attr("href").replace("#", "");
        scannerUrlHashSalon(hashTab);
        return false;
    });

    if (typeof viewUrlHash != "undefined" && viewUrlHash) {
        hashTab = window.location.hash.replace("#", "");
        scannerUrlHashSalon(hashTab);
    }
    /* salon tabs */

    /* social auth */
    $(".auth-service.vkontakte").eauth({"popup": {"width": 585, "height": 350}, "id": "vkontakte"});
    $(".auth-service.facebook").eauth({"popup": {"width": 585, "height": 290}, "id": "facebook"});
    $(".auth-service.odnoklassniki").eauth({"popup": {"width": 680, "height": 500}, "id": "odnoklassniki"});
    /* social auth */

    if (typeof(articlesDetail) != "undefined") {
        if ($(window).scrollTop() >= 220) {
            $(".articles-detail_social").addClass("articles-detail_social__fixed");
        }

        $(window).scroll(function () {
            if ($(window).scrollTop() >= 220) {
                $(".articles-detail_social").addClass("articles-detail_social__fixed");
            } else {
                $(".articles-detail_social").removeClass("articles-detail_social__fixed");
            }
        })
    }

    // Автосохранение в ЛК
    var form_html = $('.prof-cont form').serialize();
    $('.prof-menu div a').on('click', function () {
        var form_new_html = $('.prof-cont form').serialize();
        if (form_html != form_new_html) {
            var link = $(this).attr('href');
            $('.redirect_link').val(link);
            $(".prof-btn_next .button").closest("form").submit();
            return false;
        }
    });

    $(document).on('change', '#master-is-free input[type=checkbox]', function () {
        var value = $(this).is(':checked');
        $.get(homeUrl + 'index.php?r=lk/changeFreeStatus', {status: value ? 1 : 0});
    });

    if ($('.gallery-isotope').length) {
        $(window).scrollTop(0);
        $(window).load(function () {
            $('.gallery-list_adaptive__loading').removeClass('gallery-list_adaptive__loading');
            galleryAdaptive();
        });
        $(window).bind('scroll', function () {
            btnUpPage();
        }).trigger('scroll');
    }

    $('.btn-top-page').click(function () {
        $('html, body').animate({scrollTop: 0}, 400);
    });

    // Подгружает работы мастера в список мастеров (возможность прокликать все работы из списка, а не только три превьюшки)
    $(document).on('click', '.not-all-works a', function () {
        var masterId = $(this).attr('master-id'),
            salonId = $(this).attr('salon-id'),
            specId = $(this).data('filteredSpec'),
            serviceId = $(this).data('.filteredService'),
            $this = $(this),
            index = $this.index(),
            viewCount = $(this).data('count');

        $.get(homeUrl + 'ajax/filteredWorks', {
            master_id: masterId,
            salon_id: salonId,
            spec_id: specId,
            service_id: serviceId,
            view_count:viewCount
        }, function (data) {
            var container = $this.closest('.not-all-works');
            container.removeClass('not-all-works').empty().append(data);
            initPhotos();
            container.find('a:eq(' + index + ')').trigger('click');
        }, 'html');

        return false;
    });

});

function btnUpPage() {
    if ($(window).scrollTop() > 100) {
        $('.btn-top-page').addClass('btn-top-page-show');
    } else {
        $('.btn-top-page').removeClass('btn-top-page-show');
    }
}

function galleryAdaptive() {
    $('.gallery-isotope .items').isotope({
        itemSelector: '.gallery-list_adaptive__item',
        layoutMode: 'masonry',
        animate: true,
        masonry: {
            columnWidth: 230,
            gutter: 20
        },
        hiddenStyle: {
            opacity: 0,
            transform: 'scale(0)'
        },
        visibleStyle: {
            opacity: 1,
            transform: 'scale(1)'
        }
    });
}

// Клик по работе, срабатывает счетчик кликов
function initWorkCounter() {
    $('.gallery-list_adaptive__pic').off('click.counterClick').on('click.counterClick', function () {
        var id = $(this).data('work-id');
        if (typeof(id) != 'undefined') {
            $.post(homeUrl + 'ajax/workCounter', {id: id});
        }
    });
}

function galleryAdaptiveUpdate(newItems) {
    $this = $(this);
    $(newItems).css('opacity', '0').addClass('gallery-list_adaptive__item-appended');
    $('#infscr-loading').addClass('infscr-loading-show');
    $('.gallery-list_adaptive__wrap .items').css('overflow', 'hidden');
    imagesLoaded($(newItems), function () {
        $('.gallery-isotope .items').isotope("appended", $(newItems));
        $(newItems).removeClass('gallery-list_adaptive__item-appended').css('opacity', '1');
        initCardLikes();
        initPhotos();
        initWorkCounter();
        $('.gallery-list_adaptive__wrap .items').css('overflow', '');
        $('#infscr-loading').removeClass('infscr-loading-show');
    });

    return true;
}

var scannerUrlHashSalon = function (str) {
    if ($("#salon-tabs_" + hashTab).length > 0) {
        $(".salon-tabs a.act").removeClass("act");
        $("#salon-tabs_" + hashTab).addClass("act");
        $(".salon-tabs_cont__item.act").removeClass("act");
        $("#salon-tabs_cont__" + hashTab).addClass("act");
        $("body, html").animate({scrollTop: $(".salon-tabs").position().top}, 400);
        window.location.hash = hashTab;
    }
}

var displayFiles = function (file) {
    if (!file[0].type.match(/image.*/)) {
        return true;
    }

    $('#load-img_name').show().text(file[0].name);

    if (typeof FileReader != "undefined") {
        var loadWrap = $("#load-img_wrap");

        loadWrap.css({height: loadWrap.height()});
        loadWrap.addClass("load-img_loaded");
        loadWrap.find("img").remove();

        var img = $('<img/>').appendTo(loadWrap);

        var reader = new FileReader();

        reader.onload = (function (aImg) {
            return function (e) {
                aImg.attr('src', e.target.result);
                aImg.attr('width', '100%');
            };
        })(img);

        img.load(function () {
            loadWrap.animate({height: img.height()}, 500);
        });

        reader.readAsDataURL(file[0]);
    }
}

var showPopup = function (className, position) {
    var popup = $('#popup');
    popup.removeAttr('class');
    if (className) {
        popup.addClass(className);
        $classNameForPopup = className;
    }
    setPopupPosition(position);
    popup.fadeIn(300);
};


$(function () {
    $(window).on("resize", function () {
        waitForFinalEvent(function () {
            if (isMobile()) {
                setPopupPosition();
            }
        }, 500, 'popup resize');
    });
});

function setPopupPosition(position) {
    var popup = $('#popup');
    if (position != true && popup.is(':visible')) {
        return false;
    }
    var windowSizes = getViewPort();
    if (windowSizes.height > popup.height())
        $top = $(document).scrollTop() + (windowSizes.height - popup.height()) / 2;
    else
        $top = $(document).scrollTop();

    if (popup.outerWidth() > windowSizes.width && popup.outerWidth() / windowSizes.width < 1.5)
        popup.width(windowSizes.width - 32);

    if (isMobile()) {
        popup.css({
            left: window.scrollX + windowSizes.width / 2,
            top: windowSizes.height < popup.outerHeight()
                ? window.scrollY
                : window.scrollY + windowSizes.height / 2
        });
    }

    popup.css({top: $top, marginLeft: "-" + (popup.outerWidth()) / 2 + "px"});

    return true;
}

function getViewPort() {
    var viewPortWidth;
    var viewPortHeight;

    // the more standards compliant browsers (mozilla/netscape/opera/IE7) use window.innerWidth and window.innerHeight
    if (typeof window.innerWidth != 'undefined') {
        viewPortWidth = window.innerWidth,
            viewPortHeight = window.innerHeight
    }

// IE6 in standards compliant mode (i.e. with a valid doctype as the first line in the document)
    else if (typeof document.documentElement != 'undefined'
        && typeof document.documentElement.clientWidth !=
        'undefined' && document.documentElement.clientWidth != 0) {
        viewPortWidth = document.documentElement.clientWidth,
            viewPortHeight = document.documentElement.clientHeight
    }

    // older versions of IE
    else {
        viewPortWidth = document.getElementsByTagName('body')[0].clientWidth,
            viewPortHeight = document.getElementsByTagName('body')[0].clientHeight
    }
    return {
        width: viewPortWidth,
        height: viewPortHeight
    }
}

function isMobile() {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}


/*rules*/
$(".rules-cont").on("click", ".master-rules", function (e) {
    e.preventDefault();
    $(".master-text").show();
    $(".salon-text").hide();
});

$(".rules-cont").on("click", ".salon-rules", function (e) {
    e.preventDefault();
    $(".master-text").hide();
    $(".salon-text").show();
});

/*rules*/

/* lk-appointment */

$(".prof-tab-appointment__btn__time").on("click", ".app_today", function (e) {
    e.preventDefault();
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();
    if (dd < 10) {
        dd = '0' + dd;
    }
    if (mm < 10) {
        mm = '0' + mm;
    }
    today = dd + '.' + mm + '.' + yyyy;
    $("#from_date").val(today);
    $("#to_date").val(today);
    $("#date_button").val("today");
    $("#app-filter-form").submit();

});

$(".prof-tab-appointment__btn__time").on("click", ".app_yesterday", function (e) {
    e.preventDefault();
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();
    if (dd < 10) {
        dd = '0' + dd;
    }
    if (mm < 10) {
        mm = '0' + mm;
    }
    today = dd + '.' + mm + '.' + yyyy;
    var day_ago = new Date((new Date()).valueOf() + 60 * 60 * 24 * 1000);
    dd = day_ago.getDate();
    mm = day_ago.getMonth() + 1; //January is 0!
    yyyy = day_ago.getFullYear();
    if (dd < 10) {
        dd = '0' + dd;
    }
    if (mm < 10) {
        mm = '0' + mm;
    }
    day_ago = dd + '.' + mm + '.' + yyyy;
    $("#from_date").val(today);
    $("#to_date").val(day_ago);
    $("#date_button").val("yesterday");
    $("#app-filter-form").submit();

});

$(".prof-tab-appointment__btn__time").on("click", ".app_all", function (e) {
    e.preventDefault();
    $("#from_date").val('');
    $("#to_date").val('');
    $("#date_button").val("all");
    $("#app-filter-form").submit();

});

$(".prof-tab-appointment__btn__time").on("click", ".app_week", function (e) {
    e.preventDefault();
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();
    if (dd < 10) {
        dd = '0' + dd;
    }
    if (mm < 10) {
        mm = '0' + mm;
    }
    today = dd + '.' + mm + '.' + yyyy;
    var week_ago = new Date((new Date()).valueOf() + 60 * 60 * 24 * 7000);
    dd = week_ago.getDate();
    mm = week_ago.getMonth() + 1; //January is 0!
    yyyy = week_ago.getFullYear();
    if (dd < 10) {
        dd = '0' + dd;
    }
    if (mm < 10) {
        mm = '0' + mm;
    }
    week_ago = dd + '.' + mm + '.' + yyyy;
    $("#to_date").val(week_ago);
    $("#from_date").val(today);
    $("#date_button").val("week");
    $("#app-filter-form").submit();

});

$(".prof-tab-appointment__btn__time").on("click", ".app_month", function (e) {
    e.preventDefault();
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();
    if (dd < 10) {
        dd = '0' + dd;
    }
    if (mm < 10) {
        mm = '0' + mm;
    }
    today = dd + '.' + mm + '.' + yyyy;
    var month_ago = new Date((new Date()).valueOf() + 60 * 60 * 24 * 30000);
    dd = month_ago.getDate();
    mm = month_ago.getMonth() + 1; //January is 0!
    yyyy = month_ago.getFullYear();
    if (dd < 10) {
        dd = '0' + dd;
    }
    if (mm < 10) {
        mm = '0' + mm;
    }
    month_ago = dd + '.' + mm + '.' + yyyy;
    $("#to_date").val(month_ago);
    $("#from_date").val(today);
    $("#date_button").val("month");
    $("#app-filter-form").submit();

});

$(".prof-tab-appointment__btn__time").on("click", ".app_3month", function (e) {
    e.preventDefault();
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();
    if (dd < 10) {
        dd = '0' + dd;
    }
    if (mm < 10) {
        mm = '0' + mm;
    }
    today = dd + '.' + mm + '.' + yyyy;
    var month_ago = new Date((new Date()).valueOf() + 60 * 60 * 24 * 30000 * 3);
    dd = month_ago.getDate();
    mm = month_ago.getMonth() + 1; //January is 0!
    yyyy = month_ago.getFullYear();
    if (dd < 10) {
        dd = '0' + dd;
    }
    if (mm < 10) {
        mm = '0' + mm;
    }
    month_ago = dd + '.' + mm + '.' + yyyy;
    $("#to_date").val(month_ago);
    $("#from_date").val(today);
    $("#date_button").val("3month");
    $("#app-filter-form").submit();

});
/* lk-appointment */

function initCardLikes() {
    $('.work-social-likes').each(function () {
        if (typeof($(this).data('socialLikes')) != 'undefined')
            return true;
        $(this).socialLikes({
            title: $("<div/>").html($(this).data('socialTitle')).text(),
            forceUpdate: true
        });
    });
}

/**
 * Выбрать подходящую для числа $number словоформу из $cases.
 * cases имеет вид:
 *   [
 *     'штука',
 *     'штуки',
 *     'штук'
 *   ];
 *
 * @param number
 * @param cases
 * @return string
 */
function caseForNumber(number, cases) {
    number = Math.abs(number % 100);
    if (number > 10 && number < 20)
        return cases[2];

    number = number % 10;
    return cases[(number !== 1 ? 1 : 0) + (number >= 5 || !number ? 1 : 0)];
}

if (typeof(initPhotosCallback) != 'function') {
    var initPhotosCallback = function () {
    };
}
function initPhotos() {
    $("a[rel^='prettyPhoto']").prettyPhoto({
        theme: 'dark_rounded',
        social_tools: false,
        allow_resize: false,
        deeplinking: false,
        overlay_gallery: false,
        changepicturecallback: function (a, b) {
            if (isMobile()) {
                var holder = $('.pp_pic_holder');
                var viewPort = getViewPort();
                holder.css({
                    left: (window.scrollX + viewPort.width / 2) - (holder.outerWidth() / 2),
                    top: viewPort.height < holder.outerHeight()
                        ? window.scrollY
                        : (window.scrollY + viewPort.height / 2) - (holder.outerHeight() / 2)
                });
            }
        }
    });

    initPhotosCallback();
}

function createWorkUploader() {
    var uploader = new qq.FineUploaderBasic({
        multiple: true,
        button: $('#load-img_wrap').get(0),
        request: {
            endpoint: homeUrl + 'lk/uploadWork'
        },
        classes: {
            success: 'alert alert-success',
            fail: 'alert alert-error'
        },
        validation: {
            allowedExtensions: ['jpeg', 'jpg', 'gif', 'png'],
            sizeLimit: 1024 * 1024 * 10
        },
        callbacks: {
            onSubmit: function (id) {

            },
            onUpload: function (id) {

            },
            onProgress: function (id, fileName, loaded, total) {

            },
            onComplete: function (id, fileName, data) {
                if (data.success) {
                    $('.works-uploaded').append($('#workTmpl').tmpl({
                        id: id,
                        imagePath: data.path,
                        imageName: data.name
                    }));
                    $(".prof-btn_next").show();
                    $('body').trigger('specs');
                } else {
                    alert(data.error);
                }
            }
        }
    });
}

/**
 * Добавляет/удаляет в топ10 работы из списка работ в лк
 * @param button
 * @returns {boolean}
 */
function toggleTop10(button) {
    $.get(homeUrl + 'work/markAsMain', {id: $(button).data('id')}, function (success) {
        if (success == 0) {
            /** этот блок применяем когда количество топов достигло 10 **/
            $parentButton = $(button).closest('.item');
            if (!$(button).hasClass('b-btn_top__work-add')) {
                if (!$parentButton.find('.popup-note').length) {
                    $parentButton.append('<div class="popup-note popup-info-top"><div class="popup-note_cont">Вы уже отметили 10 работ</div><div class="popup-arr"></div></div>');
                }
                $parentButton.find('.popup-info-top').stop(true, true).fadeIn(400);
            }
            /** end **/
        } else {
            $(button).toggleClass('b-btn_top__work-add');
        }
    });

    return false;
}

/**
 * Управляет галками ТОП10 в добавлении работ
 * @param input
 * @returns {boolean}
 */
function createTop10(input) {
    var count = top10_count + $('.top10-wrapper input:checked').length;
    if (typeof(input) != 'undefined') {
        if (count > 10) {
            if ($(input).is(':checked')) {
                $('.top10-wrapper .popup-info-top').remove();
                $(input).closest('.b-top_check__wrap')
                    .append('<div class="popup-note popup-info-top"><div class="popup-note_cont">Вы уже отметили 10 работ</div><div class="popup-arr"></div></div>')
                    .find('.popup-info-top')
                    .stop(true, true)
                    .fadeIn(400);
                $(input).closest('.form-inp_check').trigger('click');
            }
        }
    }

    return true;
}

$(function () {
    $('.prof-photo_imgs').on('mouseleave', '.popup-info-top', function () {
        $(this).fadeOut(400);
    });
});

$(function () {
    $('.lk-edit-works').on('mouseleave', '.b-top_check__wrap', function () {
        $(this).find('.popup-info-top').fadeOut(400);
    });
});