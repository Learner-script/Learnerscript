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
 * Schedule a timings on a calender.
 *
 * @module     block_learnerscript/schedule
 * @copyright  2023 Moodle India Information Solutions Private Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery',
    'core/ajax',
    'block_learnerscript/ajax',
    'core/event',
    'block_learnerscript/select2/select2',
    'block_learnerscript/report',
    'core/str'
], function($, Ajax, ajax, Event, select2, report, Str) {

    var schedule = {
        /**
         * To display scheduled timings for report
         */
        /**
         * Schedule report form in popup in dashboard
         * @param  {object} args reportid
         * @return Popup with schedule form
         */
        schreportform: function(args) {
            Str.get_strings([{
                key: 'addschedulereport',
                component: 'block_learnerscript'
            }, {
                key: 'schedulereport',
                component: 'block_learnerscript'
            }, {
                key: 'close',
                component: 'block_learnerscript'
            }]).then(function(s) {
                var promise = Ajax.call([{
                    methodname: 'block_learnerscript_schreportform',
                    args: {
                        reportid: args.reportid,
                        instance: args.instanceid
                    },
                }]);
                promise[0].done(function(response) {
                    response = $.parseJSON(response);
                    $('body').append("<div class='schreportform" + args.instanceid + "'>" + response + "</div>");
                    var title_img = "<img class='dialog_title_icon' alt=" + s[1] + " src='" +
                        M.util.image_url("schedule_icon", "block_learnerscript") + "'/>";
                    var dlg = $(".schreportform" + args.instanceid).dialog({
                        resizable: true,
                        autoOpen: false,
                        width: "90%",
                        title: s[0],
                        modal: true,
                        appendTo: "#inst" + args.instanceid,
                        position: {
                            my: "center",
                            at: "center",
                            of: "#reportcontainer" + args.instanceid
                        },
                        open: function() {
                            $(this).closest(".ui-dialog").find(".ui-dialog-titlebar-close")
                                .removeClass("ui-dialog-titlebar-close")
                                .html("<span class='ui-button-icon-primary ui-icon ui-icon-closethick'></span>");
                                var Closebutton = $('.ui-icon-closethick').parent();
                                $(Closebutton).attr({
                                    "title" : s[2]
                                });
                            $(this).closest(".ui-dialog").find('.ui-dialog-title')
                                .html(title_img + s[1]);
                        },
                        close: function() {
                            $(this).dialog('destroy').remove();
                        }
                    });
                    dlg.dialog("open");
                    $("#id_role" + args.instanceid).select2();
                    this.SelectRoleUsers();
                    this.schformvalidation({
                        reportid: args.reportid,
                        form: 'schform' + args.instanceid,
                        reqclass: 'schformreq' + args.instanceid,
                        instanceid: args.instanceid
                    });
                }).fail(function() {
                    // Do something with the exception
                    //  console.log(ex);
                });
            });
        },
        ScheduledTimings: function(args) {
            $("select[data-select2='1']").select2({
                theme: "classic"
            });
            this.SelectRoleUsers();
            Str.get_string('all','block_learnerscript').then(function(s) {
                $('#scheduledtimings').dataTable({
                    "processing": true,
                    "serverSide": true,
                    "lengthMenu": [
                        [5, 10, 25, 50, -1],
                        [5, 10, 25, 50, s]
                    ],
                    "idisplaylength": 5,
                    'ordering': false,
                    "ajax": {
                        "method": "GET",
                        "url": M.cfg.wwwroot + "/blocks/learnerscript/components/scheduler/ajax.php",
                        "data": args
                    }
                });
            });
        },
        ViewSchUsersTable: function(args) {
            Str.get_string('all','block_learnerscript').then(function(s) {
                $('#scheduledusers').dataTable({
                    "processing": true,
                    "serverSide": true,
                    "lengthMenu": [
                        [5, 10, 25, 50, -1],
                        [5, 10, 25, 50, s]
                    ],
                    "idisplaylength": 5,
                    'ordering': false,
                    "ajax": {
                        "method": "GET",
                        "url": M.cfg.wwwroot + "/blocks/learnerscript/components/scheduler/ajax.php",
                        "data": {
                            action: 'viewschusersdata',
                            reportid: args.reportid,
                            scheduleid: args.scheduleid,
                            schuserslist: args.schuserslist
                        }
                    }
                });
            });
        },
        /**
         * Add validation script to AJAX reponse MOODLE form
         * @param  {object} args Form name and required classname
         * @return {[type]}      [description]
         */
        schformvalidation: function(args) {
            document.getElementById(args.form).addEventListener('submit', function(ev) {
                try {
                    var myValidator = report.validate_scheduled_reports_form(args);
                } catch (e) {
                    return true;
                }
                if (typeof window.tinyMCE !== 'undefined') {
                    window.tinyMCE.triggerSave();
                }
                if (!myValidator) {
                    ev.preventDefault();
                }
                return false;
            });
        },
        /**
         * Validates and return error message for each element of given form
         * @param  {object} args Formname and required classname
         * @return Returns list of error messages from validation
         */
        validate_scheduled_reports_form: function(args) {
            var self = this;
            var skipClientValidation = false;
            if (skipClientValidation) {
                $('.schreportform' + args.instanceid).dialog('destroy').remove();
            }
            var ret = true;
            var first_focus = false;
            $("[data-class='" + args.reqclass + "']").each(function(index, value) {
                var element = $(value).data('element');
                ret = self.validate_scheduled_reports_form_element(value, element, args) && ret;
                if (!ret && !first_focus) {
                    first_focus = true;
                    Y.use('moodle-core-event', function() {
                        Y.Global.fire(M.core.globalEvents.FORM_ERROR, {
                            formid: args.form,
                            elementid: 'id_error_' + element + args.instanceid
                        });
                        document.getElementById('id_error_' + element + args.instanceid).focus();
                    });
                }
            });
            return ret;
        },
        /**
         * Format error message string for each element
         * @param  {object} element Element object
         * @param  {string} escapedName Element name
         * @param  {object} args Element object
         * @return Formatted error messages for each element
         */
        validate_scheduled_reports_form_element: function(element, escapedName, args) {
            if (undefined == element) {
                //required element was not found, then let form be submitted without client side validation
                return true;
            }
            var value = '';
            var errFlag = new Array();
            var _qfMsg = '';
            var frm = element.parentNode;
            if ((undefined != element.name) && (frm != undefined)) {
                while (frm && frm.nodeName.toUpperCase() != 'FORM') {
                    frm = frm.parentNode;
                }
                value = new Array();
                var valueIdx = 0;
                for (var i = 0; i < element.options.length; i++) {
                    if (element.options[i].selected) {
                        value[valueIdx++] = element.options[i].value;
                    }
                }
                if (value == '' && !errFlag[escapedName]) {
                    errFlag[escapedName] = true;
                    Str.get_string('supplyvalue', 'block_learnerscript').then(function(s) {
                        _qfMsg = _qfMsg + s;
                    });
                }
                return this.qf_errorHandler(element, _qfMsg, escapedName, args);
            } else {
                //element name should be defined else error msg will not be displayed.
                return true;
            }
        },
        /**
         * Render and display error message for each element
         * @param  {object} element  Element object
         * @param  {string} _qfMsg  Error message
         * @param  {string} escapedName Element name
         * @param  {object} args  Element object
         * @return Render and display error message for each element
         */
        qf_errorHandler: function(element, _qfMsg, escapedName, args) {
            var event = $.Event(Event.Events.FORM_FIELD_VALIDATION);
            $(element).trigger(event, _qfMsg);
            if (event.isDefaultPrevented()) {
                return _qfMsg == '';
            } else {
                // Legacy mforms.
                var div = element.parentNode;
                if ((div == undefined) || (element.name == undefined)) {
                    // No checking can be done for undefined elements so let server handle it.
                    return true;
                }
                if (_qfMsg != '') {
                    var errorSpan = document.getElementById('id_error_' + escapedName + args.instanceid);
                    if (!errorSpan) {
                        errorSpan = document.createElement('span');
                        errorSpan.id = 'id_error_' + escapedName + args.instanceid;
                        errorSpan.className = 'error';
                        element.parentNode.insertBefore(errorSpan, element.parentNode.firstChild);
                        document.getElementById(errorSpan.id).setAttribute('TabIndex', '0');
                        document.getElementById(errorSpan.id).focus();
                    }
                    while (errorSpan.firstChild) {
                        errorSpan.removeChild(errorSpan.firstChild);
                    }
                    errorSpan.appendChild(document.createTextNode(_qfMsg.substring(3)));
                    if (div.className.substr(div.className.length - 6, 6) != ' error' && div.className != 'error') {
                        div.className += ' error';
                        linebreak = document.createElement('br');
                        linebreak.className = 'error';
                        linebreak.id = 'id_error_break_' + escapedName + args.instanceid;
                        errorSpan.parentNode.insertBefore(linebreak, errorSpan.nextSibling);
                    }
                    return false;
                } else {
                    var errorSpan = document.getElementById('id_error_' + escapedName + args.instanceid);
                    if (errorSpan) {
                        errorSpan.parentNode.removeChild(errorSpan);
                    }
                    var linebreak = document.getElementById('id_error_break_' + escapedName + args.instanceid);
                    if (linebreak) {
                        linebreak.parentNode.removeChild(linebreak);
                    }
                    if (div.className.substr(div.className.length - 6, 6) == ' error') {
                        div.className = div.className.substr(0, div.className.length - 6);
                    } else if (div.className == 'error') {
                        div.className = '';
                    }
                    return true;
                }
            }
        },
        frequency_schedule: function(args) {
            var promise = Ajax.call([{
                methodname: 'block_learnerscript_frequency_schedule',
                args: {
                    frequency: $("#id_frequency" + args.reportinstance).val()
                }
            }]);
            promise[0].done(function(resp) {
                resp = $.parseJSON(resp);
                var template = '';
                if (resp) {
                    $.each(resp, function(index, value) {
                        template += '<option value = ' + index + ' >' + value + '</option>';
                    });
                } else {
                    Str.get_string('selectall', 'block_reportdashboard').then(function(s) {
                        template += '<option value=null >' + s + '</option>';
                    });
                }
                $("#id_updatefrequency" + args.reportinstance).html(template);
            }).fail(function() {
                // do something with the exception
                //  console.log(ex);
            });
        },
        /**
         * Preview selected users to schedule report
         * @param  {object} args reportid,scheduleid and userslist
         * @return Preview users in dialog
         */
        viewschusers: function(args) {
            Str.get_strings([{
                key: 'viewschusers',
                component: 'block_learnerscript'
            }, {
                key: 'close',
                component: 'block_learnerscript'
            }]).then(function(s) {
                args.schuserslist = $('#schuserslist' + args.reportinstance).val();
                var promise = ajax.call({
                    methodname: 'viewschuserstable',
                    args: {
                        action: 'viewschuserstable',
                        reportid: args.reportid,
                        scheduleid: args.scheduleid,
                        schuserslist: args.schuserslist
                    },
                    url: M.cfg.wwwroot + "/blocks/learnerscript/ajax.php",
                });
                promise.done(function(response) {
                    $('body').append("<div class='viewschuserstable'>" + response + "</div>");
                    var dlg = $(".viewschuserstable").dialog({
                        resizable: true,
                        autoOpen: false,
                        width: "60%",
                        title: s[0],
                        modal: true,
                        close: function() {
                            $(this).dialog('destroy').remove();
                        },
                        open: function() {
                            $(this).closest(".ui-dialog")
                                .find(".ui-dialog-titlebar-close")
                                .removeClass("ui-dialog-titlebar-close")
                                .html("<span class='ui-button-icon-primary ui-icon ui-icon-closethick'></span>");
                                var Closebutton = $('.ui-icon-closethick').parent();
                                $(Closebutton).attr({
                                    "title" : s[1]
                                });
                        }
                    });
                    schedule.ViewSchUsersTable(args);
                    dlg.dialog("open").prev(".ui-dialog-titlebar").css("color", "#0C75B6");
                }).fail(function() {
                    // do something with the exception
                    //console.log(ex);
                });
            });
        },
        /**
         * Add users to schedule report
         * @param  {object} args reportid
         * @return Adds users to schedule report form
         */
        addschusers: function(args) {
            var selschusers = $('.schforms' + args.reportinstance + ' #id_users_data' + args.reportinstance).val();
            var selusers = $('.schforms' + args.reportinstance + ' #schuserslist' + args.reportinstance).val();
            var total;
            if (selusers) {
                selusers = selusers.split(',');
                total = selusers.concat(selschusers);
            } else {
                total = selschusers;
            }
            if (total && total.includes('-1')) {
                var index = total.indexOf('-1');
                total.splice(index, 1);
            }
            $('#id_users_data' + args.reportinstance).find('option').not(':selected').each(function(k, v) {
                if (total && total.includes(v.value)) {
                    var index = total.indexOf(v.value);
                    total.splice(index, 1);
                }
            });
            var d = total && total.filter(function(item, pos) {
                return total.indexOf(item) == pos;
            });
            $('#schuserslist' + args.reportinstance).val(d);
        },
        /**
         * Get roleusers for selected report
         */
        SelectRoleUsers: function() {
            var reportid = $(".schform").data('reportid');
            require(['block_learnerscript/helper'], function(helper) {
                helper.Select2Ajax({
                    reportid: reportid,
                    action: 'rolewiseusers',
                    maximumselectionlength: 5
                });
            });
        },
        rolewiseusers: function(args) {
            $('#id_users_data' + args.reportinstance).val(null).trigger('change');
        }
    };
    return schedule;
});
