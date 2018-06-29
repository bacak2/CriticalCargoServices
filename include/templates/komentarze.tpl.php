<div class="form-title">
        <?php echo $desc; ?> <span class="small_head_to_comments">[ komentarze do zadań ]</span>
</div>
<div class="clear"></div>
<div id="comments_list">

	<!-- wyświetlanie komentarzy -->
	<?php
	foreach($comments as $comment)
	{	?>
		<div class="event_comment">

			<div class="event_comment_head">
				napisał: <?php echo $this->Userzy[$comment['id_uzytkownik']]; ?>, dnia: <?php echo $comment['data_zakonczenia']; ?> 
			</div>
			<div class="event_comment_body">
				<?php echo nl2br($comment['komentarz']); ?>
			</div>

		</div>
		<?php
	}
	?>

</div>