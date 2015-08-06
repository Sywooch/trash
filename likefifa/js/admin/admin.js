$(function () {
    var hash = window.location.hash;
    hash && $('ul.nav a[href="' + hash + '"]').tab('show');

    $('.nav-tabs a').click(function (e) {
        $(this).tab('show');
        var scrollmem = $('body').scrollTop();
        window.location.hash = this.hash;
        $('html,body').scrollTop(scrollmem);
    });

    Notification.init();
});

var showPopup = function (className) {
    if (className) {
        $("#popup").addClass(className);
        $classNameForPopup = className;
    }

    if ($(window).height() > $("#popup").height())
        $top = $(document).scrollTop() + ($(window).height() - $("#popup").height()) / 2;
    else
        $top = $(document).scrollTop();
    $("#popup").fadeIn(300).css({top: $top, marginLeft: "-" + ($("#popup").width()) / 2 + "px"});
}

var closePopup = function () {
    $("#popup").fadeOut(300, function () {
        $("#overlay").hide();
        $("#popup").removeClass($classNameForPopup);
        $("#popup").html("");
    });
}

$('body').on('click', function (e) {
    $('[data-toggle="popover"]').each(function () {
        if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
            $(this).popover('hide');
        }
    });
});

/**
 * Обновляет цену услуги
 *
 * @param {int} masterId  идентификатор мастера
 * @param {int} serviceId идентификатор услуги
 *
 * @return {void}
 */
function updatePriceForAppointment(masterId, serviceId, type) {
    var url = "/admin/appointment/servicePrice/";
    $.get(url, {
        id: masterId,
        serviceId: serviceId,
        type: type
    }, function (data) {
        $(".appointment-service-price").text(data);
        $("#LfAppointment_service_price").val(data);
    });
}

function appointmentMasterChange(masterId, type) {
    if (type == 'master') {
        $('#LfAppointment_salon_id').select2('val', '')
    } else {
        $('#LfAppointment_master_id').select2('val', '')
    }

    var url = "/admin/appointment/masterData/";
    $.getJSON(url, {id: masterId, type: type}, function (data) {
        if (!data) {
            return false;
        }

        $(".appointment-master-phone").text(data["phone"]);
        $(".appointment-master-services").html(data["services"]);

        updatePriceForAppointment(masterId, $("#LfAppointment_service_id").val(), type);
        $("#LfAppointment_service_id").on("change", function () {
            updatePriceForAppointment(masterId, $(this).val(), type);
        });
    });
}

