function AnotherSoundcloudQuicktagUI(){
	jQuery(function(){
		jQuery('#soundcloudDialog').dialog({
			autoOpen: false, modal: true, resizable: true, maxHeight:500, minHeight:240, minWidth: 500, maxWidth: 700, title: 'Another SoundCloud Quicktag', closeOnEscape: true,
			buttons: {
				"Insert": function() {
					var soundlink = jQuery("#soundcloudDialog-link").val();
					var comment = jQuery("#soundcloudDialog-comments").val();
					var autoplay = jQuery("#soundcloudDialog-autoplay").val();
					var quicktag = '[soundcloud params="auto_play='+autoplay+'&show_comments='+comment+'"]'+soundlink+'[/soundcloud]';
					ed = tinyMCE.activeEditor;
					ed.focus();
					ed.execCommand('mceInsertContent', false, quicktag);			
					jQuery(this).dialog("close");
				},
				"Close": function() {
					jQuery(this).dialog("close");
				}
			}
		});
	});

	jQuery('.ui-dialog').addClass('asq-styler');
	jQuery('#soundcloudDialog').dialog('open');
}

(function() {
    tinymce.create('tinymce.plugins.AnotherSoundcloudQuicktag', {
		init : function(ed, url){
			ed.addButton('AnotherSoundcloudQuicktag', {
            	title : 'Insert SoundCloud Quicktag',
	            image: url + "/soundcloud.png",
	            onclick : function() {	    
	            	AnotherSoundcloudQuicktagUI();
                }
            });
        },
        
		getInfo : function() {
			return {
				longname : 'Another Soundcloud Quicktag',
				author : 'Enrico Nemack',
				authorurl : 'http://nemack.com',
				infourl : 'http://wordpress.nemack.com',
				version : "1.2"
            };
        }
    });
 
	// Register plugin
	tinymce.PluginManager.add('AnotherSoundcloudQuicktag', tinymce.plugins.AnotherSoundcloudQuicktag);
})();