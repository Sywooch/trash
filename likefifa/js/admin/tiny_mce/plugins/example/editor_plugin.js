(function(){tinymce.PluginManager.requireLangPack('line-height');tinymce.create('tinymce.plugins.Line-heightPlugin',{init:function(ed,url){ed.addCommand('mceLine-height',function(){ed.windowManager.open({file:url+'/dialog.htm',width:320+parseInt(ed.getLang('line-height.delta_width',0)),height:120+parseInt(ed.getLang('line-height.delta_height',0)),inline:1},{plugin_url:url,some_custom_arg:'custom arg'})});ed.addButton('line-height',{title:'line-height.desc',cmd:'mceLine-height',image:url+'/img/example.gif'});ed.onNodeChange.add(function(ed,cm,n){cm.setActive('line-height',n.nodeName=='IMG')})},createControl:function(n,cm){return null},getInfo:function(){return{longname:'Line-height plugin',author:'Some author',authorurl:'http://tinymce.moxiecode.com',infourl:'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/example',version:"1.0"}}});tinymce.PluginManager.add('line-height',tinymce.plugins.ExamplePlugin)})();