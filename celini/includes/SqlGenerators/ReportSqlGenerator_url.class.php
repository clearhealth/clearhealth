<?php
/**#@+
 * Load require file
 */
$loader->requireOnce('includes/SqlGenerators/ReportSqlGenerator.abstract.php');
/**#@-*/

/**
 * Generates a URL to be used inside an SQL query
 *
 * The tag inside the report to use this generator is:
 * <code>{url:controller=ControllerName&action=ActionName}</code>
 *
 * @author Sean Murphy <smurphy@uversainc.com>
 * @todo Move to phReporting module
 */
class ReportSqlGenerator_url extends ReportSqlGenerator
{
	/**
	 * {@inheritdoc}
	 */
	function sql($parameters = array()) {
		$action = isset($parameters['action']) ? $parameters['action'] : true;
		$controller = isset($parameters['controller']) ? $parameters['controller'] : true;
		$manager = isset($parameters['manager']) ? $parameters['manager'] : true;
		$default = isset($parameters['default']) ? $parameters['default'] : false;
		return Celini::link($action, $controller, $manager, $default);
	}
}

