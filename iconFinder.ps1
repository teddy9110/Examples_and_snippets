$json = Get-Content -Raw -Path C:\Users\alexi\Desktop\db.json | ConvertFrom-Json


foreach($object_properties in $json.PsObject.Properties)
{
   foreach($value in $object_properties.value.PsObject.Properties) {
     $jsonvalue = $value.Value.c
      $Result = Invoke-WebRequest -URI "https://xivapi.com/action/$jsonvalue"| ConvertFrom-Json 
      $value.Value | add-member Noteproperty iconLink $Result.Icon
         Write-Output $value.Value  
   }
}

$json | ConvertTo-Json -depth 100 | Out-File "C:\Users\alexi\Desktop\test.json"

