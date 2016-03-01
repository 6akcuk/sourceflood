<?php

add_action('admin_menu', 'sourceflood_add_menu_items');
function sourceflood_add_menu_items()
{
    add_menu_page('Source Flood', 'Source Flood', 6, 'sourceflood');
    
    add_submenu_page('sourceflood', 'Posting', 'Posting', 1, 'sourceflood', 'sourceflood_posting');
    add_submenu_page('sourceflood', 'Projects', 'Projects', 1, 'sourceflood_projects', 'sourceflood_projects');
    add_submenu_page('sourceflood', 'Shortcodes', 'Shortcodes', 2, 'sourceflood_shortcodes', 'sourceflood_shortcodes');
}