<?php use_helper('a') ?>

<?php // Defining the <body> class ?>
<?php slot('a-body-class','a-home') ?>

<?php // Breadcrumb is removed for the home page template because it is redundant ?>
<?php slot('a-breadcrumb', '') ?>

<?php // Subnav is removed for the home page template because it is redundant ?>
<?php //slot('a-subnav', '') ?>



<?php include_partial('a/areaTemplate', array('name' => 'body', 'width' => 680, 'class'=>'post')) ?>

<?php //include_partial('a/areaTemplate', array('name' => 'sidebar', 'width' => 240)) ?>

<?php slot('home-right') ?>

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
    
<?php end_slot() ?>