$(function () {
    // Создание новой заявки в БО
    $("#LfAppointment_phone").mask("+7 (999) 999 99 99", {placeholder: " "});
    if ($(".masters-drop-down").length) {
        $(".masters-drop-down").select2();
    }

    $(".masters-drop-down").on("change", function () {
        appointmentMasterChange($(this).val(), $(this).data('type'));
    });

    if (typeof(initMasterService) != 'undefined' && initMasterService == true) {
        $('#LfAppointment_master_id').trigger('change');
    }

    // Рассылка
    $("#mailing .select-all").on("click", function () {
        $("#mailing .masters-list input").each(function () {
            $(this).attr('checked', true);
        });
        $("#mailing .groups input").each(function () {
            $(this).attr('checked', true);
        });
        return false;
    });
    $("#mailing .take-off-all").on("click", function () {
        $("#mailing .masters-list input").each(function () {
            $(this).attr('checked', false);
        });
        $("#mailing .groups input").each(function () {
            $(this).attr('checked', false);
        });
        return false;
    });
    $("#mailing .groups input").on("change", function () {
        var groupClass = $(this).attr("id");
        if ($(this).is(':checked')) {
            $("#mailing .masters-list ." + groupClass).each(function () {
                $(this).attr('checked', true);
            });
        } else {
            $("#mailing .masters-list ." + groupClass).each(function () {
                $(this).attr('checked', false);
            });
        }
    });
    $("#mailing .search").on("keyup", function () {
        var search = $(this).val();
        if (search != "") {
            regV = new RegExp(search, 'gi');

            $("#mailing .masters-list label").each(function () {
                var masterName = $(this).text();
                $(this).parent().removeClass("hide");
                if (!masterName.match(regV)) {
                    $(this).parent().addClass("hide");
                }
            });
        } else {
            $("#mailing .masters-list .master-item").removeClass("hide");
        }
    });
    $("#mailing .send-button").on("click", function () {
        var idString = "";
        $("#mailing .masters-list .master-item").each(function () {
            if (!$(this).hasClass("hide")) {
                if ($(this).find("input").prop("checked")) {
                    idString += $(this).attr("master-id") + ", ";
                }
            }
        });
        $("#mailing .id-string").val(idString);
    });

    $("*").on("click", ".education-container .close", function () {
        $(this).parent().remove();
    });

    $("*").on("click", ".expirience-container .close", function () {
        $(this).parent().remove();
    });

    $("#masterForm").on("submit", function () {
        $(".education-container").each(function (index) {
            $(this).find("input").each(function () {
                var name = $(this).attr("name").replace(/INDEX/g, index + 1);
                $(this).attr("name", name);
            });
        });
        $(".education-container-example").remove();
        $(".expirience-container-example").remove();
    });

    $(".education-add").on("click", function () {
        var eduForm = $(".education-container-example").html();
        $(".educations-master").append(eduForm);
        return false;
    });

    $("input[name='LfMaster[phone_cell]'], input[name='LfSalon[phone]']").mask("+7 (999) 999 99 99", {placeholder: " "});

    if ($(".master-accordion").length > 0) {
        $(".master-accordion").accordion({
            animated: 'bounceslide',
            heightStyle: "content"
        });
    }

    $.widget("custom.combobox", {
        _create: function () {
            this.wrapper = $("<span>")
                .addClass("custom-combobox")
                .insertAfter(this.element);

            this.element.hide();
            this._createAutocomplete();
            this._createShowAllButton();
        },

        _createAutocomplete: function () {
            var selected = this.element.children(":selected"),
                value = selected.val() ? selected.text() : "";

            this.input = $("<input>")
                .appendTo(this.wrapper)
                .val(value)
                .attr("title", "")
                .addClass("custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left")
                .autocomplete({
                    delay: 0,
                    minLength: 0,
                    source: $.proxy(this, "_source")
                })
                .tooltip({
                    tooltipClass: "ui-state-highlight"
                });

            this._on(this.input, {
                autocompleteselect: function (event, ui) {
                    ui.item.option.selected = true;
                    this._trigger("select", event, {
                        item: ui.item.option
                    });
                },

                autocompletechange: "_removeIfInvalid"
            });
        },

        _createShowAllButton: function () {
            var input = this.input,
                wasOpen = false;

            $("<a>")
                .attr("tabIndex", -1)
                .attr("title", "Show All Items")
                .tooltip()
                .appendTo(this.wrapper)
                .button({
                    icons: {
                        primary: "ui-icon-triangle-1-s"
                    },
                    text: false
                })
                .removeClass("ui-corner-all")
                .addClass("custom-combobox-toggle ui-corner-right")
                .mousedown(function () {
                    wasOpen = input.autocomplete("widget").is(":visible");
                })
                .click(function () {
                    input.focus();

                    // Close if already visible
                    if (wasOpen) {
                        return;
                    }

                    // Pass empty string as value to search for, displaying all results
                    input.autocomplete("search", "");
                });
        },

        _source: function (request, response) {
            var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");
            response(this.element.children("option").map(function () {
                var text = $(this).text();
                if (this.value && ( !request.term || matcher.test(text) ))
                    return {
                        label: text,
                        value: text,
                        option: this
                    };
            }));
        },

        _removeIfInvalid: function (event, ui) {

            // Selected an item, nothing to do
            if (ui.item) {
                return;
            }

            // Search for a match (case-insensitive)
            var value = this.input.val(),
                valueLowerCase = value.toLowerCase(),
                valid = false;
            this.element.children("option").each(function () {
                if ($(this).text().toLowerCase() === valueLowerCase) {
                    this.selected = valid = true;
                    return false;
                }
            });

            // Found a match, nothing to do
            if (valid) {
                return;
            }

            // Remove invalid value
            this.input
                .val("")
                .attr("title", value + " didn't match any item")
                .tooltip("open");
            this.element.val("");
            this._delay(function () {
                this.input.tooltip("close").attr("title", "");
            }, 2500);
            this.input.data("ui-autocomplete").term = "";
        },

        _destroy: function () {
            this.wrapper.remove();
            this.element.show();
        }
    });

    /** popups **/
    $(".on_change").on("click", function () {
        var url = homeUrl + 'admin/appointment/changeOwner/' + $(this).attr('appointment') + "/";
        $.get(url, function (data) {
            $('.modal').empty().html(data).modal('show');
            $('.modal').find('select').select2();
        }, 'html');
        return false;
    });

    $("#overlay").click(function () {
        closePopup();
    });

    $("#popup").on("click", ".popup-close", function () {
        closePopup();
    });
    /** popups **/

        // Изменяет баланс мастеру в БО
    $("body").on("submit", ".recharge-popup form", function () {
        var data = $(this).serialize();
        var masterId = $("#masterIdRecharge").val();

        $.ajax({
            url: "/admin/master/recharge/",
            type: "POST",
            data: data,
            success: function (data) {
                $(".master-" + masterId + "-balance").html(data);
                $('.modal').modal('hide');
            }
        });

        return false;
    });

    $('.crop-image').on('click', function () {
        $.get(this.href, function (data) {
            var modal = $('.modal');
            modal.empty().modal('show');

            var container = $('#crop-modal-data').children().clone();
            modal.append(container);
            container.find('.modal-body').html(data).find('.save-crop-image').remove();
        }, 'html');
        return false;
    });

    $('.prof-photo_imgs a.del').on('click', function () {
        var $this = $(this);
        $.get(this.href, function () {
            $this.closest('.work-item').remove();
        });
        return false;
    });
});

