<?php

// Your Supabase credentials
$supabaseUrl = 'https://emuaejyzofclcsxmilhr.supabase.co';
$supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImVtdWFlanl6b2ZjbGNzeG1pbGhyIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTcyNTI4MzIzOCwiZXhwIjoyMDQwODU5MjM4fQ.gO6JV_lsqSsu7GcPKCBXpkD5v_RPj9pxZto2JTm6u8M';

// The name of the bucket where you want to upload the files
$bucketName = 'kastmyrensBilder';

// Files array from form submission (assuming a form with multiple file inputs)
$files = $_FILES['images'];

// Loop through each file and upload
foreach ($files['name'] as $key => $name) {
    $fileTmpName = $files['tmp_name'][$key];
    $fileType = $files['type'][$key];
    
    // Open the file in binary mode
    $fileData = file_get_contents($fileTmpName);

    // File path in the bucket (you can modify the path as needed)
    $filePath = "webbshop/" . $name;

    // cURL setup to send the file to Supabase storage
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "$supabaseUrl/storage/v1/object/$bucketName/$filePath",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $supabaseKey,
            'Content-Type: ' . $fileType
        ],
        CURLOPT_POSTFIELDS => $fileData,
    ]);

    $response = curl_exec($curl);
    $error = curl_error($curl);

    curl_close($curl);

    if ($error) {
        echo "cURL Error #:" . $error . "<br>";
    } else {
        echo "File uploaded successfully: " . $name . "<br>";
    }
}