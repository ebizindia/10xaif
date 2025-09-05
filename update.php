<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'inc-oth.php';

// Database connection details
$host = 'localhost'; 
$dbname = 'dirmymstar_mym'; 
$username = 'dirmymstar_mym'; 
$password = 'g2d*M4T0-3s0';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $dbname: " . $e->getMessage());
}

// Path to the CSV file in cPanel
$inputFileName = 'membership.csv';

// Read the entire CSV file into an associative array for faster lookup
$csvData = [];
if (($handle = fopen($inputFileName, "r")) !== FALSE) {
    // Skip the header row
    $header = fgetcsv($handle);
    
    // Read the CSV and store in an associative array where key = email, value = member id
    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $csvEmail = strtolower(trim($row[0])); // Trim whitespaces and convert to lowercase
        $csvMemberId = trim($row[1]); // Trim any whitespaces in the member ID
        
        // Check for invalid member ID values (like #REF! from Excel errors)
        if (!empty($csvEmail) && !empty($csvMemberId) && $csvMemberId !== '#REF!') {
            $csvData[$csvEmail] = $csvMemberId;
        }
    }
    fclose($handle);
} else {
    die("Could not open the file $inputFileName");
}

// Now fetch emails from the database and check if they exist in the CSV
$query = $pdo->prepare("SELECT id, email FROM members");
$query->execute();
$members = $query->fetchAll(PDO::FETCH_ASSOC);

foreach ($members as $member) {
    $dbEmail = strtolower(trim($member['email'])); // Convert DB email to lowercase and trim

    // Check if the email from the database exists in the CSV file
    if (array_key_exists($dbEmail, $csvData)) {
        $memberIdFromCsv = $csvData[$dbEmail];

        // Update the membership_no for the member in the database
        try {
            $updateQuery = $pdo->prepare("UPDATE members SET membership_no = ? WHERE email = ?");
            $updateQuery->execute([$memberIdFromCsv, $member['email']]);

            echo "Updated membership_no for email: {$member['email']} to $memberIdFromCsv<br>";
        } catch (PDOException $e) {
            echo "Error updating membership_no for email {$member['email']}: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "Email not found in CSV: {$member['email']}<br>";
    }
}

echo "Data update process completed!";
?>
