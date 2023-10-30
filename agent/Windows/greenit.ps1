###############################################################################
## OCSINVENTORY-NG
## Copyleft Antoine ROBIN 2023
## Web : http://www.ocsinventory-ng.org
##
## This code is open source and may be copied and modified as long as the source
## code is always made freely available.
## Please refer to the General Public Licence http://www.gnu.org/ or Licence.txt
################################################################################

$data = $null

if(Test-Path 'C:\ProgramData\GreenIT\data.json') {
    $dataContent = Get-Content -Path 'C:\ProgramData\GreenIT\data.json'
}
if($null -eq $dataContent) {
    $xml = "<GREENIT/>"
} else {
    $xml = ""

    $regex =  "`"(?<DATE>[0-9]+-[0-9]+-[0-9]+)`": {`"CONSUMPTION`":`"(?<CONSUMPTION>[\s\S]+?)`",`"UPTIME`":`"(?<UPTIME>[0-9]+)`"},"
    foreach($data in $dataContent)
    {
        if($data -match $regex)
        {
            $xml += "<GREENIT>`n"
            $xml += "<DATE>" + $Matches.DATE + "</DATE>`n"
            if($Matches.CONSUMPTION -eq "VM detected")
            {
                $xml += "<CONSUMPTION>" + $Matches.CONSUMPTION + "</CONSUMPTION>`n"
            }
            else
            {
                $xml += "<CONSUMPTION>" + $Matches.CONSUMPTION + "</CONSUMPTION>`n"
            }
            $xml += "<UPTIME>" + $Matches.UPTIME + "</UPTIME>`n"
            $xml += "</GREENIT>`n"
        }
    }
}

$PSDefaultParameterValues['Out-File:Encoding'] = 'utf8'
[Console]::WriteLine($xml)