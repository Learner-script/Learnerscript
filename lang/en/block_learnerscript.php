<?php
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

/** A Moodle block for creating customizable reports
 * @package   block_learnerscript
 * @copyright 2023 Moodle India
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['pluginname'] = "Learner script";
$string['blockname'] = "Learner script";
// Capabilities.
$string['learnerscript:addinstance'] = 'Add a new Learnerscript reports block';
$string['learnerscript:myaddinstance'] = 'Add a new LearnerScript reports block to my home';
$string['learnerscript:manageownreports'] = "Manage own reports";
$string['learnerscript:managereports'] = "Mange reports";
$string['learnerscript:managesqlreports'] = "Manage sql reports";
$string['learnerscript:viewreports'] = "View reports";
$string['learnerscript:designreport'] = "Design reports";
// Reports.
$string['report_courseactivities'] = 'Course activities';
$string['report_courseprofile'] = "Course profile";
$string['report_courses'] = "Courses";
$string['report_coursesoverview'] = 'Learner\'s courses overview';
$string['report_courseviews'] = 'Course views';
$string['report_gradedactivity'] = 'Graded activity';
$string['report_grades'] = 'Course activity grades';
$string['report_noofviews'] = 'Activity views';
$string['report_sql'] = "SQL";
$string['report_statistics'] = 'Statistic';
$string['report_userprofile'] = "Users profile";
$string['report_users'] = "Users";
$string['report_useractivities'] = 'Learner\'s course activities';
$string['useractivitiescolumns'] = 'User activity columns';

$string['managereports'] = "Manage reports";
$string['userprofile'] = "User profile";
$string['report'] = "Report";
$string['reports'] = "Reports";
$string['calendar'] = "Calendar";
$string['graph'] = "Graph";

$string['columns'] = "Columns";
$string['conditions'] = "Conditions";
$string['permissions'] = "Permissions";
$string['plot'] = "Plot - Graphs";
$string['filters'] = "Filters	";
$string['calcs'] = "Calculations";
$string['ordering'] = "Ordering";
$string['customsql'] = "Custom SQL";
$string['addreport'] = "Add report";
$string['type'] = "Type of report";
$string['columncalculations'] = "Column calculations";
$string['newreport'] = "New report";
$string['confirmdeletereport'] = "Are you sure you want to delete this report?";
$string['noreportsavailable'] = "No reports available";
$string['downloadreport'] = "Download report";
$string['reportlimit'] = "Report row limit";
$string['reportlimitinfo'] = "Limit the number of rows that are displayed in the report table (default is 5000 rows. Better to have some limit, so users will not over load the DB engine)";
$string['exportoptions'] = "Export options";
$string['field'] = "Field";
// Report form.
$string['typeofreport'] = "Type of report";
$string['enablejsordering'] = "Enable javaScript ordering";
$string['enablejspagination'] = "Enable javaScript pagination";
$string['export_csv'] = "Export in csv format";
$string['export_ods'] = "Export in ods format";
$string['export_xls'] = "Export in xls format";
$string['export_pdf'] = "Export in pdf format";
$string['viewreport'] = "View report";
$string['norecordsfound'] = "No records found";
$string['jsordering'] = 'JavaScript ordering';
$string['cron'] = 'Auto run daily';
$string['crondescription'] = 'Schedule this query to run each day (at night)';
$string['cron_help'] = 'Schedule this query to run each day (at night)';
$string['setcourseid'] = 'Set courseid';
// Columns.
$string['column'] = "Column";
$string['nocolumnsyet'] = "No columns yet";
$string['tablealign'] = "Table align";
$string['tablecellspacing'] = "Table cellspacing";
$string['tablecellpadding'] = "Table cellpadding";
$string['tableclass'] = "Table class";
$string['tablewidth'] = "Table width";
$string['cellalign'] = "Cell align";
$string['cellwrap'] = "Cell wrap";
$string['cellsize'] = "Cell size";
// Conditions.
$string['conditionexpr'] = "Condition";
$string['conditionexprhelp'] = "Enter a valid condition i.e: (c1 and c2) or (c4 and c3)";
$string['noconditionsyet'] = "No conditions yet";
$string['operator'] = "Operator";
$string['value'] = "Value";
// Filter.
$string['filter'] = "Filter";
$string['nofilteryet'] = "No filters yet";
$string['courses'] = "Courses";
$string['nofiltersyet'] = "No filters yet";
$string['filter_all'] = 'All';
$string['filter_apply'] = 'Apply';
$string['filter_clear'] = 'Clear';
$string['filter_searchtext'] = 'Search text';
$string['searchtext'] = 'Search text';
$string['filter_searchtext_summary'] = 'Free text filter';
$string['years'] = 'Year (numeric)';
$string['filteryears'] = 'Year (numeric)';
$string['filteryears_summary'] = 'Filter by years (numeric representation, 2012...)';
$string['filteryears_list'] = '2010,2011,2012,2013,2014,2015';
$string['semester'] = 'Semester (Hebrew)';
$string['filtersemester'] = 'Semester (Hebrew)';
$string['filtersemester_summary'] = 'מאפשר סינון לפני סמסטרים (בעברית, למשל: סמסטר א,סמסטר ב)';
$string['filtersemester_list'] = 'סמסטר א,סמסטר ב,סמסטר ג,סמינריון';
$string['subcategories'] = 'Category (include sub categories)';
$string['filtersubcategories'] = 'Category (include sub categories)';
$string['filtersubcategories_summary'] = 'Use: %%FILTER_CATEGORIES:mdl_course_category.path%%';
$string['yearnumeric'] = 'Year (numeric)';
$string['filteryearnumeric'] = 'Year (numeric)';
$string['filteryearnumeric_summary'] = 'Filter is using numeric years (2013,...)';
$string['yearhebrew'] = 'Year (hebrew)';
$string['filteryearhebrew'] = 'Year (hebrew)';
$string['filteryearhebrew_list'] = 'תשע,תשעא,תשעב,תשעג,תשעד,תשעה';
$string['filteryearhebrew_summary'] = 'Filter is using hebrew years (תשעג,...)';
$string['role'] = 'Role';
$string['filterrole'] = 'role';
$string['filterrole_summary'] = 'Filter system roles (Teacher, Student, ...)';
$string['coursemodules'] = 'Course module';
$string['filtercoursemodules'] = 'Course module';
$string['filtercoursemodules_summary'] = 'Filter course modules';
$string['user'] = 'Course user (id)';
$string['filteruser'] = 'Current course user';
$string['filteruser_summary'] = 'Filter a user (id) from current course users';
$string['users'] = 'Users';
$string['filterusers'] = 'System user';
$string['enrolledstudents'] = 'Enrolled students';
$string['filterusers_summary'] = 'Filter a user (by id) from system user list';
$string['filterenrolledstudents'] = 'Enrolled course students';
$string['filterenrolledstudents_summary'] = 'Filter a user (by id) from enrolled course students';
$string['student'] = 'Student';
$string['filterappnoagentcode'] = "Appno agentcode";
$string['filterbelt'] = 'Belt';
$string['filteremployeecode'] = 'Employee code';
$string['filterprimarytrainer'] = 'Primary trainer';
$string['filterprimarytrainercode'] = 'Primary trainer code';
$string['filtertrainercode'] = 'Trainer code';
$string['appnoagentcode'] = "Appno agentcode";
$string['belt'] = 'Belt';
$string['employeecode'] = 'Employee code';
$string['primarytrainer'] = 'Primary trainer';
$string['primarytrainercode'] = 'Primary trainer code';
$string['trainercode'] = 'Trainer code';
$string['customroles'] = 'Roles';
// Calcs.
$string['nocalcsyet'] = "No calculations yet";
// Plot.
$string['noplotyet'] = "No plots yet";
// Permissions.
$string['nopermissionsyet'] = "No permissions yet";
$string['chartname'] = "Chart Name";
$string['chartnamerequired'] = "Please enter the chart name";
$string['year'] = 'Year';
$string['all'] = 'All';
// Ordering.
$string['noorderingyet'] = "No ordering yet";
$string['userfieldorder'] = "User field order";
// Plugins.
$string['coursefield'] = "Course field";
$string['ccoursefield'] = "Course field condition";
$string['roleusersn'] = "Number of users with role...";
$string['coursecategory'] = "Course in category";
$string['filter_courses'] = "Courses";
$string['filter_courses_summary'] = "This filter shows a list of courses. Only one course can be selected at the same time";
$string['roleincourse'] = "User with the selected role/s";
$string['reportscapabilities'] = "Report capabilities";
$string['reportscapabilities_summary'] = "Users with the capability moodle/site:viewreports enabled";
$string['sum'] = "Sum";
$string['max'] = "Maximum";
$string['min'] = "Minimum";
$string['average'] = "Average";
$string['pie'] = "Pie";
$string['piesummary'] = "A pie graph";
$string['pieareaname'] = "Name";
$string['pieareavalue'] = "Value";
$string['serieslabel'] = "Series Label";
$string['showlegend'] = "Show legend";
$string['datalabels'] = "Data Labels";

$string['anyone'] = "Anyone";
$string['anyone_summary'] = "Any user in the LMS will be able to view this report";

$string['currentuserfinalgrade'] = "Current user final grade in course";

$string['currentuserfinalgrade_summary'] = "This column shows the final grade of the current user in the row-course";
$string['userfield'] = "User profile field";

$string['cuserfield'] = "User field condition";
$string['direction'] = "Direction";

$string['courseparent'] = "Courses whose parent is";
$string['coursechild'] = "Courses that are children of";
$string['table'] = 'Report table';
$string['currentusercourses'] = "Current user enrolled courses";
$string['currentusercourses_summary'] = "A list of the current users courses (only visible courses)";
$string['currentreportcourse'] = "Current report course";
$string['currentreportcourse_summary'] = "The course where the report has been created";

$string['coursefieldorder'] = "Course field order";

$string['fcoursefield'] = "Course field filter";
$string['usersincoursereport'] = "Any user in the current report course";

$string['groupvalues'] = "Group same values (sum)";
$string['fuserfield'] = "User field filter";

$string['module'] = "Module";

$string['usersincurrentcourse'] = "Users in current report course";
$string['usersincurrentcourse_summary'] = "Users with the role/s selected in the report course";

$string['usermodoutline'] = "User module outline stats";
$string['donotshowtime'] = "Do not show date information";
$string['usermodactions'] = "User module actions";

$string['currentuser'] = "Current user";
$string['currentuser_summary'] = "The user that is viewing the report";

$string['puserfield'] = "User field value";
$string['puserfield_summary'] = "User with the selected value in the selected field";

$string['startendtime'] = "Start / End date filter";
$string['starttime'] = "Start Date";
$string['endtime'] = "End Date";

$string['fromtime'] = "From";
$string['totime'] = "To";

$string['template'] = "Template";
$string['availablemarks'] = "Available marks";
$string['header'] = "Header";
$string['footer'] = "Footer";
$string['templaterecord'] = "Record template";
$string['querysql'] = "SQL Query";
$string['filterstartendtime_summary'] = "Start / End date filter";

$string['pagination'] = "Pagination";
$string['reportcolumn'] = "Other report column";

$string['reporttable'] = "Report table";
$string['columnandcellproperties'] = "Column and cell properties";
$string['componenthelp'] = "Component help";

$string['badsize'] = 'Incorrect size, it must be numeric.';
$string['badtablewidth'] = 'Incorrect width, it must be in &#37; or absolute value';
$string['missingcolumn'] = "A column is required";
$string['error_operator'] = "Operator not allowed";

$string['error_field'] = "Field not allowed";
$string['error_value_expected_integer'] = "Expected integer value";
$string['badconditionexpr'] = "Incorrect condition expression";

$string['notallowedwords'] = "Not allowed words";
$string['nosemicolon'] = "No semicolon";
$string['noexplicitprefix'] = "No explicit prefix";
$string['queryfailed'] = "Query failed";
$string['norowsreturned'] = "No rows returned";

$string['listofsqlreports'] = 'Press F11 when cursor is in the editor to toggle full screen editing. Esc can also be used to exit full screen editing.<br/><br/><a href="http://docs.moodle.org/en/ad-hoc_contributed_reports" target="_blank">List of SQL Contributed reports</a>';

$string['usersincoursereport_summary'] = "Any user in the current report course";

$string['printreport'] = 'Print report';

$string['importreport'] = "Import report";
$string['exportreport'] = "Export report";

$string['download'] = "Download";

$string['timeline'] = 'Timeline';
$string['timemode'] = 'Time mode';
$string['previousdays'] = 'Previous days';
$string['fixeddate'] = 'Fixed date';
$string['previousstart'] = 'Previous start';
$string['previousend'] = 'Previous end';
$string['forcemidnight'] = 'Force midnight';
$string['timeinterval'] = 'Time interval';
$string['date'] = 'Date';
$string['dateformat'] = 'Date format';
$string['customdateformat'] = 'Custom date format';
$string['custom'] = 'Custom';

$string['line'] = 'Line';
$string['userstats'] = 'User statistics';
$string['stat'] = 'Statistic';
$string['statslogins'] = 'Logins in the platform';
$string['activityview'] = 'Activity views';
$string['activitypost'] = 'Activity posts';
$string[''] = '';
$string['globalstatsshouldbeenabled'] = 'Site statistics must be enabled. Go to Admin -> Server -> Statistics';

$string['xaxis'] = 'X Axis';
$string['yaxis'] = 'Y Axis';
$string['yaxis_line'] = 'Line - Y Axis';
$string['yaxis_bar'] = 'Column - Y Axis';
$string['barlinecolumnsequal'] = 'Same values not allowed on both types.';
$string['serieid'] = 'Series column';
$string['groupseries'] = 'Group series';
$string['linesummary'] = 'A line graph with multiple series of data';

$string['bar'] = 'Bar';
$string['barsummary'] = 'A bar graph with multiple series of data';

$string['coursestats'] = 'Course stats';
$string['statstotalenrolments'] = 'Total enrolments';
$string['statsactiveenrolments'] = 'Active (last week) enrolments';
$string['youmustselectarole'] = 'At least a role is required';

$string['categoryfield'] = 'Category field';
$string['categoryfieldorder'] = 'Category field order';
$string['categories'] = 'Categories';
$string['parentcategory'] = 'Parent category';
$string['filtercategories'] = 'Filter categories';
$string['filtercategories_summary'] = 'To filter by category';

$string['includesubcats'] = 'Include subcategories';

$string['coursededicationtime'] = 'Course dedication time';

$string['jsordering_help'] = 'JavaScript ordering allow you to order the report table without reloading the page';
$string['pagination_help'] = 'Number of records to show in each page. Zero means no pagination';
$string['typeofreport_help'] = 'Choose the type of report you want to create.
For security, SQL Report requires an additional capability';

$string['comp_ordering'] = 'Ordering';
$string['comp_ordering_help'] = '<p>Here you can choose how to order the report using fields and directions</p>

<p>More help: <a href="http://docs.moodle.org/en/blocks/learnerscript/" target="_blank">Plugin documentation</a></p>';
$string['comp_permissions'] = 'Permissions';
$string['comp_permissions_help'] = '<p>Here you can choose who can view a report.</p>

<p>You can add a logical expression to calculate the final permission if you are using more than one condition.</p>
<p>Final condition is the combination of conditions and role conditions</p>';
$string['comp_plot'] = 'Plot';
$string['comp_plot_help'] = '<p>Here you can add graphs to your report based on the report columns and values</p>

<p>More help: <a href="http://docs.moodle.org/en/blocks/learnerscript/" target="_blank">Plugin documentation</a></p>';
$string['comp_template'] = 'Template';
$string['comp_template_help'] = '<p>You can modify the report\'s layout by creating a template</p>

<p>For creating a template see the replacemnet marks you can use in header, footer and for each report record using the help buttons or the information displayed in the same page.</p>

<p>More help: <a href="http://docs.moodle.org/en/blocks/learnerscript/" target="_blank">Plugin documentation</a></p>';
$string['comp_filters'] = 'Filters';
$string['comp_filters_help'] = '<p>Here you can choose which filters will be displayed</p>

<p>A filter lets an user to choose columns from the report to filter the report results</p>

<p>For using filters if your report type is sql see: <a href="http://docs.moodle.org/en/blocks/learnerscript/#Creating_a_SQL_Report" target="_blank">Creating a sql report tutorial</a></p>

<p>More help: <a href="http://docs.moodle.org/en/blocks/learnerscript/" target="_blank">Plugin documentation</a></p>';
$string['comp_columns'] = 'Columns';
$string['comp_columns_help'] = '<p>Here you can choose the different columns of your report depending on the type of report</p>

<p>More help: <a href="http://docs.moodle.org/en/blocks/learnerscript/" target="_blank">Plugin documentation</a></p>';

$string['coursecategories'] = 'Categories';
$string['filtercoursecategories'] = 'Select category';
$string['filtercoursecategories_summary'] = 'Filter courses by their any parent category';

$string['dbhost'] = "Db host";
$string['dbhostinfo'] = "Remote database host name (on which, we will be executing our SQL queries)";
$string['dbname'] = "Db name";
$string['dbnameinfo'] = "Remote database name (on which, we will be executing our sql queries)";
$string['dbuser'] = "Db username";
$string['dbuserinfo'] = "Remote database username (should have SELECT privileges on above db)";
$string['dbpass'] = "Db password";
$string['dbpassinfo'] = "Remote database password (for above username)";

$string['totalrecords'] = 'Total record count = {$a->totalrecords}';
$string['lastexecutiontime'] = 'Execution time = {$a} (Sec)';

$string['reportcategories'] = '1) Choose a remote report categories';
$string['reportsincategory'] = '2) Choose a report form the list';
$string['remotequerysql'] = 'Sql query';
$string['executeat'] = 'Execute at';
$string['executeatinfo'] = 'Moodle cron will run scheduled sql queries after selected time. Once in 24h';
$string['sharedsqlrepository'] = 'Shared sql repository';
$string['sharedsqlrepositoryinfo'] = 'Name of github account owner + slash + repository name';
$string['sqlsyntaxhighlight'] = 'Highlight sql syntax';
$string['sqlsyntaxhighlightinfo'] = 'Highlight sql syntax in code editor (CodeMirror js library)';
$string['datatables'] = 'Enable datatables js library';
$string['datatablesinfo'] = 'Datatables js library (column sort, fixed header, search, paging...)';
$string['reporttableui'] = 'Report table ui';
$string['reporttableuiinfo'] = 'Display the report table as: Simple scrollable html table, jQuery with column sorting or datatables js library (column sort, fixed header, search, paging...)';
$string['reportchartui'] = 'Report chart ui';
$string['reportchartuiinfo'] = 'Display the report chart as: Simple image graphs, using highcharts js library or d3 js library';

$string['email_subject'] = 'Subject';
$string['email_message'] = 'Message';
$string['email_send'] = 'Send';

$string['sqlsecurity'] = 'SQL security';
$string['sqlsecurityinfo'] = 'Disable for executing sql queries with statements for inserting data (github account owner + slash + repository name)';

$string['global'] = 'Global report';
$string['enableglobal'] = 'This is a global report (accesible from any course)';
$string['global_help'] = 'Global report can be accessed from any course in the platform just appending &courseid=MY_COURSE_ID in the report URL';
$string['disabletable'] = 'Disable table';
$string['enabletable'] = 'Disable for report table';

$string['crrepository'] = 'Reports repository';
$string['crrepositoryinfo'] = 'Remote shared repository with sample reports fully functional';
$string['importfromrepository'] = 'Import report from repository';
$string['repository'] = 'Reports repository';
$string['reportcreated'] = 'Report successfully created';
$string['usersincohorts'] = 'User who are member of a/several cohorts';
$string['usersincohorts_summary'] = 'Only the users who are members of the selected cohorts';
$string['displayglobalreports'] = 'Display global reports';
$string['displayreportslist'] = 'Display the reports list in the block body';

$string['usercompletion'] = 'User course completion status';
$string['usercompletionsummary'] = 'Course completion status';

$string['finalgradeincurrentcourse'] = 'Final grade in current course';
$string['legacylognotenabled'] = 'Legacy logs must be enabled.
 Go to Site administration / Plugins / Logging Enable the Legacy log and inside the log settings check log legacy data';

$string['scheduledreportsettings'] = 'Scheduled report settings';
$string['export'] = 'Export';
$string['schedule'] = 'Schedule';
$string['schedulereport'] = 'Schedule report';
$string['updatefrequency'] = 'Update frequency';
$string['scormtimespent'] = 'Scorm timespent';
$string['userscormtimespent'] = 'User scorm timeSpent';
$string['userquiztimespent'] = 'User quiz timespent';
$string['userbigbluebuttonbnspent'] = 'User bigbluebuttonbn timespent';
$string['daily'] = 'Daily';
$string['weekly'] = 'Weekly';
$string['monthly'] = 'Monthly';
$string['at'] = 'at';
$string['on'] = 'on';
$string['onthe'] = 'on the';
$string['reportname'] = 'Name';
$string['exportformat'] = 'Export format';
// Sarath added this string.
$string['users_data'] = 'User';
$string['PleaseSelectRole'] = 'Please select role';
$string['PleaseSelectUser'] = 'Please  select user';
$string['addmoreusers'] = '';
$string['viewusers'] = '';
$string['bulkupload'] = 'Bulkupload';
$string['uploadusers'] = 'Upload users';
$string['sample_excel'] = 'Sample excel';
$string['sample_csv'] = 'Sample csv';
$string['deletescheduledreport'] = 'Delete schedule report';
$string['delconfirm'] = 'Are you sure you want to delete this schedule';
$string['frequency'] = 'Frequency';
$string['Wednesday'] = 'Wednesday';
$string['Sunday'] = 'Sunday';
$string['Tuesday'] = 'Tuesday';
$string['Thursday'] = 'Thursday';
$string['Friday'] = 'Friday';
$string['Saturday'] = 'Saturday';
$string['Monday'] = 'Monday';
$string['dependency'] = 'Schedule';
// Sarath endeed.
$string['addschedulereport'] = 'Add schedule report';
$string['editscheduledreport'] = 'Edit scheduled report';
$string['exportfilesystem'] = 'Export to file system';
$string['exportfilesystempath'] = 'Export file system path';
$string['exportfilesystempathdesc'] = 'Absolute file system path to a writeable directory where reports can be exported and stored.';
$string['exporttoemail'] = 'Send report to mail';
$string['exporttoemailandsave'] = 'Save to file system and send email';
$string['exporttosave'] = 'Save to file system';
$string['exportfilesystemoptions'] = 'Export process';
$string['odsformat'] = 'Ods Format';
$string['pdfformat'] = 'Pdf Format';
$string['xlsformat'] = 'Excel Format';
$string['csvformat'] = 'Csv Format';
$string['scheduledreportmessage'] = '<p>Hi,</p>
<p>Here attached a copy of the \'{$a->reportname}\' report in {$a->exporttype}.</p>
<p>You can also view this report online at: {$a->reporturl}.</p>
<p>You are scheduled to receive this report {$a->schedule}.</p>

<p>{$a->nodata}</p>

<p>Regards,</p>
<p>{$a->admin}</p>';
$string['error:failedtoremovetempfile'] = 'Failed to remove temporary report export file';

/* Added by sowmya */
// Strings for Total Trained Reports.
$string['state'] = 'States';
$string['month'] = 'Training month';
$string['reports_view'] = "Reports view";
$string['startyear'] = 'Year';
$string['filter_year'] = 'Filter year';
$string['filteryear_summary'] = 'Filter year summary';
$string['trainertype'] = 'Trainer type';
$string['courseduration'] = 'Course duration';
$string['coursename'] = 'Course name';
/* settings strings added by anusha */
$string['learnerscriptreports'] = "Learnerscript reports";
$string['url'] = "URL";
$string['urlinfo'] = "Enter the url for access.";
$string['analytics_color'] = "Export header Color";
$string['analytics_color_desc'] = "Export header color for reports";
$string['logo'] = "Logo";
$string['logo_desc'] = "Logo for reports";
$string['filteryearnumeric_list'] = "2010,2011,2012,2013,2014,2015,2016,2017";
$string['viewschusers'] = 'View scheduled users list';
$string['combination'] = 'Combination';
$string['combinationsummary'] = 'A combination graph with multiple graphs';
$string['columnsummary'] = 'A column grpah with multiple values';
$string['listofcharts'] = 'List of charts';
$string['enabletabs'] = 'Enable tabs for charts';
$string['enabletabs_help'] = 'To enable and view charts in tabs format';

