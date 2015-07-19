<?php

// Find static page's route stack value
$route_stack = false;
foreach(Route::$routes as $route) {
    if($route['pattern'] === '(:any)') {
        $route_stack = $route['stack'];
        break;
    }
}

// The child page route
Route::accept('(:any)/(:any)', function($parent = "", $child = "") use($config) {
    // Check if parent page does not exist
    if( ! $page_parent = Get::pageAnchor($parent)) {
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
        'page_title' => $page_child->title . $config->title_separator . $page_parent->title . $config->title_separator . $config->title,
        'page_type' => 'page',
        'page' => $page_child
    ));
    // Attach the shield
    Shield::attach('page-' . $child);
}, $route_stack ? $route_stack - 1 : 99);

// Disallow child pages to be accessed directly as a normal page
Route::over('(:any)', function($slug = "") {
    $page = Get::pageHeader($slug);
    if(isset($page->fields->parent_page_slug) && trim($page->fields->parent_page_slug) !== "") {
        Shield::abort('404-page');
    }
});