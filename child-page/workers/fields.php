<?php

$o = array("" => '&mdash; ' . $speak->none . ' &mdash;');
$o_max = 500;
$o_count = 0;

if($pages = Get::pages()) {
    for($i = 0, $o_count = count($pages); $i < $o_max; ++$i) {
        if($i === $o_count) break;
        $parts = explode('_', File::N($pages[$i]), 3);
        $o[$parts[2]] = Get::pageAnchor($pages[$i])->title;
    }
}

return array(
    'parent_page_slug' => array(
        'title' => $speak->plugin_child_page_title_parent,
        'type' => $o_count <= $o_max ? 'o' : 't',
        'value' => $o_count <= $o_max ? $o : "",
        'description' => $speak->plugin_child_page_description_parent,
        'scope' => 'page'
    )
);