<?php
// Test login API endpoint with HTTPS and different credentials
$url = "https://localtext.businesslocal.com.au/api/v1/login";

// Let's try a few different combinations
$testCredentials = [
    ['username' => 'riidgyy', 'password' => '123456'],
    ['username' => 'riidgyy', 'password' => 'password'],
    ['username' => 'riidgyy', 'password' => 'riidgyy'],
    ['username' => 'riidgyy@gmail.com', 'password' => '123456'],
];

foreach ($testCredentials as $data) {
    echo "Testing: " . $data['username'] . " / " . $data['password'] . "\n";
    
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
        echo "Error: Failed to call API\n";
    } else {
        echo "Response: " . $result . "\n";
        
        $response = json_decode($result, true);
        if (isset($response['status']) && $response['status'] === 'success') {
            echo "✅ LOGIN SUCCESS!\n";
            break;
        }
    }
    echo "---\n";
}
?>
