/* Файл с общими для бэка и фронта скриптами */

$(function () {
    $(document).on('click', '.rotate', function () {
        var button = $(this);
        $.ajax(homeUrl + 'work/rotate', {
            data: {
                id: button.data('id'),
                direction: button.data('direction'),
                name: typeof(button.data('name')) != 'undefined' ? button.data('name') : null
            },
            beforeSend: function () {
                button.parent().find('.loader').show();
                button.parent().find('.loader-overlay').show();
            },
            success: function (data) {
                button.parent().find('.loader').hide();
                button.parent().find('.loader-overlay').hide();
                button.parent().find('img').not('.remmove-img').attr("src", data);
            }
        });
    });

    $("body").on("click", ".save-crop-image", function () {
        var form = $(this).closest("form");

        // костыль для админки
        if (form.length == 0) {
            form = $(this).closest('.modal-footer').siblings('.modal-body').find('form');
        }

        var data = form.serialize();
        var workId = $("#workId").val();
        var workName = $('#workName').val();

        var x1 = $('#x1').val(),
            x2 = $('#x2').val(),
            y1 = $('#y1').val(),
            y2 = $('#y2').val(),
            jcropWidth = $('#jcropWidth').val(),
            jcropHeight = $('#jcropHeight').val();
        $.ajax({
            url: "/work/cropImage/",
            type: "POST",
            data: data,
            success: function (data) {
                if (typeof($.fancybox) != 'undefined') {
                    $.fancybox.close();
                } else if (typeof($().modal) != 'undefined') {
                    $('.modal').modal('hide');
                }

                if (workId != '') {
                    $(".lk-work-" + workId).attr("src", data);
                } else {
                    $('img[data-name="' + workName + '"]').attr("src", data);
                    var parent = $('img[data-name="' + workName + '"]').closest('.work-item');

                    parent.find('input[data-target=x1]').val(x1);
                    parent.find('input[data-target=x2]').val(x2);
                    parent.find('input[data-target=y1]').val(y1);
                    parent.find('input[data-target=y2]').val(y2);
                    parent.find('input[data-target=jcropWidth]').val(jcropWidth);
                    parent.find('input[data-target=jcropHeight]').val(jcropHeight);
                    parent.find('input[data-target=crop]').val(0);
                }
            }
        });

        return false;
    });
});

/**
 * Обновляет превьюшку при обрезке
 *
 * @param obj c модель изображения jcrop
 *
 * @return void
 */
function updatePreviewAndCoords(c) {
    if (parseInt(c.w) > 0) {
        var rx = xsize / c.w;
        var ry = ysize / c.h;

        $pimg.css({
            width: Math.round(rx * boundx) + 'px',
            height: Math.round(ry * boundy) + 'px',
            marginLeft: '-' + Math.round(rx * c.x) + 'px',
            marginTop: '-' + Math.round(ry * c.y) + 'px'
        });

        $('#x1').val(c.x);
        $('#y1').val(c.y);
        $('#x2').val(c.x2);
        $('#y2').val(c.y2);

        $("#jcropWidth").val($(".jcrop-holder").width());
        $("#jcropHeight").val($(".jcrop-holder").height());

        $("#crop-window").width($(".jcrop-holder").width() + 220);
        $(".fancybox-wrap").width($(".jcrop-holder").width() + 250);
    }
}

var jcrop_api,
    boundx,
    boundy,
    $preview,
    $pcnt,
    $pimg,
    xsize,
    ysize;
/**
 * Инициализирует кропинг
 *
 * @param bigWidth
 * @param bigHeight
 * @param smallWidth
 * @param smallHeight
 */
function initJcrop(bigWidth, bigHeight, smallWidth, smallHeight) {
    $preview = $('#preview-pane');
    $pcnt = $('#preview-pane .preview-container');
    $pimg = $('#preview-pane .preview-container img');
    xsize = $pcnt.width();
    ysize = $pcnt.height();
    $('#target').Jcrop({
        onChange: updatePreviewAndCoords,
        onSelect: updatePreviewAndCoords,
        setSelect: [0, 0, bigWidth, bigHeight],
        minSize: [smallWidth, smallHeight],
        aspectRatio: xsize / ysize
    }, function () {
        var bounds = this.getBounds();
        boundx = bounds[0];
        boundy = bounds[1];
        jcrop_api = this;
        $preview.appendTo(jcrop_api.ui.holder);
    });
}


/**
 * Объект для отображения подсказок в формах поиска
 * @returns {SearchSuggest}
 * @constructor
 */
function SearchSuggest() {
    this.pressKeyEnter = false;
    this.addValInpSuggest = false;
    this.formId = null;
    this.callback = null;
    this.metroStations = null;

    return this;
}

SearchSuggest.prototype.initSpec = function (id) {
    var $this = this;
    $('#' + id)
        .jsonSuggest({
            url: homeUrl + 'index.php?r=ajax/specSuggest',
            width: "100%",
            maxHeight: 300,
            onSelect: function (item) {
                $('#specialization').val(item.id[0]);
                $('#service').val(item.id[1]);
                $this.addValInpSuggest = true;
                if ($this.pressKeyEnter) {
                    $("#" + $this.formId).submit();
                }
            }
        }).on('keyup', function () {
            if ($.trim(this.value) == '') {
                $('#specialization').val('');
                $('#service').val('');
            }
        });

    this.initEvents(id);
};

SearchSuggest.prototype.getMetroStations = function (callback) {
    var $this = this;
    if (this.metroStations == null) {
        $.get(homeUrl + 'index.php?r=ajax/metroSuggest', function (data) {
            $this.metroStations = data;
            if (typeof(callback) != 'undefined') {
                callback(data);
            }
        }, 'json');
    } else {
        if (typeof(callback) != 'undefined') {
            callback(this.metroStations);
        }
    }
};

SearchSuggest.prototype.initMetro = function (id, targetId, submit) {
    if (typeof(targetId) == 'undefined' || targetId == null) {
        targetId = 'stations';
    }
    if (typeof(submit) == 'undefined') {
        submit = true;
    }
    var $this = this;
    this.getMetroStations(function (data) {
        $('#' + id).jsonSuggest({
            data: data,
            width: "100%",
            maxHeight: 300,
            onSelect: function (item) {
                $('#' + id).val(item.name);
                $('#' + targetId).val(item.id);
                $this.addValInpSuggest = true;
                if (submit && $this.pressKeyEnter) {
                    $("#" + $this.formId).submit();
                }
            }
        });
    });

    $('#' + id).on('keyup', function () {
        if ($.trim(this.value) == '') {
            $('#stations').val('');
        }
    });

    this.initEvents(id);
};

SearchSuggest.prototype.initEvents = function (id) {
    var $this = this;
    $('#' + id).keyup(function (e) {
        if (e.keyCode === 13) {
            $this.pressKeyEnter = true;
        }
    }).keydown(function (e) {
        if (e.keyCode === 13) {
            $("#" + $this.formId).submit(function (e) {
                if (!resSuggestEmpty && !$this.addValInpSuggest)
                    e.preventDefault();
                else if ($this.callback) {
                    $this.callback();
                    e.preventDefault();
                    e.stopPropagation();
                }
            });
        }
    });
};

