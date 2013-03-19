require([
  // Libs
  "jquery",
  "use!backbone"
],
        
function( jQuery, Backbone) {

    var App = {
            Views: {},
            Models: {},
            Collections: {},
            Routers: {},
            init: function() {
                debug('initializing');
                debug(Conf);
                debug(App);
                App.Views.appview = new App.Views.AppView();
            }
        };

    
    
      // Our overall **AppView** is the top-level piece of UI.
      App.Views.AppView = Backbone.View.extend({

        // Instead of generating a new element, bind to the existing
          
        el: '#app',
        // Delegated events for creating new items, and clearing completed ones.
        events: {
          "click .submit-btn" : "getMessages",
          "click .rememberme" : "rememberMe",
          "click A.saveme" : "saveMe",
          "click A.deleteme" : "deleteMe",
          "keyup .input-mailbox" : "checkMe",
          "click .input-mailbox" : "checkValue",
          "click .more" : "openUp",
          "click .less" : "closeDown"
         }, 
        
        initialize: function () { 
            _.bindAll(this, "render");
            //render
            this.render();
        },

        render: function () {
            debug('binding to application element: ');
            this.prewire();
        },
        
        checkValue: function(e){
        	if(Conf.messages.initbox == $(e.currentTarget).val().trim())
        	{
        		$(e.currentTarget).val("");
        	}
        		
        },
        checkMe: function(e){
        	var res = checkStrength($(e.currentTarget).val());
            $('#result').removeClass();
            $('#result').addClass(res);
    		$('#result').html( Conf.messages[res] + " <a class='whycare'>" + Conf.messages.whycare + "</a>");
        },
        saveMe: function(e){
    		Conf.ref = $(e.currentTarget).attr('ref');
    		Conf.autofrom = $(e.currentTarget).attr('from');

    		$( "#save-confirm" ).dialog( "open" );
      		$('#autofrom').html("<a href='" + Conf.autofrom + "'><i>" + Conf.autofrom + "</i></a>");

      		if(readCookie('remremail'))
    		{
      			 $('#rememberremail').attr('checked','checked');
    	         var box = readCookie('rememail');
    	         $('#remailto').val(box);
    		}
    	},        
        prewire: function(e){

        	if($('.rememberme').is(':checked'))
        	{
                 var box = readCookie('oopzybox');
                 if( box.length > 2)
             	{
                	 $('#mailbox').val(box);
                	 setTimeout(function(){
                	 $('.submit-btn').trigger('click');
                	 },2000);
             	}
        	}
        	
        	$('#savepop').append(Conf.JST['tl/save'](Conf.messages));
            $( "#save-confirm" ).dialog({
            	autoOpen: false,
              	resizable: true,
              	width:320,
              	height:280,
              	modal: true,
                	buttons: {
                      	  "Save": function() {

                      		var box = $('#remailto').val();

                      		if(isValidEmail(box))
                      		{
                            	createCookie('rememail', box, 365);

                            	$("#wait").html("<img src = '/assets/images/loading.gif' />");
                            	
                            	var auto = { ref: Conf.ref, to: $('#remailto').val() };
                            	
                            	if($('.rememberme').is(':checked'))
                            	{
                            	    auto['auto'] = 'yes';
                            	    auto['autofrom'] = Conf.autofrom;
                            	}

                        		$.post("/api/save_message", auto,
                          		function(data) {
                              		debug(data.error);

                              		$("#wait").html("");

                              		if ("" != data.error) {
                              	      $("#tips").html(Conf.JST['tl/verifying']({'error':data.error}));
                              	      $("#tips").dialog({
                              	    	width:325,
                              	      	height:285,
                              	    	hide: "explode",
                              	    	autoOpen:true,
                                    	modal: true,
                                      	buttons: {
                                        	  "Ok": function() {
                                        		   $("#tips").dialog("close");
                                        		   $("#tips").html("");
                                        		   $( "#save-confirm" ).dialog( "close" );
                                          		}
                                      	}
                              	      });
                              	    }


                        		},'json'
                        		);
                        		$( "#save-confirm" ).dialog( "close" );
                      		}
                      		 else
                      		{
                               alert('Invalid email!');
                      		}
                    	},
                    	Cancel: function() {
                    		$( "#save-confirm" ).dialog( "close" );
                    	}
                	}
            	});
        }
        ,
        //this changes remember me cookie
        rememberMe: function(e){
        	var $this = $(e.currentTarget);
            if ($this.is(':checked')) {
            	createCookie('rememberme','1',365);
            } else {
            	eraseCookie('rememberme');
            }
        },
        getMessages: function(e){

        	try{
                
        		//var p = waiting();
        		var box = $('.input-mailbox').val();
        		
               	if($('.rememberme').is(':checked'))
            	{
               		createCookie('oopzybox',box,365);
            	}
        		
               	box = encodeURIComponent(box);
            	var api_url = Conf.api.home + '/api/messages/' + box;
            	
                $.ajax({
                    url: api_url,
                    dataType: "jsonp",
                    jsonp : "callback",
                    success: function(data) {
           			 $('#messages').html("");
           			 $.each(data, function(key, val) {
           				 $('#messages').append(Conf.JST['tl/message'](val));
           				 debug(val.debug);
           			});
           		},
                    error: function(data) {
                    	$('#nomessage').remove();
              			 setTimeout(function(){
              				 $('#messages').prepend("<PRE class='message' id='nomessage'><h2>"+Conf.messages.nomessages+"...</h2></PRE>");
              	        	 },1000);
                        }
                     });
                
                debug('finished api url:' + api_url);
                
            }
              catch (err)
            {
                debug(err);
            }
        },
        deleteMe: function(e){
        	
        	var key = $(e.currentTarget).attr('ref');
        	var elref = $(e.currentTarget).attr('elref');

        	try{
                
            	var api_url = Conf.api.home + '/api/message_delete/' + encodeURIComponent(key);
            	
                $.ajax({
                    url: api_url,
                    dataType: "jsonp",
                    jsonp : "callback",
                    success: function(data) {
           			
           			$('DIV.'+elref).remove();
           		},
                    error: function(data) {
                          debug('can not finish api request to ' + api_url);
                          debug(data);
                        }
                     });
                
                debug('finished api url:' + api_url);
                
            }
              catch (err)
            {
                debug(err);
            }
        } ,
        closeDown: function(e)
        {
        	    var text = $('SPAN.less').text().substring(0,140);
		    	$('SPAN.less').text(text);
				$('SPAN.less').addClass("more");
				$('SPAN.less').removeClass("less");
        },
        openUp: function(e){
        	
        	var key = $(e.currentTarget).attr('ref');
        	var elref = $(e.currentTarget).attr('elref');

        	try{
                
        		//first close any other open messages
    		
    			this.closeDown();
        		
            	var api_url = Conf.api.home + '/api/message/' + encodeURIComponent(key);
            	
                $.ajax({
                    url: api_url,
                    dataType: "jsonp",
                    jsonp : "callback",
                    success: function(data) {
                    	$.each(data, function(key, val) {
              				 $('DIV.'+elref).replaceWith(Conf.JST['tl/message'](val));
              				$('SPAN.'+elref).removeClass("more");
              				$('SPAN.'+elref).addClass("less");
              				 debug(val.debug);
              			});
           		},
                    error: function(data) {
                          debug('can not finish api request to ' + api_url);
                          debug(data);
                        }
                     });
                
                debug('finished api url:' + api_url);
                
            }
              catch (err)
            {
                debug(err);
            }
        }
        
        
      });

    App.init();
});
