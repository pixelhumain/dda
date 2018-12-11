<?php
/**
 * CoController.php
 *
 * Cocontroller always works with the PH base 
 *
 * @author: Tibor Katelbach <tibor@pixelhumain.com>
 * Date: 14/03/2014
 */
class CoController extends CommunecterController {


    protected function beforeAction($action) {
        //parent::initPage();
		return parent::beforeAction($action);
  	}

  	public function actions()
	{
	    return array(
	        'index'  => 'dda.controllers.actions.IndexAction',
	        'getcoopdata' => 'dda.controllers.actions.GetCoopDataAction',
	        'savevote' => 'dda.controllers.actions.SaveVoteAction',
	        'deleteamendement' => 'dda.controllers.actions.DeleteAmendementAction',
	        'getmydashboardcoop' => 'dda.controllers.actions.GetMyDashboardCoopAction',
	        'previewcoopdata' => 'dda.controllers.actions.PreviewCoopDataAction',
	    );
	}

}

//co2/cooperation/getcoopdata > dda/co/getcoopdata