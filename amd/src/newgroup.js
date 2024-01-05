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
 * Add a create new group modal to the page.
 *
 * @module     block_learnerscript/newgroup
 * @class      NewGroup
 * @package
 * @copyright  2017 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/str', 'core/modal_factory', 'core/modal_events',
'core/fragment', 'block_learnerscript/ajax'],
        function($, Str, ModalFactory, ModalEvents, Fragment, Ajax) {

    /**
     * Each call to init gets it's own instance of this class.
     * @param {object} args
     * @param {string} url
     */
    var NewGroup = function(args, url) {
        this.args = args;
        this.contextid = 1;
        this.url = url;
        this.nodeContent = args.nodeContent || 'ajaxForm';
        this.init(this.args);
    };

    /**
     * @var {Modal} modal
     * @private
     */
    NewGroup.prototype.modal = null;

    /**
     * @var {int} contextid
     * @private
     */
    NewGroup.prototype.contextid = -1;

    /**
     * Initialise the class.
     *
     * @private
     * @return {Promise}
     */
    NewGroup.prototype.init = function() {
                var resp = this.getBody();
                $('body').append("<div class='" + this.nodeContent + "'></div>");
                var self = this;
                resp.done(function(data) {
                    $('.ajaxForm').html(data.html);
                    $('head').append(data.javascript);
                });

                var dlg = $("." + this.nodeContent).dialog({
                    resizable: true,
                    autoOpen: false,
                    width: "60%",
                    title: this.args.title,
                    modal: true,
                    close: function() {
                        $(this).dialog('destroy').remove();
                    }
                });
                var self = this;
                $('.' + this.nodeContent + ' .mform').bind('submit', function(e) {
                    e.preventDefault();
                    self.submitFormAjax(this);
                });
                dlg.dialog("open");

    };

    /**
     * @param {object} formdata
     * @method getBody
     * @private
     * @return {Promise}
     */
    NewGroup.prototype.getBody = function(formdata) {
        if (typeof formdata === "undefined") {
            formdata = null;
        } else {
            // Get the content of the modal.
           this.args.jsonformdata = JSON.stringify(formdata);
        }

        var promise = Ajax.call({
            args: this.args,
            url: this.url
            }, false);

        return promise;
    };

    /**
     * @param {object} data
     * @method handleFormSubmissionResponse
     * @private
     * @return {Promise}
     */
    NewGroup.prototype.handleFormSubmissionResponse = function(data) {
        if (data.formerror) {
            $('.ajaxForm').html(data.html);
            $('head').append(data.javascript);
                var self = this;
                $('.' + this.nodeContent + ' .mform').bind('submit', function(e) {
                    e.preventDefault();
                    self.submitFormAjax(this);
                });
        } else {
            alert("Success!");
        }
    };

    /**
     * @method handleFormSubmissionFailure
     * @private
     * @return {Promise}
     */
    NewGroup.prototype.handleFormSubmissionFailure = function() {
    };

    /**
     * Private method
     *
     * @method submitFormAjax
     * @param {object} form
     * @private
     */
    NewGroup.prototype.submitFormAjax = function(form) {

        // We don't want to do a real form submission.
        // Convert all the form elements values to a serialised string.
        this.args.jsonformdata = $(form).serialize();
        var self = this;
            var promise = Ajax.call({
                args: this.args,
                url: this.url
            });
            promise.done(function(response) {
                self.handleFormSubmissionResponse(response);
            }).fail(function() {
            });
    };

    /**
     * This triggers a form submission, so that any mform elements can do final tricks before the form submission is processed.
     *
     * @method submitForm
     * @param {Event} e Form submission event.
     * @private
     */
    NewGroup.prototype.submitForm = function(e) {
        e.preventDefault();
        this.modal.getRoot().find('form').submit();
    };

    return /** @alias module:core_group/newgroup */ {
        // Public variables and functions.
        /**
         * Attach event listeners to initialise this module.
         *
         * @method init
         * @param {string} args The CSS selector used to find nodes that will trigger this module.
         * @return {Promise}
         */
        init: function(args) {
            return new NewGroup(args);
        }
    };
});
