
<!-- przejdź do daty -->
<div id="go_to_chosen_day">
	<div id="calendar_current_left"></div>
	<div id="calendar_current">
		<a href="<?php echo $this->dane['link']['current'].$this->dane['link']['current_get'].($this->dane['link']['set']['type']=='week' ? '&calendar_view=week' : ''); ?>" title="wyświetla aktualny <?php echo ($this->dane['link']['set']['type']=='week' ? 'tydzień' : 'miesiąc'); ?>">
			aktualny <?php echo ($this->dane['link']['set']['type']=='week' ? 'tydzień' : 'miesiąc'); ?>
		</a>
		</div>
	<div id="calendar_current_right"></div>

	<img src="images/select_button.png" onmouseout="this.src='images/select_button.png'" onmouseover="this.src='images/select_button_hover.png'" id="year_picker" alt="wybierz rok" />
	<div class="calendar_select">
		<span id="year_chosen">- <?php echo $this->dane['current']['year']; ?> -</span>
		<ul id="year_option">
			<?php
			for($i=(intval(date('Y'))-5);$i<(intval(date('Y'))+11);$i++)
			{	?>
				<li id="calendar_year_<?php echo $i; ?>">
					<?php echo $i; ?>
				</li>
				<?php
			}
			?>
		</ul>
	</div>
	<div class="calendar_select_left"></div>

	<div class="space"></div>

	<img src="images/select_button.png" onmouseout="this.src='images/select_button.png'" onmouseover="this.src='images/select_button_hover.png'" id="month_picker" alt="wybierz miesiąc" />
	<div class="calendar_select">
		<span id="month_chosen">- <?php echo $this->dane['month_array'][$this->dane['current']['month_int']]; ?> -</span>
		<ul id="month_option">
			<?php
			for($i=1;$i<13;$i++)
			{	?>
				<li id="calendar_month_<?php echo $i; ?>">
					<?php echo $this->dane['month_array'][$i]; ?>
				</li>
				<?php
			}
			?>
		</ul>
	</div>
	<div class="calendar_select_left"></div>

	<input type="hidden" value="<?php echo $this->dane['current']['month_int']; ?>" id="month_picker_calendar" />
	<input type="hidden" value="<?php echo $this->dane['current']['year']; ?>" id="year_picker_calendar" />
	<input type="hidden" value="<?php echo $this->dane['link']['current']; ?>" id="calendar_go_to" />

	<div class="clear"></div>
</div>

<!-- nagłówek, zmiana wyświetlanego miesiąca/tygodnia, przełączanie widoków: tygodniowy, miesięczny -->
<div id="current_days" style="margin-top: 20px;">
	<div class="switch_date" id="prev_date">
		<a href="<?php echo $this->dane['link']['prev']; ?>" title="poprzedni">
			<img src="images/prev_button.png" onmouseout="this.src='images/prev_button.png'" onmouseover="this.src='images/prev_button_hover.png'" alt="poprzedni" />
		</a>
	</div>

	<div class="switch_date" id="next_date">
		<a href="<?php echo $this->dane['link']['next']; ?>" title="następny">
			<img src="images/next_button.png" onmouseout="this.src='images/next_button.png'" onmouseover="this.src='images/next_button_hover.png'" alt="następny" />
		</a>
	</div>

	<div id="current_date_left"></div>
	<div id="current_date_right"></div>
	<div id="current_date">
		<?php echo $this->dane['current']['desc']; ?>
		<a id="switch_calendar_view" href="<?php echo $this->dane['link']['set']['link']; ?>" title="przełącz widok">
			<?php echo ($this->dane['link']['set']['type']=='month' ? '[ tydzień ]' : '[ miesiąc ]'); ?>
		</a>
	</div>
</div>

<!-- kalendarz -->
<div id="calendar">
	<?php
            $this->ShowCalendarViev();
        ?>
</div>