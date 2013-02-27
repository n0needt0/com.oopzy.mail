<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="Served-From" content="<?php echo $oopzy['served_from']; ?>" />
<meta name="viewport" content="width=1030;" />
<link rel="shortcut icon" href="/assets/favicon.ico" />
<title><?php echo $oopzy['site_name'];?></title>
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
</script>
<script src="/assets/combined.js.php?r=<?php echo $oopzy['cache_param']; ?>"></script>
</head>
<body>

<div id="main_hold">
  <div id="main_content"> <?php echo $content; ?> </div>
</div>

<div id="foot_hold">
  <div id="foot_bar">
     Copyright &copy; <?php echo date('Y'); ?> oopzy.net All Rights Reserved | rSVN_REVISION
  </div>
</div>

<div id='server_stats'></div>

</body>
</html>