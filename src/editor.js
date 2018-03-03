import AddComponent from './Component/Add.js';

let addTag = document.createElement(`add-blogcard`);
document.body.appendChild(addTag);

(() => {
  /* Register the buttons */
  tinymce.create('tinymce.plugins.BlogCardButtons', {
    init : (ed, url) => {
      const addComponent = new AddComponent(ed);
      /**
      * Inserts shortcode content
      */
      ed.addButton('blogcard_shortcode', {
        title : 'BlogCard shortcode',
        image : url + '/images/icon.svg',
        cmd: 'blogcard_shortcode_cmd'
      });
      ed.addCommand('blogcard_shortcode_cmd', () => {
        addComponent.show(ed.selection.getContent());
      });
    },
    createControl : (n, cm) => {
      return null;
    },
  });
  /* Start the buttons */
  tinymce.PluginManager.add('blogcard_plugin_script', tinymce.plugins.BlogCardButtons);
})();
