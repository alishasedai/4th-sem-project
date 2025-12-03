<?php
session_start();
include './includes/db_connect.php';

// Restrict access
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'contractor') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['submit'])) {

    $user_id = $_SESSION['user_id'];
    $service_name = $_POST['service_name'];
    $experience = $_POST['experience'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $description = $_POST['description'];
    $services = $_POST['services'];

    // Upload directory
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // ---------------------------------------------------------------
    // 1️⃣ Upload PROFILE PHOTO
    // ---------------------------------------------------------------
    $profile_photo = "";
    if (!empty($_FILES['profile_photo']['name'])) {

        $profile_name = time() . "_" . basename($_FILES['profile_photo']['name']);
        $target_profile = $target_dir . $profile_name;

        if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_profile)) {
            $profile_photo = $profile_name;
        } else {
            echo "<script>alert('Profile photo upload failed');</script>";
        }
    }

    // ---------------------------------------------------------------
    // 2️⃣ Upload MULTIPLE WORK PHOTOS
    // ---------------------------------------------------------------
    $uploaded_work_photos = [];

    if (!empty($_FILES['work_photos']['name'][0])) {

        foreach ($_FILES['work_photos']['tmp_name'] as $key => $tmp_name) {

            $file_name = time() . "_" . basename($_FILES['work_photos']['name'][$key]);
            $target_file = $target_dir . $file_name;

            if (move_uploaded_file($tmp_name, $target_file)) {
                $uploaded_work_photos[] = $file_name;
            }
        }
    }

    $work_photos_str = implode(",", $uploaded_work_photos);

    // ---------------------------------------------------------------
    // 3️⃣ Insert Data Into Database
    // ---------------------------------------------------------------
    $sql = "INSERT INTO contractor_details 
            (user_id, service_name, experience, phone, address, description, services, profile_photo, work_photos)
            VALUES 
            ('$user_id', '$service_name', '$experience', '$phone', '$address', '$description', '$services', '$profile_photo', '$work_photos_str')";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        echo "<script>alert('Your service details have been added successfully!'); 
        window.location='contractor_dashboard.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Service - Contractor Dashboard</title>
    <link rel="stylesheet" href="./css/dashboard.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f6f8;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background: #000;
            padding: 15px 60px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-right: 20px;
            font-weight: 500;
        }

        .container {
            max-width: 800px;
            margin: 60px auto;
            background: #fff;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        form label {
            font-weight: 600;
            display: block;
            margin-bottom: 6px;
            color: #333;
        }

        form input,
        form textarea {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
            outline: none;
        }

        form textarea {
            resize: none;
            height: 80px;
        }

        .btn {
            background: #000;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }

        .btn:hover {
            background: #333;
        }
    </style>
</head>

<body>

    <div class="navbar">
        <div><strong><?= $_SESSION['user_name']; ?></strong></div>
        <div>
            <a href="contractor_dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>Add Your Service Details</h2>

        <form method="POST" enctype="multipart/form-data">
            <label>Service Name</label>
            <input type="text" name="service_name" placeholder="e.g. Luxury Ceiling Design" required>

            <label>Experience (in years)</label>
            <input type="text" name="experience" placeholder="e.g. 5 years" required>

            <label>Phone Number</label>
            <input type="tel" name="phone" placeholder="Enter your phone number" required>

            <label>Address</label>
            <input type="text" name="address" placeholder="Enter your address" required>

            <label>Description</label>
            <textarea name="description" placeholder="Briefly describe your work" required></textarea>

            <label>Services Offered (comma separated)</label>
            <textarea name="services" placeholder="e.g. Gypsum Ceiling, POP Work, 2x2 Board, Lighting"
                required></textarea>

            <label>Profile Photo</label>
            <input type="file" name="profile_photo" accept="image/*" required>

            <label>Work Sample Photos</label>
            <input type="file" name="work_photos[]" accept="image/*" multiple required>


            <button type="submit" class="btn" name="submit">Save Details</button>
        </form>
    </div>

</body>

</html>