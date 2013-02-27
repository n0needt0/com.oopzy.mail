<script>
$(document).ready(function(){

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
		    		  poll();
		    		  var items = [];

		    		  $.each(data, function(key, val) {

		    			    items.push('<li>' + key + ' = ' + val + '</li>');
	        			});

		    		  $('#server_stats').replaceWith('<ul>' + items.join('') + '</ul>');

		      }, dataType: "json"});
		  }, 3000);
		})();

	$('#submit').attr('disabled', 'disabled');
    $('#submit').click(function(){

    	try{
        	if( $('#mailbox').val().length > 2)
        	{
        		$.getJSON('/get_messages/' + $('#mailbox').val() , function(data) {
        			 $.each(data, function(key, val) {
        				 debug( key + '=>' + val);
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

    $('#mailbox').autocomplete({
   	   source: "/get_boxes/",
   	   minLength: 2
 	   });
});
</script>

<div id='top'>
  <div id='form'>
      <input id='mailbox' value='<?php echo $box;?>'> <button id='submit'>Retrieve</button>
  </div>
  <div id='content'></div>
</div>
