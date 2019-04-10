<?php
if (!defined('LICENSE'))
{
	exit('Access Denied');
}
function formatIcon()
{
	switch (TEMPLATE)
	{
		case 'cyborg.css':
			return 'icon-white';

		case 'slate.css':
			return 'icon-white';

		default:
			return '';
	}
}



/**
 * TableSorter Stylesheet Chooser
 *
 * Dark templates have a specific tablesorter stylesheet
 */
function formatTableSorter()
{
	switch (TEMPLATE)
	{
		case 'cyborg.css':
			return 'sorter-dark.css';

		case 'slate.css':
			return 'sorter-dark.css';

		default:
			return 'sorter.css';
	}
}



/**
 * Format the status
 *
 * Online / Offline -- Active / Inactive / Suspended / Pending -- Started / Stopped
 */
function formatStatus($status)
{
	switch ($status)
	{
		case 'Active':
			return "<span class=\"label label-success\">Active</span>";

		case 'Inactive':
			return "<span class=\"label\">Inactive</span>";

		case 'Suspended':
			return "<span class=\"label label-warning\">Suspended</span>";

		case 'Pending':
			return "<span class=\"label label-warning\">Pending</span>";

		case 'Online':
			return "<span class=\"label label-success\">Online</span>";

		case 'Offline':
			return "<span class=\"label label-important\">Offline</span>";

		case 'Started':
			return "<span class=\"label label-success\">Started</span>";

		case 'Stopped':
			return "<span class=\"label label-warning\">Stopped</span>";

		default:
			return "<span class=\"label\">Default</span>";
	}
}

?>
