<?php

$uptimeFormat = [
    "0" => "-----",
    "s" => "s",
    "m-s" => "m-s",
    "h-m-s" => "h-m-s",
];

echo '
    <div class="form-group">
        <label class="col-sm-8 text-left" for="'.strtoupper(str_replace(" ", "_",$l->g(80801))).'">'.$l->g(80801).'</label>
        <div class="col-sm-4">
            <input name="'.strtoupper(str_replace(" ", "_",$l->g(80801))).'" id="'.strtoupper(str_replace(" ", "_",$l->g(80801))).'" class="form-control" type="number" min="1" max="365" value="'.($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80801)))] ?? $config->COLLECT_INFO_PERIOD).'"\>
        </div>
    </div>
    <hr/>
    <div class="form-group">
        <label class="col-sm-8 text-left" for="'.strtoupper(str_replace(" ", "_",$l->g(80802))).'">'.$l->g(80802).'</label>
        <div class="col-sm-4">
            <input name="'.strtoupper(str_replace(" ", "_",$l->g(80802))).'" id="'.strtoupper(str_replace(" ", "_",$l->g(80802))).'" class="form-control" type="number" min="1" value="'.($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80802)))] ?? $config->COMPARE_INFO_PERIOD).'"\>
        </div>
    </div>
    <hr/>
    <div class="form-group">
        <label class="col-sm-8 text-left" for="'.strtoupper(str_replace(" ", "_",$l->g(80803))).'">'.$l->g(80803).'</label>
        <div class="col-sm-4">
            <input name="'.strtoupper(str_replace(" ", "_",$l->g(80803))).'" id="'.strtoupper(str_replace(" ", "_",$l->g(80803))).'" class="form-control" type="number" min="1" max="10" value="'.($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80803)))] ?? $config->CONSUMPTION_ROUND).'"\>
        </div>
    </div>
    <hr/>
    <div class="form-group">
        <label class="col-sm-8 text-left" for="'.strtoupper(str_replace(" ", "_",$l->g(80804))).'">'.$l->g(80804).'</label>
        <div class="col-sm-4">
            <input name="'.strtoupper(str_replace(" ", "_",$l->g(80804))).'" id="'.strtoupper(str_replace(" ", "_",$l->g(80804))).'" class="form-control" type="number" min="1" max="10" value="'.($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80804)))] ?? $config->COST_ROUND).'"\>
        </div>
    </div>
    <hr/>
    <div class="form-group">
        <label class="col-sm-8 text-left" for="'.strtoupper(str_replace(" ", "_",$l->g(80805))).'">'.$l->g(80805).'</label>
        <div class="col-sm-4">
            <input name="'.strtoupper(str_replace(" ", "_",$l->g(80805))).'" id="'.strtoupper(str_replace(" ", "_",$l->g(80805))).'" class="form-control" type="text" value="'.($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80805)))] ?? $config->COST_UNIT).'"\>
        </div>
    </div>
    <hr/>
    <div class="form-group">
        <label class="col-sm-8 text-left" for="'.strtoupper(str_replace(" ", "_",$l->g(80806))).'">'.$l->g(80806).'</label>
        <div class="col-sm-4">
            <input name="'.strtoupper(str_replace(" ", "_",$l->g(80806))).'" id="'.strtoupper(str_replace(" ", "_",$l->g(80806))).'" class="form-control" type="number" min="0" step="0.000001" value="'.($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80806)))] ?? $config->KILOWATT_COST).'"\>
        </div>
    </div>
    <hr/>
    <div class="form-group">
        <label class="col-sm-8 text-left" for="'.strtoupper(str_replace(" ", "_",$l->g(80807))).'">'.$l->g(80807).'</label>
        <div class="col-sm-4">
            <select name='.strtoupper(str_replace(" ", "_",$l->g(80807))).' id='.strtoupper(str_replace(" ", "_",$l->g(80807))).' class="form-control">"
';
                if(is_array($uptimeFormat)) {
                    foreach ($uptimeFormat as $option => $value){
                        echo "<option value='".$option."' ".($option == ($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80807)))] ?? $config->UPTIME_FORMAT) ? 'selected' : '').">".($uptimeFormat[$option] ? $uptimeFormat[$option] : $option)."</option>";
                    }
                }
echo '
            </select>
        </div>
    </div>
    <hr/>
';

?>