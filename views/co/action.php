<?php 
	//var_dump($action); exit;
	$auth = Authorisation::canParticipate(Yii::app()->session['userId'], $action["parentType"], $action["parentId"]);
	$parentRoom = Room::getById($action["idParentRoom"]);
	$author = Person::getById(@$action["creator"]);
	$profilThumbImageUrl = Element::getImgProfil($author, "profilThumbImageUrl", $this->module->assetsUrl);
	$myId = Yii::app()->session["userId"];

	if(isset(Yii::app()->session['userId'])){
		$me = Element::getByTypeAndId("citoyens", Yii::app()->session['userId']);
		$myRoles = @$me["links"]["memberOf"][@$action["parentId"]]["roles"] ? 
				   @$me["links"]["memberOf"][@$action["parentId"]]["roles"] : array();
	}else{
		$myRoles = array();
	}	
	
	//lock access if the user doesnt have the good role
	$accessRoom = @$parentRoom ? Room::getAccessByRole($parentRoom, $myRoles) : ""; 
	if($accessRoom == "lock") exit;

?>
<div class="pageContent">
	
	<div class="portfolio-modal modal fade" id="openModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-content">
        <div class="close-modal" data-dismiss="modal">
            <div class="lr">
                <div class="rl">
                </div>
            </div>
        </div>
        <div class="col-sm-12 container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="modal-header text-dark">
                        <h3 class="modal-title text-center" id="ajax-modal-modal-title">
                            <i class="fa fa-angle-down"></i> <i class="fa " id="ajax-modal-icon"></i> 
                        </h3>
                    </div>
                    
                    <div id="ajax-modal-modal-body" class="modal-body">
                        
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 text-center" style="margin-top:50px;margin-bottom:50px;">
            <hr>
            <a href="javascript:" style="font-size: 13px;" type="button" class="" data-dismiss="modal">
            <i class="fa fa-times"></i> <?php echo Yii::t("common","Back") ?>
            </a>
        </div>
    </div>
</div>
</div>

<?php if(@$access=="deny"){ ?>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h5 class="padding-left-10 letter-red">
			<i class="fa fa-ban"></i> Vous n'êtes pas autorisé à accéder à ce contenu		  	
		</h5>
		<h5 class="padding-left-10 letter-red">
			<small>Devenez membre ou contributeur</small>  	
		</h5>
	</div>
<?php exit; } ?>


<div class="col-lg-7 col-md-6 col-sm-6 pull-left margin-top-15">
	<h4 class="letter-turq load-coop-data title-room" 
  		data-type="room" data-dataid="<?php echo @$action["idParentRoom"]; ?>">
  		<i class="fa fa-connectdevelop"></i> <?php echo @$parentRoom["name"]; ?>
	</h4>
	<h3 class=" padding-10  radius-5 col-xs-12 bg-turq text-white text-bold"  >	
		<?php 
			$str = "";
			if(@$action["name"]) $str = "<i class='fa fa-hashtag'></i> ".@$action["name"];
			if(@$action["parentSurvey"]){
				// $url = Yii::app()->createUrl('/survey/co/action/id/'.$action["parentSurvey"]["id"].'/action/'.(String) $action["_id"]);

				$url = Yii::app()->createUrl('/survey/co/action/id/'.(String) $action["_id"]);
				$str = '<a href="'.$url.'" target="_blank" >'.$str.'</a>';
			} 
			echo $str;

			?>
	</h3>
</div>

