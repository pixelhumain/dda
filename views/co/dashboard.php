<?php 

$cssAnsScriptFilesModule = array( 
	'/js/uiCoop.js'
);
HtmlHelper::registerCssAndScriptsFiles($cssAnsScriptFilesModule, Yii::app()->getModule("dda")->getAssetsUrl() );

	$isAdmin = Authorisation::isElementAdmin(@$parentId, @$parentType, Yii::app()->session['userId']);

	$allElements = array();
	if(@$organizations) $allElements[] = $organizations;
	if(@$projects) $allElements[] = $projects;

	$countProposal = 0; $countResolution = 0; $countAction = 0;
	foreach($allElements as $elements){ 
		foreach($elements as $elem){ 

			foreach(array("tovote", "amendable") as $s) 
				foreach($elem[$s]["proposalList"] as $k => $x)
					$countProposal++;

			foreach(array("resolved") as $s) 
				foreach($elem[$s]["resolutionList"] as $k => $x)
					$countResolution++;

			foreach(array("actions") as $s) 
				foreach($elem[$s]["actionList"] as $k => $x)
					$countAction++;
		}
	}
?>

<style>
	.menu-dashboard-dda .btn-link{
		padding:10px;
		margin:0px;
		letter-spacing: 0px;
	}
	.menu-dashboard-dda .btn-link.no-border{
		border:0px !important;
	}

	.menu-dashboard-dda .btn-link:hover,
	.menu-dashboard-dda .btn-link:active,
	.menu-dashboard-dda .btn-link:focus{
		background-color: #229296 !important;
		color:#FFF;
		text-decoration: none;
	}
	
	.menu-dashboard-dda .btn-link:hover .topbar-badge,
	.menu-dashboard-dda .btn-link:active .topbar-badge,
	.menu-dashboard-dda .btn-link:focus .topbar-badge{
		background-color: #FFF !important;
		color:#229296;
		text-decoration: none;
	}
	
	.focus label.info{
		text-transform: none;
		font-size:11px;
		font-weight: 200;
		color: gray;
		letter-spacing: 0px;
	}

</style>

<?php //render of modal for coop spaces 
		// $params = array(  "element" => @$elem, 
  //                           "type" => @$type, 
  //                           "edit" => @$edit,
  //                           "thumbAuthor"=>@$thumbAuthor,
  //                           "openEdition" => @$openEdition,
  //                           "iconColor" => @$iconColor
  //                       );

  //   	$this->renderPartial('dda.views.co.pod.modals', $params ); 
    ?>
<div class="modal-body col-lg-12 col-md-12 col-sm-12 padding-15">
	  <ul id="menuCoop" class="menuCoop col-lg-2 col-md-3 col-sm-3">
	</ul>
	<div id="main-coop-container" class="col-lg-10 col-md-9 col-sm-9"></div>
</div>
<div class="col-xs-12 no-padding menu-dashboard-dda shadow2 font-montserrat" style="margin-top:70px">
	<button class="col-xs-4 btn btn-link no-border" data-type-coop="proposals">
		<i class="fa fa-gavel"></i>
		<span class="dda-proposal-count topbar-badge badge animated bounceIn bg-turq">
			<?php echo $countProposal; ?>
		</span><br>propositions
	</button>
	<button class="col-xs-4 btn btn-link" data-type-coop="resolutions">
		<i class="fa fa-certificate"></i>
		<span class="dda-proposal-count topbar-badge badge animated bounceIn bg-turq">
			<?php echo $countResolution; ?>
		</span><br>résolutions
	</button>
	<button class="col-xs-4 btn btn-link" data-type-coop="actions">
		<i class="fa fa-ticket"></i>
		<span class="dda-proposal-count topbar-badge badge animated bounceIn bg-turq">
			<?php echo $countAction; ?>
		</span><br>actions
	</button>
</div>

