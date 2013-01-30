<?php
/*****************************************************************************
*       PatientWidgetsController.php
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


class PatientWidgetsController extends WebVista_Controller_Action
{
    function demographicsAction() {
        $this->view->message = "This is the demographics widget summary display";
        $this->render('demographics');
    }
    
    function medicationsAction() {
        $this->view->message = "This is the medications widget summary display";
        $this->render('medications');
    }

    function allergiesAction() {
        $this->view->message = "This is the allergies widget summary display";
        $this->render('allergies');
    }
    function vitalsAction() {
        $this->view->message = "This is the allergies widget summary display";
        $this->render('vitals');
    }
    function problemListAction() {
        $this->view->message = "This is the allergies widget summary display";
        $this->render('problem-list');
    }
}
