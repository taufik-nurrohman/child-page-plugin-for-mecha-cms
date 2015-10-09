<?php

$o = array();
$o_max = 200;
$o_count = 0;

if($pages = Get::pages()) {
    for($i = 0, $o_count = count($pages); $i < $o_max; ++$i) {
        if($i === $o_count) break;
        $parts = explode('_', File::N($pages[$i]), 3);
        $o[$parts[2]] = Get::pageAnchor($pages[$i])->title;
    }
}

asort($o);

return array(
    'parent_page_slug' => array(
        'title' => $speak->plugin_child_page_title_parent,
        'type' => $o_count <= $o_max ? 'option' : 'text',
        'value' => $o_count <= $o_max ? array("" => '&mdash; ' . $speak->none . ' &mdash;') + $o : "",
        'description' => $speak->plugin_child_page_description_parent,
        'scope' => 'page'
    )
);