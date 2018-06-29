<div class="calendar_content_month" id="<?php echo str_replace('-','_',$date); ?>">

<?php

/*wyświetlanie odpowiedniego obrazka w zależności, czy treść dotyczy dnia dzisiejszego, czy nie*/
if($date===date('Y-m-d'))
{	?>
	<img src="images/background/calendar_clock_bg_warning.png"
		 onmouseout="this.src='images/background/calendar_clock_bg_warning.png'"
		 onmouseover="this.src='images/background/calendar_clock_bg_warning_hover.png'"
		 onclick="getCalendarMonthContent('<?php echo str_replace('-','_',$date); ?>','<?php echo $date; ?>','')" 
		 alt="zadania na dziś [<?php echo $date; ?>]" 
		 title="zadania na dziś [<?php echo $date; ?>]" />
	<?php
}
else
{	?>
	<img src="images/background/calendar_clock_bg.png"
		 onmouseout="this.src='images/background/calendar_clock_bg.png'"
		 onmouseover="this.src='images/background/calendar_clock_bg_hover.png'"
		 onclick="getCalendarMonthContent('<?php echo str_replace('-','_',$date); ?>','<?php echo $date; ?>','')" 
		 alt="zadania na dzień <?php echo $date; ?>" 
		 title="zadania na dzień <?php echo $date; ?>" />
	<?php
}
?>

<?php
if($content!==true && is_string($content)===true)
{	/*jeżeli została przesłana jakaś treść, to zostaje wyświetlona*/
	?>
	<div class="proper_content">
		<?php echo $content; ?>
	</div>
	<?php
}
?>

</div>