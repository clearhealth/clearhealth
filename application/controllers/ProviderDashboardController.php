<?php
/*****************************************************************************
*       ProviderDashboardController.php
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


class ProviderDashboardController extends WebVista_Controller_Action {
    protected $_model;
    protected $_date = '';
	
	public function init() {
		$this->_date = date('Y-m-d');
	}

    public function indexAction()    {
		$db = Zend_Registry::get('dbAdapter');
		$sql = "select per.person_id, 
				CONCAT(per.last_name, ', ', per.first_name, ' ', per.middle_name, ' -> ', r.name) as provider 
				from appointment app
				inner join event ev on ev.event_id = app.event_id
				inner join person per on per.person_id = app.provider_id
				left join rooms r on r.id = app.room_id
				where ev.start between '" . date('Y-m-d',strtotime($this->_date)) ." 00:00:00' and '" . date('Y-m-d',strtotime($this->_date)) . " 23:59:59'  group by app.provider_id order by per.last_name";
		$sql = "select per.person_id, 
				CONCAT(per.last_name, ', ', per.first_name, ' ', per.middle_name) as provider 
				from appointments app
				inner join person per on per.person_id = app.providerId
				left join rooms r on r.id = app.roomId
				where app.start between '" . date('Y-m-d',strtotime($this->_date)) ." 00:00:00' and '" . date('Y-m-d',strtotime($this->_date)) . " 23:59:59'  group by app.providerId order by per.last_name";
		$stmt = $db->query($sql);
		$providersArray =  array();
		$providersArray[] = '';
		foreach ($stmt->fetchAll() as $row => $data) {
			$providersArray[$data['person_id']] = $data['provider'];
		}
		$this->view->providersArray = $providersArray;
		$this->view->currentPersonId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$this->view->selectableLayouts = $this->getSelectableLayouts();
		$this->render();
	}

	public function listAppointmentsAction() {
		$providerId = (int) $this->_getParam('providerId');
		$db = Zend_Registry::get('dbAdapter');
                $sql = "select *, appointment_id as appointmentId
                                from appointment app
                                inner join event ev on ev.event_id = app.event_id
				inner join person per on per.person_id = app.patient_id
				inner join patient pat on pat.person_id = app.patient_id
				where  ev.start between '" . date('Y-m-d',strtotime($this->_date)) ." 00:00:00' and '" . date('Y-m-d',strtotime($this->_date)) . " 23:59:59' and  app.provider_id = " . (int)$providerId . " order by ev.start ASC, ev.end ASC";
                $sql = "select *
                                from appointments app
				inner join person per on per.person_id = app.patientId
				inner join patient pat on pat.person_id = app.patientId
				where  app.start between '" . date('Y-m-d',strtotime($this->_date)) ." 00:00:00' and '" . date('Y-m-d',strtotime($this->_date)) . " 23:59:59' and  app.providerId = " . (int)$providerId . " order by app.start ASC, app.end ASC";
		$appointments = array();
		//trigger_error($sql,E_USER_NOTICE);
		foreach($db->query($sql)->fetchAll() as $row) {
			
			$appTime = date('h:i a',strtotime($row['start'])) . " - " . date('h:i a',strtotime($row['end']));
			$icon = Zend_Registry::get('baseUrl') . "img/sm-scheduled.png^Schedule";
			if ($row['appointmentCode'] == "CAN" || $row['appointmentCode'] =="NS") {
			$tooltip = ($row['appointmentCode'] == "CAN") ? __("Canceled") : __("No Show");	
			$icon = Zend_Registry::get('baseUrl') . "img/sm-cancelscheduled.png^" . $tooltip;
				
			}
			else if ($row['appointmentCode'] == "COM") {
				$icon = Zend_Registry::get('baseUrl') . "img/sm-completed.png^Completed";
			}
			else if ($row['appointmentCode'] == "CFM") {
				$icon = Zend_Registry::get('baseUrl') . "img/sm-confirmedscheduled.png^Confirmed";
	
			}
			else if ($row['walkin'] == 1) {
				$icon = Zend_Registry::get('baseUrl') . "img/sm-notscheduled.png^Walk In";
			}
			$arrivedText = ($row['arrived'] > 0) ? "<img src='" . $this->view->baseUrl . "/img/arrived.png' title='" . __("arrived") . "'/> " : "";
			$patientData = $arrivedText . $row['last_name'] . ", " . $row['first_name'] . " " . substr($row['middle_name'],0,1) . " #" . $row['record_number'];
			$arrivedFlag = ($row['arrived'] > 0) ? $this->view->baseUrl . "/routing.raw/list-patient-stations?personId=" . $row['patientId'] : ""; 
                        $appointments[] = array(
				"id" => $row['appointmentId'],
				"data" => array($row['person_id'],$appTime,$icon,$arrivedFlag,$patientData)
			);
                }
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
		$json->direct(array('rows' => $appointments));
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
                $sql = "select *
				from appointments app
				inner join person per on per.person_id = app.patient_id
				inner join patient pat on pat.person_id = app.patient_id
                                where
                                app.appointmentId = " . (int)$appointmentId ;
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
	$userTempArr = array();
	$globTempArr = array();
        if ( $userTemplates ) { 
            foreach ( $userTemplates as $userTempl ) {
                $userTempArr[] = array('id' => $userTempl->providerDashboardStateId, 'name' => $userTempl->name); 
            }
        } else $userTempArr = array();

        if ( count($globalTemplates) > 0 ) { 
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
	       public static function buildJSJumpLink($objectId,$signingUserId,$objectClass) {
                $objectClass = 'Provider'; // temporarily hard code objectClass based on MainController::getMainTabs() definitions
               //trigger_error("pip" . $objectId, E_USER_NOTICE);
                $js = parent::buildJSJumpLink($objectId,$objectId,$objectClass);
                return $js;
        }


}
