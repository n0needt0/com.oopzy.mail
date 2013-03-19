<style type="text/css">

      #form{
            margin-left: auto;
            margin-right: auto;
            padding-bottom: 10px;
            padding-top: 10px;
            text-align: center;
            width: 100%;
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
      margin-left:10px;
      }

      A.deleteme{
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


$(document).ready(function(){

	if($('#mailbox').val() == "")
	{
		$('#mailbox').val(Conf.messages.initbox);
	}


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
        $("#messages").load("index/price_list");
    });

	$(this).keypress(function (e){
        code = e.keyCode ? e.keyCode : e.which;
          if(code.toString() == 13)
          {
             $('#submit').click();
          }
    })

    $('#mailbox').focus();

});


//the following script actually fires up the application
var head= document.getElementsByTagName('head')[0];
var script= document.createElement('script');
script.setAttribute('type', 'text/javascript');
script.setAttribute('src', Conf.home + '/assets/vendor/require/require.js');
script.setAttribute('data-main', Conf.home + "/assets/jsapp/config_mail");
head.appendChild(script);

var link = document.createElement("link");
link.type = "text/css";
link.rel = "stylesheet";
link.href = Conf.home + "/assets/jsapp/style.css";
head.appendChild(link);

</script>

<div class="mini-layout navbar">
    <div class="mini-layout-body">
      <div id='form' class='form-inline'>
        <span id="result"></span><input id='mailbox' class='input-mailbox' value='<?php echo $box;?>'><span id='label'>@<?php echo HOST_NAME; ?></span></input><button id='submit' class='submit-btn btn btn-success btn-large'> <i class="icon-envelope"></i> receive </button>
        <label class="checkbox">
            <input type="checkbox" id='rememberme' class='rememberme' <?php echo $rememberme;?>> Remember me
        </label>
    </div>
</div>
<div id='messages'>
<?php include_once('home_message.php');?>
</div>
<div id='savepop'></div>
<div id='tips'></div>
</div>
