<?php use_helper('a') ?>

<?php // Defining the <body> class ?>
<?php slot('a-body-class','a-default') ?>

<?php include_partial('a/areaTemplate', array('name' => 'body', 'width' => 480)) ?>

<?php include_partial('a/areaTemplate', array('name' => 'sidebar', 'width' => 480)) ?>

<?php slot('a-footer') ?>
<div class='a-footer-wrapper clearfix'>
	<div class='a-footer clearfix'>
	  <?php include_partial('a/footer') ?>
		<?php include_partial('aFeedback/feedbackForm'); ?>	
	</div>
</div>
<?php end_slot() ?>