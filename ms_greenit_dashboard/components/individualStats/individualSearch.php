<?php

$form_name = "individualSearch";
$table_name = $form_name;
$tab_options_individual_search = $protectedPost;
$tab_options_individual_search['form_name'] = $form_name;
$tab_options_individual_search['table_name'] = $table_name;

$list_fields_individual_search = array(
    $l->g(35) => 'NAME',
    $l->g(190) . ' ' . strtoupper($l->g(1425)) => 'TAG',
    $l->g(25) => 'OS_NAME',
);

$list_col_cant_del_individual_search = $list_fields_individual_search;
$default_fields_individual_search = $list_fields_individual_search;

$tab_options_individual_search['LIEN_LBL'][$l->g(35)] = 'index.php?function=ms_greenit_dashboard&cat=individualstats&'.strtolower(str_replace(" ", "_",$l->g(35))).'=';
$tab_options_individual_search['LIEN_CHAMP'][$l->g(35)] = 'NAME';

echo open_form($form_name, '', '', 'form-horizontal');

echo "<br><h4>".$l->g(80904)."</h4>";

echo '
<div class="form-group">
    <div class="col-sm-12">
';

if (
    is_defined($_SESSION['GREENIT']['FILTER']['OS']) || 
    is_defined($_SESSION['GREENIT']['FILTER']['GROUP']) || 
    is_defined($_SESSION['GREENIT']['FILTER']['TAG']) ||
    is_defined($_SESSION['GREENIT']['FILTER']['ASSET'])
)
{
    msg_warning($l->g(767));
}

ajaxtab_entete_fixe($list_fields_individual_search, $default_fields_individual_search, $tab_options_individual_search, $list_col_cant_del_individual_search);

// ****************************************** FILTER ******************************************/
echo '
        <button type="button" data-toggle="collapse" data-target="#filter" class="btn">'.$l->g(735).'</button>
';

echo '
        <div id="filter" class="collapse">
';

// FILTER OS/GROUP/TAG/ASSET

// OS
echo '<div class="form-group">';
echo "<label class='control-label col-sm-2' for='OS'>".$l->g(25)."</label>";
echo "<div class='col-sm-3'>";
echo "<select name='OS' id='OS' class='form-control'>";
foreach($os as $key => $name) {
    if(isset($_SESSION['GREENIT']['FILTER']['OS']) && $_SESSION['GREENIT']['FILTER']['OS'] == $key) {
        echo "<option value='".$key."' selected>".$name."</option>";
    } else {
        echo "<option value='".$key."'>".$name."</option>";
    }
}
echo "</select>";
echo "</div>";


// GROUP
echo "<label class='control-label col-sm-2' for='GROUP'>".$l->g(583)."</label>";
echo "<div class='col-sm-3'>";
echo "<select name='GROUP' id='GROUP' class='form-control'>";
foreach($group as $key => $name) {
    if(isset($_SESSION['GREENIT']['FILTER']['GROUP']) && $_SESSION['GREENIT']['FILTER']['GROUP'] == $key) {
        echo "<option value='".$key."' selected>".$name."</option>";
    } else {
        echo "<option value='".$key."'>".$name."</option>";
    }
}
echo "</select>";
echo "</div>";
echo "</div>";

// TAG
echo '<div class="form-group">';
echo "<label class='control-label col-sm-2' for='TAG'>".$l->g(1425)."</label>";
echo "<div class='col-sm-3'>";
echo "<select name='TAG' id='TAG' class='form-control'>";
foreach($tag as $key => $name) {
    if(isset($_SESSION['GREENIT']['FILTER']['TAG']) && $_SESSION['GREENIT']['FILTER']['TAG'] == $key) {
        echo "<option value='".$key."' selected>".$name."</option>";
    } else {
        echo "<option value='".$key."'>".$name."</option>";
    }
}
echo "</select>";
echo "</div>";

// ASSET CATEGORY
echo '<div class="form-group">';
echo "<label class='control-label col-sm-2' for='ASSET'>".$l->g(2132)."</label>";
echo "<div class='col-sm-3'>";
echo "<select name='ASSET' id='ASSET' class='form-control'>";
foreach($asset as $key => $name) {
    if(isset($_SESSION['GREENIT']['FILTER']['ASSET']) && $_SESSION['GREENIT']['FILTER']['ASSET'] == $key) {
        echo "<option value='".$key."' selected>".$name."</option>";
    } else {
        echo "<option value='".$key."'>".$name."</option>";
    }
}
echo "</select>";
echo "</div>";
echo "</div>";
// END FILTER OS/GROUP/TAG/ASSET

echo '<input type="submit" class="btn btn-success" value="'.$l->g(393).'" name="SUBMIT_FORM">';
echo '<input type="submit" class="btn btn-danger" value="'.$l->g(396).'" name="RESET">';

echo '
    </div>
</div>
';


echo "<hr>";
echo close_form();

?>