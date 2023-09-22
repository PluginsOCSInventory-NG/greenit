<?php

$uptimeFormat = [
    "0" => "-----",
    "s" => "s",
    "m-s" => "m-s",
    "h-m-s" => "h-m-s",
];

echo '
    <div class="form-group">
        <label class="control-label col-sm-2" for="'.strtoupper(str_replace(" ", "_",$l->g(80716))).'">'.$l->g(80716).'</label>
        <div class="col-sm-10">
            <input name="'.strtoupper(str_replace(" ", "_",$l->g(80716))).'" id="'.strtoupper(str_replace(" ", "_",$l->g(80716))).'" class="form-control" type="number" min="1" max="365" value="'.($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80716)))] ?? $config->COLLECT_INFO_PERIOD).'"\>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2" for="'.strtoupper(str_replace(" ", "_",$l->g(80717))).'">'.$l->g(80717).'</label>
        <div class="col-sm-10">
            <input name="'.strtoupper(str_replace(" ", "_",$l->g(80717))).'" id="'.strtoupper(str_replace(" ", "_",$l->g(80717))).'" class="form-control" type="number" min="1" max="10" value="'.($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80717)))] ?? $config->CONSUMPTION_ROUND).'"\>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2" for="'.strtoupper(str_replace(" ", "_",$l->g(80718))).'">'.$l->g(80718).'</label>
        <div class="col-sm-10">
            <input name="'.strtoupper(str_replace(" ", "_",$l->g(80718))).'" id="'.strtoupper(str_replace(" ", "_",$l->g(80718))).'" class="form-control" type="number" min="1" max="10" value="'.($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80718)))] ?? $config->COST_ROUND).'"\>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2" for="'.strtoupper(str_replace(" ", "_",$l->g(80719))).'">'.$l->g(80719).'</label>
        <div class="col-sm-10">
            <input name="'.strtoupper(str_replace(" ", "_",$l->g(80719))).'" id="'.strtoupper(str_replace(" ", "_",$l->g(80719))).'" class="form-control" type="text" value="'.($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80719)))] ?? $config->COST_UNIT).'"\>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2" for="'.strtoupper(str_replace(" ", "_",$l->g(80720))).'">'.$l->g(80720).'</label>
        <div class="col-sm-10">
            <input name="'.strtoupper(str_replace(" ", "_",$l->g(80720))).'" id="'.strtoupper(str_replace(" ", "_",$l->g(80720))).'" class="form-control" type="number" min="0" step="0.000001" value="'.($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80720)))] ?? $config->KILOWATT_COST).'"\>
        </div>
    </div>
';
formGroup('select', strtoupper(str_replace(" ", "_",$l->g(80721))), $l->g(80721), '', '', $protectedPost[strtoupper(str_replace(" ", "_",$l->g(80721)))] ?? $config->UPTIME_FORMAT, '', $uptimeFormat, $uptimeFormat, '');


?>