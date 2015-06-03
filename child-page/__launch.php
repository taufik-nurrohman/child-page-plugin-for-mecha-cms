<?php

// Re-define page URL in the backend view of static page list
if($config->url_path === $config->manager->slug . '/page' || strpos($config->url_path, $config->manager->slug . '/page/') === 0) {
    Filter::add('shield:lot', function($data) {
        if(isset($data['config']->pages) && $data['config']->pages !== false) {
            foreach($data['config']->pages as &$page) {
                if(isset($page->fields->parent_page_slug) && trim($page->fields->parent_page_slug) !== "") {
                    $uri = explode('/', $page->url);
                    $uri_end = array_pop($uri);
                    $page->url = implode('/', $uri) . '/' . $page->fields->parent_page_slug . '/' . $uri_end;
                }
            }
            unset($page);
        }
        return $data;
    });
}