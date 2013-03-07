<style type="text/css">

      #form{
            margin-left: auto;
            margin-right: auto;
            padding-bottom: 10px;
            padding-top: 10px;
            text-align: center;
            width: 1000px;
      }

       #autofrom{
            margin-left:20px;
            padding-bottom: 10px;
            padding-top: 10px;
      }

      SPAN.wait{
      float:left;
      padding-top:280px;
      }

      A.saveme{
      float:right;
      }

      #label
      {
         margin: 5px;
         font-size:20px;
      }

      #mailbox {
         font-size: 20px;
         line-height: 26px;
         height: 26px;
      }

    .message {
    border-radius: 3px 3px 3px 3px;
    line-height: 30px;
    min-height: 30px;
    text-align: left;
    margin-top:10px;
}

#rememberme{
   margin-left:5px;
}

#remailto{
 margin: 10px;
 margin-left:0px;
 width:250px;
}

#result{
	margin-right:10px;
	margin-left:10px;
	background:#EEE;
	border-radius: 1px 1px 1px 1px;
	text-align:center;
}

#result A{
  text-decoration: underline;
  background:yellow;
}

SPAN.too_short{
	color:#FF0000;
}

SPAN.invalidfirst{
	color:#FF0000;
}

SPAN.bad{
	color:#E66C2C;
}

SPAN.invalid{
	color:#E66C2C;
}

SPAN.lousy{
	color:#2D98F3;
}

SPAN.good{
	color:#006400;
}

</style>

<script>

  Oopzy.messages = {
		                         too_short:'Way too short',
		                         bad:'Weak',lousy:'Lousy',
		                         good:'Pretty Good',
		                         invalid:'Makes invalid email',
		                         save_1:'Enter email where you would like us to save this message.',
		                         save_2:'Remember this email.',
		                         save_3:'Forward all emails from:',
		                         save_4:'to this email.',
		                         whycare:'why care?',
		                         initbox: 'Enter Your Mail',
		                         invalidfirst:'1st must be alfa numeric',
		                         nomessages:'No new messages'
			                   };


