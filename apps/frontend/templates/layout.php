<?php use_helper('a') ?>
<?php // This is a copy of apostrophePlugin/modules/a/templates/layout.php ?>
<?php // It also makes a fine site-wide layout, which gives you global slots on non-page templates ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<?php // If this page is an admin page we don't want to present normal navigation relative to it. ?>
	<?php $page = aTools::getCurrentNonAdminPage() ?>
  <?php $root = aPageTable::retrieveBySlug('/') ?>
<head>
	<?php include_http_metas() ?>
	<?php include_metas() ?>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<?php include_title() ?>
	<?php // 1.3 and up don't do this automatically (no common filter) ?>
	<?php // a_include_stylesheets has a built in caching combiner/minimizer when enabled ?>
  <?php a_include_stylesheets() ?>
	<?php a_include_javascripts() ?>
	
	<?php if (has_slot('og-meta')): ?>
		<?php include_slot('og-meta') ?>
	<?php endif ?>

	<?php if ($fb_page_id = sfConfig::get('app_a_facebook_page_id')): ?>
		<meta property="fb:page_id" content="<?php echo $fb_page_id ?>" />		
	<?php endif ?>
	
	<link rel="shortcut icon" href="/favicon.ico" />
	
	<!--[if lt IE 7]>
		<link rel="stylesheet" type="text/css" href="/css/ie6.css" />	
		<script type="text/javascript">
			$(document).ready(function() {
				apostrophe.IE6({'authenticated':<?php echo ($sf_user->isAuthenticated())? 'true':'false' ?>, 'message':<?php echo json_encode(__('You are using IE6! That is just awful! Apostrophe does not support editing using Internet Explorer 6. Why don\'t you try upgrading? <a href="http://www.getfirefox.com">Firefox</a> <a href="http://www.google.com/chrome">Chrome</a> 	<a href="http://www.apple.com/safari/download/">Safari</a> <a href="http://www.microsoft.com/windows/internet-explorer/worldwide-sites.aspx">IE8</a>', null, 'apostrophe')) ?>});
			});
		</script>
	<![endif]-->	

	<!--[if lte IE 8]>
		<link rel="stylesheet" type="text/css" href="/apostrophePlugin/css/a-ie.css" />	
		<link rel="stylesheet" type="text/css" href="/css/ie7-8.css" />	
	<![endif]-->
	<!-- Mobile viewport optimized: j.mp/bplateviewport -->
 	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <link href="/css/style.css" rel="stylesheet" />
    <link href="http://fonts.googleapis.com/css?family=Droid+Serif:regular,bold" rel="stylesheet" /> <!-- Load Droid Serif from Google Fonts -->
    
    <!-- All JavaScript at the bottom, except for Modernizr and Respond.
    	Modernizr enables HTML5 elements & feature detects; Respond is a polyfill for min/max-width CSS3 Media Queries -->
    <script src="/js/modernizr-1.7.min.js"></script>
    <script src="/js/respond.min.js"></script>
</head>

<?php $a_bodyclass = '' ?>
<?php $a_bodyclass .= ($sf_user->isAuthenticated()) ? ' logged-in':' logged-out' ?> 
<?php $a_bodyclass .= ($page && $page->archived) ? ' a-page-unpublished' : '' ?> 
<?php $a_bodyclass .= ($page && $page->view_is_secure) ? ' a-page-secure' : '' ?> 
<?php $a_bodyclass .= (sfConfig::get('app_a_js_debug', false)) ? ' js-debug':'' ?>

<?php // a-body-class allows you to set a class for the body element from a template ?>
<?php // body_class is preserved here for backwards compatibility ?>
<body class="<?php if (has_slot('a-body-class')): ?> <?php include_slot('a-body-class') ?><?php endif ?><?php if (has_slot('body_class')): ?> <?php include_slot('body_class') ?><?php endif ?><?php echo $a_bodyclass ?>">


	<?php include_partial('a/doNotEdit') ?>
  <?php include_partial('a/globalTools') ?>
	
	<div class="a-wrapper wrapper clearfix">

    <?php // Note that just about everything can be suppressed or replaced by setting a ?>
    <?php // Symfony slot. Use them - don't write zillions of layouts or do layout stuff ?>
    <?php // in the template (except by setting a slot). To suppress one of these slots ?>
    <?php // completely in one line of code, just do: slot('a-whichever', '') ?>
      
    
   <div id="main" class="clearfix" style="margin-top: 21px;"> 
    
<nav id="menu" class="clearfix" role="navigation">
		<?php if (has_slot('a-tabs')): ?>
			<?php include_slot('a-tabs') ?>
		<?php else: ?>
			<?php include_component('aNavigation', 'tabs', array('root' => $root, 'active' => $page, 'name' => 'main', 'draggable' => true, 'dragIcon' => false)) # Top Level Navigation ?>
		<?php endif ?>
</nav>
		

    <?php if (has_slot('a-page-header')): ?>
			<?php include_slot('a-page-header') ?>
 		<?php endif ?>
	
            
		<div class="a-content content clearfix">
			<?php echo $sf_data->getRaw('sf_content') ?>
		
                    <?php if (has_slot('home-right')): ?>
	  <?php include_slot('home-right') ?>
	<?php endif; ?>
            
	</div>
</div>
            </div>
	<?php if (has_slot('a-footer')): ?>
	  <?php include_slot('a-footer') ?>
	<?php else: ?>
		<div class='a-footer-wrapper clearfix'>
			<div class='a-footer clearfix'>
	  	  <?php include_partial('a/footer') ?>
			</div>
		</div>
	<?php endif ?>	

	<?php include_partial('a/googleAnalytics') ?>
	
  <?php // Invokes apostrophe.smartCSS, your project level JS hook and a_include_js_calls ?>
	<?php include_partial('a/globalJavascripts') ?>

</body>
</html>
<script>
    $(document).ready(function(){
        
        $('#menu ul').removeClass('a-nav a-nav-main tabs nav-depth-0 clearfix ui-sortable');
        $('#sub-nav div').removeClass('a-ui a-subnav-wrapper');
        if(window.location=='http://apostrophe.local/'){
            window.location.href='http://apostrophe.local/about-me';
        }
    });
</script>