// Schedule BulkUpload strings.
$string['uploaddec'] = 'Upload list of users to schedule reports for a report.';
$string['uploaddec_help'] = 'Upload list of users to schedule reports for a report.';
$string['dailysampleinfo'] = ' This following details given example for daily schedule type only.';
$string['weeklysampleinfo'] = ' This following details given example for weekly schedule type only.';
$string['monthlysampleinfo'] = ' This following details given example for monthly schedule type only.';
$string['mandatoryinfo'] = ' All fields  are the mandatory fields. Please delete the rows are 2, 6, 14, 20 before uploading.';
$string['csvdelimiter'] = 'Csv delimiter';
$string['csvdelimiter_help'] = 'Csv delimiter of the csv file.';
$string['csvfileerror'] = 'There is something wrong with the format of the csv file. Please check the number of headings and columns match, and that the delimiter and file encoding are correct: {$a}';
$string['csvline'] = 'Line';
$string['encoding'] = 'Encoding';
$string['encoding_help'] = 'Encoding of the csv file.';
$string['rowpreviewnum'] = 'Preview rows';
$string['rowpreviewnum_help'] = 'Number of rows from the csv file that will be previewed in the next page. This option exists in
order to limit the next page size.';
$string['noschedule'] = 'Schedules not Available.';
$string['bulk_upload'] = 'Bulk upload';
$string['exporttofilesystem'] = 'Export to filesystem';
$string['nocourseexist'] = 'coursedoesnotexists';
$string['noreportexists'] = 'reportdoesnotexists';
$string['nocourseid'] = 'No such course id';
$string['badcomponent'] = 'badcomponent';
$string['noplugin'] = 'nosuchplugin';
$string['errorsaving'] = 'errorsaving';
$string['Pluginnotfound'] = 'Plugin not found';
$string['errorimporting'] = 'errorimporting';
$string['nodirectaccess'] = 'Direct access to this script is forbidden.';
$string['databaseconnectionerror'] = 'An error occurred while connecting to the database.';
$string['errorinfo'] = 'The error reported by the server was: ';
$string['sentemailforreport'] = 'Sent email for report ';
$string['noreportemailsent'] = 'No scheduled report email has been send';
$string['sendingemailreportfailed'] = 'Failed to send email for report';
$string['listofusers'] = 'List of users';
$string['reporttypeerror'] = 'report type error';
$string['altreportimage'] = 'Alt image text';
$string['reportheader'] = 'Report header';
$string['schedule_reports'] = 'Schedule reports';
$string['badpermissions'] = 'Bad permission';
$string['missingparam'] = '{$a} missing value.';
$string['selectroles'] = 'Select a role.';
$string['selectusers'] = 'Select users';
$string['sendemails'] = 'Send emails';
$string['fsearchuserfield'] = 'User fields';
$string['licensemissing'] = 'Licence key missing';
$string['totalcount'] = 'Total count';
$string['completed'] = 'Completed';
$string['progress'] = 'Progress';
$string['avggrade'] = 'Avg. grade';
$string['dynamiccolumn'] = 'Column';
$string['filteractivities'] = 'Activities';
$string['activities'] = 'Activities';
$string['filtermodules'] = 'Module';
$string['coursesoverview'] = 'Courses overview';
$string['activityinfo'] = 'Activity information';
$string['courseparticipation'] = 'Course participation';
$string['detailusercourseinfo'] = 'User courses information';
$string['listofactivities'] = 'List of activities in a course';
$string['courseactivitiesinfo'] = 'Course activity info';
$string['detailcourseinfo'] = 'Detailed course info';
$string['userlist'] = 'Users list';
$string['scormactivitiescourse'] = 'SCORM activities course columns';
$string['competencycompletion'] = 'Competency completion reports';
$string['competencycompletioncolumns'] = 'Competency completion columns';

