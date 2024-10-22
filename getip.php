<?php

// Nyilvános IP-cím lekérésének URL-je
$ip_info_url = "http://ipinfo.io/json";

// Lekérjük az IP-címet
$ip_info = file_get_contents($ip_info_url);

// JSON dekódolás 
$ip_data = json_decode($ip_info, true);

// Az IP-cím kiírása
echo "Nyilvános IP-cím: " . $ip_data['ip'];

?>
