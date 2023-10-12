<?php

$uptimeFormat = [
    "0" => "-----",
    "s" => "s",
    "m-s" => "m-s",
    "h-m-s" => "h-m-s",
];

$consumptionType = [
    "PX_ELE_I_TTES_TRANCHES" => $l->g(102013),
    "PX_ELE_I_IA" => $l->g(102014),
    "PX_ELE_I_IB" => $l->g(102015),
    "PX_ELE_I_IC" => $l->g(102016),
    "PX_ELE_I_ID" => $l->g(102017),
    "PX_ELE_I_IE" => $l->g(102018),
    "PX_ELE_I_IF" => $l->g(102019),
    "PX_ELE_I_IG" => $l->g(102020),
];

$form_name = "configuration";
echo open_form($form_name, '', '', 'form-horizontal');

echo '
<h4>' . $l->g(102001) . '</h4>
<div class="form-group">
    <label class="col-sm-7 text-left" for="' . strtoupper(str_replace(" ", "_", $l->g(102002))) . '">' . $l->g(102002) . '</label>
    <div class="col-sm-5">
        <input name="' . strtoupper(str_replace(" ", "_", $l->g(102002))) . '" id="' . strtoupper(str_replace(" ", "_", $l->g(102002))) . '" class="form-control" type="number" min="1" max="365" value="' . ($protectedPost[strtoupper(str_replace(" ", "_", $l->g(102002)))] ?? $config->COLLECT_INFO_PERIOD) . '" required />
    </div>
</div>

<div class="form-group">
    <label class="col-sm-7 text-left" for="' . strtoupper(str_replace(" ", "_", $l->g(102003))) . '">' . $l->g(102003) . '</label>
    <div class="col-sm-5">
        <input name="' . strtoupper(str_replace(" ", "_", $l->g(102003))) . '" id="' . strtoupper(str_replace(" ", "_", $l->g(102003))) . '" class="form-control" type="number" min="1" value="' . ($protectedPost[strtoupper(str_replace(" ", "_", $l->g(102003)))] ?? $config->COMPARE_INFO_PERIOD) . '" required />
    </div>
</div>

<div class="form-group">
    <label class="col-sm-7 text-left" for="' . strtoupper(str_replace(" ", "_", $l->g(102004))) . '">' . $l->g(102004) . '</label>
    <div class="col-sm-5">
        <input name="' . strtoupper(str_replace(" ", "_", $l->g(102004))) . '" id="' . strtoupper(str_replace(" ", "_", $l->g(102004))) . '" class="form-control" type="number" min="1" max="10" value="' . ($protectedPost[strtoupper(str_replace(" ", "_", $l->g(102004)))] ?? $config->CONSUMPTION_ROUND) . '" required />
    </div>
</div>

<div class="form-group">
    <label class="col-sm-7 text-left" for="' . strtoupper(str_replace(" ", "_", $l->g(102005))) . '">' . $l->g(102005) . '</label>
    <div class="col-sm-5">
        <input name="' . strtoupper(str_replace(" ", "_", $l->g(102005))) . '" id="' . strtoupper(str_replace(" ", "_", $l->g(102005))) . '" class="form-control" type="number" min="1" max="10" value="' . ($protectedPost[strtoupper(str_replace(" ", "_", $l->g(102005)))] ?? $config->COST_ROUND) . '" required />
    </div>
</div>

<div class="form-group">
    <label class="col-sm-7 text-left" for="' . strtoupper(str_replace(" ", "_", $l->g(102006))) . '">' . $l->g(102006) . '</label>
    <div class="col-sm-5">
        <input name="' . strtoupper(str_replace(" ", "_", $l->g(102006))) . '" id="' . strtoupper(str_replace(" ", "_", $l->g(102006))) . '" class="form-control" type="text" value="' . ($protectedPost[strtoupper(str_replace(" ", "_", $l->g(102006)))] ?? $config->COST_UNIT) . '" required />
    </div>
</div>

<div class="form-group">
    <label class="col-sm-7 text-left" for="' . strtoupper(str_replace(" ", "_", $l->g(102007))) . '">' . $l->g(102007) . '</label>
    <div class="col-sm-5">
        <select name=' . strtoupper(str_replace(" ", "_", $l->g(102007))) . ' id=' . strtoupper(str_replace(" ", "_", $l->g(102007))) . ' class="form-control">"
';
if (is_array($uptimeFormat)) {
    foreach ($uptimeFormat as $option => $value) {
        echo "<option value='" . $option . "' " . ($option == ($protectedPost[strtoupper(str_replace(" ", "_", $l->g(102007)))] ?? $config->UPTIME_FORMAT) ? 'selected' : '') . ">" . ($uptimeFormat[$option] ? $uptimeFormat[$option] : $option) . "</option>";
    }
}
echo '
        </select>
    </div>
</div>
<hr/>

<h4>' . $l->g(102008) . '</h4>

<div class="form-group">
    <label class="col-sm-7 text-left" for="' . strtoupper(str_replace(" ", "_", $l->g(102009))) . '">' . $l->g(102009) . '</label>';
if (is_defined($config->API_KEY)) {
    echo '
    <div class="col-sm-3">
        <input name="' . strtoupper(str_replace(" ", "_", $l->g(102009))) . '" id="' . strtoupper(str_replace(" ", "_", $l->g(102009))) . '" class="form-control" type="text" placeholder="' . $l->g(102011) . '" value="' . ($protectedPost[strtoupper(str_replace(" ", "_", $l->g(102009)))] ?? $config->API_KEY) . '" />
    </div>
    <div class="col-sm-2">
        <button class="btn btn-success" name="TEST_API">' . $l->g(102010) . '</button>
    </div>
    ';
} else {
    echo '
    <div class="col-sm-5">
        <input name="' . strtoupper(str_replace(" ", "_", $l->g(102009))) . '" id="' . strtoupper(str_replace(" ", "_", $l->g(102009))) . '" class="form-control" type="text" placeholder="' . $l->g(102011) . '" value="' . ($protectedPost[strtoupper(str_replace(" ", "_", $l->g(102009)))] ?? $config->API_KEY) . '" />
    </div>
    ';
}
echo '
</div>

<div class="form-group">
    <label class="col-sm-7 text-left" for="' . strtoupper(str_replace(" ", "_", $l->g(102012))) . '">' . $l->g(102012) . '</label>
    <div class="col-sm-5">
        <select name=' . strtoupper(str_replace(" ", "_", $l->g(102012))) . ' id=' . strtoupper(str_replace(" ", "_", $l->g(102012))) . ' class="form-control">"
';
if (is_array($consumptionType)) {
    foreach ($consumptionType as $option => $value) {
        echo "<option value='" . $option . "' " . ($option == ($protectedPost[strtoupper(str_replace(" ", "_", $l->g(102012)))] ?? $config->CONSUMPTION_TYPE) ? 'selected' : '') . ">" . $consumptionType[$option] . "</option>";
    }
}
echo '
        </select>
    </div>
</div>

<hr/>

<button class="btn btn-success" name="SUBMIT_FORM">' . $l->g(103) . '</button>
';

echo close_form();

?>