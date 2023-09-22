<?php

$form_name = "individualSearch";

echo open_form($form_name, '', '', 'form-horizontal');

echo "<br><h4>".$l->g(80726)."</h4>";

echo '
    <div class="form-group">
        <label class="control-label col-sm-2" for="'.strtoupper(str_replace(" ", "_",$l->g(35))).'">'.$l->g(35).'</label>
        <div class="col-sm-8">
            <input 
            name="'.strtoupper(str_replace(" ", "_",$l->g(35))).'"
            id="'.strtoupper(str_replace(" ", "_",$l->g(35))).'"
            class="form-control"
            type="text"
            ' . (isset($protectedPost[strtoupper(str_replace(" ", "_",$l->g(35)))]) ? 'value="'.($protectedPost[strtoupper(str_replace(" ", "_",$l->g(35)))] ?? "").'"\>' : '\>').'
            </div>
            <div class="col-sm-2">
                <input type="submit" name="Valid" value="'.$l->g(30).'" class="btn btn-success">
            </div>
    </div>
';

echo "<hr>";
echo close_form();

?>