$string['myquizs'] = 'My quizzes';
$string['modules'] = 'Modules';
$string['activitystatuscolumns'] = 'Activity status columns';
$string['id'] = 'id';
$string['percentage'] = 'Percentage';
$string['uploaderrors'] = 'Uploaded errors';
$string['filteractivities_summary'] = 'Activity filters';
$string['gradecolumns'] = 'User Activity grade columns';
$string['usercoursescolumns'] = 'User courses columns';
$string['listofactivitiescolumns'] = 'List of activities columns';
$string['courseactivitiesinfocolumns'] = 'Course activities information columns';
$string['coursesoverviewcolumns'] = 'Courses overview columns';
$string['filtermodules_summary'] = 'This filter shows a list of modules. Only one module can be selected at the same time';
$string['quizs'] = 'Quizs';
$string['statisticsreportsnotavailable'] = 'Reports not available';
$string['reportsnotavaliable'] = 'Reports not available';
$string['nodataavailable'] = 'No data available';
$string['graphnotfound'] = 'Graph not found';
$string['startdateerror'] = 'Start date should not more than current date.';
$string['enddateerror'] = 'Start date should not more than end date.';
$string['xandynotequal'] = 'Series column and Y-Axis should not equal.';
$string['supplyvalue'] = 'You must supply a value here.';
$string['deleteallconfirm'] = 'Are you sure, want to delete this?';
$string['eventcreate_report'] = 'Report created';
$string['eventupdate_report'] = 'Report updated';
$string['eventdelete_report'] = 'Report deleted';
$string['eventview_report'] = 'Report viewed';
$string['eventschedule_report'] = 'Reprot scheduled';
$string['spacevalidation'] = 'You must supply the value without space';
$string['save'] = 'Save';
$string['enable_exports'] = 'Enable exports';
$string['preview'] = 'Preview';
$string['courseaveragecolumns'] = 'Course average columns';
$string['noyaxis'] = 'Previously configured Y-axis elements <b>{$a}</b> not available now. Please reconfigure graph <br />';
$string['areaname'] = 'Previously configured area name <b>{$a}</b> not available now. Please reconfigure graph <br />';
$string['areavalue'] = 'Previously configured area value <b>{$a}</b> not available now. Please reconfigure graph <br />';
$string['noseries'] = 'Previously configured series <b>{$a}</b> not available now. Please reconfigure graph <br />';
$string['applypurify'] = 'Please select below required parameters to get report.';
$string['nolsinstance'] = 'LearnerScript report instances not configured in this page.';
$string['getreport'] = 'Get report';
$string['worldmap'] = 'World map';
$string['worldmapareaname'] = 'Area';
$string['worldmapareavalue'] = 'Value';
$string['worldmapsummary'] = 'A world map';
$string['activityfield'] = 'Activity field';
$string['activitytype'] = 'Activity type';
$string['finalgrade'] = 'Final grade';
$string['grademax'] = 'Max grade';
$string['resourcescolumns'] = 'Resources columns';
$string['resourcesaccessedcolumns'] = 'Resources accessed columns';
$string['resourcesaccessed'] = 'Resources accessed';
$string['badgename'] = 'Badge name';
$string['userbadges'] = 'My badges';
$string['timecreated'] = 'Time created';
$string['criteria'] = 'Criteria';
$string['issuername'] = 'Issuer name';
$string['description'] = 'Description';
$string['course'] = 'Course';
$string['recipients'] = 'Recipients';
$string['no_report_columns'] = 'Add columns in design to view the report.';
$string['manual'] = 'Help Manual';
$string['back_upload'] = 'Back To Upload';
$string['helpmanual'] = 'Download sample csv sheet and fill the field values in the format specified below.';
$string['uploadscheduletime'] = 'Upload schedule times';
$string['activitystats'] = 'Activity stats';
$string['gradepass'] = 'Pass grade';
$string['grademin'] = 'Min grade';
$string['gradedactivity'] = 'Graded activities';
$string['usercolumns'] = 'User columns';
$string['enabled'] = 'Enabled';
$string['disabled'] = 'Disabled';
$string['myscormcolumns'] = 'My scorm columns';
$string['scorm'] = 'Scorm columns';
$string['coursescolumns'] = 'Courses columns';
$string['filter_course'] = 'Select course';
$string['filter_user'] = 'Select user';
$string['filter_category'] = 'Select category';
$string['filter_role'] = 'Select role';
$string['filter_module'] = 'Select module';
$string['inprogress'] = 'In progress';
$string['notyetstarted'] = 'Not yet started';
$string['notattempted'] = 'Not attempted';
$string['nocomponent'] = 'Component not available';
$string['nouserrole'] = 'User not available for the selected role.';
$string['select_activity'] = 'Select activity';
$string['roleincourseconditionexpr'] = 'Roles condition';
$string['deleteconfirmation'] = 'Delete confirmation';
$string['dashboard'] = 'Learnerscript dashboard';
$string['courseviews'] = 'Course views columns';
$string['noofviews'] = 'Activity views columns';
$string['installreqplugins'] = 'Install {$a} plugin for Learnerscript';
$string['enablereqplugins'] = 'Enable {$a} plugin for Learnerscript';
$string['selectedfilter'] = '{$a} :';
$string['lsconfigtitle'] = 'Learnerscript configuration';
$string['lsreportsconfig'] = 'Configuring learnerScript';
$string['lsreportsconfigdone'] = 'LearnerScript reports already configured';
$string['limit'] = 'Limit';
$string['sortby'] = 'Sort by';
$string['sendmessage'] = 'Send message';
$string['messageconformation'] = 'Message sent';
$string['messageconformationsent'] = 'Message sent successfully';
$string['manageschusers'] = 'Manage scheduled users';
$string['adv'] = 'Adv';
$string['customdate'] = 'Start date - End date ';
$string['licensekeyrequired'] = 'License key is required';
$string['selectcalc'] = 'Select calc';
$string['selectordering'] = 'Select ordering';
$string['generatedmodel'] = 'Generated model';
$string['graphcannotbedeleted'] = 'You cannot delete last graph in this Report';
$string['report_users_help'] = '<p><strong>Description:</strong>Users report displays the details of the user course enrollments and their progress. This report enables the user to know the learner activities details</p>';
$string['report_courseactivities_help'] = '<p><strong>Description: </strong>This report gives the overview of the course activities and the activity types, learner status whether user has completed the activity or not, there grading, time spent and the number of views.</p>';
$string['report_courses_help'] = '<p><strong>Description:</strong> Course report helps user to understand the status and progress of functions which are carried within a course. (like users, completions, grading in a course and summary of activities)</p>';
$string['report_coursesoverview_help'] = '<p><strong>Description: </strong>Using this report user can see the list of courses which are enrolled by a particular learner and the course status by column in progress, completed and the overall grading achieved by the learner for that particular course. A learner can be select by using filter.</p>';
$string['report_gradedactivity_help'] = '<p><strong>Description:</strong> Graded activity information shows the list of activities which are graded items and there detailed gradings. </p>';
$string['report_usercourses_help'] = '<p><strong>Description: </strong>This report enables the user to have the complete details of the learners as list of learners, date of enrollment, completed assignments, quizzes, scorm activities count and the completion information, badges and Time Spent etc.</p><p><strong>Activity Progress:</strong>This graph shows only the completed assignments, quizzes, scorms, activities data.</p><p><strong>Top Learners:</strong>This report provides activities completion data and grade.</p>';
$string['report_courseprofile_help'] = '<p><strong>Description: </strong>This report provides the information like enrollments, activities, badges etc., of the each course . Using this report multiple courses information can be compared.</p>';
$string['report_courseviews_help'] = '<p><strong>Description: </strong>This report helps the user to know the number of hits by learners in each course.</p>';
$string['report_noofviews_help'] = '<p><strong>Description: </strong>This report helps the user to know the number of hits by learners in each activity.</p>';
$string['report_userprofile_help'] = '<p><strong>Description: </strong>This report provides the information like enrolled courses, grades, badges etc., of the user. Using this report multiple users information can be compared.</p>';
$string['report_useractivities_help'] = '<p><strong>Description: </strong>Using this report user can see a course activities of a learners, their gradings, activity access details, completed activities and the total time spent.</p>';

