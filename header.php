<!DOCTYPE html>
<html prefix="og: http://ogp.me/ns#">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title( '::' ) ?></title>
	<?php wp_head(); ?>
</head>
<body <?php body_class('wpinventory'); ?>>
<div class="container">
    <div class="headerwrapper">
        <header>
            <div class="site_center">
				<?php
                echo wpim_theme_logo();
				echo wpim_get_theme_social();
				?>
	            <?php wpim_theme_social(); ?>
                <div class="navwrapper">
                    <nav id="main">
                        <ul class="menu">
				            <?php echo acg_wp_list_pages( 'theme_location=primary&title_li=&depth=3&echo=0' ); ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </header>
    </div>
