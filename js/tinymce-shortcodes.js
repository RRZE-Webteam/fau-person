(() => {
  // src/js/tinymce-shortcodes.js
  (function() {
    tinymce.PluginManager.add("personrteshortcodes", function(editor) {
      editor.addMenuItem("shortcode_person", {
        text: "Person einf\xFCgen",
        context: "tools",
        onclick: function() {
          editor.insertContent('[kontakt id="" format="" show="" hide=""]');
        }
      });
      editor.addMenuItem("shortcode_persons", {
        text: "Personengalerie einf\xFCgen",
        context: "tools",
        onclick: function() {
          editor.insertContent('[kontakt category="" format="" show="" hide=""]');
        }
      });
    });
  })();
})();
//# sourceMappingURL=tinymce-shortcodes.js.map
