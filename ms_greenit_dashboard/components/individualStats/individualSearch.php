<?php

$form_name = "individualSearch";
$table_name = $form_name;
$tab_options_individual_search = $protectedPost;
$tab_options_individual_search['form_name'] = $form_name;
$tab_options_individual_search['table_name'] = $table_name;

$list_fields_individual_search = array(
    $l->g(35) => 'NAME',
    $l->g(190) . ' ' . strtoupper($l->g(1425)) => 'TAG',
    $l->g(25) => 'OSNAME',
);

$list_col_cant_del_individual_search = $list_fields_individual_search;
$default_fields_individual_search = $list_fields_individual_search;

$sql_individual_search['SQL'] = "SELECT DISTINCT hardware.NAME as NAME, hardware.OSNAME as OSNAME, accountinfo.TAG as TAG FROM hardware INNER JOIN accountinfo ON hardware.ID = accountinfo.hardware_id INNER JOIN greenit ON hardware.ID = greenit.HARDWARE_ID";

$tab_options_individual_search['LIEN_LBL'][$l->g(35)] = 'index.php?function=ms_greenit_dashboard&cat=individualstats&'.strtolower(str_replace(" ", "_",$l->g(35))).'=';
$tab_options_individual_search['LIEN_CHAMP'][$l->g(35)] = 'NAME';

echo open_form($form_name, '', '', 'form-horizontal');

echo "<br><h4>".$l->g(80904)."</h4>";

echo '
<div class="form-group">
    <div class="col-sm-12">
';

ajaxtab_entete_fixe($list_fields_individual_search, $default_fields_individual_search, $tab_options_individual_search, $list_col_cant_del_individual_search);

echo '
    </div>
</div>
';


echo "<hr>";
echo close_form();

?>