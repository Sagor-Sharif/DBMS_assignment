<?php
$host = 'localhost';
$dbName = 'student';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbName", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $dob = htmlspecialchars($_POST['dob']);
    $gender = htmlspecialchars($_POST['gender']);
    $address = htmlspecialchars($_POST['address']);

    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $resume = $_FILES['resume'];
        $uploadDir = "uploads/";
        $uploadFile = $uploadDir . basename($resume['name']);

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($resume['tmp_name'], $uploadFile)) {
            $resumePath = $uploadFile;
        } else {
            $resumePath = "Failed to upload the resume.";
        }
    } else {
        $resumePath = "No file uploaded.";
    }

    try {
        $stmt = $conn->prepare("INSERT INTO applications (name, email, phone, dob, gender, address, resume_path) 
                                VALUES (:name, :email, :phone, :dob, :gender, :address, :resumePath)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':dob', $dob);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':resumePath', $resumePath);

        $stmt->execute();

        echo "<h1>Application Submitted Successfully</h1>";
        echo "<p><strong>Full Name:</strong> $name</p>";
        echo "<p><strong>Email Address:</strong> $email</p>";
        echo "<p><strong>Phone Number:</strong> $phone</p>";
        echo "<p><strong>Date of Birth:</strong> $dob</p>";
        echo "<p><strong>Gender:</strong> $gender</p>";
        echo "<p><strong>Address:</strong> $address</p>";
        echo "<p><strong>Resume:</strong> <a href='$resumePath' target='_blank'>View Resume</a></p>";
    } catch (PDOException $e) {
        echo "Error saving application: " . $e->getMessage();
    }
} else {
    echo "<p>Invalid request. Please submit the form correctly.</p>";
}
?>
