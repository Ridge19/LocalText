<?php
// Test login API endpoint with HTTPS
$url = "https://localtext.businesslocal.com.au/api/v1/login";
$data = [
    'username' => 'riidgyy',
    'password' => 'password123'  // Replace with actual password
];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ]
];

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === FALSE) {
    echo "Error: Failed to call HTTPS API\n";
    if (isset($http_response_header)) {
        var_dump($http_response_header);
    }
} else {
    echo "HTTPS API Response:\n";
    echo $result . "\n";
}
?>
