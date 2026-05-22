@extends('layouts.app')

@section('content')

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">



<html>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
	
	<head>
		<title>RECON Payment Services</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="Content-Type" content="payment/html; charset=utf-8">
		<link rel="icon" type="image/png" href="?Tue Mar 21 12:13:44 HKT 2023">
	</head>
	<body>
		<form id="submit3DPurchaseForm" name="submit3DPurchaseForm" method="post" action="https://recon-uat.cityline.com:443/ws/submitParams">
			<input type="hidden" id="parameter" name="parameter" value="fe7a5ee0678f931a768833e1149832dbc40f34f9d9dd33908ebbe650325ba6d476d778be03955f7d4a9b247cf7ea5000a9ac997132ee8bbeaa3c8ad138b54a6776c8bb1769ad92d44a8b9f45b5469d2be0f7db9c75c500f5045ea9838c21768ed9f59bafa581140c845c4a7ef06360cf32421c10461d89a162e80d50ece5a5637f15eefc4b8c5f2f596519dd551fdd65253a6305f2ec8d9fb68f4a59aed28bce0c35071e0e763855d997c3a39eded9da7ccd512a4ec58861c8f66b8aaf3bc13df4b8d32f0761339507a61782db9c2cfb3ded56b955b7a67ad56edc5a4e01c1f944f7026022b27e931d66c0add0995b7103e38cde7b2886282aa8abb3ece3eb75d1bd794b23972b1747e0bd7aeda166de1a8b9c1b45187b8e004767e6fa0a5845" />
			<input type="hidden" id="lang" name="lang" value="en" />
			<div style="font-family: Helvetica, Arial, Microsoft YaHei; text-align:center; height: 70%; position: absolute; margin: auto; top: 0; right: 0; bottom: 0; left: 0;">
				<img style="text-align:center; max-width:100%;" src="images/processing.gif">
			</div>
		</form>
	</body>
	<script language="javascript">
		document.oncontextmenu=new Function("return false");
		function window_onload()
		{
			document.submit3DPurchaseForm.submit();
		}
		window.onload = window_onload;
	</script>
</html>

</html>
@endsection
