
<!-- wyświetlanie dni -->
<div class="month_days">
	<?php
	/*wyświetlanie komórek z opisem słownym dni*/
	foreach($this->dane['days']['desc'] as $key => $day)
	{	?>
		<div class="calendar_cell day_as_string" style="<?php echo($key==6 || $key==5 ? 'color:red;' : ''); ?>">
			<?php echo $day; ?>
		</div>
		<?php
	}
	
	/*komórki z dniami*/
	foreach($this->dane['days']['days'] as $key => $day)
	{	?>
		<div class="calendar_cell day<?php echo ($key%7==6 || $key%7==0 ? ' weekend' : ''); ?><?php echo ($day===false ? ' not_day' : ''); ?>"<?php echo (($day==$this->dane['days']['active'] && $day!==false) ? 'id="active_day"' : '');?>>
			<div class="day_desc">
				<?php echo $day; ?>
			</div>
			<div class="day_content_month">
                                <?php
                                    if(!is_null($this->dane['days']['content'][$day])){
                                        $content = $this->dane['days']['content'][$day];
                                        $date = $this->dane['days']['content'][$day]['date'];
                                        include(SCIEZKA_SZABLONOW."calendar/month_content.tpl.php");
                                    }
                                ?>
			</div>
		</div>
		<?php
		if($key%7==0)
		{	/*kontrolowanie tygodnia*/
			if($current_month==1 && $this->dane['first_week']>5)
			{	/*jeżeli to styczeń*/
				if($this->dane['first_week']==date('W',mktime(0,0,0,1,1,$current_year)))
				{	$_temp=explode('&',$week_link);
					foreach($_temp as $_temp_part)
					{	$_temp_small_part=explode('=',$_temp_part);
						if($_temp_small_part[0]=='year')
						{	$week_link=str_replace($_temp_part,'year='.(intval($current_year)-1),$week_link);
							break;						
						}
					}					
				}
				elseif($this->dane['first_week']>date('W',mktime(0,0,0,1,1,$current_year)))
				{	$_temp=explode('&',$week_link);
					foreach($_temp as $_temp_part)
					{	$_temp_small_part=explode('=',$_temp_part);
						if($_temp_small_part[0]=='year')
						{	$week_link=str_replace($_temp_part,'year='.intval($current_year),$week_link);
							break;						
						}
					}
					$this->dane['first_week']=1;
				}
			}
			elseif($current_month==12)
			{	/*jeżeli to grudzień*/
				if($this->dane['first_week']>52)
				{	if(intval(date('W',mktime(0,0,0,12,31,$current_year)))===1)
					{	$_temp=explode('&',$week_link);
						foreach($_temp as $_temp_part)
						{	$_temp_small_part=explode('=',$_temp_part);
							if($_temp_small_part[0]=='year')
							{	$week_link=str_replace($_temp_part,'year='.intval($current_year+1),$week_link);
								break;						
							}
						}
						$this->dane['first_week']=1;
					}
				}
			}
			/*łamanie lini*/
			?>
			<div class="week_cell">
				<a href="<?php echo $this->dane['week_link'].$this->dane['first_week']; ?>" title="tydzień <?php echo $this->dane['first_week']; ?>">
					<img src="images/week_button.png" onmouseout="this.src='images/week_button.png'" onmouseover="this.src='images/week_button_hover.png'" alt="tydzień <?php echo $this->dane['first_week']++; ?>" />
				</a>
			</div>
			<div class="clear"></div>
			<?php
		}
	}	
	?>
</div>