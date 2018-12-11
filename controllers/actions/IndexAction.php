<?php
class IndexAction extends CAction
{
    public function run($slug=null)
    {
    	$this->getController()->layout = "//layouts/empty";
    	if( @$id )
    	{
		 	echo $this->getController()->render("index",$params );
	 		
		} 
		else {
		 	echo $this->getController()->render("home" , array());
		}
    }
}