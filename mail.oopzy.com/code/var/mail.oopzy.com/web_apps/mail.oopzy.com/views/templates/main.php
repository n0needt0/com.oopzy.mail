<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="Served-From" content="<?php echo SERVER_NAME; ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="/assets/favicon.ico" />
<title><?php echo SITE_NAME;?></title>

<script src="/assets/combined.js.php?r=<?php echo CACHEVER; ?>"></script>

<script>
var Conf = Conf || {};

Conf.messages = {
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
Conf.ref = 0;
Conf.from = '';

<?php
  if( JSDEBUG )
  {
      echo " Conf.DEBUG_MODE = 'console'; ";
  }
?>

Conf.APP_NAME = 'oopzy'; //application name is hard coded
Conf.server_name = '<?php echo SERVER_NAME; ?>';
Conf.protocol = 'http';
<?php

if(!empty($_SERVER['SERVER_HTTPS']))
{
	echo "Conf.protocol = 'https';";
}
?>

Conf.home = Conf.protocol + '://' + Conf.server_name;

Conf.api = { home:Conf.home, // this is location of a rest api for this application
             key:'ABCDEFG' //this is key for use in this API
           };


function debug(msg){

   if('debug' === Conf.DEBUG_MODE)
   {
       eval('debugger;');
   }

   if('console' === Conf.DEBUG_MODE)
   {
       console.log(msg);
   }
}

function waiting(){
	  $('#wait').html("<div class='wait_message'><img class='special_image' src = '/assets/images/loading.gif' /> Thinking...</div>");
	  $('#wait').dialog({
	      title:'One moment..',
	      autoOpen: true,
	      resizable: false,
	      width:300,
	      height:200,
	      modal: true
	    });

	    $('.ui-widget-overlay').css('height', 3000);
	    $('.ui-widget-overlay').css('width', $(window).width());
	    $(".ui-dialog-titlebar").hide();
 }

 function done(){
	 $('#wait').dialog("close");
	 $('#wait').html("");
 }


 var uvOptions = {};
 (function() {
   var uv = document.createElement('script'); uv.type = 'text/javascript'; uv.async = true;
   uv.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'widget.uservoice.com/WRmCdhfqqyO0zvAZvjqA.js';
   var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(uv, s);
 })();

</script>
<link rel="stylesheet" type="text/css" href="/assets/combined.css.php?r=<?php echo CACHEVER; ?>" />

<style type="text/css">
      body {
        padding-top: 50px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
        font-size:14px;
      }

      .hl-yellow{
        background:yellow;
      }

      .hl-underline{
       text-decoration: underline;
      }


      .normaltext{
        font-size:16px;
      }


      #title{
      float:right;
      }

      /* Mini layout previews
        -------------------------------------------------- */
        .mini-layout {
          border: 1px solid #ddd;
          -webkit-border-radius: 6px;
             -moz-border-radius: 6px;
                  border-radius: 6px;
          -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.075);
             -moz-box-shadow: 0 1px 2px rgba(0,0,0,.075);
                  box-shadow: 0 1px 2px rgba(0,0,0,.075);
        }
        .mini-layout,
        .mini-layout .mini-layout-body,
        .mini-layout.fluid .mini-layout-sidebar {
          height: 100%;
        }
        .mini-layout {
          margin-bottom: 20px;
          padding: 9px;
        }
        .mini-layout div {
          -webkit-border-radius: 3px;
             -moz-border-radius: 3px;
                  border-radius: 3px;
        }
        .mini-layout .mini-layout-body {
          background-color: #dceaf4;
          margin: 0 auto;
          width: 100%;
        }
        .mini-layout.fluid .mini-layout-sidebar,
        .mini-layout.fluid .mini-layout-header,
        .mini-layout.fluid .mini-layout-body {
          float: left;
        }
        .mini-layout.fluid .mini-layout-sidebar {
          background-color: #bbd8e9;
          width: 20%;
        }
        .mini-layout.fluid .mini-layout-body {
          width: 77.5%;
          margin-left: 2.5%;
        }

        #btmfooter {
            color: #AAAAAA;
            font-size: 11px;
            margin-left: auto;
            margin-right: auto;
            padding-bottom: 10px;
            padding-top: 10px;
            text-align: center;
            width: 100%;
            height:20px;   /* Height of the footer */
         }

 .specialImage{
  position:fixed;
  bottom:0;
  left:0;
  z-index:100; /* or higher/lower depending on other elements */
}

</style>

</head>
<body>

<div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="/">Oopzy Mail</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li class="active"><a href="/">Home</a></li>
              <li><a href="about">About</a></li>
              <li><a href="mailto:!nospamfor3ver@oopzy.com">Contact</a></li>
            </ul>
            <a class='brand hidden-phone' id='title'> simply the best :)</a>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

<div id='server_stats'></div>

<div class='container' id='app'>
      <div> <?php echo $content; ?> </div>
 </div>
<img src="/assets/images/oopzy.png" class="specialImage hidden-phone" />
<div id="btmfooter"> Copyright &copy; <?php echo date('Y') ; ?> oopzy.com All Rights Reserved | rMY_REVISION  </div>

</body>
</html>