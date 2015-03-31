<?php

class IndexController extends Zend_Controller_Action
{

	public function init() {
		$this->_helper->redirector->setUseAbsoluteUri(true);
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
	}

	public function preDispatch()
	{
		$admin_session_space = new Zend_Session_Namespace('admin_space');
		if (!isset($admin_session_space) || !$admin_session_space->admin) {
	  			$this->_redirect('/auth/login');
		}
		
	}
	public function indexAction(){
	    // ilosc uzytkowników
	    
	    $oUserModel = new Application_Model_DbTable_User();
	    $aUserModel = $oUserModel->countUser();
	    $this->view->user = $aUserModel;
	    // koniec ilosc uzytkownikow
	    
	    // ilosc news
	    $oContentModel = new Application_Model_DbTable_Content();
	    $aNewsModel = $oContentModel->countContent(1);
	    $this->view->news = $aNewsModel;
	    // ilosc ofert
	    $aOfferModel = $oContentModel->countContent(2);
	    $this->view->offer = $aOfferModel;
	    
	    // koniec
	    
	    $oNewsletterModel = new Application_Model_DbTable_Newsletter();
	    $aNewsletterModel = $oNewsletterModel->countEmailNewsletter();
	    $this->view->newsletter = $aNewsletterModel;
	    
	    /*
	    define('ga_email','mariusz24245@gmail.com');
	    define('ga_password','mariusz242425');
	    define('ga_profile_id','ga:53606967');
        
	    require APPLICATION_PATH.'/../library/My/gaapi/gapi.class.php';
	    require APPLICATION_PATH.'/../library/My/gaapi/googleanalytics.class.php';

	    $ga = new gapi(ga_email,ga_password);

	    $ga->requestReportData(ga_profile_id,array('browser','browserVersion'),array('pageviews','visits'));
        print_r($ga);
        */
        /*
	    try {
	        // create an instance of the GoogleAnalytics class using your own Google {email} and {password}
	        $ga = new GoogleAnalytics(ga_email,ga_password);

	        // set the Google Analytics profile you want to access - format is 'ga:123456';
	        $ga->setProfile(ga_profile_id);

	        // set the date range we want for the report - format is YYYY-MM-DD
	        //$ga->setDateRange('2012-04-01','2012-04-07');

	        // get the report for date and country filtered by Australia, showing pageviews and visits
	        $report = $ga->getReport(
	        array('dimensions'=>urlencode('ga:date,ga:country'),
    			'metrics'=>urlencode('ga:pageviews,ga:visits'),
    			'sort'=>'-ga:pageviews'
    			)
    			);

    			//print out the $report array
    			print "<pre>";
    			print_r($report);
    			print "</pre>";
    			 
	    } catch (Exception $e) {
	        print 'Error: ' . $e->getMessage();
	    }
	    */
	    $this->view->ga = $ga;
	}
}
