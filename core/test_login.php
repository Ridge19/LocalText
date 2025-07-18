<?php
// Test login API endpoint
$url = "http://localtext.businesslocal.com.au/api/v1/login";
$data = [
    'username' => 'riidgyy',
    'password' => 'password123'  // Replace with actual password
];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    ]
];

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === FALSE) {
    echo "Error: Failed to call API\n";
    var_dump($http_response_header);
} else {
    echo "API Response:\n";
    echo $result . "\n";
    echo "\nResponse Headers:\n";
    var_dump($http_response_header);
}
?>
