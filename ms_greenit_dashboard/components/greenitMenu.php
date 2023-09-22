<?php

global $protectedGet;
$menu_serializer = new XMLMenuSerializer();
$menu = $menu_serializer->unserialize(file_get_contents('extensions/greenit/ms_greenit_dashboard/config/menu.xml'));

$menu_renderer = new MenuRenderer();


echo "<div class='col-md-2'>";
echo "<ul class='nav nav-pills nav-stacked'>";

foreach ($menu->getChildren() as $menu_elem) {
    if (isset($protectedGet['cat']) && $protectedGet['cat'] == explode('=',$menu_elem->getUrl())[2]) {
        echo$menu_renderer->setActiveLink($menu_elem->getUrl());
    }
    echo $menu_renderer->renderElem($menu_elem);
}

echo '</ul>';
echo '</div>';

?>