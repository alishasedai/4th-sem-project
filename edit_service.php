<?php
session_start();
include './includes/db_connect.php';

// ✅ Restrict access to logged-in contractors
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'contractor') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch contractor details
$sql = "SELECT * FROM contractor_details WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $sql);
$contractor = mysqli_fetch_assoc($result);

if (!$contractor) {
    echo "<script>alert('No record found! Please add your details first.'); window.location='add_service.php';</script>";
    exit();
}

// ✅ Handle form submission for update
if (isset($_POST['update'])) {
    $service_name = $_POST['service_name'];
    $experience = $_POST['experience'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $description = $_POST['description'];
    $services = $_POST['services'];

    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Profile photo update
    if (!empty($_FILES['profile_photo']['name'])) {
        $profile_photo = basename($_FILES['profile_photo']['name']);
        move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_dir . $profile_photo);
    } else {
        $profile_photo = $contractor['profile_photo']; // keep old photo
    }

    // Work photos update (multiple)
    $uploaded_work_photos = [];

    if (!empty($_FILES['work_photos']['name'][0])) {
        foreach ($_FILES['work_photos']['tmp_name'] as $key => $tmp_name) {
            $file_name = basename($_FILES['work_photos']['name'][$key]);
            $target_file = $target_dir . $file_name;

            if (move_uploaded_file($tmp_name, $target_file)) {
                $uploaded_work_photos[] = $file_name;
            }
        }

        // Merge old and new photos
        $all_photos = array_merge(explode(',', $contractor['work_photos']), $uploaded_work_photos);
        $work_photos_str = implode(',', $all_photos);
    } else {
        $work_photos_str = $contractor['work_photos'];
    }

    // ✅ Update query
    $update_sql = "UPDATE contractor_details SET 
        service_name='$service_name',
        experience='$experience',
        phone='$phone',
        address='$address',
        description='$description',
        services='$services',
        profile_photo='$profile_photo',
        work_photos='$work_photos_str'
        WHERE user_id='$user_id'";

    if (mysqli_query($conn, $update_sql)) {
        echo "<script>alert('Your details have been updated successfully!'); window.location='contractor_dashboard.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Service - Contractor Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f6f8;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background: #000;
            color: #fff;
            padding: 15px 60px;
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
            margin: 50px auto;
            background: #fff;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
        }

        input,
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        textarea {
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

        .photo-box {
            display: inline-block;
            position: relative;
            margin: 5px;
        }

        .photo-box img {
            width: 100px;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .delete-btn {
            position: absolute;
            top: -8px;
            right: -8px;
            background: red;
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 12px;
            width: 20px;
            height: 20px;
            cursor: pointer;
        }


        .btn:hover {
            background: #333;
        }

        .preview-images img {
            width: 100px;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
            margin: 5px;
            border: 1px solid #ccc;
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
        <h2>Edit Your Service Details</h2>

        <form method="POST" enctype="multipart/form-data">
            <label>Service Name</label>
            <input type="text" name="service_name" value="<?= htmlspecialchars($contractor['service_name']); ?>" required>

            <label>Experience</label>
            <input type="text" name="experience" value="<?= htmlspecialchars($contractor['experience']); ?>" required>

            <label>Phone</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($contractor['phone']); ?>" required>

            <label>Address</label>
            <input type="text" name="address" value="<?= htmlspecialchars($contractor['address']); ?>" required>

            <label>Description</label>
            <textarea name="description" required><?= htmlspecialchars($contractor['description']); ?></textarea>

            <label>Services Offered</label>
            <textarea name="services" required><?= htmlspecialchars($contractor['services']); ?></textarea>

            <label>Profile Photo (optional)</label>
            <input type="file" name="profile_photo" accept="image/*">
            <div class="preview-images">
                <p>Current Profile:</p>
                <img src="uploads/<?= htmlspecialchars($contractor['profile_photo']); ?>" alt="Profile Photo">
            </div>

            <label>Add More Work Photos (optional)</label>
            <input type="file" name="work_photos[]" accept="image/*" multiple>
            <div class="preview-images">
                <p>Existing Work Photos:</p>
                <div id="work-photos-container">
                    <?php
                    $existing_photos = explode(',', $contractor['work_photos']);
                    foreach ($existing_photos as $photo) {
                        if (!empty($photo)) {
                            echo '
                <div class="photo-box">
                    <img src="uploads/' . htmlspecialchars(trim($photo)) . '" alt="Work Photo">
                    <button type="button" class="delete-btn" data-photo="' . htmlspecialchars(trim($photo)) . '">❌</button>
                </div>';
                        }
                    }
                    ?>
                </div>
            </div>


            <button type="submit" class="btn" name="update">Save Changes</button>
        </form>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll(".delete-btn").forEach(btn => {
                btn.addEventListener("click", function() {
                    const photoName = this.dataset.photo;
                    if (confirm("Are you sure you want to delete this photo?")) {
                        fetch("delete_photo.php", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/x-www-form-urlencoded"
                                },
                                body: "photo=" + encodeURIComponent(photoName)
                            })
                            .then(res => res.text())
                            .then(data => {
                                if (data.includes("Deleted")) {
                                    this.parentElement.remove(); // remove photo from UI
                                    alert("Photo deleted successfully!");
                                } else {
                                    alert("Error: " + data);
                                }
                            });
                    }
                });
            });
        });
    </script>

</body>

</html>