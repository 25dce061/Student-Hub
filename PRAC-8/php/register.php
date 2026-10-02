<?php
// ==============================================================================
// StudentHub - Student Registration Processing Script
// ==============================================================================

// Step 1: Ensure form was submitted using HTTP POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Step 2: Receive and Sanitize Form Inputs
    $fullname         = htmlspecialchars(trim($_POST['fullname'] ?? ''));
    $username         = htmlspecialchars(trim($_POST['username'] ?? ''));
    $email            = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password         = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Step 3: Server-Side Validation
    $errors = [];

    if (empty($fullname)) {
        $errors[] = "Full Name cannot be empty.";
    }

    if (empty($username)) {
        $errors[] = "Username cannot be empty.";
    }

    if (empty($email)) {
        $errors[] = "Email Address cannot be empty.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please provide a valid email address.";
    }

    if (empty($password)) {
        $errors[] = "Password cannot be empty.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Confirm password does not match the password.";
    }

    // Step 4: If no validation errors, store data in CSV file privately
    $success = "";
    if (empty($errors)) {
        // Save to CSV files silently (both server copy and workspace copy)
        $fileLocations = [
            __DIR__ . "/../data/registrations.csv",
            "C:/Users/LENOVO/OneDrive/Desktop/prac 7/data/registrations.csv"
        ];

        $headers = ["Full Name", "Username", "Email", "Password", "Registration Date"];
        $record  = [$fullname, $username, $email, $password, date("Y-m-d H:i:s")];
        $savedAny = false;

        foreach ($fileLocations as $csvFile) {
            $folder = dirname($csvFile);
            if (!file_exists($folder)) {
                @mkdir($folder, 0777, true);
            }

            $isNewFile = !file_exists($csvFile);
            $fileHandle = @fopen($csvFile, "a");

            if ($fileHandle !== false) {
                if ($isNewFile) {
                    fputcsv($fileHandle, $headers);
                }
                fputcsv($fileHandle, $record);
                fclose($fileHandle);
                $savedAny = true;
            }
        }

        if ($savedAny) {
            $success = "Registration successful!";
        } else {
            $errors[] = "Failed to save registration to CSV file.";
        }
    }

} else {
    // If accessed directly without submitting the POST form, redirect back
    header("Location: ../pages/register.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Status - Student Hub</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <style>
        .result-container {
            max-width: 520px;
            margin: 80px auto;
            background: #ffffff;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.12);
            text-align: center;
            font-family: inherit;
        }
        .result-header {
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .msg-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 18px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .msg-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 18px;
            border-radius: 8px;
            text-align: left;
            margin-bottom: 25px;
        }
        .msg-error ul {
            margin: 8px 0 0 20px;
            padding: 0;
        }
        .btn-group {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .btn-link {
            display: inline-block;
            background-color: #315b7d;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 24px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 15px;
            transition: background-color 0.2s ease;
        }
        .btn-link:hover {
            background-color: #1f3d56;
        }
        .btn-secondary {
            background-color: #64748b;
        }
        .btn-secondary:hover {
            background-color: #475569;
        }
    </style>
</head>
<body>

    <div class="result-container">
        <?php if (!empty($success)): ?>
            <div class="result-header" style="color: #2e7d32;">
                🎉 Done!
            </div>

            <div class="msg-success">
                <?php echo $success; ?>
            </div>

            <p style="color: #64748b; margin-bottom: 30px; font-size: 15px;">
                Your account details have been securely recorded.
            </p>

            <div class="btn-group">
                <a href="../pages/login.html" class="btn-link">Go to Login</a>
                <a href="../index.html" class="btn-link btn-secondary">Home</a>
            </div>

        <?php elseif (!empty($errors)): ?>
            <div class="result-header" style="color: #c62828;">
                ⚠️ Registration Failed
            </div>

            <div class="msg-error">
                <strong>Please fix the following issues:</strong>
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo $err; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="btn-group">
                <a href="javascript:history.back()" class="btn-link">← Go Back</a>
                <a href="../pages/register.html" class="btn-link btn-secondary">Try Again</a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>