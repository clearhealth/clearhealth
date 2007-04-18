<?php
class C_Refreporting extends Controller
{
	/**
	 * Display a collection of grids that report requests in their various
	 * status.
	 *
	 * {@internal All of the DS's are initialized with the "key" value of the
	 *    enum refStatus.}
	 *
	 * @todo Consider making an array of grids to pass into the template
	 */
	function actionList() {
		
		$ajax =& Celini::ajaxInstance();
		$ajax->stubs[] = 'LockManager';
		
		$dsLoader =& new DatasourceFileLoader();
		$dsLoader->load('refRequestListByStatus_DS');
		
		$requested =& new refRequestListByStatus_DS(1);
		$requestedGrid =& new cGrid($requested);
		$requestedGrid->name = 'RequestedReferrals';
		
		$returned =& new refRequestListByStatus_DS(7);
		$returnedGrid =& new cGrid($returned);
		$returnedGrid->name = 'CancelledReferrals';
		
		$apptPending =& new refRequestListByStatus_DS(3);
		$apptPendingGrid =& new cGrid($apptPending);
		$apptPendingGrid->name = 'ApptPendingReferrals';
		
		$eligPending =& new refRequestListByStatus_DS(2);
		$eligPendingGrid =& new cGrid($eligPending);
		$eligPendingGrid->name = 'EligPendingReferrals';
		
		$apptConfirmed =& new refRequestListByStatus_DS(4);
		$apptConfirmedGrid =& new cGrid($apptConfirmed);
		$apptConfirmedGrid->name = 'ApptConfirmedReferrals';
		
		
		$this->view->assign_by_ref('requestedGrid', $requestedGrid);
		$this->view->assign_by_ref('returnedGrid', $returnedGrid);
		$this->view->assign_by_ref('apptPendingGrid', $apptPendingGrid);
		$this->view->assign_by_ref('eligPendingGrid', $eligPendingGrid);
		$this->view->assign_by_ref('apptConfirmedGrid', $apptConfirmedGrid);
		
		return $this->view->render('list.html');
	}
}

