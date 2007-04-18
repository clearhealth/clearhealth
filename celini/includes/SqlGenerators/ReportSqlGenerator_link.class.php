<?php
/**#@+
 * Load require file
 */
$loader->requireOnce('includes/SqlGenerators/ReportSqlGenerator.abstract.php');
/**#@-*/

/**
 * Generates an HTML link to be used as a given column inside an SQL query
 *
 * The tag inside the report to use this generator is:
 * <code>{link:controller=ControllerName&action=ActionName&columnName=column_name&id=column_name&field=id}</code>
 *
 * The "id" parameter is optional, and if not specified will default to the columnName.
 * The "field" parameter is optional, and if not specified will default to "id".
 *
 * @author Travis Swicegood <tswicegood@uversainc.com>
 * @todo Move to phReporting module
 */
class ReportSqlGenerator_link extends ReportSqlGenerator
{
	/**
	 * {@inheritdoc}
	 */
	function sql($parameters = array()) {
		if (!isset($parameters['id'])) {
			$parameters['id'] = $parameters['columnName'];
		}
		if (!isset($parameters['field'])) {
			$parameters['field'] = 'id';
		}
		$action = isset($parameters['action']) ? $parameters['action'] : true;
		$controller = isset($parameters['controller']) ? $parameters['controller'] : true;
		$manager = isset($parameters['manager']) ? $parameters['manager'] : true;
		$default = isset($parameters['default']) ? $parameters['default'] : false;
		return 'CONCAT("<a href=\"' . Celini::link($action, $controller, $manager, $default) . $parameters['field'].'=", ' . $parameters['id'] . ', "\">", ' . $parameters['columnName'] . ', "</a>")'; 
	}
}

