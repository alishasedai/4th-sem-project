<?php
session_start();
include './includes/db_connect.php';

// Check if user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['user_id'];
$contractor_id = isset($_GET['contractor_id']) ? intval($_GET['contractor_id']) : 0;

if ($contractor_id <= 0) {
    die("Invalid contractor specified.");
}

// Check if customer has completed a booking with this contractor
$sql = "SELECT id FROM bookings WHERE customer_id = ? AND contractor_id = ? AND status = 'completed' LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $customer_id, $contractor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("You can only leave a review after completing a booking with this contractor.");
}

// Handle review submission
$success_message = "";
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : "";

    // Validate rating
    if ($rating < 1 || $rating > 5) {
        $error_message = "Please select a rating between 1 and 5.";
    } else {
        // Insert review
        $insert_sql = "INSERT INTO reviews (customer_id, contractor_id, rating, comment) VALUES (?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iiis", $customer_id, $contractor_id, $rating, $comment);

        if ($insert_stmt->execute()) {
            $success_message = "Thank you for your review!";
        } else {
            $error_message = "Error submitting your review. Please try again.";
        }
    }
}

// Fetch contractor info for display (optional)
$contractor_sql = "SELECT service_name FROM contractor_details WHERE user_id = ?";
$contractor_stmt = $conn->prepare($contractor_sql);
$contractor_stmt->bind_param("i", $contractor_id);
$contractor_stmt->execute();
$contractor_result = $contractor_stmt->get_result();
$contractor = $contractor_result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Leave Review for <?= htmlspecialchars($contractor['service_name'] ?? 'Contractor'); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px auto;
            max-width: 600px;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
        }

        h1 {
            margin-bottom: 20px;
            color: #333;
        }

        form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 5px #ccc;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        select,
        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: vertical;
        }

        button {
            background-color: #007BFF;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>

<body>
    <h1>Leave a Review for <?= htmlspecialchars($contractor['service_name'] ?? 'Contractor'); ?></h1>

    <?php if ($success_message): ?>
        <div class="message success"><?= $success_message ?></div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="message error"><?= $error_message ?></div>
    <?php endif; ?>

    <?php if (!$success_message): ?>
        <form method="post" action="">
            <label for="rating">Rating (1 to 5):</label>
            <select name="rating" id="rating" required>
                <option value="">-- Select Rating --</option>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?> star<?= $i > 1 ? 's' : '' ?></option>
                <?php endfor; ?>
            </select>

            <label for="comment">Comments (optional):</label>
            <textarea name="comment" id="comment" rows="5" placeholder="Write your experience..."></textarea>

            <button type="submit">Submit Review</button>
        </form>
    <?php endif; ?>
</body>

</html>