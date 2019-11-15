<?php 
$placeholder = ACGTheme::get_option('placeholder_text', 'Search');
$search_button = ACGTheme::get_option('search_button_text', 'Search &raquo;');
?>
<form method="get" id="searchform" action="<?php echo home_url(); ?>">
    <div>
        <p><input type="text" name="s" id="s" value="" placeholder="<?php echo $placeholder; ?>"><button type="button" id="searchsubmit" class="button button-search"><?php echo $search_button; ?> <i class="fa fa-arrow-circle-right"></i></button></p>
    </div>
</form>