$string['lsreportconfigimport'] = 'LearnerScript config status';
$string['graphdeleted'] = 'Graph successfully deleted';
$string['reportschedule'] = 'Report scheduled successfully';
$string['deleteschedulereport'] = 'Schedule report deleted successfully';
$string['updateschedulereport'] = 'Schedule report updated successfully';
$string['graphcreated'] = 'Graph created successfully';
$string['graphupdated'] = 'Graph updated successfully';
$string['mailscheduled'] = 'Mails scheduled successfully, will be delivered in 5mins.';
$string['messagesent'] = 'Message sent successfully to ';
$string['graphdelete'] = 'Graph deleted successfully';
$string['installplugins'] = 'Install plugins.';
$string['notasssignedrole'] = 'You are not assigned to any role';
$string['columntype'] = 'Column type:';
$string['clickhere'] = 'Click here';
$string['tocontinue'] = 'to continue.';
$string['addgraph'] = 'Add graph';
$string['jumpto'] = 'Jump to';
$string['addusers'] = 'Add users';
$string['lsdashboard'] = 'Learnerscript dashboard';
// LearnerScript CLI.
$string['ls_cli_version'] = 'Learnerscript version : {$a}.';
$string['ls_cli_missing'] = 'Missing {$a} name.';
$string['ls_cli_create'] = '{$a} created successfully.';
$string['ls_cli_exists'] = '{$a} already exists.';
// LearnerScript privacy.

