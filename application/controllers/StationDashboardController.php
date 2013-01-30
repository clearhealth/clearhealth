<?php
/*****************************************************************************
*       StationDashboardController.php
*
*       Author:  ClearHealth Inc. (www.clear-health.com)        2009
*       
*       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
*       respective logos, icons, and terms are registered trademarks 
*       of ClearHealth Inc.
*
*       Though this software is open source you MAY NOT use our 
*       trademarks, graphics, logos and icons without explicit permission. 
*       Derivitive works MUST NOT be primarily identified using our 
*       trademarks, though statements such as "Based on ClearHealth(TM) 
*       Technology" or "incoporating ClearHealth(TM) source code" 
*       are permissible.
*
*       This file is licensed under the GPL V3, you can find
*       a copy of that license by visiting:
*       http://www.fsf.org/licensing/licenses/gpl.html
*       
*****************************************************************************/


class StationDashboardController extends WebVista_Controller_Action {
    protected $_model;
    protected $_date;
	public function init() {
		$this->_date = date('Y-m-d');
	}

    public function indexAction()    {
		$db = Zend_Registry::get('dbAdapter');
		//$stationsArray = Enumeration::getEnumArray("Routing","key","name");
		$stationsArray = Enumeration::getEnumArray(Routing::ENUM_PARENT_NAME);
		$stationsArray = array_merge(array('' => ''),$stationsArray);
		$this->view->stationsArray = $stationsArray;
        	$this->view->selectableLayouts = $this->getSelectableLayouts();
		$this->render();
	}

	public function listRoutingAction() {
		$stationId = $this->_getParam('stationId');
		$db = Zend_Registry::get('dbAdapter');
                $sql = "select *
                                from routing
				inner join person per on per.person_id = routing.personId
				inner join patient pat on pat.person_id = per.person_id
				where  routing.stationId = " . $db->quote($stationId) . " order by per.last_name ASC, per.first_name ASC";
		$patients = array();
		trigger_error($sql,E_USER_NOTICE);
		foreach($db->query($sql)->fetchAll() as $row) {
			
			$arrivedText = ($row['checkInTimestamp'] != "0000-00-00") ? "<img src='" . $this->view->baseUrl . "/img/checked-in.png' title='" . __("checked in") . "'/> " : "<img src='" . $this->view->baseUrl . "/img/arrived.png' title='" . __("arrived") . "'/> ";
			$patientData = $row['last_name'] . ", " . $row['first_name'] . " " . substr($row['middle_name'],0,1) . " #" . $row['record_number'];
			$arrivedFlag = $this->view->baseUrl . "/routing.raw/list-patient-stations?personId=" . $row['person_id'];
			$icon = ($row['checkInTimestamp'] == "0000-00-00 00:00:00") ? Zend_Registry::get('baseUrl') . "img/arrived.png^En Route" : Zend_Registry::get('baseUrl') . "img/checked-in.png^Checked In" ;
                        $patients[] = array(
				"id" => $row['routingId'],
				"data" => array($row['person_id'],$row['timestamp'],$icon,$arrivedFlag,$patientData)
			);
                }
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
		$json->direct(array('rows' => $patients));
	}

	function patientDemographicsByAppointmentIdAction() {
		$appointmentId = (int) $this->_getParam('appointmentId');
		$db = Zend_Registry::get('dbAdapter');
                $sql = "select *
				from appointment app
				inner join person per on per.person_id = app.patient_id
				inner join patient pat on pat.person_id = app.patient_id
                                where
                                app.appointment_id = " . (int)$appointmentId ;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
		$json->direct($db->query($sql)->fetchAll());
	}

    public function getDashboardWidgetList() {
        $urlArr = array();
        $dashcom = DashboardComponent::getIterAllComponents();
        foreach ( $dashcom as $dashComponent ) {
                //echo "<pre>" . print_r($dashComponent, true) ."</pre>";
            $urlArr[ $dashComponent->dashboardComponentId ] = $dashComponent->name; 
        }
        return $urlArr;
    }

    public function dwpopupAction() {
        $this->view->selectableWidgets  = $this->getDashboardWidgetList();
        $this->view->refreshInterval    = $this->getRefreshIntervalList();
        $this->render();
    }

    public function slpopupAction() {
        $this->render();
    }

    public function getCurrentUserId() {
        return 1000254; // hardcoded for now
    }

    public function getDashComContentAction($dashboardComponentId = 0) {
        try {
            $guid = ( $dashboardComponentId ) ? $dashboardComponentId : $this->_getParam('guid');
            $dashcom = DashboardComponent::populateWithGUID($guid);

            if ( !$dashboardComponentId ) {
                $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
                $json->direct($dashcom);
            } else return $dashcom;
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
            echo "Message: " . $e->getMessage() . "\n";
        }
    }

