<?php

/* Manage Selected Rows Widget */

class manageSelected extends CWidget
{

    // Controller ID
    public $controller;
    // CGridView ID
    public $gridId;
    // Column ID
    public $columnId;
    // Buttons
    public $buttons = array('delete');
    // Request URL
    public $urlRequest;

    public function run()
    {
        parent::run();
        $baseDir = dirname(__FILE__);

        $btns = array();

        if (in_array('delete', $this->buttons))
            $btns['manage_btn'] = true;
        $this->render('widget', array('controller' => $this->controller, 'buttons' => $btns));
    }

    public function init()
    {
        parent::init();

        $this->registerClientScript();
    }

    protected function registerClientScript()
    {
        if (Yii::app()->request->enableCsrfValidation) {
            $csrfTokenName = Yii::app()->request->csrfTokenName;
            $csrfToken = Yii::app()->request->csrfToken;
            $csrf = "\n\t\tdata:{ '$csrfTokenName':'$csrfToken' },";
        }
        else
            $csrf = '';

        $js = <<<JAVASCRIPT
function manageSelected(item, status) {
    var selectedItems = $.fn.yiiGridView.getChecked('{$this->gridId}','{$this->columnId}');
    var requestUrl  = '{$this->urlRequest}' + '?status=' + status + '&';
    for (i=0;i<selectedItems.length;i++)
        requestUrl += 'selectedItems[]=' + selectedItems[i] + "&";
    $.fn.yiiGridView.update('{$this->gridId}', {
        type:'POST',
        url:requestUrl,$csrf
        success:function() {
            $.fn.yiiGridView.update('{$this->gridId}');
        },
    });
}

$("input[id='blockSelected']").live('click', function(e) {
    var items = $.fn.yiiGridView.getChecked('{$this->gridId}','{$this->columnId}');    
    e.preventDefault();
    if(items.length != 0) {
        if(confirm('Вы точно хотите заблокировать выбранные анкеты?')) manageSelected($(this), 4);
    } else {
        alert('Выберите центры для блокирования анкет');
    }
});

$("input[id='unblockSelected']").live('click', function(e) {
    var items = $.fn.yiiGridView.getChecked('{$this->gridId}','{$this->columnId}');    
    e.preventDefault();
    if(items.length != 0) {
        if(confirm('Вы точно хотите разблокировать выбранные анкеты?')) manageSelected($(this), 3);
    } else {
        alert('Выберите центры для разблокирования анкет');
    }
});

$("input[id='archiveSelected']").live('click', function(e) {
    var items = $.fn.yiiGridView.getChecked('{$this->gridId}','{$this->columnId}');    
    e.preventDefault();
    if(items.length != 0) {
        if(confirm('Вы точно хотите перенести выбранные анкеты в архив?')) manageSelected($(this), 5);
    } else {
        alert('Выберите центры для переноса анкет в архив');
    }
});
JAVASCRIPT;

        $cs = Yii::app()->getClientScript();

        $cs->registerScript(__CLASS__ . '#' . $this->id, $js);
    }

}

?>