/* Schedule summary */
$string['privacy:metadata:scheduletablesummary'] = 'This stores the schedule task information.';
$string['privacy:metadata:reportid'] = 'The id of the report.';
$string['privacy:metadata:userid'] = 'The id of the user.';
$string['privacy:metadata:exporttofilesystem'] = 'The file system stores exported file.';
$string['privacy:metadata:exportformat'] = 'Format of the exported file.';
$string['privacy:metadata:frequency'] = 'Frequency.';
$string['privacy:metadata:schedule'] = 'Schedule.';
$string['privacy:metadata:nextschedule'] = 'Next schedule task.';
$string['privacy:metadata:roleid'] = 'Role id.';
$string['privacy:metadata:sendinguserid'] = 'Sending userid.';
$string['privacy:metadata:timecreated'] = 'Schedule time created';
$string['privacy:metadata:timemodified'] = 'Schedule updated time.';
$string['privacy:metadata:contextlevel'] = 'Context level.';

/* Course Time Summary */
$string['privacy:metadata:coursetimesummary'] = 'This stores the time that user spent on course.';
$string['privacy:metadata:courseuserid'] = 'Userid.';
$string['privacy:metadata:courseid'] = 'Course id';
$string['privacy:metadata:coursetimespent'] = 'User spent on course';
$string['privacy:metadata:coursetimecreated'] = 'Course created time';
$string['privacy:metadata:coursetimemodified'] = 'Course updated time';

