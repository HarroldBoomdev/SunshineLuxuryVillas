<?php

$url = "https://feed.ultrait.me/kyero/?Guid=782deecd-f3de-40f9-9c33-f7b7900b9020";

echo "<h2>PHP cURL Test</h2>";

$ch = curl_init($url);

// Core curl settings
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// SSL fixes for Windows
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

// Force IPv4 only (important)
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

// Force HTTP/1.1 (fix for some problematic servers)
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

$data = curl_exec($ch);

if (curl_errno($ch)) {
    echo "<strong>CURL ERROR:</strong> " . curl_error($ch);
} else {
    echo "<strong>SUCCESS:</strong> Downloaded " . strlen($data) . " bytes";
}

curl_close($ch);

echo "<hr>";

// DNS resolution test
echo "<h2>DNS / Socket Test</h2>";
$ip = gethostbyname("feed.ultrait.me");
echo "GETHOSTBYNAME: $ip<br>";

$fp = @fsockopen("feed.ultrait.me", 443, $errno, $errstr, 10);
if (!$fp) {
    echo "FSOCKOPEN: FAIL — $errstr ($errno)<br>";
} else {
    echo "FSOCKOPEN: SUCCESS — Connected OK<br>";
    fclose($fp);
}

?>
