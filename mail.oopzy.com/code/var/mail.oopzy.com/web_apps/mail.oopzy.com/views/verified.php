<script>
$(document).ready(function(){

		(function poll(){
			   setTimeout(function(){
				   window.location = "/"
			  }, 2000);
			})();
});

</script>

<PRE class='message normaltext'>
 <h2>Success</h2>
  Your email [<?php echo $to ?>] is verified and linked to your Oopzy box [<?php echo $oopzybox?>].

  Redirecting .... <img src = '/assets/images/loading.gif' />
</PRE>