/* Modules Time Summary */
$string['privacy:metadata:modulestimesummary'] = 'This stores the time that user spent on module.';
$string['privacy:metadata:moduserid'] = 'The id of the user';
$string['privacy:metadata:modcourseid'] = 'Course id';
$string['privacy:metadata:instanceid'] = 'Instance id';
$string['privacy:metadata:activityid'] = 'Activity id';
$string['privacy:metadata:modtimespent'] = 'User spent on module';
$string['privacy:metadata:modtimecreated'] = 'Module created time';
$string['privacy:metadata:modtimemodified'] = 'Module modified time';

/* User LMS access Summary */
$string['privacy:metadata:userlmsaccess'] = 'This stores the user LMS access during last week.';
$string['privacy:metadata:loggeduserid'] = 'The id of the user';
$string['privacy:metadata:logindata'] = 'Login data of the user';
$string['privacy:metadata:usertimecreated'] = 'Time created';
$string['privacy:metadata:usertimemodified'] = 'Time modified';

$string['resetingls'] = 'Resetting Learnerscript';
$string['usertimepsent'] = 'Learner script';
$string['contextid'] = 'Context level';
$string['closegraph'] = 'Close graph';
$string['lsreportconfigstatus'] = 'Learnerscript reports configuration status';
$string['report_coursecompetency'] = 'Course competency';
$string['coursecompetency'] = 'Course competency';
$string['report_coursecompetency_help'] = '<p><strong>Description: </strong>This report provides the course competency information.</p>';

