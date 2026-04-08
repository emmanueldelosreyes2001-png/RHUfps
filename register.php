<?php
include 'config.php';

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Personal Info
    $firstname   = trim($_POST['firstname']);
    $middlename  = trim($_POST['middlename']);
    $lastname    = trim($_POST['lastname']);
    $suffix      = trim($_POST['suffix']);
    $age         = intval($_POST['age']);
    $gender      = trim($_POST['gender']);
    $address     = trim($_POST['address']);
    $status      = trim($_POST['status']);

    // Account Info
    $username = trim($_POST['username']);
    $role     = trim($_POST['role']);
    $password = trim($_POST['password']);

    if ($firstname && $lastname && $age && $gender && $address && $username && $role && $password) {

        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {

            $message = "Username already exists.";

        } else {

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users 
                (firstname, middlename, lastname, suffix, age, gender, address, status, username, role, password) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param(
                "ssssissssss",
                $firstname,
                $middlename,
                $lastname,
                $suffix,
                $age,
                $gender,
                $address,
                $status,
                $username,
                $role,
                $hash
            );

            if ($stmt->execute()) {
                $success = true;
                $message = "Registration successful! Redirecting to login...";
                header("refresh:2;url=login.php");
            } else {
                $message = "Registration failed.";
            }
        }

        $stmt->close();

    } else {
        $message = "All required fields must be filled.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Family Planning System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .register-container {
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 380px;
            max-height: 95vh;
            overflow-y: auto;
        }
        .register-container h1 {
            margin: 0 0 15px;
            color: #0f8f5f;
            text-align: center;
        }
        .register-container h2 {
            margin: 0 0 20px;
            font-size: 18px;
            color: #555;
            text-align: center;
        }

        .register-container input,
        .register-container select,
        .register-container textarea {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }

        .register-container textarea {
            resize: none;
            height: 60px;
        }

        .register-container button {
            width: 100%;
            padding: 10px 12px;
            background: #0f8f5f;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }
        .register-container button:hover {
            background: #0d7a4a;
        }

        .message {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
            color: <?php echo $success ? '#22c55e' : '#ef4444'; ?>;
        }

        .register-container a {
            display: block;
            text-align: center;
            margin-top: 12px;
            color: #0f8f5f;
            text-decoration: none;
            font-size: 14px;
        }
        .register-container a:hover {
            text-decoration: underline;
        }

        hr {
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1>Family Planning System</h1>
        <h2>Register</h2>

        <form method="POST">

            <!-- Personal Information -->
            <input type="text" name="firstname" placeholder="First Name" required>
            <input type="text" name="middlename" placeholder="Middle Name">
            <input type="text" name="lastname" placeholder="Last Name" required>
            <input type="text" name="suffix" placeholder="Suffix (Optional)">
            <input type="number" name="age" placeholder="Age" required>

            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>

            <textarea name="address" placeholder="Complete Address" required></textarea>

            <select name="status">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>

            <hr>

            <!-- Account Information -->
            <input type="text" name="username" placeholder="Username" required>
            <input type="text" name="role" placeholder="Role (Admin, Nurse)" required>
            <input type="password" name="password" placeholder="Password" required>

            <button type="submit">Register</button>
        </form>

        <p class="message"><?php echo $message; ?></p>
        <a href="login.php">Already have an account? Login</a>
    </div>
</body>
</html>