<div class="col-lg-5 col-md-6 col-sm-6">
	<button class="btn btn-default pull-right margin-left-5 margin-top-10 tooltips" 
				data-original-title="Fermer cette fenêtre" data-placement="bottom"
				id="btn-close-action">
		<i class="fa fa-times"></i>
	</button>
	<?php if($auth && @$action["creator"] == Yii::app()->session['userId']){ ?>
		 <div class="pull-right dropdown">
		  <button class="btn btn-default margin-left-5 margin-top-10" data-toggle="dropdown">
			<i class="fa fa-cog"></i> options
		  </button>
		  <ul class="dropdown-menu">
		    <li><a href="javascript:" id="btn-edit-action" 
		    		data-id-action="<?php echo $action["_id"]; ?>">
		    	<i class="fa fa-pencil"></i> Modifier l'action
		    	</a>
		    </li>
		    <li><a href="javascript:" class="btn-option-status-action" 
		    		data-id-action="<?php echo $action["_id"]; ?>"
		    		data-status="disabled">
		    	<i class="fa fa-times"></i> Désactiver l'action
		    	</a>
		    </li>
		    <!-- <li><hr class="margin-5"></li> -->
		    <li><a href="javascript:" class="btn-option-status-action" 
		    		data-id-action="<?php echo $action["_id"]; ?>"
		    		data-status="done">
		    		<i class="fa fa-trash"></i> Fermer l'action
		    	</a>
		    </li>
		  </ul>
		</div> 
	<?php } ?>
	<button class="btn btn-default pull-right margin-left-5 margin-top-10 tooltips" 
				data-original-title="Actualiser les données" data-placement="bottom"
				data-id-action="<?php echo $action["_id"]; ?>"
				id="btn-refresh-action"><i class="fa fa-refresh"></i></button>

	<button class="btn btn-default pull-right margin-left-5 margin-top-10 btn-extend-action tooltips" 
				data-original-title="Agrandir l'espace de lecture" data-placement="bottom">
		<i class="fa fa-long-arrow-left"></i>
	</button>
	<button class="btn btn-default pull-right margin-left-5 margin-top-10 hidden btn-minimize-action tooltips" 
				data-original-title="Réduire l'espace de lecture" data-placement="bottom">
		<i class="fa fa-long-arrow-right"></i>
	</button>
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pull-left">

<?php
	//if no assignee , no startDate no end Date
    $statusLbl = Yii::t("rooms", @$post["status"]);
    //if startDate passed, or no startDate but has end Date
    if(@$action["status"] == "todo"){
	    if( (bool)strtotime(@$action["startDate"]) == FALSE && (bool)strtotime(@$action["endDate"]) == FALSE ){
	    	$action["status"] = "nodate";	
	    } 
	    else if( strtotime(@$action["startDate"]) > time() )
	      	$action["status"] = "startingsoon";
	    else if( ( isset($action["startDate"]) && strtotime($action["startDate"]) <= time() )  || 
	    		   ( !@$action["startDate"] && @$action["endDate"] ) ) {
	        $action["status"] = "progress";
	      	if( strtotime(@$action["endDate"]) < time()  )
	        	$action["status"] = "late";
	      
	    } 
	}

