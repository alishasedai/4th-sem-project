<?php
// contractor_card.php
// $row = contractor array from DB
// $show_buttons = true/false to show 'View Profile' & 'Book Now'

if (!isset($row)) return;

// Fetch average rating and review count
$contractor_id = $row['user_id'];
$rating_sql = "SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews 
               FROM reviews WHERE contractor_id = $contractor_id";
$rating_result = mysqli_query($conn, $rating_sql);
$rating_data = mysqli_fetch_assoc($rating_result);
$avg_rating = round($rating_data['avg_rating'], 1) ?: 0;
$total_reviews = $rating_data['total_reviews'] ?: 0;
?>

<div class="contractor-card">
    <img src="uploads/<?= htmlspecialchars($row['profile_photo'] ?? '') ?>" class="profile" alt="Contractor">

    <div class="names">
        <h3><?= htmlspecialchars($row['contractor_name']) ?></h3>
        <div class="rating">
            ⭐ <?= $avg_rating ?>
            <?php if ($total_reviews > 0): ?>
                <span><?= $total_reviews ?> review<?= $total_reviews > 1 ? 's' : '' ?></span>
            <?php endif; ?>
        </div>
    </div>

    <p><?= htmlspecialchars($row['description']) ?></p>

    <div class="project-images">
        <?php
        if (!empty($row['work_photos'])) {
            $photos = explode(',', $row['work_photos']);
            foreach ($photos as $photo) {
                if (trim($photo) !== '') {
                    echo '<img src="uploads/' . htmlspecialchars(trim($photo)) . '" alt="Project">';
                }
            }
        } else {
            echo '<p style="color:#888; font-size:13px;">No project photos uploaded yet.</p>';
        }
        ?>
    </div>

    <?php if (!empty($show_buttons)): ?>
        <div class="card-buttons">
            <button class="view"><a href="contractor_profile.php?id=<?= $row['user_id'] ?>">View Profile</a></button>
            <button class="contact"><a href="booking.php?contractor_id=<?= $row['user_id'] ?>&service=<?= urlencode($row['service_name']) ?>">Book Now</a></button>
        </div>
    <?php endif; ?>
</div>