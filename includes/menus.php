<?php

add_action('admin_menu', 'sourceflood_add_menu_items');
function sourceflood_add_menu_items()
{
    add_menu_page('Work Horse', 'Work Horse', 6, 'workhorse');
    
    add_submenu_page('workhorse', 'Posting', 'Posting', 1, 'workhorse', 'sourceflood_posting');
    add_submenu_page('workhorse', 'Projects', 'Projects', 1, 'workhorse_projects', 'sourceflood_projects');
    add_submenu_page('workhorse', 'Shortcodes', 'Shortcodes', 2, 'workhorse_shortcodes', 'sourceflood_shortcodes');
    add_submenu_page('workhorse', 'Settings', 'Settings', 2, 'workhorse_settings', 'sourceflood_settings');

    add_submenu_page('workhorse', 'Builder', 'Builder', 0, 'workhorse_builder', 'workhorse_builder');
}