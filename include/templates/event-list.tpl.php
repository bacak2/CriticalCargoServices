
<!-- menu -->
<div id="event_list_menu">
	<div class="orange_small_button_left"></div>
	<div class="orange_small_button_middle">
		<a href="<?php echo $this->LinkPowrotu; ?>" title="wyświetla aktualny dzień" class="orange_button">
			aktualny dzień
		</a>
		</div>
	<div class="orange_small_button_right"></div>

	<div class="clear"></div>
</div>

<!-- wyświetlana data -->
<div id="event_list_header"  style="margin-top: 20px;">
	<div class="orange_header_left"></div>
	<div class="orange_header_right"></div>
	<div class="orange_header_middle">
		Zdarzenia w dniu <?php echo $_current_date; ?>
	</div>

	<div class="clear"></div>
</div>
<table class="table">
    <tr class="table_row table_desc_row">
        <td class="cols table_event_col_lp">
            Lp
        </td>
        <td class="cols">
            Klient
        </td>
</tr>
    <?php
        foreach($EventList as $idx => $event){
        ?>
            <tr class="cols_bg_1 table_row table_value_row">
                <td class="cols table_event_col_lp"><?php echo $event[0]; ?></td>
                <td class="cols"><div class="table_event_col_customer"><?php echo $event[1] ?></div></td>
            </tr>
<?php
    }
?>
</table>