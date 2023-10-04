<?php

$form_name = "filteredSearch";
$table_name = $form_name;
$tab_options_filtered_search = $protectedPost;
$tab_options_filtered_search['form_name'] = $form_name;
$tab_options_filtered_search['table_name'] = $table_name;

$list_fields_filtered_search = array(
    $l->g(23) => 'NAME',
    $l->g(190) . ' ' . strtoupper($l->g(1425)) => 'TAG',
    $l->g(25) => 'OS_NAME',
);

$list_col_cant_del_filtered_search = $list_fields_filtered_search;
$default_fields_filtered_search = $list_fields_filtered_search;

$tab_options_filtered_search['LIEN_LBL'][$l->g(23)] = 'index.php?function=ms_greenit_dashboard&cat=filteredstats&' . strtolower(str_replace(" ", "_", $l->g(23))) . '=';
$tab_options_filtered_search['LIEN_CHAMP'][$l->g(23)] = 'NAME';

echo open_form($form_name, '', '', 'form-horizontal');

echo "<h4>" . $l->g(102800) . "</h4>";

echo '
<div class="form-group">
    <div class="col-sm-12">
';

if (
    is_defined($_SESSION['GREENIT']['FILTER']['OS']) ||
    is_defined($_SESSION['GREENIT']['FILTER']['GROUP']) ||
    is_defined($_SESSION['GREENIT']['FILTER']['TAG']) ||
    is_defined($_SESSION['GREENIT']['FILTER']['ASSET'])
) {
    msg_warning($l->g(767));
    echo '
        <a 
            href="index.php?function=ms_greenit_dashboard&cat=filteredstats&' . strtolower(str_replace(" ", "_", $l->g(729))) . '=' . $computers . '" 
            class="btn btn-success"
        >
            ' . $l->g(102801) . '
        </a>
    ';
}

ajaxtab_entete_fixe($list_fields_filtered_search, $default_fields_filtered_search, $tab_options_filtered_search, $list_col_cant_del_filtered_search);

// ****************************************** FILTER ******************************************/
echo '
        <button type="button" data-toggle="collapse" data-target="#filter" class="btn">' . $l->g(735) . '</button>
        <div id="filter" class="collapse">
';

// FILTER OS/GROUP/TAG/ASSET

// OS
echo '
        <div class="form-group">
            <label class="control-label col-sm-2" for="OS">' . $l->g(25) . '</label>
            <div class="col-sm-3">
                <select name="OS" id="OS" class="form-control">
';
foreach ($os as $key => $name) {
    if (isset($_SESSION['GREENIT']['FILTER']['OS']) && $_SESSION['GREENIT']['FILTER']['OS'] == $key) {
        echo "<option value='" . $key . "' selected>" . $name . "</option>";
    } else {
        echo "<option value='" . $key . "'>" . $name . "</option>";
    }
}
echo '
                </select>
            </div>
';

// GROUP
echo '
            <label class="control-label col-sm-2" for="GROUP">' . $l->g(583) . '</label>
            <div class="col-sm-3">
                <select name="GROUP" id="GROUP" class="form-control">
';
foreach ($group as $key => $name) {
    if (isset($_SESSION['GREENIT']['FILTER']['GROUP']) && $_SESSION['GREENIT']['FILTER']['GROUP'] == $key) {
        echo "<option value='" . $key . "' selected>" . $name . "</option>";
    } else {
        echo "<option value='" . $key . "'>" . $name . "</option>";
    }
}
echo '
                </select>
            </div>
        </div>
';

// TAG
echo '
        <div class="form-group">
            <label class="control-label col-sm-2" for="TAG">' . $l->g(1425) . '</label>
            <div class="col-sm-3">
                <select name="TAG" id="TAG" class="form-control">
';
foreach ($tag as $key => $name) {
    if (isset($_SESSION['GREENIT']['FILTER']['TAG']) && $_SESSION['GREENIT']['FILTER']['TAG'] == $key) {
        echo "<option value='" . $key . "' selected>" . $name . "</option>";
    } else {
        echo "<option value='" . $key . "'>" . $name . "</option>";
    }
}
echo '
                </select>
            </div>
';

// ASSET CATEGORY
echo '
            <label class="control-label col-sm-2" for="ASSET">' . $l->g(2132) . '</label>
            <div class="col-sm-3">
                <select name="ASSET" id="ASSET" class="form-control">
';
foreach ($asset as $key => $name) {
    if (isset($_SESSION['GREENIT']['FILTER']['ASSET']) && $_SESSION['GREENIT']['FILTER']['ASSET'] == $key) {
        echo "<option value='" . $key . "' selected>" . $name . "</option>";
    } else {
        echo "<option value='" . $key . "'>" . $name . "</option>";
    }
}
echo '
                </select>
            </div>
        </div>
';
// END FILTER OS/GROUP/TAG/ASSET

echo '
        <input type="submit" class="btn btn-success" value="' . $l->g(393) . '" name="SUBMIT_FORM">
        <input type="submit" class="btn btn-danger" value="' . $l->g(396) . '" name="RESET">
    </div>
</div>
';

echo close_form();

?>