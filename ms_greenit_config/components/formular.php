<?php

$uptimeFormat = [
    "0" => "-----",
    "s" => "s",
    "m-s" => "m-s",
    "h-m-s" => "h-m-s",
];

$form_name = "configuration";
echo open_form($form_name, '', '', 'form-horizontal');

echo '
<div class="form-group">
    <label class="col-sm-8 text-left" for="'.strtoupper(str_replace(" ", "_",$l->g(102001))).'">'.$l->g(102001).'</label>
    <div class="col-sm-4">
        <input name="'.strtoupper(str_replace(" ", "_",$l->g(102001))).'" id="'.strtoupper(str_replace(" ", "_",$l->g(102001))).'" class="form-control" type="number" min="1" max="365" value="'.($protectedPost[strtoupper(str_replace(" ", "_",$l->g(102001)))] ?? $config->COLLECT_INFO_PERIOD).'"\>
    </div>
</div>
<hr/>

<div class="form-group">
    <label class="col-sm-8 text-left" for="'.strtoupper(str_replace(" ", "_",$l->g(102002))).'">'.$l->g(102002).'</label>
    <div class="col-sm-4">
        <input name="'.strtoupper(str_replace(" ", "_",$l->g(102002))).'" id="'.strtoupper(str_replace(" ", "_",$l->g(102002))).'" class="form-control" type="number" min="1" value="'.($protectedPost[strtoupper(str_replace(" ", "_",$l->g(102002)))] ?? $config->COMPARE_INFO_PERIOD).'"\>
    </div>
</div>
<hr/>

<div class="form-group">
    <label class="col-sm-8 text-left" for="'.strtoupper(str_replace(" ", "_",$l->g(102003))).'">'.$l->g(102003).'</label>
    <div class="col-sm-4">
        <input name="'.strtoupper(str_replace(" ", "_",$l->g(102003))).'" id="'.strtoupper(str_replace(" ", "_",$l->g(102003))).'" class="form-control" type="number" min="1" max="10" value="'.($protectedPost[strtoupper(str_replace(" ", "_",$l->g(102003)))] ?? $config->CONSUMPTION_ROUND).'"\>
    </div>
</div>
<hr/>

<div class="form-group">
    <label class="col-sm-8 text-left" for="'.strtoupper(str_replace(" ", "_",$l->g(102004))).'">'.$l->g(102004).'</label>
    <div class="col-sm-4">
        <input name="'.strtoupper(str_replace(" ", "_",$l->g(102004))).'" id="'.strtoupper(str_replace(" ", "_",$l->g(102004))).'" class="form-control" type="number" min="1" max="10" value="'.($protectedPost[strtoupper(str_replace(" ", "_",$l->g(102004)))] ?? $config->COST_ROUND).'"\>
    </div>
</div>
<hr/>

<div class="form-group">
    <label class="col-sm-8 text-left" for="'.strtoupper(str_replace(" ", "_",$l->g(102005))).'">'.$l->g(102005).'</label>
    <div class="col-sm-4">
        <input name="'.strtoupper(str_replace(" ", "_",$l->g(102005))).'" id="'.strtoupper(str_replace(" ", "_",$l->g(102005))).'" class="form-control" type="text" value="'.($protectedPost[strtoupper(str_replace(" ", "_",$l->g(102005)))] ?? $config->COST_UNIT).'"\>
    </div>
</div>
<hr/>

<div class="form-group">
    <label class="col-sm-8 text-left" for="'.strtoupper(str_replace(" ", "_",$l->g(102006))).'">'.$l->g(102006).'</label>
    <div class="col-sm-4">
        <input name="'.strtoupper(str_replace(" ", "_",$l->g(102006))).'" id="'.strtoupper(str_replace(" ", "_",$l->g(102006))).'" class="form-control" type="number" min="0" step="0.000001" value="'.($protectedPost[strtoupper(str_replace(" ", "_",$l->g(102006)))] ?? $config->KILOWATT_COST).'"\>
    </div>
</div>
<hr/>

<div class="form-group">
    <label class="col-sm-8 text-left" for="'.strtoupper(str_replace(" ", "_",$l->g(102007))).'">'.$l->g(102007).'</label>
    <div class="col-sm-4">
        <select name='.strtoupper(str_replace(" ", "_",$l->g(102007))).' id='.strtoupper(str_replace(" ", "_",$l->g(102007))).' class="form-control">"
';
            if(is_array($uptimeFormat)) {
                foreach ($uptimeFormat as $option => $value){
                    echo "<option value='".$option."' ".($option == ($protectedPost[strtoupper(str_replace(" ", "_",$l->g(102007)))] ?? $config->UPTIME_FORMAT) ? 'selected' : '').">".($uptimeFormat[$option] ? $uptimeFormat[$option] : $option)."</option>";
                }
            }
echo '
        </select>
    </div>
</div>
<hr/>

<input type="submit" class="btn btn-success" value="'.$l->g(103).'" name="SUBMIT_FORM">
';

echo close_form();

?>