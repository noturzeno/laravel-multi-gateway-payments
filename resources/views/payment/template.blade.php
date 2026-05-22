<html>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=big5">

<head>
	<title>RECON Payment Services</title>
</head>

<body onKeyDown="onKeyDown();">
	<form id="unavailableForm" name="unavailableForm" method="post"
		action="https://recon-uat.cityline.com:443/ws/unavailable">
		<input type="hidden" name="resMsg" value="[9001] Internal error">
		<input type="hidden" name="merCode" value="Default">
		</form>
</body>
<script language="javascript">
	document.oncontextmenu=new Function("return false");
		function onKeyDown(){event.keyCode = 0;event.returnValue = false;}
		function window_onload()
		{
			document.unavailableForm.submit();
		}
		window.onload = window_onload;
</script>

</html>