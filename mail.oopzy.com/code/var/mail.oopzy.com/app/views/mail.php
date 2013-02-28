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
}

SPAN.too_short{
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

  var strenght_messages = {too_short:'Way too short', bad:'Weak',lousy:'Lousy',good:'Pretty Good',invalid:'Makes invalid email'};

  function checkStrength(str){

  	  var strength = 0;
      var res = '';

        //if the password length is less than 6, return message.
        if (str.length < 6) {
      	    res = 'too_short';
      	    return res;
    	}

        //length is ok, lets continue.

    	//if length is 8 characters or more, increase strength str
    	if (str.length > 7){
        	strength += 1;
    	}

    	//if password contains both lower and uppercase characters, increase strength str
    	if (str.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)){
        	strength += 1;
    	}

    	//if it has numbers and characters, increase strength str
    	if (str.match(/([a-zA-Z])/) && str.match(/([0-9])/)){
        	  strength += 1;
    	}

    	//if it has one special character, increase strength str
        if (str.match(/([!,%,&,@,#,$,^,*,?,_,~])/)){
              strength += 1;
        }

    	//if it has two special characters, increase strength str
        if (str.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,%,&,@,#,$,^,*,?,_,~])/)){
            strength += 1;
        }

    	//now we have calculated strength str, we can return messages

    	//if str is less than 2
    	if (strength < 2 ) {
    		res = 'bad';
    	} else if (strength == 2 ) {
    		res = 'lousy';
    	} else {
    		res = 'good';
    	}

    	if(!isValidEmail(str + '@domain.com'))
    	{
    	    $(out).removeClass();
    		$(out).addClass('weak');
    		res = 'invalid';
    	}

    	return res;
  }

    function isValidEmail(emailAddress)
    {
        var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
        return pattern.test(emailAddress);
    };

    function createCookie(name,value,days)
    {
    	if (days)
        {
    	        var date = new Date();
    	        date.setTime(date.getTime()+(days*24*60*60*1000));
    	        var expires = "; expires="+date.toGMTString();
    	}
    	else var expires = "";
    	document.cookie = escape(name)+"="+escape(value)+expires+"; path=/";
	}

	function readCookie(name)
	{
    	var nameEQ = escape(name) + "=";
    	var ca = document.cookie.split(';');
    	for(var i=0;i < ca.length;i++)
        {
    	    var c = ca[i];
    	    while (c.charAt(0)==' ') c = c.substring(1,c.length);
    	    if (c.indexOf(nameEQ) == 0) return unescape(c.substring(nameEQ.length,c.length));
    	}
    	return '';
	}

	function eraseCookie(name)
	{
	    createCookie(name,"",-1);
	}

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

    $('#submit').click(function(){

    	try{
        	var box = $('#mailbox').val();

        	if( box.length > 2)
        	{
            	//set cookie
            	createCookie('oopzybox', box, 365);

        		$('#messages').html("");
        		$.getJSON('/get_messages/' + box , function(data) {
        			 $.each(data, function(key, val) {
        				 $('#messages').append(Oopzy.JST['tl/message'](val));
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
        	 $('#submit').trigger('click');
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
                		$.post("/save_message", { ref: Oopzy.ref, to: $('#remailto').val() })
                  		.done(function(data) {
                      		debug(data);
                      		$( "#save-confirm" ).dialog( "close" );
                		});
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

  		if($('#rememberremail').is(':checked'))
		{
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

    $('#mailbox').keyup(function(){
        var res = checkStrength($('#mailbox').val());
        $('#result').removeClass();
        $('#result').addClass(res);
		$('#result').html( strenght_messages[res] );
	})
});
</script>

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
</div>
</div>

<div id="save-confirm" title="Save this email">
  <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
  Enter email where you would like us to save this message. </br>
  <input id='remailto'> </br><input type="checkbox" id='rememberremail'> Remember this email. </br>
  <input type="checkbox" id='alwaysremail'>Forward all emails from: </br><span id='autofrom'></span> to this email.</p>
</div>

