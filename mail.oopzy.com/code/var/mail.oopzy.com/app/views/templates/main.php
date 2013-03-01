<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="Served-From" content="<?php echo $oopzy['served_from']; ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="/assets/favicon.ico" />
<title><?php echo $oopzy['site_name'];?></title>

<script src="/assets/combined.js.php?r=<?php echo $oopzy['cache_param']; ?>"></script>

<script>
var Oopzy = Oopzy || {};

<?php
  if( $oopzy['jsdebug'] )
  {
      echo " Oopzy.DEBUG_MODE = 'console'; ";
  }
?>

function debug(msg){

   if('debug' === Oopzy.DEBUG_MODE)
   {
       eval('debugger;');
   }

   if('console' === Oopzy.DEBUG_MODE)
   {
       console.log(msg);
   }
}

var uvOptions = {};
(function() {
  var uv = document.createElement('script'); uv.type = 'text/javascript'; uv.async = true;
  uv.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'widget.uservoice.com/WRmCdhfqqyO0zvAZvjqA.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(uv, s);
})();

</script>
<link rel="stylesheet" type="text/css" href="/assets/combined.css.php?r=<?php echo $oopzy['cache_param']; ?>" />

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
              <li><a href="contact">Contact</a></li>
            </ul>
            <a class='brand' id='title'> simply the best single use email & re-mail service :)</a>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

<div id='server_stats'></div>

<div class='container'>
      <div> <?php echo $content; ?> </div>
 </div>
<img src="/assets/images/oopzy.png" class="specialImage" />
<div id="btmfooter"> Copyright &copy; <?php echo date('Y') ; ?> oopzy.com All Rights Reserved | rMY_REVISION  </div>

</body>
</html>