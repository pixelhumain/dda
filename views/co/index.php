<?php 
//assets from ph base repo
$cssJS = array(
    
    '/plugins/jquery.dynForm.js',
    
    '/plugins/jQuery-Knob/js/jquery.knob.js',
    '/plugins/jQuery-Smart-Wizard/js/jquery.smartWizard.js',
    '/plugins/jquery.dynSurvey/jquery.dynSurvey.js',

    '/plugins/jquery-validation/dist/jquery.validate.min.js',
    '/plugins/select2/select2.min.js' , 
    '/plugins/moment/min/moment.min.js' ,
    '/plugins/moment/min/moment-with-locales.min.js',

    // '/plugins/bootbox/bootbox.min.js' , 
    // '/plugins/blockUI/jquery.blockUI.js' , 
    
    '/plugins/bootstrap-fileupload/bootstrap-fileupload.min.js' , 
    '/plugins/bootstrap-fileupload/bootstrap-fileupload.min.css',
    '/plugins/jquery-cookieDirective/jquery.cookiesdirective.js' , 
    '/plugins/ladda-bootstrap/dist/spin.min.js' , 
    '/plugins/ladda-bootstrap/dist/ladda.min.js' , 
    '/plugins/ladda-bootstrap/dist/ladda.min.css',
    '/plugins/ladda-bootstrap/dist/ladda-themeless.min.css',
    '/plugins/animate.css/animate.min.css',
); 

HtmlHelper::registerCssAndScriptsFiles($cssJS, Yii::app()->request->baseUrl);
$cssJS = array(
    '/js/dataHelpers.js',
    '/js/sig/geoloc.js',
    '/js/sig/findAddressGeoPos.js',
    '/js/default/loginRegister.js'
);
HtmlHelper::registerCssAndScriptsFiles($cssJS, Yii::app()->getModule( Yii::app()->params["module"]["parent"] )->getAssetsUrl() );
$cssJS = array(
'/assets/css/default/dynForm.css',
'/assets/js/comments.js',
);
HtmlHelper::registerCssAndScriptsFiles($cssJS, Yii::app()->theme->baseUrl);


?>



        <?php if(@$form["custom"]['header']){
            echo $this->renderPartial( $form["custom"]['header'],array("form"=>$form,"answers"=>$answers));
        }else { ?>
        <div class="container col-xs-12 padding-20" >
    
            <div id="surveyContent" class="formChart  col-xs-offset-1 col-xs-10 padding-bottom-20" >    
            <h4 style="font-variant:small-caps;" class="text-center margin-top-15"><span class="stepFormChart"></span> <?php echo $form["title"] ?></h4>
            <hr class="col-xs-10 col-xs-offset-1"></hr>
            <div id="surveyDesc" class="col-xs-offset-1 col-xs-10">
                <p class="text-center"><?php echo $form["description"] ?></p>
            </div>
        <?php } ?>

         <div id="surveyBtn" class="margin-top-15 col-xs-offset-1 col-xs-10"></div>
        
        <form id="ajaxFormModal"></form>
    </div>

</div>

<?php 
if(@$form["custom"]['footer']){
    echo $this->renderPartial( $form["custom"]["footer"],array("form"=>$form,"answers"=>$answers));
}
?>

<script type="text/javascript">
var answers = null;
var surveyContry = "<?php echo @$form["countryCode"] ?>";
var formSession = "<?php echo @$_GET["session"]; ?>";
var answerId = "<?php echo @$_GET["answer"]; ?>";



