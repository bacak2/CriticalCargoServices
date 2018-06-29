<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-2">
	<title></title>
	<script language="JavaScript">
	<!--
		szerokosc = screen.width/2 - <?PHP echo $width; ?>/2;
		wysokosc = screen.height/2 - <?PHP echo $height; ?>/2;
		self.moveTo(szerokosc,wysokosc);
		wysokosc = <?PHP echo $height; ?> + 50;
		szerokosc = <?PHP echo $width; ?> + 12;
		self.resizeTo(szerokosc, wysokosc);
	//-->
	</script>
</head>
<body bgcolor="white" leftmargin="0" marginheight="0" marginwidth="0" topmargin="0">
	<div align="center">
		<span onclick="window.close();"><img src="<?PHP echo $obrazek;?>" border="0"></span>
	</div>
</body>
</html>