$string['session'] = 'Session';
$string['sessions'] = 'Sessions';
$string['filtersession'] = 'Select session';
$string['filter_session'] = 'Select session';
$string['filtercohort'] = 'Select cohort';
$string['filter_cohort'] = 'Select cohort';
$string['cohort'] = 'Cohort';

$string['report_pendingactivities'] = 'Pending activities';
$string['pendingactivities'] = 'Pending activities';
$string['report_pendingactivities_help'] = 'Pending activities';
$string['report_needgrading'] = 'Need grading';
$string['needgrading'] = 'Need grading';
$string['report_needgrading_help'] = 'Need grading';
$string['views'] = 'Views';
$string['assignmentsubmitted'] = 'Assignment was submitted';
$string['daylate'] = 'Day late';
$string['achieved'] = 'Completed (achieved pass grade)';

$string['reportdoesnotexists'] = 'Report does not exist';
$string['nosuchoperator'] = 'No such operator';
$string['errorsavingreport'] = 'Error saving report';
$string['eventimport_report'] = 'Import report';
$string['nosuchcourseid'] = 'Course ID does not exists';
$string['cannotduplicate'] = 'Cannot duplicate the report';
$string['nocolumns'] = 'No columns available';
$string['noreportpermission'] = 'No report permissions';
$string['invalidscheduledreportid'] = 'Scheduled report ID is invalid';
$string['permissiondenied'] = 'Permissions denied';
$string['userlmsaccess'] = 'User LMS access';
$string['useridmissing'] = 'User ID missing';
$string['report_usercourses'] = 'User courses';
$string['report_userbadges'] = 'User badges';
$string['courseprofile'] = 'Course profile';
$string['noupcomingactivities'] = 'No upcoming activities';
$string['competencycolumns'] = 'Competency columns';
$string['useractivities'] = 'User activities';
$string['courseactivitiescolumns'] = 'Course activity columns';
$string['report_userbadges_help'] = '<p><strong>Description: </strong>This report helps the user to know about the badges achieved by the learners and the detailed overview of the badges. Using columns like course, issuer, created on, completion criteria for the course and the expiry date. </p>';
$string['report_grades_help'] = '<p><strong>Description: </strong>This report provides the users grade information of the selected course and activity.</p>';
$string['invaliduserid'] = 'Invalid user ID';