?>			

	<hr style="margin-top:5px;">
	<h4 class="no-margin status-breadcrum pull-left">
		
		<small><i class="fa fa-certificate"></i></small>
				
		<?php if(@$action["status"] == "todo"){ ?>
			<span class="letter-green underline"><?php echo Yii::t("cooperation", $action["status"]); ?></span>	
		<?php }else if(@$action["status"] == "late"){ ?>
			<span class="letter-orange underline"><?php echo Yii::t("cooperation", $action["status"]); ?></span>	
		<?php }else if(@$action["status"] == "progress"){ ?>
			<span class="letter-green underline"><?php echo Yii::t("cooperation", $action["status"]); ?></span>	
		<?php }else if(@$action["status"] == "startingsoon"){ ?>
			<span class="letter-green underline"><?php echo Yii::t("cooperation", $action["status"]); ?></span>	
		<?php }else if(@$action["status"] == "nodate"){ ?>
			<span class="letter-orange underline"><?php echo Yii::t("cooperation", $action["status"]); ?></span>	
		<?php }else if(@$action["status"] == "disabled"){ ?>
			<span class="letter-orange underline"><?php echo Yii::t("cooperation", $action["status"]); ?></span>	
		<?php }else{ ?>
			<small><?php echo Yii::t("cooperation", "todo"); ?></small>	
		<?php } ?>

		<small><i class="fa fa-chevron-right"></i></small>
		<?php if(@$action["status"] == "done"){ ?>
			<span class="letter-red underline"><?php echo Yii::t("cooperation", $action["status"]); ?></span>
		<?php }else{ ?>
			<small><?php echo Yii::t("cooperation", "done"); ?></small>
		<?php } ?>
	</h4>

	<!-- <hr> -->
	<?php if(@$action["creator"]){ ?>
		<label class="bg-light pull-right">
			<small> <?php echo Yii::t("cooperation","Author"); ?> : </small>
			<img class="img-circle" id="menu-thumb-profil" 
	         width="30" height="30" src="<?php echo $profilThumbImageUrl; ?>" alt="image" >
			<a href="#page.type.citoyens.id.<?php echo $action["creator"]; ?>" class="lbh lbl-author-coop elipsis">
				<?php echo $author["username"]; ?></a><?php if($myId == $action["creator"]){ ?>
			<?php } ?>
		</label>
	<?php } ?>
		
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><hr></div>
	<!-- <label class=""><i class="fa fa-bell"></i> Status : 
		<small class="letter-<?php echo Cooperation::getColorCoop($action["status"]); ?>">
			<?php echo Yii::t("cooperation", $action["status"]); ?>
		</small>
	</label>
	<hr> -->

	<h4 class="no-margin pull-left">
		<i class="fa fa-clock-o"></i> Action à réaliser 
		<?php
			if( @$action["startDate"] && (bool)strtotime(@$action["startDate"]) != FALSE ){
		?> 
			du <small class="letter-blue"><?php echo date('d/m/Y', strtotime($action["startDate"])); ?>
		<?php } ?>
		
		<?php if( @$action["endDate"] && (bool)strtotime(@$action["endDate"]) != FALSE ){ ?> 
			au <?php echo date('d/m/Y', strtotime($action["endDate"])); ?></small>
		<?php } ?>		
	</h4>

	<?php if(@$action["idParentResolution"]){ $reso = Resolution::getById($action["idParentResolution"]); ?>
		<hr>
		<h5>
			Cette action est liée à la résolution suivante : 
			<a href="javascript:;"
			 class="load-coop-data" data-type="resolution" data-dataid="<?php echo $action['idParentResolution']; ?>">
				<i class="fa fa-hashtag"></i> 
				<?php echo @$reso["title"] ? @$reso["title"] : substr(@$reso["description"], 0, 150); ?>
			</a>
		</h5>
	<?php } ?>
</div>