function addToFavorites(link) {
    $.get(link.href, function (result) {
        console.log($(link).closest('tr'));
        $(link).closest('tr').toggleClass('fav-appointment', result == 1);
    });
    return false;
}

function masterSearchMetroResultInit(element, callback) {
    var id = $(element).val();
    var url = $(element).data('select2').opts.ajax.url
    if (id !== "") {
        $.ajax(url, {
            data: {
                id: id
            },
            dataType: "json"
        }).done(function (data) {
            callback(masterSearchMetroResult(data).results);
        });
    }
}

function masterSearchMetroResult(data, page) {
    var results = [];
    for (var n in data) {
        results.push({
            id: data[n].id,
            text: data[n].text
        });
    }
    return {results: results};
}

var IndexGrid = {
    init: function (link) {
        $.get(link, function (data) {
            $('.modal')
                .empty()
                .html(data)
                .modal('show')
                .find('.index-grid .tr div')
                .on('click', IndexGrid.select)
                .hover(IndexGrid.hoverIn, IndexGrid.hoverOut);
        }, 'html');
        return false;
    },

    select: function (container) {
        var id = $(this).find('img.preview').data('id');
        var index = $(this).data('index');

        $(this).closest('.tr-container').find('img[data-id=' + id + ']').not('.preview').remove();

        $(this).find('img').not('.preview').remove();
        $(this).find('.preview').removeClass('preview').show();


        $('.modal')
            .find('.index-grid .tr div')
            .unbind('mouseenter mouseleave click');

        $.get(homeUrl + 'admin/work/saveGridIndex', {id: id, index: index});
        return false;
    },

    hoverIn: function () {
        var preview = $('.index-grid .image-preview').html();
        $(this).find('img').hide();
        $(this).append(preview);
    },

    hoverOut: function () {
        $(this).find('.preview').remove();
        $(this).find('img').show();
    }
};

/**
 * Управляет отображением нотификейшнов
 * Работает через сокеты
 *
 * @type {{init: Function, message: Function}}
 */
var Notification = {
    /**
     * Инициализирует подключение к сокету
     */
    init: function () {
        if (navigator.userAgent.toLowerCase().indexOf('chrome') != -1) {
            this.socket = io.connect(document.location.protocol + '//' + document.location.host + ':' + notificationPort, {
                'transports': ['xhr-polling'],
                secure: true
            });
        } else {
            this.socket = io.connect(document.location.protocol + '//' + document.location.host + ':' + notificationPort, {secure: true});
        }

        var $this = this;

        this.socket.on('connect', function () {
            $this.socket.on('message', function (msg) {
                $this.message(msg);
            });
        });
    },

    /**
     * Обрабатывает принятое сообщение
     *
     * @param data
     * @returns {boolean}
     */
    message: function (data) {
        switch (data.event) {
            case 'appointment_call' :
                this.appointmentCall(data.data);
                break;
            default:
                return false;
        }
        return true;
    },

    /**
     * Напоминание о звонке клиенту
     *
     * @param data
     */
    appointmentCall: function (data) {
        var text = 'Заявка №' + data['id'];
        /*if(data['service']) {
            text += ', ' + data['service'];
        }*/
        text += '<br/>Перезвонить ' + data['date'] + ' в ' + data['time'];
        if(data['operator']) {
            text += '<br/>Оператор ' + data['operator'];
        }


        $.gritter.add({
            title: 'Напоминание о звонке!',
            text: text,
            time: 50000
        });
    }
};