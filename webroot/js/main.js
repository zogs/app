var spanLoader = '<span class="ajaxLoader" id="ajaxLoader"></span>';
var ajaxLoader = "#ajaxLoader";



/*===========================================================
	JQUERY 
============================================================*/
$(document).ready(function(){
	

	/*===========================================================
		Autocomplete cityName input
	============================================================*/

	var inputcity = $("#inputcityName");
	if(inputcity.size()){
		var hiddencity = $("#cityID");
		var url = inputcity.attr('data-autocomplete-url');

	  	inputcity.autocomplete({
	  			serviceUrl:url,
	  			minChars:3,
	  			onSelect:function(value,data){ 
	  				
	  				hiddencity.val(data)},
	  		});
  	}
	/*===========================================================
		Security token send with AJAX /!\
	============================================================*/

	$("body").bind("ajaxSend", function(elm, xhr, settings){
		if (settings.type == "POST") {
			if(settings.data) {
				settings.data += "&token="+CSRF_TOKEN;				
			}		
		}
	});

	/*===========================================================
		Tooltip bootstrap
	============================================================*/
	$('a.bubble-top').livequery(function(){

		$(this).tooltip( { delay: { show: 500, hide: 100 }} );
	});
	$('a.bubble-bottom').livequery(function(){

		$(this).tooltip( { placement : 'bottom', delay: { show: 2000, hide: 100 }} );
	});
	

	/*===========================================================
		EXPANDABLE
		@param data-maxlenght
		@param data-expandtext
		@param data-collapsetext
	============================================================*/
	var expands = $('.expandable');
	if(expands.size()){
		expands.livequery(function(){
	    	$(this).expander({
	    		slicePoint: $(this).attr('data-maxlength'),
	    		expandPrefix: ' ',
	    		expandText: $(this).attr('data-expandtext'),
	    		userCollapseText: $(this).attr('data-collapsetext'),
	    		userCollapsePrefix: ' ',
	    	});
    	});
	}

		
	/*===========================================================
		GEO LOCATE
	============================================================*/

    $(".geo-select").select2();
    $("#CC1").select2({ formatResult: addCountryFlagToSelectState, formatSelection: addCountryFlagToSelectState});


    /*===========================================================
		COMMENT SYSTEM
	============================================================*/
    if($("a#refresh_com")){

    	//Params
        showComments_url = $("#refresh_com").attr('href');                  
		showComments_params = {};
		enablePreviewComment = false;
		loadingComments = false;        
        pageComments = 1;
        newestCommentId = 0;

        refreshComments = false;
        refreshComments_s = 600;
        setIntervalRefresh(refreshComments_s);       
        
        tcheckComments = true;
        tcheckComments_s = 60;
        setIntervalTcheck(tcheckComments_s);                

        show_comments();

        infiniteComment();
        
		

        /*===========================================================
        	refresh button
        ============================================================*/
        $("a#refresh_com").on('click',function(){            
            clean_params('page','order','type','newer','bottom');
            pageComments = 1;
            construct_params('?page=1');
            show_comments('clear');
            return false;
        });
        /*===========================================================
        	type of comment (not use yet)
        ============================================================*/
        $("a.type_com").bind('click',function(){
            $("a.type_com").each(function(){ $(this).removeClass('dropdown_active'); });
            $(this).addClass('dropdown_active');
            var param = $(this).attr('href');            
            construct_params(param);
            construct_params('?page=1');
            pageComments=1;
            show_comments('clear');
            return false;            
        });
        /*===========================================================
        	select refresh timer (not use yet)
        ============================================================*/
        $("a.set_refresh").bind('click',function(){
            $("a.set_refresh").each(function(){ $(this).removeClass('dropdown_active'); });
            $(this).addClass('dropdown_active');
            var second = $(this).attr('href');
            setIntervalRefresh(second);
            return false;
        });



        /*===========================================================
        	show more comments Button
        ============================================================*/
        $("#showMoreComments").bind('click',function(){

        	showMoreComments();

        	return false;
        });
        /*===========================================================
        	hover comments
        ============================================================*/
        $(".post").livequery(function(){ 
            $(this) 
                .hover(function() { 
                    $(this).find('.actions').css('visibility','visible'); 
                }, function() { 
                    $(this).find('.actions').css('visibility','hidden'); 
                }); 
            }, function() {                 
                $(this) 
                    .unbind('mouseover') 
                    .unbind('mouseout'); 
        }); 

        /*===========================================================
        	display more reply
        ============================================================*/
        $(".showReplies").livequery('click',function(){
        		$(this).parent().next('.hiddenReplies').show();
        		$(this).next('.hideReplies').show();
        		$(this).hide();    		
        		return false;
        });
        $(".hideReplies").livequery('click',function(){
        		$(this).parent().next('.hiddenReplies').hide();
        		$(this).prev('.showReplies').show();
        		$(this).hide();    		
        		return false;
        });

        /*===========================================================
        	display reply form
        ============================================================*/
        $(".btn-comment-reply").livequery('click',function(){

            var form = $('#formCommentReply');
            var url = form.attr('data-url');
            var reply_to = $(this).attr('href');
            var reply_login = $(this).attr('data-comlogin');
            var comment_id = $(this).attr('data-comid');
            form.find('input[name=reply_to]').val(reply_to);
            form.find('textarea').attr('placeholder','Reply to '+reply_login);
            form.appendTo($("#com"+comment_id));      

            return false;
        });

        /*===========================================================
        	Submit reply to comment 
        ============================================================*/
        $(".formCommentReply").livequery('submit',function(){

            var url = $(this).attr('action');
            var datas = $(this).serialize();
            var parent_id = $(this).find('input[name=reply_to]').val();            

            $.ajax({
                type:'POST',
                url: url,
                data: datas,
                success: function( com ){

                   if(!com.fail){
							                    
                    $("#formCommentReply").appendTo("#hiddenFormReply");
                    var html = $('<div />').html(com.content).text(); //Jquery trick to decode html entities
                    $("#com"+parent_id).next('.replies').remove();
                    $("#com"+parent_id).replaceWith(html);

                   }
                   else {
                        alert( com.fail );
                   }
                },
                dataType:'json'
                });
            
            return false;

        });


        /*===========================================================
        	Vote for comment
        ============================================================*/
        $(".btn-vote").livequery('click',function(){ 

            var badge = $(this).find('.badge');
            var id = $(this).attr('data-id');
            var url = $(this).attr('data-url');
                
            $.post(url,{id:id},function(data){ 

                if(is_numeric(data.note)){
                    badge.html(data.note);
                    badge.show();
                }
                else{
                    alert(data.erreur);
                }
            },'json');
        });




	    /*===========================================================	        
	    	Save comment in ajax request
	    ============================================================*/
	    $("#submitComment").on('click',function(){


	        var form = $("#commentForm");		        
	        var url = form.attr('action');
	        var textarea = $("#commentTextarea");
	        var text = textarea.val();
	        var preview = $("#commentPreview");
	        var media = $("input#media");
	        var media_url = $('input#media_url');

	        //if there is a media preview
	        if(preview.html()!="") {

	        	//remove uneccessery preview elements
	            preview.find(".previewMedia-totalthumbnails").remove();
	            preview.find(".previewMedia-thumbnail.hide").remove();
	            preview.find(".previewMedia-close").remove();
	            //set media field with preview content
	            media.val(preview.html());

	            //set media_url with currentUrlPreview
	            media_url.val(CurrentUrlPreview);           
	        }

	        //get the data from the form
	        var data = form.serialize();

	        //if comment not empty
	        if( trim(text) != "") {

	        	//send POST request
	            $.ajax({
	            	url:url, 
	            	type:"POST", 
	            	data: data, 
	            	dataType: 'json',
	                success: function(data){
	                    
	                    if(data.success){
	                    	//display new comments
	                        show_comments();
	                        //reset textarea
	                        textarea.val('');
	                        //reset preview container  
	                        preview.empty();
	                        //reset hidden media wrapper
	                        media.empty();
	                        //get comment id for futur use
	                        var insert_id = data.insert_id;                         
	                    }   
	                    else {
	                        alert(data.msg);
	                    }                                                                   		                     
	                  }                    
	               
	            });
            } 
	        return false;

	    });

	    $("#commentTextarea").on('focus',function(){ $(this).css('height','80px'); });

	    CurrentUrlPreview = '';


	    /*===========================================================
	    	Autodetect URL in comment textarea
	    ============================================================*/

	    $('#commentTextarea').bind('keyup change',function(event){

	    	if(!enablePreviewComment) return false;

	        var content = $(this).val();        
	        var previewURL = $(this).attr('data-url-preview');
	        var urlRegex = /\b((?:[a-z][\w-]+:(?:\/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:(?:[^\s()<>.]+[.]?)+|\((?:[^\s()<>]+|(?:\([^\s()<>]+\)))*\))+(?:\((?:[^\s()<>]+|(?:\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'".,<>?«»“”‘’]))/gi;
	        //var pattern = new RegExp("http\:\/\/[a-zA-Z0-9\-\.\_]+\.[a-zA-Z]{2,4}\/?/\\S*\\s*","gi"); 

	        //on each key entered
	        if(event.type=='keyup'){

	        	//pour éviter l'appel ajax trop fréquent on vérifie que la chaine finisse par un espace
	        	var spaceRegex = /\s$/;
	        	var spaceRegex = new RegExp(spaceRegex);
	        	var space = spaceRegex.exec(content);	        	
	        	if(space==null) return false;

	        	//On vérifie et récupére la presence d'une URL dans le contenu	        		        	  
	        	var keyUpMatches = new RegExp(urlRegex);		        							
	        	var urlMatches = keyUpMatches.exec(content); 
	        }

	        //on mouse event ( like focus out textarea)
	        if(event.type=='change'){

	        	var changeMatches = new RegExp(urlRegex);
	        	var urlMatches = changeMatches.exec(content);
	        }
	                                            

	        // console.log('event'+event.type+' content='+content);
	        // console.log('match= --'+urlMatches+'--');
	        // console.log('currenturl= --'+CurrentUrlPreview+'--');

	        if(urlMatches!=null && trim(urlMatches[0])!=trim(CurrentUrlPreview)){

	            $("#commentPreview").empty().html('loading...');

	            var url = urlMatches[0];
	            CurrentUrlPreview = url; 
	            url = encodeURIComponent(url); //good encode for GET parameter      

	            $.ajax({
	                type : 'GET',
	                url : previewURL,
	                data : {url:url},
	                success: function( data ){

	                    var decoded = $('<div />').html(data.content).text(); //Jquery trick to decode html entities
	                    console.log(decoded);
	                    $("#commentPreview").empty().html(decoded);
	                    $("input#media").val(data.content);
	                    $("input#type").val(data.type);

	                },
	                dataType : 'json'
	            });
	            

	        }
	        if(urlMatches == null) {
	            $("#commentPreview").empty();
	        }
	        

	    });

		/*===========================================================
			close and empty preview media
		============================================================*/
	    $(".previewMedia-close").livequery('click',function(){

	        $("#commentPreview").empty();
	        $("input#media").val('');
	        $("input#type").val('com');

	    });
	    
	    /*===========================================================
	    	display next thumbnail
	    ============================================================*/
	    $('#next_thumb').livequery("click", function(){
	        
	        var img = $('#commentPreview .previewMedia-thumbnails').find('.previewMedia-thumbnail:visible');
	        var next = img.next('.previewMedia-thumbnail');
	        if(next.length>0) {
	            img.addClass('hide');
	            next.removeClass('hide');
	        }
	        return false;
	        }); 
	    /*===========================================================
	    	display previous thumbnail
	    ============================================================*/
	    $('#prev_thumb').livequery("click", function(){
	        
	        var img = $('#commentPreview .previewMedia-thumbnails').find('.previewMedia-thumbnail:visible');
	        var prev = img.prev('.previewMedia-thumbnail');     
	        if(prev.length>0){
	            prev.removeClass('hide');   
	            img.addClass('hide');
	        } 
	        return false;
	        });


	    /*===========================================================
	    	launch media when clicking thumbnail
	    ============================================================*/
		$(".previewMedia-video, .previewMedia-img, .previewMedia-link").livequery(function(){

			$(this).find('.previewMedia-thumbnail').on('click',function(){

				var id = $(this).attr('data-comid');
		        var media = $(this).attr('data-media-url');
		        var type = $(this).attr('data-type');
		       
		        if(type=='video'){

		        	var container = $(this).parent().parent();
		        	container.empty().html(urldecode(media));		            

		        }
		        if(type=='img'){
		            window.open(media,'_newtab');
		        }
		        if(type=='link'){
		            window.open(media,'_newtab');
		        }
	        
	        
			});
	        

	    });

	    /*===========================================================
	    	hide moderate comments
	    ============================================================*/
	    $('.commentIsModerate').livequery(function(){

	    	$(this).next().hide();
	    	$(this).find('a').click(function(){
	    		$(this).parent().next().toggle();
	    		return false;
	    	});
	    })
	} 

	//end if comments listener
	//=================================================
	//==================================================



    /*===========================================================	        
    SHOW COMMENTS
    @param use params in showComments_params[]
    @param use $arguments[] , string clear,newer,start
    ============================================================*/ 
    
	function show_comments(){
		
		$("#ajaxLoader").show();
		$("#showMoreComments").hide();
		$("#loadingComments").show();

        var arg = (arguments[0]) ? arguments[0] : 'clear';

        clean_params('newer','start'); 

        if(arg=='new') 
             construct_params("?newer="+newestCommentId);
        if(arg=='bottom')
            construct_params("?start="+newestCommentId);    

        
        //console.log(JSON.stringify(showComments_params));
		$.ajax({
		  type: 'GET',
		  url: showComments_url,
		  data: arrayParams2string(showComments_params),
		  success: function( data ) 
          {	   

          	//console.log(data);
    		//Si pas de commentaires return false
    		if(data.commentsNumber==0 && data.commentsTotal!=0) {

    			$("#loadingComments").hide();
    			$("#ajaxLoader").hide();
    			return false;
    		}
            //Jquery trick to decode html entities
            var html = $('<div />').html(data.html).text();

            if(html!=''){
	            //Get id of the first comment
				if(arg=='new' || arg=='clear'){

	                var first_id = $(html).first('.post').attr('id');
	                first_id = first_id.replace('com','');
	                newestCommentId = first_id;
	                //console.log('firstID'+newestCommentId);
	                $("#badge").empty().hide();                        
	                $("#noMoreComments").hide();
	                if(arg=='new')
	                	$("#comments").prepend(html);
	                if(arg=='clear')
	                	$('#comments').empty().append(html);

	            }
	            else if(arg=='bottom') {                           
	                $('#comments').append(html);                       
	            }
	        }           	

            //If there is no comment yet, display NoCommentYet
            if(data.commentsTotal==0){
            	
            	$("#noCommentYet").show();
            }

            //ii there is no more comments to show
            else if(data.commentsLeft==0){
            	
            	 $("#showMoreComments").hide();
       	     	 $("#noMoreComments").show();		       	     
       	    }
       	    //else display number of comments lefts
       	    else {
   	    		 $("#showMoreComments").show();
	       	     $("#commentsLefts").text(data.commentsLeft);
	       	     $("#noMoreComments").hide();
       	    }
                  
             
            $("#ajaxLoader").hide();	                
            $("#loadingComments").hide();
            loadingComments = false;    
			
		},
		  dataType: 'json'
		});
		return;

	}

	/*===========================================================	        
	INFINITE SCROLL
	if scroll to the bottom of page
	increment page and call show_comments
	==========================================================*/								
    function infiniteComment() {

        $(window).scroll(function(){
            
            var ylastCom = $("#bottomComments").offset(); 
            var scrollPos = parseInt($(window).scrollTop()+$(window).height());
            //console.log(ylastCom.top+' <= '+scrollPos);
            if( (ylastCom.top <= scrollPos ) && loadingComments===false ) 
            {   
            	
                loadingComments = true;
                new_page        = pageComments+1;
                pageComments   = new_page;
                construct_params("?page="+new_page);                    
                show_comments('bottom');		                    
                
            }

        });
    };	    


    /*===========================================================
    	SHOW MORE COMMENTS
    	add the next page of comments
    ============================================================*/
    function showMoreComments() {

    	loadingComments = true;
    	new_page = parseFloat(pageComments)+1;
    	pageComments = new_page;
    	construct_params("?page="+new_page);
    	show_comments('bottom');

    }

    /*===========================================================
    	CONSTRUCT PARAMS
    	@param string ?param=value
    ============================================================*/
	function construct_params(param){
		if(param!=''){
			var p = [];
			if(strpos(param,'?',0)==0){
				param = str_replace('?','',param);
				p = explode('=',param);
				showComments_params[p[0]] = p[1];	
			}
			else alert('href doit commencer par ?');                
			return param;
		}
	}

    /*===========================================================
    	CLEAN PARAMS
    ============================================================*/
    function clean_params(){
        for(var key in arguments) {   
            for(var cle in showComments_params){                    
                //console.debug(' key:'+arguments[key]+'    cle:'+cle+'   value:'+showComments_params[cle]);
                if(arguments[key]==cle){
                    showComments_params[cle] = 0;
                }                    
            }
        }                         
    }

    /*===========================================================
    	??
    ============================================================*/
    function arrayParams2string(array){            
        var str ='';
        for(key in array){  

                str += key+'='+array[key]+'&';
                
        }
        str = str.substring(0,str.length-1);
        return str;
    }

    /*===========================================================
    	SET INTERVAL REFRESH
    ============================================================*/
    function setIntervalRefresh(ms){

    	if(!refreshComments) clearInterval(refreshComments);
    	else refreshComments = setInterval(function(){ show_comments('new');}, ms*1000)       
    }
    /*===========================================================
    	SET INTERVAL TCHECK
    ============================================================*/
    function setIntervalTcheck(ms){
    	
        if(!tcheckComments) clearInterval(tcheckComments);            
        else tcheckComments = setInterval(tcheckcomments,ms*1000);        
    }

    /*===========================================================
    	TCHECK COMMENTS
    ============================================================*/
    function tcheckcomments(){

        
        var obj = $('#refresh_com');
        var badge = obj.find('#badge');
        var url = obj.attr('data-url-count-com');
        var lastid = newestCommentId;
        url += lastid;

        $.ajax({
            type: 'GET',
            url: url,
            success: function(data){
                //$('#manifeste').empty().html(data);
                if(is_numeric(data.count)){
                    if(data.count>0){
                        badge.empty().html(trim(data.count));
                        badge.show();
                    }
                    else {
                        badge.hide();
                    }
                }
                //else alert(data);

            },
            dataType: 'json'
        });
    }


	/*===========================================================
		FORM AJAX
	============================================================*/
	$('form.form-ajax').livequery('submit',function(){

		var url = $(this).attr('action');
		var params = $(this).serialize();

		$.ajax({
			type : 'POST',
			url : url,
			data : params,
			contentType: 'multipart/form-data',
			success : function( data ){
				$('#myModal').empty().html( data );
			},
			dataType: 'html'
		});
		return false;
	});


	/*===========================================================
		CHECK DUPLICATE MAIL AND LOGIN
	============================================================*/

	$("#inputlogin,#inputemail").bind('blur',function(){

		var input = $(this);
		var control = input.parent().parent();
		var help = input.next('p.help-inline');
		var value = $(this).val();
		var url = $(this).attr('data-url');
		var type = $(this).attr('name');

		var c = forbiddenchar(value);
		if(c && type=='login'){
			control.addClass('control-error');
			help.removeClass('hide').empty().html("Le caractère suivant n'est pas autorisé : "+c);
		}
		else {
			control.removeClass('control-error');
			help.addClass('hide').empty();


			$.ajax({
				type: 'GET',
				url: url,
				data: {type : type, value : value},
				success: function(data){

					if(data.error){	
						control.removeClass('control-success');					
						control.addClass('control-error');
						help.removeClass('hide').empty().html( data.error );
					}
					if(data.available) {;
						control.removeClass('control-error');
						control.removeClass('control-success');
						help.removeClass('hide').empty().html( data.available );
					}
				},
				dataType: 'json'
			});
		}


	});

	function forbiddenchar(string){

		var carac = new RegExp("[ @,\.;:/!&$£*§~#|)(}{ÀÂÇÈÉÊËÎÔÙÛàâçèéêëîôöùû]","g");
		var c = string.match(carac);
		if(c) return c;
	}

	/*===========================================================
		MODAL BOX
	============================================================*/
  	$('a.callModal').livequery('click',function(){
	        
	        var href = $(this).attr('href');
	        callModalBox(href);  	        
	        return false;
	  });
  	//===============================



});



/*===========================
	MODAL BOX
============================*/

modalBox = $("#myModal");

modalBox.modal({
        backdrop:true,
        keyboard: true,
        show:false
});

	
function callModalBox(href){

	var modal = $("#myModal");
	$.get(href,function(data){ $(modal).empty().html(data)},'html');
	$(modal).modal('show');
}



/*============================
	SELECTION GEOGRAPHIQUE
=============================*/
CC1 = ''; 
ADM1=''; 
ADM2=''; 
ADM3=''; 
ADM4='';
function showRegion(value,region)
{

	$("#"+region).nextAll('select').empty().remove();
	$("#"+region).next('.select2-container').nextAll('.select2-container').empty().remove();

	if(value!='')
	{		
		CC1 = $("#CC1").val();
		if(region=='ADM1') { ADM1 = value; ADM2=''; ADM3=''; ADM4=''; }
		if(region=='ADM2') { ADM2 = value; ADM3 = ''; ADM4 = ''; }
		if(region=='ADM3') { ADM3 = value; ADM4 = ''; }
		if(region=='ADM4') { ADM4 = value; }
		if(region=='city') return false;		

		var url = $('#submit-state').attr('data-url');

		$.ajax({
			type : 'GET',
			url : url,
			data : { parent:value, ADM: region, CC1:CC1, ADM1:ADM1, ADM2:ADM2, ADM3:ADM3, ADM4:ADM4 },
			dataType: 'json',
			success: function(data){
				
				if(trim(data)!='empty'){ 				
					$('#'+region).next('.select2-container').after(data.SelectELEMENT);
					$("#"+data.SelectID).select2();
				}
			}
		});
	}
}

//Function for select2 plugin
function addCountryFlagToSelectState(state) {

	return "<i class='flag flag-"+state.id.toLowerCase()+"'></i>"+state.text;
}

/*============================
	SELECTION CATEGORY
=============================*/
function showCategory(parent,level){

	var url = $('#submit-category').attr('data-url');

	$.ajax({
		type:'POST',
		url:url,
		data: { parent:parent, level:level},
		success: function(data){
			//alert(data);
			if(trim(data)!='empty'){
				$('#cat'+level).empty().remove();
				$('#cat'+(level-1)).after(data);
			}
		}
	});
}



//=============================
//    LOCAL STORAGE
//============================

jQuery(function($){

	$.fn.formBackUp = function(){

		if(!localStorage){
			return false;
		}

		var forms = this;
		var datas = {};
		var ls = false;
		datas.href = window.location.href;

		if(localStorage['formBackUp']){
			ls = JSON.parse(localStorage['formBackUp']);
			if(ls.href = datas.href){
				for( var id in ls){
					if(id != "href"){
						$("#"+id).val(ls[id]);
						datas[id] = ls[id];
					}
				}
			}
		}

		forms.find('input,textarea').keyup(function(){
			datas[$(this).attr('id')] = $(this).val();
			localStorage.setItem('formBackUp',JSON.stringify(datas));
		});

		forms.submit(function(e){
			localStorage.removeItem('formBackUp');
		});
	}

});