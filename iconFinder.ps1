$json = Get-Content -Raw -Path C:\Users\alexi\Desktop\db.json | ConvertFrom-Json


foreach($object_properties in $json.PsObject.Properties)
{
   foreach($value in $object_properties.value.PsObject.Properties) {
    if ($value.Value.deprecated -ne $null)
    {
        continue
    }
    if ($value.Value.c)
        {
            $jsonvalue = $value.Value.c
        } else {
            $jsonvalue = $value.Value.id
        }
      $Result = Invoke-WebRequest -URI "https://xivapi.com/action/$jsonvalue"| ConvertFrom-Json 
      $value.Value | add-member Noteproperty iconLink $Result.Icon  
   }
}

$timestamp = Get-Date -Format o | ForEach-Object { $_ -replace “:”, “.” }

$json | ConvertTo-Json -depth 100 | Out-File "C:\Users\alexi\Desktop\$timestamp.json"