/*strings added*/
$string['configureplot'] = 'Configure plot';
$string['columnsdropdown'] = 'Columns dropdown';
$string['possiblecolumns'] = 'Possible selected columns';
$string['numerics'] = 'Numerics only';
$string['selectedcolumns'] = 'Selected columns';
$string['entervalues']  = 'Enter the values like <b>C1 and (C2 OR C3)</b>';
$string['createduser'] = 'The user with id {$a->userid} created New {$a->type} report with id {$a->objectid}.';
$string['emailsave'] = 'Email and save scheduled report to file.';
$string['schedulesave'] = 'Save scheduled report to file system only.';
$string['emailschedule'] = 'Email scheduled report';
$string['taskcomplete'] = 'Task completed';
$string['queryexception'] = 'Sql query wrong!';
$string['selectstatus'] = 'Select status';
$string['notcompleted'] = 'Not completed';
$strig['completed'] = 'Completed';
$string['select'] = '--Select--';
$string['notyeststarteds'] = 'Not yet started';
$string['reportnotavailable'] = 'Report Not Available.';
$string['selected'] = 'Selected';
$string['missingreportid'] = 'Missing report id.';
$string['viewmore'] = 'View more';
$string['sun'] = 'Sun';
$string['mon'] = 'Mon';
$string['tue'] = 'Tue';
$string['wed'] = 'Wed';
$string['thu'] = 'Thu';
$string['fri'] = 'Fri';
$string['sat'] = 'Sat';
$string['learnerscriptwidget'] = 'Learnerscript widget';
$string['learnerscripttiles'] = 'Learnerscript report tiles';
$string['pass'] = 'Pass';
$string['fail'] = 'Fail';
$string['sqlerroroccured'] = 'An sql error occurred: ';
$string['columnname'] = 'Column name';
$string['usersnotfound'] = 'Users not found';
$string['importreports'] = 'Import reports';
$string['learnerscriptreportconfiguration'] = 'LearnerScript reports configuration';
$string['alreadyimportstarted'] = 'Already import started';
$string['installenable'] = 'Install/Enable';
$string['pluginclick'] = 'plugin(s), click here to';
$string['installplugin'] = 'Install plugins';
$string['pluginclick'] = 'plugin(s), click here to';
$string['configurestarted'] = 'LearnerScript configuration already started';
$string['continue'] = 'Continue';
$string['reset'] = 'reset';
$string['import'] = 'import';
