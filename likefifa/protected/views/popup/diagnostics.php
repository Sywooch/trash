<script>
    $(function () {
        $("#tree").jstree({ 
            "json_data" : {
                "ajax" : {
                    "url" : "/admin/diagnosticCenter/ajaxFillTree/<?php echo $centerId; ?>"
                }
            },
            "plugins" : [ "themes", "json_data", "ui", "checkbox"],
            "themes" : {
                "theme" : "apple",
                "dots" : false,
                "icons" : false
            }
        }).bind("loaded.jstree", function (event, data) {
            $('.jstree-unchecked, .jstree-undetermined').each(function(){
              if($(this).find('a').hasClass('checked'))
                    $(this).removeClass('jstree-unchecked').addClass('jstree-checked');
            });
            $('.jstree-closed').each(function(){
                if($(this).find('a').hasClass('checked'))
                    $(this).removeClass('jstree-checked').addClass('jstree-undetermined');
            });
            $('.jstree-leaf').each(function(){
                if($(this).find('a').hasClass('checked'))
                    $(this).removeClass('jstree-checked').addClass('jstree-checked');
            });
        });
    });
</script>

<div class="treeWrap">
    <h2>Выбор диагностики:</h2>
    <div id="tree"></div>
    <div class="right">
        <div id="diagnostics-submit" class="button but-metro">
            <span>Применить</span>
        </div>
    </div>
</div>