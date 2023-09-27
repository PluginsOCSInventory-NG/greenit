<?php

$form_name = "computerSearch";
echo open_form($form_name, '', '', 'form-horizontal');

echo "<h4>".$l->g(102106)."</h4>";

echo '
<div class="form-group">
    <div class="col-sm-12">
        <label class="col-sm-8 text-left" for="'.strtolower(str_replace(" ", "_",$l->g(25))).'">'.$l->g(25).'</label>
        <div class="col-sm-4">
            <select name="'.strtolower(str_replace(" ", "_",$l->g(25))).'" id="'.strtolower(str_replace(" ", "_",$l->g(25))).'" class="form-control">
';
                foreach($os as $key => $name) {
                    if(isset($_SESSION['GREENIT']['SEARCH']['OS']) && $_SESSION['GREENIT']['SEARCH']['OS'] == $key) {
                        echo "<option value='".$key."' selected>".$name."</option>";
                    } else {
                        echo "<option value='".$key."'>".$name."</option>";
                    }
                }
echo '
            </select>
        </div>
    <input type="submit" class="btn btn-success" value="'.$l->g(393).'" name="SUBMIT_FORM">
    </div>
</div>
';

echo close_form();

?>