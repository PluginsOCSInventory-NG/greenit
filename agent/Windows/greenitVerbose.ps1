$logPath = "C:\Windows\Temp\OCSInventoryGreenITVerboseLog.txt"

function Log-VerboseMessage {
    param ($message)
    $timeStamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    Add-Content -Path $logPath -Value "$timeStamp [VERBOSE] $message"
}

Log-VerboseMessage "Script started. Checking if data file exists."

$data = $null

if(Test-Path 'C:\ProgramData\GreenIT\data.json') {
    Log-VerboseMessage "File 'data.json' found. Reading and converting JSON content."
    try {
        $data = Get-Content -Path 'C:\ProgramData\GreenIT\data.json' -Raw | ConvertFrom-Json
        Log-VerboseMessage "JSON content successfully converted."
    }
    catch {
        Log-VerboseMessage "Error converting JSON content: $_"
        $data = $null
    }
} else {
    Log-VerboseMessage "File 'data.json' NOT found."
}

if($data -eq $null) {
    Log-VerboseMessage "Data is null or empty. Initializing empty XML."
    $xml = "<GREENIT/>"
} else {
    Log-VerboseMessage "Processing JSON data to XML format."
    $xml = ""

    foreach($date in $data.PSObject.Properties.Name)
    {
        $entry = $data.$date
        Log-VerboseMessage "Processing entry for date: $date, consumption: $($entry.consumption), uptime: $($entry.uptime)"
        $xml += "<GREENIT>`n"
        $xml += "<DATE>$date</DATE>`n"
        $xml += "<CONSUMPTION>$($entry.consumption)</CONSUMPTION>`n"
        $xml += "<UPTIME>$($entry.uptime)</UPTIME>`n"
        $xml += "</GREENIT>`n"
    }
}

Log-VerboseMessage "Outputting XML content."
$PSDefaultParameterValues['Out-File:Encoding'] = 'utf8'
Write-Output $xml
Log-VerboseMessage "Script ended."
