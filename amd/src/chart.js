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
 * Describe different types of charts.
 *
 * @module     block_learnerscript/chart
 * @copyright  2023 Moodle India Information Solutions Private Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery',
        'core/ajax',
        'block_learnerscript/charts/chart',
        'block_learnerscript/smartfilter',
        'core/str'
    ],
    function($, Ajax, Chart, smartfilter, Str) {
        /**
         * Get highchart report for report with Ajax request
         * @param object reportid and reportdata
         * @return Generate highchart report
         */
        var chart = {
            HighchartsAjax: function(args) {
                args.cols = JSON.stringify(args.cols);
                args.instanceid = args.reportid;
                args.filters = args.filters || smartfilter.FilterData(args.instanceid);
                args.basicparams = JSON.stringify(smartfilter.BasicparamsData(args.instanceid));
                args.filters['lsfstartdate'] = $('#lsfstartdate').val();
                args.filters['lsfenddate'] = $('#lsfenddate').val();
                if (typeof args.filters['filter_courses'] == 'undefined') {
                    var filter_courses = $('.dashboardcourses').val();
                    if (filter_courses != 1) {
                        args.filters['filter_courses'] = filter_courses;
                    }
                }
                args.filters = JSON.stringify(args.filters);

                // Request
                var promise = Ajax.call([{
                    methodname: 'block_learnerscript_generate_plotgraph',
                    args: args,
                }]);

                // Preload
                $('#report_plottabs').show();
                $("#plotreportcontainer" +
                args.instanceid).html('<img src="' +
                M.util.image_url('loading', 'block_learnerscript') + '" id="reportloadingimage" />');

                // Response process
                promise[0].done(function(response) {
                    response = JSON.parse(response);
                    if (response.plot) {
                        if (response.plot.error === true) {
                            $('.ls-report_graph_container').removeClass('hide').addClass('show');
                            $('.ls-report_graph_container').css('height', '100px');
                            Str.get_string('nodataavailable', 'block_learnerscript').then(function(s) {
                                $('#plotreportcontainer' +
                                args.instanceid).html("<div class='alert alert-warning'>" + s + "</div>");
                            });
                            $(document).ajaxStop(function() {
                                $("#reportloadingimage").remove();
                            });
                        } else {
                            response.plot.reportid = args.reportid;
                            response.plot.reportinstance = args.reportid;
                            if (response.plot.data && response.plot.data.length > 0) {
                                $('.ls-report_graph_container').removeClass('hide').addClass('show');
                                $('#plotreportcontainer' + args.instanceid).css('height', '500px');
                                require(['block_learnerscript/report'], function(report) {
                                    report.generate_plotgraph(response.plot);
                                });
                                $(document).ajaxStop(function() {
                                    $("#reportloadingimage").remove();
                                });
                            } else {
                                $("#plotreportcontainer" + args.instanceid).css('height', '100px');
                                Str.get_string('nodataavailable', 'block_learnerscript').then(function(s) {
                                    $('#plotreportcontainer' +
                                    args.instanceid).html("<div class='alert alert-warning'>" + s + "</div>");
                                });
                                $(document).ajaxStop(function() {
                                    $("#reportloadingimage").remove();
                                });
                            }
                        }
                    }
                });
            },
            combinationchart: function(chartdata) {
                var canvas = document.createElement('canvas');

                canvas.id = "mixedcanvas";
                canvas.style.width = "100%";
                canvas.style.height = "500px";
                canvas.style.zIndex = 8;
                canvas.style.position = "absolute";
                var body = document.getElementById(chartdata.containerid);
                body.appendChild(canvas);
                var cursorlayer = document.getElementById("mixedcanvas").getContext("2d");
                new Chart(cursorlayer, {
                    type: 'bar',
                    data: {
                        datasets: chartdata.data,
                        labels: chartdata.categorydata
                    }//end data
                });
            },
            lbchart: function(chartdata) {
                var canvas = document.createElement('canvas');
                canvas.id = "canvaschart";
                canvas.style.width = "100%";
                canvas.style.height = "500px";
                canvas.style.zIndex = 8;
                canvas.style.position = "absolute";
                var body = document.getElementById(chartdata.containerid);
                body.appendChild(canvas);
                var cursorlayer = document.getElementById("canvaschart").getContext("2d");
                new Chart(cursorlayer, {
                    type: 'bar',
                    data: {
                        datasets: chartdata.data,
                        labels: chartdata.categorydata
                    }//end data
                });
            },
        };
        return chart;
    });
