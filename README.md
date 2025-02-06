# matomo-cloud-ip-lists.php
## PHP script to create Cloud IP list filtered

1.) copy the `matomo-cloud-ip-lists.php` in a directory.
`/example/`

2.) Download the Cloud IP lists and save it into a sub-directory:
`/example/cloud-ip-lists/`

amazon aws: https://ip-ranges.amazonaws.com/ip-ranges.json    
microsoft azure: https://www.microsoft.com/en-us/download/confirmation.aspx?id=56519    
google digital ocean: https://www.digitalocean.com/geo/google.csv    
google cloud: https://www.gstatic.com/ipranges/cloud.json    
oracle cloud: https://docs.cloud.oracle.com/en-us/iaas/tools/public_ip_ranges.json   

3.) run the matomo-cloud-ip-lists.php on the command line.

`php matomo-cloud-ip-lists.php >matomo-cloud-ip-lists.txt`

Use at your own risk.

# cloud-ip-list.txt
## Cloud IP list filtered
Cloud IP list filtered (duplicate IP / IP ranges filtered)

Count:    
2022-10-22: 41.017    
2023-12-16: 49.611    
2024-03-04: 51.957    

https://github.com/matomoto/cloud-ip-list/blob/main/cloud-ip-list.txt

Use at your own risk.
