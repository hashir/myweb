<?php use_helper('a') ?>

<?php $page = aTools::getCurrentNonAdminPage() ?>
	
<?php a_slot('footer', 'aRichText', array(
	'global' => true,
	'edit' => (isset($page) && $sf_user->hasCredential('cms_admin')) ? true : false,
)) ?>