$(document).ready(function(){

Oopzy.ref = 0;
Oopzy.from = '';
Oopzy.JST = Oopzy.JST || {};

	Oopzy.JST['tl/message'] = _.template(
			  "<div class='message'>"+
			  "<PRE>"+
			  "<a class='saveme' ref='<%= key%>' from='<%= from%>' href='#'>save</a>"+
			  "<b>from:</b> <a href='mailto:<%= from%>'><i><%= from%> </i></a> <%= dt%></br><b>Subject:</b><%= subject%>"+

			  "<%= body%>"+
			  "</PRE>"+
			  "</div>"
			  );

	Oopzy.JST['tl/save'] = _.template(
			  "<div id='save-confirm' title='Save this email'>"+
			  "<p><span id='wait'></span>"+
			  "<span class='ui-icon ui-icon-alert' style='float: left; margin: 0 7px 20px 0;'></span>"+
			  "<%=save_1 %> </br>"+
			  "<input id='remailto'> </br><input type='checkbox' id='rememberremail'> <%=save_2 %> </br>"+
			  "<input type='checkbox' id='alwaysremail'> <%=save_3 %> </br><span id='autofrom'></span> <%=save_4 %></p>" +
			  "</div>"
			);

	Oopzy.JST['tl/verifying'] = _.template(
			"<pre class='hl-yellow'><span class='ui-icon ui-icon-alert' style='float: left; margin: 0 7px 20px 0;'></span><%= error%></pre>"
			);

	(function poll(){
		   setTimeout(function(){
		      $.ajax({ url: "/get_server_info",

		    	  complete: function(jqXHR) {
		    		  var contentType = jqXHR.getResponseHeader("Content-Type");
		    		  if (jqXHR.status === 200 && contentType.toLowerCase().indexOf("text/html") >= 0)
		    		  {
		    		     // assume that our login has expired - reload our current page if required

		    		  }

		    	  },

		    	  success: function(data){
		    		  //logged in
		    		  //poll();

		    		  var items = [];

		    		  $.each(data, function(key, val) {

		    			    //items.push('<li>' + key + ' = ' + val + '</li>');
	        			});

		    		  //$('#server_stats').replaceWith('<ul>' + items.join('') + '</ul>');

		      }, dataType: "json"});
		  }, 3000);
		})();


	if($('#mailbox').val() == "")
	{
		$('#mailbox').val(Oopzy.messages.initbox);
	}

    $('#submit').click(function(){

    	try{
        	var box = $('#mailbox').val();

        	if( box.length > 2)
        	{
        		//set cookie
            	createCookie('oopzybox', box, 365);
        		$.getJSON('/get_messages/' + box , function(data) {

        			 if(0 == data.length)
        			 {
            			 $('#nomessage').remove();
            			 setTimeout(function(){
            				 $('#messages').prepend("<PRE class='message' id='nomessage'><h2>"+Oopzy.messages.nomessages+"...</h2></PRE>");
            	        	 },1000);
            		 }
        			   else
        			 {
        				   $('#messages').html("");
        			 }
        			 $.each(data, function(key, val) {
        				 $('#messages').append(Oopzy.JST['tl/message'](val));
        				 debug(val.debug);
        			});
        		});

        	}

        }
    	  catch(err)
    	{
        	alert(err.message);
        }
    });

    $('#mailbox').click(function(){
          $('#mailbox').val("");
          $('#submit').removeAttr('disabled');
        });


    $('#rememberme').on('click', function() {
        var $this = $(this);
        if ($this.is(':checked')) {
        	createCookie('rememberme','1',365);
        } else {
        	eraseCookie('rememberme');
        }
    });

    if($('#rememberme').is(':checked'))
	{
         var box = readCookie('oopzybox');
         if( box.length > 2)
     	{
        	 $('#mailbox').val(box);
        	 setTimeout(function(){
        	 $('#submit').trigger('click');
        	 },2000);
     	}
          else
        {
             $('#submit').attr('disabled', 'disabled');
        }
	}
	 else
	{
        $('#submit').attr('disabled', 'disabled');
	}

	if($('#mailbox').val() != Oopzy.messages.initbox)
    {
		$('#submit').removeAttr('disabled');
	}


    $('#savepop').append(Oopzy.JST['tl/save'](Oopzy.messages));

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

                		$.post("/save_message", { ref: Oopzy.ref, to: $('#remailto').val() },
                  		function(data) {
                      		debug(data.error);

                      		$("#wait").html("");

                      		if ("" != data.error) {
                      	      $("#tips").html(Oopzy.JST['tl/verifying']({'error':data.error}));
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

	$('A.saveme').live("click", function(){
		Oopzy.ref = $(this).attr('ref');
		Oopzy.autofrom = $(this).attr('from');

		$( "#save-confirm" ).dialog( "open" );
  		$('#autofrom').html("<a href='" + Oopzy.autofrom + "'><i>" + Oopzy.autofrom + "</i></a>");

  		if(readCookie('remremail'))
		{
  			 $('#rememberremail').attr('checked','checked');
	         var box = readCookie('rememail');
	         $('#remailto').val(box);
		}
	});

    $('#rememberremail').live('click', function() {
        var $this = $(this);
        if ($this.is(':checked')) {
        	createCookie('remremail','1',365);
        } else {
        	eraseCookie('remremail');
        }
    });

    $('.whycare').live('click',function(){
        //opens explanation
        $("#messages").load("price_list");
    });

    $('#mailbox').keyup(function(){
        var res = checkStrength($('#mailbox').val());
        $('#result').removeClass();
        $('#result').addClass(res);
		$('#result').html( Oopzy.messages[res] + " <a class='whycare'>" + Oopzy.messages.whycare + "</a>");
	})

	$(this).keypress(function (e){
        code = e.keyCode ? e.keyCode : e.which;
          if(code.toString() == 13)
          {
             $('#submit').click();
          }
    })

    $('#mailbox').focus();

});
</script>
<form>
<div class="mini-layout">
    <div class="mini-layout-body">
      <div id='form' class='form-inline'>
        <span id="result"></span><input id='mailbox' value='<?php echo $box;?>'><span id='label'>@<?php echo $GLOBALS['host_name']; ?></span></input><button id='submit' class='btn btn-success btn-large'> <i class="icon-envelope"></i> receive </button>
        <label class="checkbox">
            <input type="checkbox" id='rememberme' <?php echo $rememberme;?>> Remember me
        </label>
    </div>
</div>
<div id='messages'>
<?php include_once('home_message.php');?>
</div>
<div id='savepop'></div>
<div id='tips'></div>
</div>
</form>
