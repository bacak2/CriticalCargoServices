
<?php	
echo $this->dane['days']['head']['date']['var'];
/*wy�wietlanie kom�rek z opisem s�ownym dni*/
for($i=0;$i<5;$i++)
{	?>
	<div class="calendar_cell_week day_as_string"<?php echo (($this->dane['days']['head']['date'][$i+1]==$this->dane['days']['active']) ? ' style="color:#ff6928"' : '');?>>
		<?php echo $this->dane['days']['head']['desc'][$i]; ?><br />
		<?php
			$date_parts=explode('-',$this->dane['days']['head']['date'][$i+1]);
			echo $date_parts[2].' '.$this->dane['days']['month_desc'][$date_parts[1]];
		?>
	</div>
	<?php
}
/*kom�rki z dniami*/
for($i=0;$i<5;$i++)
{	?>
	<div class="calendar_cell_week week_day"<?php echo (($this->dane['days']['head']['date'][$i+1]==$this->dane['days']['active']) ? ' id="active_day"' : '');?>>
		<div class="day_content_week">
                        <?php
                            include(SCIEZKA_SZABLONOW."calendar/week_content.tpl.php");
                        ?>
		</div>
	</div>
	<?php
}
?>