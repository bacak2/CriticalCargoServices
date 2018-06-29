
<?php
/*wyciąganie numeru dnia, do id*/
$content = $this->dane['days']['content'][$this->dane['days']['head']['date'][$i+1]];
$_temp_array=explode('-',$content['date']);
$_id_part=$_temp_array[2];
?>

<!-- nagłówek -->
<div class="calendar_content_week_head">
	<!-- data -->
	<div class="ab_week_content_date" id="date_<?php echo $_id_part; ?>">
		<?php echo $content['date']; ?>
	</div>
	
	<!-- menu -->
	<ul class="ab_week_content_menu" id="menu_<?php echo $_id_part; ?>">
		<li>do wykonania: <?php echo $content['task_no']; ?></li>
		<li>zaległe: <?php echo $content['task_past']; ?></li>
		<li class="last"><a href="<?php echo PP_CTRL; ?>/zdarzenia/index/<?php echo $content['date']; ?>" title="przejdź">przejdź</a></li>
	</ul>
</div>

<!-- treść -->
<?php
foreach($content['event'] as $k => $v)
{	?>
	<div class="calendar_content_week_customer">
		<div class="name">
			<span><?php echo ($k+1); ?>.</span>&nbsp;
			<span class="event_content link <?php echo $v['class']; ?>" id="event_<?php echo $v['id']; ?>" onclick="getSmallEventDesc('','<?php echo $v['id']; ?>')">
				<?php echo substr($v['name'],0,9).'...'; ?>
			</span>
		</div>
	</div>
	<?php
}
?>