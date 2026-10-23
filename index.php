<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$message = "";
$status_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);

    if (!empty($username) && !empty($email)) {
        // DynamoDB API Payload JSON
        $data = json_encode([
            'TableName' => 'users',
            'Item' => [
                'username' => ['S' => $username],
                'email' => ['S' => $email],
                'password' => ['S' => $password],
                'created_at' => ['S' => date('Y-m-d H:i:s')]
            ]
        ]);

        // DynamoDB Put-Item command via AWS CLI
        $cmd = "aws dynamodb put-item --cli-input-json " . escapeshellarg($data) . " --region ap-south-1 2>&1";
        $output = shell_exec($cmd);

        if ($output === null || empty($output)) {
            $message = "User registered successfully in DynamoDB!";
            $status_type = "success";
        } else {
            $message = "Error saving to DynamoDB: " . $output;
            $status_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AWS DynamoDB Registration Portal</title>
    <!-- Google Fonts & FontAwesome Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            color: #ffffff;
        }

        .badge {
            display: inline-block;
            background: rgba(255, 153, 0, 0.2);
            color: #ff9900;
            border: 1px solid #ff9900;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .card h2 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .card p.subtitle {
            color: #94a3b8;
            font-size: 0.88rem;
            margin-bottom: 25px;
        }

        .input-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 1.1rem;
        }

        .input-group input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            color: #fff;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .input-group input:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 10px rgba(56, 189, 248, 0.3);
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: linear-gradient(90deg, #ff9900 0%, #ff5500 100%);
            border: none;
            border-radius: 8px;
            color: #ffffff;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 153, 0, 0.3);
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 0.88rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid #22c55e;
            color: #4ade80;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid #ef4444;
            color: #f87171;
        }

        .footer-text {
            text-align: center;
            margin-top: 25px;
            font-size: 0.75rem;
            color: #64748b;
        }
    </style>
</head>
<body>

    <div class="card">
        <span class="badge"><i class="fa-brands fa-aws"></i> AWS Cloud Server</span>
        <h2>Create Account</h2>
        <p class="subtitle">Serverless Database Integration with AWS DynamoDB</p>

        <?php if(!empty($message)): ?>
            <div class="alert <?php echo ($status_type === 'success') ? 'alert-success' : 'alert-error'; ?>">
                <i class="fa-solid <?php echo ($status_type === 'success') ? 'fa-circle-check' : 'fa-circle-exclamation'; ?>"></i>
                <span><?php echo $message; ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="input-group">
                <i class="fa-solid fa-user"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="input-group">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" name="email" placeholder="Email Address" required>
            </div>

            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" class="btn-submit">
                <span>Register User</span>
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </form>

        <div class="footer-text">
            Powered by AWS EC2 &amp; DynamoDB Backend
        </div>
    </div>

</body>
</html>
