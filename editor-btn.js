(function() {
  /* Register the buttons */
  tinymce.create('tinymce.plugins.BlogCardButtons', {
      init : function(ed, url) {
      /**
      * Inserts shortcode content
      */
      ed.addButton('blogcard_shortcode', {
        title : 'BlogCard shortcode',
        icon: 'code',
        //  image : url + '/images/btn.png',
        cmd: 'blogcard_shortcode_cmd'
      });
      ed.addCommand('blogcard_shortcode_cmd', function() {
        var selected_text = ed.selection.getContent();
        var return_text = '[blog-card href="'  + selected_text + '"]';
        ed.execCommand('mceInsertContent', 0, return_text);
      });
    },
    createControl : function(n, cm) {
      return null;
    },
  });
  /* Start the buttons */
  tinymce.PluginManager.add('blogcard_plugin_script', tinymce.plugins.BlogCardButtons);
})();
