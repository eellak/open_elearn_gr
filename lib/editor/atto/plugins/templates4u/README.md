# moodle-atto_templates4u

This is an atto plugin for Moodle which allows the administrator to define a set of predefined templates which a teacher can insert into their course pages.

It behaves in a very similar way as the TinyMCE templates function (<https://www.tinymce.com/docs-3x/reference/plugins/Plugin3x@template/>). Rather than storing the templates as stand-alone files, they are stored as settings in Moodle.

## Installing

1. Drop code into /lib/editor/atto/plugins
2. Go to Site administration -> Notifications to install
3. Add as many templates as you like
4. Add templates4u to your atto toolbar

## Plugin renamed

Since Moodle 3.7.5 and Moodle 3.8.2 "templates" is regarded as a reserved directory. This change in Moodle meant that this plugin would no longer install, so I have had to change its name. It's a little bit cheesy, but I've changed it to atto_templates4u (though the display name remains Templates).

The consequences of this change means that Moodle will regard atto_templates4u as a new plugin; and if you have a clone or fork of this plugin you will need to change your remote url to the new repository url.

### Installing alongside the existing atto_templates plugin

In the install script I have added a check for the existing atto_templates plugin. If it is found, all the existing settings are copied to atto_templates4u when you install atto_templates4u.

Once atto_templates4u has been installed, you will need to uninstall atto_templates. Go to <https://yourmoodle.com/admin/plugins.php> and scroll down until you find atto_templates, and click uninstall.

You may need to manually remove the templates directory, depending on your server permissions.

### Change toolbar

Your atto toolbar probably references templates. Change this to templates4u.

### Change your remote url

If you are using Git and have a clone or fork of <https://github.com/sharpchi/moodle-atto_templates> you will need to change this to: <https://github.com/sharpchi/moodle-atto_templates4u>

```shell
# Git clone with SSH
$ git remote origin set-url git@github.com:sharpchi/moodle-atto_templates4u.git

# Git clone with HTTPS
$ git remote origin set-url https://github.com/sharpchi/moodle-atto_templates4u.git
```

## Credits

Heavily cribbed from [atto_generico](https://github.com/justinhunt/moodle-atto_generico) and [atto_styles](https://github.com/dthies/moodle-atto_styles)

- [Mike Churchward](https://github.com/mchurchward) - for spotting the change in Moodle that stopped the plugin working