<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-top-25">
	
	<div class=" col-xs-12" id="container-text-action" style="padding:15px 0px 0px 40px;background-color: #eee" ><?php echo @$action["description"]; ?></div>
	<div class="col-xs-12"  >	
		<?php if(@$action["tags"]){ ?>
			<br> <b>Tags : </b>
			<?php foreach($action["tags"] as $key => $tag){ ?>
				<span class="label label-danger margin-right-15">#<?php echo $tag; ?></span>
			<?php } ?>	
			
		<?php } ?>
	</div>
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-top-25" >

	<?php if(@$action["urls"]){ ?>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
		<h4 class=""><i class="fa fa-angle-down"></i> Liens externes</h4>
		<?php foreach($action["urls"] as $key => $url){ ?>
			<a href="<?php echo $url; ?>" target="_blank" class="btn btn-default bg-white shadow2 margin-bottom-5">
				<i class="fa fa-external-link"></i> <?php echo $url; ?>
			</a>
		<?php } ?>
		<hr>	
	</div>
	<?php } ?>



		<hr>

	<?php if( $auth && !@$action["links"]["contributors"][Yii::app()->session['userId']]  ){ ?>
		<button class="btn btn-default letter-green bold pull-right btn-assignee" 
				data-target="#modalAssignMe" data-toggle="modal">
			<i class="fa fa-handshake-o"></i> 
			<?php echo Yii::t("rooms","I'll Do it") ?>
	   	</button>
	   	<?php 

	   		$urlLink = "#element.invite.type.".Action::COLLECTION.".id.".(string)$action["_id"];
	   	?>
	   	<!-- <a 	href="<?php echo $urlLink ; ?>" 
			class="btn btn-default letter-green bold btn-assignee"
			data-placement="bottom" 
			data-original-title="lala" > 
	        <i class="fa fa-user-plus "></i><?php echo Yii::t("common",'Invite people') ; ?>
	    </a><br/> -->
	    <button class="btn btn-default letter-green bold  btn-assignee" 
				data-target="#modalLinkAction" data-toggle="modal">
			<i class="fa fa-user-plus "></i> <?php echo Yii::t("common",'Assigner une personne') ; ?>
	   	</button><br/>
	<?php }else if( $auth ){ ?>
		<h5 class="letter-green pull-right"><i class="fa fa-check"></i> Vous participez à cette action</h5>
		<button class="btn btn-default letter-green bold  btn-assignee" 
				data-target="#modalLinkAction" data-toggle="modal">
			<i class="fa fa-user-plus "></i> <?php echo Yii::t("common",'Assigner une personne') ; ?>
	   	</button><br/>
	<?php }	?>

	<?php if( @$action["links"]["contributors"] ) {	?>
		<h4 class="pull-left">
			<i class="fa fa-angle-down"></i> <i class="fa fa-group"></i> Ils participent à cette action
		</h4>
	<?php }	?>
		


	<?php if( @$action["links"]["contributors"] ) {	?>
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-top-15">
		<?php foreach ($action["links"]["contributors"] as $id => $att) { // var_dump($att);
				$contrib = Element::getByTypeAndId($att["type"], $id); ?>
				<div class="col-lg-4 col-md-4 col-sm-6 link-assignee ">
					<a href="#page.type.citoyens.id.<?php echo $id; ?>" 
						class="elipsis shadow2 lbh">
						<img width="40" height="40"  alt="image" class="img-circle tooltips" 
							 <?php if(@$contrib['profilThumbImageUrl']){ ?>
							 src="<?php echo Yii::app()->createUrl('/'.$contrib['profilThumbImageUrl']) ?>" 
							 <?php } ?>
							 data-placement="top" data-original-title="<?php echo @$contrib['name']; ?>">
							<span class="">
								<?php if(false && @$att["isAdmin"]==true){ ?>
									<i class="fa fa-user-secret letter-red"></i>
								<?php } ?>
								<b><?php echo @$contrib['name']; ?></b>
							</span>
					</a>
				</div>
		<?php } ?>
		</div>
	
	<?php }else{ ?>
		<h4><i class="fa fa-ban"></i> <i class="fa fa-group"></i> Aucun participant</h4>
	<?php }	?>

</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-top-50 padding-bottom-5">
	<h4 class="text-center">
		<i class="fa fa-comments fa-2x margin-bottom-10"></i><br>Discussion<br>
		<i class="fa fa-angle-down"></i>
	</h4>
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-top-10 margin-bottom-50" id="comments-container"><hr></div>


<script type="text/javascript">
	var parentTypeElement = "<?php echo $action['parentType']; ?>";
	var parentIdElement = "<?php echo $action['parentId']; ?>";
	var idAction = "<?php echo $action['_id']; ?>";
	var idParentRoom = "<?php echo $action['idParentRoom']; ?>";
	var msgController = "<?php echo @$msgController ? $msgController : ''; ?>";

	currentRoomId = idParentRoom;

	jQuery(document).ready(function() { 
		$("#container-text-action").html(dataHelper.markdownToHtml($("#container-text-action").html()) )
		uiCoop.initUIAction();
		$(".load-coop-data[data-type='proposal']").removeClass("active");
		$(".load-coop-data[data-type='action']").removeClass("active");
		$(".load-coop-data[data-type='resolution']").removeClass("active");
		$(".load-coop-data[data-type='action'][data-dataid='"+idAction+"']").addClass("active");

		$('#modifLink').off().click(function() {
			$('#modalLinkAction').modal("show");
		});

	});

	
</script>