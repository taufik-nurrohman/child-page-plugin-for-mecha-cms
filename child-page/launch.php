<?php


Route::accept('(:any)/(:any)', function($parent = "", $child = "") use($config) {

    // Check if parent page does not exist
    if( ! $page_parent = Get::pageHeader($parent)) {
        Shield::abort('404-page');
    }

    // Check if child page does not exist
    if( ! $page_child = Get::page($child)) {
        Shield::abort('404-page');
    }

    // Check if custom field is not available
    if( ! isset($page_child->fields->parent_page_slug)) {
        Shield::abort('404-page');
    } else {
        // Check if custom field value != parent page slug
        if($page_child->fields->parent_page_slug != $parent) {
            Shield::abort('404-page');
        }
    }

    // Check if parent page or child page is a draft
    if($page_parent->state == 'draft' || $page_child->state == 'draft') {
        Shield::abort('404-page');
    }

    // Inject custom CSS data of child page if available
    Weapon::add('shell_after', function() use($page_child) {
        if(isset($page_child->css)) echo $page_child->css;
    });

    // Inject custom JavaScript data of child page if available
    Weapon::add('sword_after', function() use($page_child) {
        if(isset($page_child->js)) echo $page_child->js;
    });

    // Set the child page data
    Config::set(array(
        'page_type' => 'page',
        'page_title' => $page_child->title . $config->title_separator . $page_parent->title . $config->title_separator . $config->title,
        'page' => $page_child
    ));

    // Attach the shield
    Shield::attach('page-' . $child);

}, 111);


/**
 * Notice
 * ------
 *
 * Add a notice to the static page manager to indicate that the current page is a child page.
 *
 */

Weapon::add('page_footer', function($page) {
    $config = Config::get();
    $speak = Config::speak();
    if($config->page_type == 'manager' && isset($page->fields->parent_page_slug) && trim($page->fields->parent_page_slug) !== "") {
        echo ' &middot; ' . sprintf($speak->plugin_child_page_description_child_of, $config->url . '/' . $page->fields->parent_page_slug, Get::pageAnchor($page->fields->parent_page_slug)->title, $config->url . '/' . $page->fields->parent_page_slug . '/' . $page->slug);
    }
}, 21);


/**
 * Prevent Direct Access of Child Page
 * -----------------------------------
 *
 * Disallow child pages to be accessed directly as a normal page.
 *
 */

Weapon::add('before_route_function_call', function($url, $route, $params) {
    if($route['pattern'] == '(:any)') {
        $page = Get::page($params[0]);
        if(isset($page->fields->parent_page_slug) && trim($page->fields->parent_page_slug) !== "") {
            Shield::abort('404-page');
        }
    }
});