    public function renderPhpDashboardComponentAction() {
        try {
            $dashcom    = $this->getDashComContentAction($this->_getParam('dashboardComponentId'));
            $action     = $this->_getParam('actionn');
            if ( !$action && $action != 'refresh' && $action != 'render' ) $action = "render"; //default value
            $personId   = (int)$this->_getParam('personId');
            $classname  = $dashcom['systemName']."PHPDashboardComponent";

            eval($dashcom['content']);

            if ( $action == 'render' ) {
                $ref = new ReflectionClass($classname);
                $methods    = $ref->getMethods();
                $mtdRender  = new ReflectionMethod($classname, 'render');
                $mtdRefresh = new ReflectionMethod($classname, 'refresh');
            }

            $ob = new $classname;
            if ( $action == 'render' ) {
                $ob->render();
            } else if ( $action == 'refresh' ) {
                $ob->refresh($personId);
            }
            $ob = NULL;
        } catch (ReflectionException $e) {
            echo "ProviderDashboardController->renderPhpDashboardComponentAction: ".$e->getMessage();
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
            echo "Message: " . $e->getMessage() . "\n";
        }
    }

    public function saveTemplateLayoutAction() {
        try {
            // dcComponentsStr is a string in format cellId,GUID,refresh...repeated as many cells are
            $dcComponentsStr = $this->_getParam('dcComponentsStr');
            $globalLayout = (int)$this->_getParam('globalLayout');
            $nameLayout = $this->_getParam('nameLayout');
            $typeLayout = $this->_getParam('typeLayout');

            // do some modifications on the components string
            $explode = explode(',', $dcComponentsStr);
            $stateArr = array();
            while ( $cell = array_shift($explode) ) {
                $GUID = array_shift($explode);
                $refresh = array_shift($explode);
                $stateArr[$cell] = array('GUID' => $GUID, 'refresh' => $refresh);
            }

            $pov = new ProviderDashboardState();
            $pov->personId = $this->getCurrentUserId();
            $pov->facility = '1'; // hardcoded by now
            $pov->global = $globalLayout;
            $pov->state  = serialize($stateArr);
            $pov->name   = $nameLayout;
            $pov->layout = $typeLayout;
            $pov->persist();
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
            echo "Message: " . $e->getMessage() . "\n";
        }
    }


    public function getTemplatesLayoutDropdownAction() {
        $user = $this->getCurrentUserId();
        $userTemplates = ProviderDashboardState::getIterAllTemplates(0, $user);
        $globalTemplates = ProviderDashboardState::getIterAllTemplates(1);

        if ( $userTemplates ) { 
            foreach ( $userTemplates as $userTempl ) {
                $userTempArr[] = array('id' => $userTempl->providerDashboardStateId, 'name' => $userTempl->name); 
            }
        } else $userTempArr = array();

        if ( $globalTemplates ) { 
            foreach ( $globalTemplates as $globTempl ) {
                $globTempArr[] = array('id' => $globTempl->providerDashboardStateId, 'name' => $globTempl->name); 
            }
        } else $globTempArr = array();

        $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
        $json->suppressExit = true;
        $json->direct(array($userTempArr, $globTempArr));

    }


    public function getTemplateContentAction() {
        $templateId = (int)$this->_getParam('templateId');
        $templContent = ProviderDashboardState::populateWithProviderDashboardStateId($templateId);
        $templContent['state'] = unserialize($templContent['state']);
        $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
        $json->suppressExit = true;
        $json->direct($templContent);
    }
	public function appointmentsGridContextMenuAction() {
		header('Content-Type: application/xml;');
		$this->view->xmlHeader = '<?xml version="1.0" ?>';
		$this->view->stations = Enumeration::getEnumArray(Routing::ENUM_PARENT_NAME);
		$this->render();
	}

    private function getSelectableLayouts() {
        return array('1C' => __("One Column"), '2E' =>  __("2 Horizontal Rows"), '2U' =>  __("2 Vertical Columns"), 
        '3E' =>  __("3 Horizontal Rows"), '3J' =>  __("2-1 Vertical Columns"), '3L' =>  __("1-2 Vertical Columns"),
        '3T' =>  __("1-2 Horizontal Rows"), '3U' =>  __("2-1 Horizontal Rows"), '3W' =>  __("3 Vertical Columns"),
        '4H' =>  __("1-2-1 Vertical Columns"), '4L' =>  __("1-2-1 Horizontal Columns"), '4T' =>  __("1-3 Horizontal Rows"),
        '4U' =>  __("3-1 Horizontal Rows"), '5H' =>  __("1-3-1 Vertical Columns"), 
        '5I' =>  __("1-3-1 Horizontal Rows"), '6I' =>  __("1-4-1 Horizontal Rows"));
    }

    private function getRefreshIntervalList() {
        return array(
		"-1" => __("None"),
		"1200" => __("20 Minutes"),
		"600" => __("10 Minutes"),
		"300" => __("5 Minutes"),
		"120" => __("2 Minutes"),
		"60" => __("1 Minute")
	);
    }
/*	public function enumTestAction() {
		$enum = Enumeration::getEnumArray(Routing::ENUM_PARENT_NAME);
		var_dump($enum);
		exit;	
	}*/

}
