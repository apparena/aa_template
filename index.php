<?php
include_once("init.php");
?>
<!-- HTML5 standard doctype -->
<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en"> <!--<![endif]-->
<HEAD>
<?php if ( $aa['config']['target']['value'] == "top") {  ?>
<SCRIPT language="JavaScript">
<!--
var browserName=navigator.appName; 
if (browserName=="Netscape")
{ 
top.location="<?php echo $aa['config']['redirect_url']['value']?>";
}
else 
{ 
 if (browserName=="Microsoft Internet Explorer")
 {
  top.location="<?php echo $aa['config']['redirect_url']['value']?>";
 }
 else
  {
   top.location="<?php echo $aa['config']['redirect_url']['value']?>";
   }
}
//-->
</SCRIPT>
<?php } else { ?>
<SCRIPT language="JavaScript">
<!--
var browserName=navigator.appName; 
if (browserName=="Netscape")
{ 
window.open("<?php echo $aa['config']['redirect_url']['value']?>");
top.location="<?php echo $aa['instance']['fb_page_url']?>";
}
else 
{ 
 if (browserName=="Microsoft Internet Explorer")
 {
  window.open("<?php echo $aa['config']['redirect_url']['value']?>");
  top.location="<?php echo $aa['instance']['fb_page_url']?>";
 }
 else
  {
   window.open("<?php echo $aa['config']['redirect_url']['value']?>");
   top.location="<?php echo $aa['instance']['fb_page_url']?>";
   }
}
//-->
</SCRIPT>
<?php } ?>
</HEAD>

<body>
<?php __p("you_will_be_redirected"); ?>
</body>
</html>