jQuery(document).ready(function() {

    $(".openFile").click( function() { 
        //alert(modules.survey.url+$(this).data("file"));
        window.open(modules.survey.url+$(this).data("file"));
    });

    dySObj.surveyId = "#ajaxFormModal";
    dySObj.surveys = <?php echo json_encode( $form ) ?>;
    answers = <?php echo json_encode( $answers ) ?>;
    startDate = <?php echo json_encode( @$startDate )?>;
    endDate = <?php echo json_encode( @$endDate )?>;
    dySObj.surveys.json={};

    uploadObj.formId = (jsonHelper.notNull( "dySObj.surveys.parentSurvey")) ? dySObj.surveys.parentSurvey.id :dySObj.surveys.id ;
    uploadObj.answerId = "<?php echo @$_GET['answer']; ?>";
    <?php if(@$_GET['session']){ ?>
        uploadObj.session = "<?php echo @$_GET['session']; ?>";
    <?php } ?>
    
    //scenario is a list of many survey definitions that can be put together in different ways
    //$("#surveyDesc").html("");
    if(userId && dySObj.surveys.scenario ){
        if( startDate && (startDate.sec > (new Date().getTime()/1000)) )
            $("#surveyDesc").append("<h1 class='text-center text-red bold'> Période de Collecte pas encore lancé.<br/>Revenez bientot! </h1>");
        else if( false && endDate && (endDate.sec < (new Date().getTime()/1000)) )
            $("#surveyDesc").append("<h1 class='text-center text-red bold'> Période de Collecte cloturé. </h1>");
        else {
            if( (dySObj.surveys.parentSurvey && 
                Object.keys( dySObj.surveys.parentSurvey.scenario).length > Object.keys( answers).length ) ||
                ( Object.keys( dySObj.surveys.scenario).length > Object.keys( answers).length ) )
            {
                var prev = null;
                var step = 1;
                var surveyType = (dySObj.surveys.surveyType) ? dySObj.surveys.surveyType : null ;
                var str = "";

                //build front end interface 
                var sizeCol = 12 / Object.keys(dySObj.surveys.scenario).length;
                answered = false;
                $.each(answers,function(aid,ans) { 
                    if(ans.formId == dySObj.surveys.id && userId == ans.user)
                        answered = true;
                });
                
                if(!answered)
                {
                    $("#surveyDesc").append("<h4 class='text-center'>En "+Object.keys(dySObj.surveys.scenario).length+" étapes</h4>");
                    $.each(dySObj.surveys.scenario, function(i,v) { 
                        icon = (v.icon) ? v.icon : "fa-square-o";
                        color = (jsonHelper.notNull( "dySObj.surveys.parentSurvey.custom.color")) ? dySObj.surveys.parentSurvey.custom.color : "MidnightBlue" ;
                        str += '<div class="card col-xs-12 col-md-'+sizeCol+'" >'+
                          //'<img src="https://unsplash.it/g/300">'+
                          '<div class="card-body padding-15 " style="border: 2px solid '+color+';border-radius: 10px;min-height:265px;">'+
                            '<h4 class="card-title bold text-dark text-center padding-5" style="border-bottom:1px solid white">'+
                                '<i class="margin-5 fa '+icon+' fa-2x"></i><br/>'+
                                step+'. '+v.title+
                            '</h4>'+    
                            '<span class="card-text text-center col-xs-12 no-padding margin-bottom-20">'+v.description+'</span>';


                        if(surveyType == "surveyList"){
                            answered = false;
                            $.each(answers,function(aid,ans) { 
                                if(ans.formId == i && userId == ans.user)
                                    answered = true;
                            });
                            
                            if(answered)
                                str +='<a href="'+baseUrl+'/survey/co/answer/id/'+dySObj.surveys.id+'/session/'+dySObj.surveys.session+'/user/'+userId+'/#head'+i+'" style="width:100%" class="btn bg-azure">'+
                                        'Déjà rempli</span> <i class="fa fa-'+v.icon+' fa-2x "></i></a>';
                            else  
                                str +='<a href="'+baseUrl+'/survey/co/index/id/'+i+'/session/'+dySObj.surveys.session+'" class="btn btn-default answered'+answered+' hidden"  style="width:100%"> Commencer <i class="fa fa-'+v.icon+' fa-2x "></i></a>';


                        } else if( surveyType != "oneSurvey" ) {
                            dType = (v.type) ? v.type : "json" ;
                            dynType = (v.dynType) ? v.dynType : "dynForm" ;
                            str +='<a href="javascript:;" onclick="dySObj.openSurvey(\''+i+'\',\''+dType+'\',\''+dynType+'\')" class="btn btn-primary col-xs-12"  style="width:100%">C\'est parti <i class="fa fa-arrow-circle-right fa-2x "></i></a>';
                        }

                        str +='</div></div>';  
                        prev = i;
                        step++;
                    }); 
                    

                    $("#surveyDesc").append("<div class='card-columns'>"+str+'</div>');
                    $(".answeredfalse").first().removeClass("hidden");
                    
                    if ( surveyType == "oneSurvey" ){
                        $("#surveyContent").removeClass("col-xs-12").addClass("col-xs-10 col-xs-offset-1");
                        //build survey json asynchronessly
                        if(userId)
                            $("#surveyBtn").append('<div class="margin-top-15 hidden col-xs-12 " id="startSurvey"><a href="javascript:;" onclick="dySObj.openSurvey(null,null,\''+surveyType+'\')" class="btn btn-primary"  style="width:100%"> C\'est parti <i class="fa fa-arrow-circle-right fa-2x "></i></a></div>'); 
                        else 
                            $("#surveyBtn").append('<div class="margin-top-15 hidden"><a href="javascript:;" onclick="" class="btn btn-danger">Connectez-vous avant d\'accéder au formulaire <i class="fa fa-arrow-circle-right fa-2x "></i></a></div>');

                        if(dySObj.surveys.author == userId){
                            $("#surveyBtn").append('<div class="margin-top-15 col-xs-6" id="seeAnswers"><a href="'+baseUrl+'/survey/co/answers/id/'+dySObj.surveys.id+'/session/'+dySObj.surveys.session+'" class="btn btn-default"  style="width:100%">Voir votre candidature <i class="fa fa-list fa-2x "></i></a></div>');
                        }

                        dySObj.buildOneSurveyFromScenario();
                    }

                } else {
                    $("#surveyDesc").append("<h1 class='text-center text-azure bold'> Vous avez déjà répondu à cette étapes </h1><center><a href='"+baseUrl+"/survey/co/answer/id/"+( typeof dySObj.surveys.parentSurvey == 'undefined' ? dySObj.surveys.id : dySObj.surveys.parentSurvey.id )+"/session/"+( typeof dySObj.surveys.parentSurvey == 'undefined' ? dySObj.surveys.session : dySObj.surveys.parentSurvey.session )+"/user/"+userId+"' style='' class='btn bg-azure'><span>Voir votre candidature</span>build</a></center>");
                    //TODO goto read your answers
                }
            }
        } 
            
    } else {
        // other wise it's jsut one survey that can be shown
        
        dyFObj[dyFObj.activeElem] = dySObj.surveys;
        var btnLbl = (dySObj.surveys.custom.btnAcceptLbl) ? dySObj.surveys.custom.btnAcceptLbl : "J'accepte et je signe";
        if(uploadObj.answerId && uploadObj.session ){

            if( answers[ uploadObj.session ][ uploadObj.answerId ] ){
                dyFObj[dyFObj.activeElem].dynForm.jsonSchema.save = function() { 
                    data={
                        answerId : uploadObj.answerId,
                        formId : uploadObj.formId,
                        session : uploadObj.session,
                        answerSection : "answer",
                        answers : arrayForm.getAnswers( dyFObj[dyFObj.activeElem].dynForm),
                        answerUser : userId 
                    };
                    
                    console.log("save",data);
                    
                    $.ajax({ type: "POST",
                        url: baseUrl+"/survey/co/update",
                        data: data,
                        type: "POST",
                    }).done(function (data) {
                       //$(dySObj.surveyId).html("<h1>Merci pour votre participation</h1>");
                       window.location.href = baseUrl+"/survey/co/index/id/"+uploadObj.formId+"/session/"+uploadObj.session;
                    });
                    return false;
                };
                dyFObj.buildDynForm (null,null,null,dySObj.surveyId);
            }
            else 
                window.location.href = baseUrl+"/survey/co/new/id/"+uploadObj.formId+"/session/"+uploadObj.session;
        }
        else 
        {
            
            //show Btn new or just message
            if( dySObj.surveys.oneAnswer == true && uploadObj.session && Object.keys(answers[ uploadObj.session ]).length > 0 )
                $(dySObj.surveyId).html("<h1 class='text-center'>Vous avez déjà participé<br/>merci</h1>");
            else if( uploadObj.session )
                $(dySObj.surveyId).html("<div class='margin-bottom-20 text-center col-xs-12'><h1><i class='fa fa-check-circle-o'></i> <a href='"+baseUrl+"/survey/co/new/id/"+uploadObj.formId+"/session/"+uploadObj.session+"'>"+btnLbl+"</a></h1></div>");
            else {
                $.each( dySObj.surveys.session ,function(i,v) {
                    $(dySObj.surveyId).html("<div class='margin-bottom-20 text-center col-xs-12'><h1><i class='fa fa-check-circle-o'></i> <a href='"+baseUrl+"/survey/co/new/id/"+uploadObj.formId+"/session/"+i+"'>"+btnLbl+" #"+i+"</a></h1></div>");
                });
            }

            //Draw a table of all the answers
            if(uploadObj.session) {
                if ( Object.keys( answers[ uploadObj.session ] ).length > 0 ){
                    dyFObj.elementData = answers[ uploadObj.session ];
                    dyFObj.drawAnswers(dySObj.surveyId, {session:"#"+uploadObj.session} );
                }
            } else {
                $.each( dySObj.surveys.session ,function(i,v) {
                    if ( Object.keys( answers[ i ] ).length > 0 ){
                        dyFObj.elementData = answers[ i ];
                        dyFObj.drawAnswers( dySObj.surveyId ,  {session:"#"+i} );
                    }
                });
            }
        }


    } 
    if( location.hash.indexOf("#panel") >= 0 ){
        panelName = location.hash.substr(7);
        mylog.log("panelName",panelName);
        if( userId == "" ){
            if(panelName == "box-login")                
                Login.openLogin();
        else if(panelName == "box-register")
            $('#modalRegister').modal("show");
        
        }
    }
});


/*
"login" : {
    "title" : "Identity",
    "description" : "please login to conintu please login to conintu please login ",
    "path" : "/surveys/login.js",
    "type" : "script",
    "icon" : "fa-vcard-o"
},
*/

</script>