<div class="col-xs-12 no-padding" id='scroll-dashboard-dda'>

	<?php
		foreach($allElements as $elements){ 
			foreach($elements as $elem){ 

				?>
				<div class="col-xs-4">
				<?php
				$color = Element::getColorIcon($elem["type"]);
				$icon = Element::getFaIcon($elem["type"]);

				$countProposal = 0; $countResolution = 0; $countAction = 0;
				foreach(array("tovote", "amendable") as $thisStatus)
					foreach($elem[$thisStatus]["proposalList"] as $key => $x)
						$countProposal++;

				foreach(array("resolved") as $thisStatus)
					foreach($elem[$thisStatus]["resolutionList"] as $key => $x)
						$countResolution++;

				foreach(array("actions") as $thisStatus)
					foreach($elem[$thisStatus]["actionList"] as $key => $x)
						$countAction++;

				$focusType = "";
				if($countProposal>0) $focusType .= "sub-proposals ";
				if($countResolution>0) $focusType .= "sub-resolutions ";
				if($countAction>0) $focusType .= "sub-actions ";
				echo "<h5 class='col-xs-12 no-padding focus ".$focusType."'><a href='javascript:;' class=' letter-".$color." elementTitle' data-id='".$elem["id"]."'>".
						"<hr class='margin-top-5 margin-bottom-10'>".
						"<i class='fa fa-angle-down margin-left-15'></i> ".
						"<i class='fa fa-".$icon."'></i> ".
						$elem["name"]."</a>".
						" <a href='javascript:;' data-id='".$elem["id"]."' data-type='".$elem["type"]."' class=' openCoop pull-right'><i class='fa fa-external-link'></i></a>".
						"<hr class='margin-top-10 margin-bottom-5'>".
					 "</h5>".
					 '<div class="" id="data'.$elem["id"].'">';

				foreach(array("tovote", "amendable") as $thisStatus){ 
					foreach($elem[$thisStatus]["proposalList"] as $key => $proposal){ //var_dump($proposal1);exit;
						$post["status"] = $thisStatus;
						$this->renderPartial('proposalLi', 
												   array("proposal"=>$proposal,
												   		 "thisStatus" => $thisStatus,
												   		 "isAdmin" => $isAdmin,
												   		 "post" => @$post));
					} //end foreach
				} //end foreach


				foreach(array("resolved") as $thisStatus){ 
					foreach($elem[$thisStatus]["resolutionList"] as $key => $resolution){ //var_dump($proposal1);exit;
						$post["status"] = $thisStatus;
						$this->renderPartial('resolutionLi', 
												   array("resolution"=>$resolution,
												   		 "thisStatus" => $thisStatus,
												   		 "isAdmin" => $isAdmin,
												   		 "post" => @$post));
					} //end foreach
				} //end foreach


				foreach(array("actions") as $thisStatus){ 
					foreach($elem[$thisStatus]["actionList"] as $key => $action){ //var_dump($proposal1);exit;
						$post["status"] = $thisStatus;
						$this->renderPartial('actionLi', 
												   array("action"=>$action,
												   		 "thisStatus" => $thisStatus,
												   		 "auth" => false,
												   		 "isAdmin" => $isAdmin,
												   		 "post" => @$post));
					} //end foreach
				} //end foreach
				?>
				</div></div>
				<?php
			} //end foreach
		} //end foreach
	?>
</div>

<script type="text/javascript">
var contextData = {  
  "name": "<?php echo $elem['name'] ?>",
  "type": "<?php echo $elem['type'] ?>",
  "slug": "<?php echo $elem['slug'] ?>",
  "typeSig": "<?php echo $elem['type'] ?>",
  "id": "<?php echo $elem['id'] ?>"
};
jQuery(document).ready(function() { 
	
	uiCoop.initBtnLoadDataPreview();

	$(".menu-dashboard-dda .btn-link").click(function(){
		var type = $(this).data("type-coop");
		$(".focus").hide(200);
		$(".focus.sub-"+type).show(200);
		$("#scroll-dashboard-dda").scrollTop(0);
	});

	$("#list-dashboard-dda").off().mouseleave(function(){
		$("#dropdown-dda").removeClass("open");
		$("#list-dashboard-dda").off();
	});

	$(".elementTitle").off().click(function(){
		$("#data"+$(this).data('id')).toggleClass("hidden");
	});
	$(".openCoop").off().click(function(){
		contextData.id = $(this).data('id');
		contextData.type = $(this).data('type');
		uiCoop.loadCoop();
	});


	
});

function loadNewsStream(isLiveBool){

}

</script>