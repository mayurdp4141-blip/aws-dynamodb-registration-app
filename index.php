<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$message = "";
$status_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $fullname = $_POST['fullname'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $role = $_POST['role'] ?? '';
    $city = $_POST['city'] ?? '';
    $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);

    if (!empty($username) && !empty($email)) {
        // DynamoDB API Payload JSON with extra fields
        $data = json_encode([
            "TableName" => "user",
            "Item" => [
                "username" => ["S" => $username],
                "fullname" => ["S" => $fullname],
                "email" => ["S" => $email],
                "phone" => ["S" => $phone],
                "role" => ["S" => $role],
                "city" => ["S" => $city],
                "password" => ["S" => $password],
                "created_at" => ["S" => date('Y-m-d H:i:s')]
            ]
        ]);

        $file = '/tmp/item.json';
        file_put_contents($file, $data);

        $cmd = "aws dynamodb put-item --cli-input-json file://$file --region ap-south-1 2>&1";
        $output = shell_exec($cmd);

        if ($output === null || trim($output) === "") {
            $message = "User '$username' registered successfully with complete profile in DynamoDB!";
            $status_type = "success";
        } else {
            $message = "Error saving to DynamoDB: " . htmlspecialchars($output);
            $status_type = "error";
        }

        if (file_exists($file)) {
            unlink($file);
        }
    } else {
        $message = "Username and Email are required fields.";
        $status_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AWS Cloud Registration Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #0f172a;
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 520px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }

        .badge {
            display: inline-block;
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
            border: 1px solid #f59e0b;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 15px;
        }

        h2 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        p.subtitle {
            color: #94a3b8;
            font-size: 0.9rem;
            margin-bottom: 25px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .alert.success {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid #10b981;
            color: #34d399;
        }

        .alert.error {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid #ef4444;
            color: #f87171;
        }

        .input-group {
            position: relative;
            margin-bottom: 18px;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
        }

        .input-group input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 10px;
            color: #fff;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.3s;
        }

        .input-group input:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 10px rgba(56, 189, 248, 0.2);
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #3b82f6, #06b6d4);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<div class="container">
    <span class="badge"><i class="fa-brands fa-aws"></i> AWS CLOUD SERVER</span>
    <h2>Create Account</h2>
    <p class="subtitle">Serverless DynamoDB Integration with Enterprise User Profile</p>

    <?php if(!empty($message)): ?>
        <div class="alert <?php echo $status_type; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <i class="fa-solid fa-user"></i>
            <input type="text" name="username" placeholder="Username *" required>
        </div>

        <div class="input-group">
            <i class="fa-solid fa-id-card"></i>
            <input type="text" name="fullname" placeholder="Full Name" required>
        </div>

        <div class="input-group">
            <i class="fa-solid fa-envelope"></i>
            <input type="email" name="email" placeholder="Email Address *" required>
        </div>

        <div class="input-group">
            <i class="fa-solid fa-phone"></i>
            <input type="text" name="phone" placeholder="Phone Number">
        </div>

        <div class="input-group">
            <i class="fa-solid fa-briefcase"></i>
            <input type="text" name="role" placeholder="Job Title / Role (e.g. Cloud Engineer)">
        </div>

        <div class="input-group">
            <i class="fa-solid fa-location-dot"></i>
            <input type="text" name="city" placeholder="City / Location">
        </div>

        <div class="input-group">
            <i class="fa-solid fa-lock"></i>
            <input type="password" name="password" placeholder="Password *" required>
        </div>

        <button type="submit" class="btn">Register User Profile</button>
    </form>
</div>

</body>
</html>
