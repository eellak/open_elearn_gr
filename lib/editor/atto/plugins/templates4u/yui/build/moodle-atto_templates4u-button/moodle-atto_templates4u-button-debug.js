YUI.add('moodle-atto_templates4u-button', function (Y, NAME) {

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Atto Templates4U - YUI file
 * @package   atto_templates4u
 * @author    Mark Sharp <m.sharp@chi.ac.uk>
 * @copyright 2017 University of Chichester {@link www.chi.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 var COMPONENTNAME = 'atto_templates4u',
    CSS = {
        TEMPLATENAME: 'atto_templates4u_name',
        PREVIEW: 'atto_templates4u_preview',
        INSERT: 'atto_templates4u_insert',
        CANCEL: 'atto_templates4u_cancel'
    },
    SELECTORS = {
        TEMPLATES: '.' + CSS.TEMPLATENAME,
        INSERT: '.' + CSS.INSERT,
        PREVIEW: '.' + CSS.PREVIEW,
        CANCEL: '.' + CSS.CANCEL
    },
    TEMPLATES = {
        FORM: '' +
            '<form class="atto_form">' +
                '<div class="form-group">' +
                    '<label for="{{elementid}}_{{CSS.TEMPLATENAME}}">{{{get_string "selectatemplate" component}}}</label>' +
                    '<select class="{{CSS.TEMPLATENAME}} form-control" ' +
                        'id="{{elementid}}_{{CSS.TEMPLATENAME}}" ' +
                        'name="{{elementid}}_{{CSS.TEMPLATENAME}}">' +
                        '<option value="">{{get_string "selectatemplate" component}}</option>' +
                        '{{#each templates}}' +
                            '<option value="{{templatekey}}">{{templatekey}}</option>' +
                        '{{/each}}' +
                    '</select>' +
                '</div>' +
                '<label for="{{elementid}}_{{CSS.PREVIEW}}">{{get_string "preview" component}}</label>' +
                '<div class="card">' +
                    '<div class="card-block {{CSS.PREVIEW}}" id="{{elementid}}_{{CSS.PREVIEW}}">' +
                    '</div>' +
                '</div>' +
                '<div class="mdl-align">' +
                    '<button class="btn btn-primary {{CSS.INSERT}}">{{get_string "insert" component}}</button> ' +
                    '<button class="btn btn-secondary {{CSS.CANCEL}}">{{get_string "cancel" component}}</button>' +
                '</div>' +
            '</form>'
    };

Y.namespace('M.atto_templates4u').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    _content: null,
    _templates: [],
    initializer: function() {
        if (!this.get('enablebutton')) {
            return;
        }

        this.addButton({
            icon: 'icon',
            iconComponent: COMPONENTNAME,
            callback: this._displayDialogue
        });
        this._templates = this.get('templates');
    },
    _displayDialogue: function() {
        var dialogue = this.getDialogue({
            headerContent: M.util.get_string('pluginname', COMPONENTNAME),
            focusAfterHide: true,
            width: 600
        });

        var content = this._getDialogueContent();
        dialogue.set('bodyContent', content);
        dialogue.show();
    },
    _getDialogueContent: function() {
        var template = Y.Handlebars.compile(TEMPLATES.FORM);
        this._templates = this.get('templates');
        this._content = Y.Node.create(template({
            elementid: this.get('host').get('elementid'),
            component: COMPONENTNAME,
            templates: this._templates,
            CSS: CSS
        }));
        this._content.one(SELECTORS.TEMPLATES).on('change', this._previewTemplate, this);
        this._content.one(SELECTORS.INSERT).on('click', this._insertTemplate, this);
        this._content.one(SELECTORS.CANCEL).on('click', this._cancel, this);
        return this._content;
    },
    _previewTemplate: function(e) {
        var input,
            value,
            previewWindow;

        input = e.currentTarget;
        value = input.get('value');
        // Find the template.
        var template = this._templateFilter(value);
        previewWindow = Y.one(SELECTORS.PREVIEW);
        // Y.log(template.template);
        previewWindow.setHTML(template.template);
    },
    _insertTemplate: function(e) {
        var input,
            value,
            host,
            template;
        host = this.get('host');
        e.preventDefault();

        this.getDialogue({
            focusAfterHide: null
        }).hide();

        input = Y.one(SELECTORS.TEMPLATES); // Find the template dropdown.
        value = input.get('value');
        template = this._templateFilter(value);
        host.insertContentAtFocusPoint(template.template);
        this.markUpdated();

    },
    _cancel: function(e) {
        e.preventDefault();
        this.getDialogue().hide();
    },
    _templateFilter: function(value) {
        for (var x = 0; x < this._templates.length; x++) {
            if (this._templates[x].templatekey == value) {
                return this._templates[x];
            }
        }
        return [];
    }
}, {
    ATTRS: {
        templates: {
            value: {}
        },
        enablebutton: {
            value: false
        }
    }
});


}, '@VERSION@', {"requires": ["moodle-editor_atto-plugin"]});
