<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>
    <style type="text/css">
    <!--

        html {
                margin: 0px;
                padding: 0px;
        }

        body {
                margin: 0px;
                padding: 0px;
                font-family: Verdana, Arial, Helvetica, sans-serif;
                font-size: 8pt;
                text-align: center;
        }

        div {
                margin: 0px;
        }

        p {
            padding: 0; margin: 0;
}

        div.editable{
            position: absolute;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }

        div.editable textarea,  div.editable input, div.editable div.show{
            width: 97%;
            height: 90%;
            font-family: Arial;
            font-size: 11px;
            font-weight: bold;
        }

        div.editable textarea, div.editable input{
            background: transparent; 
            border: 2px solid #FF0000;
            margin: 0;
            padding: 0;
        }

        div.editable div.show{
            display: none;
            margin: 2px;
            padding: 0;
        }

        td {vertical-align: top;}

        .cke_editable{
            width: 100%;
            height: 100%;
        }

        @media print{
	  div#print {display:none}
          div#print-save {display:none}
          div.editable textarea, div.editable input {border: none; margin: 2px;}
        }
    -->
    </style>
    <script language="javascript" type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
    <script type="text/javascript">
        var CKEDITOR_BASEPATH = 'js/ckeditor/';
    </script>
    <script language="javascript" type="text/javascript" src="js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript">
        function FontResize(mode){
            var ourText = $('td');
            var currFontSize = ourText.css('fontSize');
            var finalNum = parseFloat(currFontSize, 10);
            var stringEnding = currFontSize.slice(-2);
            if(mode == 'up') {
                finalNum *= 1.2;
             }else{
                finalNum /=1.2;
             }
            ourText.css('fontSize', finalNum + stringEnding);
        }

        function GoToPrint(){
            $(".textarea").each(function(){
                text = $(this).children("textarea:first").val();
                $(this).children("textarea:first").hide();
                $(this).children("div.show").html(text).show();
            })
            $(".inputek").each(function(){
                text = $(this).children(".input_class:first").val();
                $(this).children(".input_class:first").hide();
                $(this).children("div.show").html(text).show();
            })
            $(".to-print-2").show();
            $(".to-print").hide();
        }

        function PrintBack(){
            $("textarea").show();
            $(".input_class").show();
            $("div.show").hide();
            $(".to-print-2").hide();
            $(".to-print").show();
        }

        function GoToPrint2(){
            document.formek.submit();
        }
     </script>
</head>