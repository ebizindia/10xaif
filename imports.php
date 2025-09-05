<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'inc-oth.php';
// Database connection details
$host = 'localhost'; 
$dbname = 'diryi_yidirectory'; 
$username = 'diryi_yidirectory'; 
$password = 'os0qY#fLL$;*';

$default_pswd = \eBizIndia\generatePassword();

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $dbname :" . $e->getMessage());
}

// Path to the CSV file in cPanel
$inputFileName = 'yidata.csv';

if (($handle = fopen($inputFileName, "r")) !== FALSE) {
    // Skip the header row if there is one
    $header = fgetcsv($handle);
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $timestamp = time();
    $datetime = new DateTime();
    $datetime->setTimestamp($timestamp);
    $current_datetime = $datetime->format('Y-m-d H:i:s');
    $joined = $datetime->format('Y-m-d');
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        try {
            $nameParts = explode(' ', $data[0]);

if (count($nameParts) === 3) {
    // If there are three parts
    list($firstName, $middleName, $lastName) = $nameParts;
} elseif (count($nameParts) === 2) {
    // If there are two parts
    list($firstName, $lastName) = $nameParts;
    $middleName = ""; // No middle name
} else {
    // Handle case with one part if needed
    $firstName = $nameParts[0];
    $middleName = "";
    $lastName = "";
}



            $salute = $data[2] == 'Male' ? 'Mr.' : 'Mrs.';

            $gendr = $data[2] == 'Male' ? 'M.' : 'F.';

            $city = '';

            if (is_numeric($data[5])) {
               $pin = $data[5];
               $address = '';
            }else{
              $pin = '';  
              $address = $data[5];
            }

            

            // if (preg_match('/\b([A-Za-z]+)\b(?=\s*\d{6})/', $data[7], $matches)) {
            //    $city = $matches[0];

            // }else{
            //    $city = ''; 
            // }

            $dob = date('Y-m-d', strtotime($data[1]));
           // $doj = isset($data[3]) && !empty($data[3]) ? date('Y-m-d', strtotime($data[3])) : null;
            
            // if (preg_match('/\b\d{6}\b/', $data[7], $pins)) {
            //        $pin =  $pins[0]; // Returns the first match of a 6-digit PIN code
            //    }else{
            //     $pin = '';
            //    }

             

            // Insert into the members table
            echo " Name ".$firstName;
            $sql1 = "INSERT INTO `members`(`title`, `fname`,`mname`, `lname`, `email`, `mobile`, `mobile2`, `gender`, `dob`,`residence_city`,`spouse_name` ,`residence_pin`, `residence_addrline1`,`active`, `dnd`,`created_at`, `created_from`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"; 
            $stmt1 = $pdo->prepare($sql1);

            

            $stmt1->execute([
                $salute, $firstName,$middleName, $lastName, $data[4], $data[3], $data[3],$gendr,$dob,$city,$data[11],$pin,$address,'y','n',$current_datetime,$ip_address
            ]);
            

            $memberid = $pdo->lastInsertId();

            // Insert into the users table
            $sql2 = "INSERT INTO `users`(`username`, `profile_type`, `profile_id`, `user_type`, `password`, `createdOn`, `createdFrom`) VALUES (?,?,?,?,?,?,?)"; 
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->execute([
                $data[4], 'member', $memberid, 1, password_hash('123456', PASSWORD_BCRYPT),$current_datetime,$ip_address
            ]);
            
            $userid = $pdo->lastInsertId();
            
             $sql3 = "INSERT INTO `user_roles`(`user_id`, `role_id`) VALUES (?,?)"; 
            $stmt3 = $pdo->prepare($sql3);
            $stmt3->execute([
                $userid, '2'
            ]);

        } catch (PDOException $e) {
            echo "Error inserting row: " . $e->getMessage() . "<br>";
        }
    }
    fclose($handle);
    echo "Data inserted successfully!";
} else {
    die("Could not open the file $inputFileName");
}
?>
