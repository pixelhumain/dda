<?php
/**
 * ApiController.php
 *
 * azotlive application
 *
 * @author: Tibor Katelbach <tibor@pixelhumain.com>
 * Date: 18/07/2014
 */
class ApiController extends CommunecterController {

  protected function beforeAction($action)
  {
	  return parent::beforeAction($action);
  }

  public function actions() {
      return array(
      );
  }

}