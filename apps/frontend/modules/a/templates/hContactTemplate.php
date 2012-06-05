<?php use_helper('a') ?>

<?php // Defining the <body> class ?>
<?php slot('a-body-class','a-home') ?>

<?php // Breadcrumb is removed for the home page template because it is redundant ?>
<?php slot('a-breadcrumb', '') ?>

<?php // Subnav is removed for the home page template because it is redundant ?>
<?php //slot('a-subnav', '') ?>

<?php a_slot('home-banner',
'aSlideshow', array(
	'width' => 960,
	'height' => 300,
	'resizeType' => 'c',
	'flexHeight' => false,
	'constraints' => array('minimum-width' => 960, 'minimum-height' => 300),
	'arrows' => true,
	'interval' => 8,
	'random' => true,
	'title' => false,
	'description' => false,
	'credit' => false,
	'position' => true,
	'transition' => 'crossfade',
	'duration' => 500,
	'itemTemplate' => 'homeBannerItem',
	'allowed_variants' => array('autoplay','normal'),
)) ?>

<?php include_partial('a/areaTemplate', array('name' => 'body', 'width' => 680)) ?>

<?php //include_partial('a/areaTemplate', array('name' => 'sidebar', 'width' => 240)) ?>

<?php slot('home-right') ?>
<article class="post a-area a-normal a-area-body " style="width: 600px;">
        
            <h1 class="entry-title">Contact</h1>
            
                <form action="sendmail.php" method="post" id="contactform">
                
                	<fieldset>
                    
                    	<legend>Contact me!</legend>
                    
                        <p>
                            <label for="name">Your Name</label>
                            <input type="text" name="name" id="name" value="" />
                        </p>
                        
                        <p>
                            <label for="email">Your Email</label>
                            <input type="email" name="email" id="email" required value="" />  
                        </p>
                        
                        <p>
                            <label for="subject">Subject</label>
                            <input type="text" name="subject" id="subject" value="" />
                        </p>
                        
                        <p>
                        	<label for="message">Your message</label>
                        	<textarea name="message" id="message" cols="50" rows="10" autofocus required></textarea>
                        </p>
                        
                        <!-- This is hidden for normal users -->
                        <div class="hide">
                            <label>Do not fill out this field</label>
                            <input name="spam_check" type="text" value="" />
                        </div>
                        
                        <p>
                        <input type="submit" name="submit" value="Send Email" />
                        </p>
                    
                    </fieldset>
                    
                    <p class="hide" id="response"></p>
                
                </form>
        
        </article> <!-- .post -->
    
    
<aside id="sidebar" role="complementary">
    
        <aside id="sub-nav" class="widget">
            <?php if (has_slot('a-subnav')): ?>
			<?php include_slot('a-subnav') ?>
		<?php elseif ($page): ?>
			<?php include_component('a', 'subnav', array('page' => $page)) # Subnavigation ?>
		<?php endif ?>
        </aside> <!-- .widget -->
        
        <aside class="widget">
            <!-- Non working search -->
            <form action="#" class="searchform">
                <input type="search" results="10" placeholder="Search..." />
                <input type="submit" value="Search" />
            </form> <!-- .searchform -->
        </aside> <!-- .widget -->
        
        <aside class="widget">
            <?php include_partial('a/areaTemplate', array('name' => 'quotes', 'width' => 280)) ?>
        </aside> <!-- .widget -->
    
    </aside> <!-- #sidebar -->
    </div> <!-- #content -->
<?php end_slot() ?>