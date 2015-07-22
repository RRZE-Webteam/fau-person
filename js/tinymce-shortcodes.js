(function() {

    tinymce.PluginManager.add('personrteshortcodes', function( editor )
    {
        
        editor.addMenuItem('shortcode_person', {
            text: 'Person einfügen',
            context: 'tools',
            onclick: function() {
                editor.insertContent('[person id="" format="" show="" hide=""]');
            }
        });
        
        editor.addMenuItem('shortcode_persons', {
            text: 'Personengalerie einfügen',
            context: 'tools',
            onclick: function() {
                editor.insertContent('[persons category="" showlink="0" extended="0"]');
            }
        });
            });
})();