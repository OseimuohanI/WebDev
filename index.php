<?php
$conn = mysqli_connect("127.0.0.1:3305", "root", "", "feedback_portal");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    $sql = "INSERT INTO feedbacks (name, email, rating, comment)
            VALUES ('$name', '$email', '$rating', '$comment')";
    mysqli_query($conn, $sql);
}
$filter = "";
if (isset($_GET['rating']) && $_GET['rating'] != "") {
    $r = $_GET['rating'];
    $filter = "WHERE rating = $r";
}
if (isset($_GET['keyword']) && $_GET['keyword'] != "") {
    $k = $_GET['keyword'];
    $filter = "WHERE comment LIKE '%$k%' OR name LIKE '%$k%'";
}
$sql = "SELECT * FROM feedbacks $filter ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

$avg_sql = "SELECT AVG(rating) AS avg FROM feedbacks";
if ($filter != "") {
    $avg_sql = "SELECT AVG(rating) AS avg FROM feedbacks $filter";
}
$avg_result = mysqli_query($conn, $avg_sql);
$avg_row = mysqli_fetch_assoc($avg_result);
$average = round($avg_row['avg'], 2);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Feedback Portal</title>
</head>
<body>
<h1>Feedback Collection Portal</h1>
<form method="POST" action="">
    <p>Name: <input type="text" name="name" required></p>
    <p>Email: <input type="email" name="email" required></p>
    <p>Rating (1–5): <input type="number" name="rating" min="1" max="5" required></p>
    <p>Comment:<br><textarea name="comment" rows="3" cols="40"></textarea></p>
    <p><input type="submit" name="submit" value="Submit Feedback"></p>
</form>
<hr>
<form method="GET" action="">
    <p>Filter by rating:
        <select name="rating">
            <option value="">All</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>
        <input type="submit" value="Filter">
    </p>
</form>
<form method="GET" action="">
    <p>Search keyword:
        <input type="text" name="keyword">
        <input type="submit" value="Search">
    </p>
</form>
<hr>
<h3>Average Rating: <?php echo $average ? $average : 'No feedback yet'; ?></h3>
<h2>All Feedback</h2>
<?php
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<p><b>" . $row['name'] . "</b> (" . $row['email'] . ")<br>";
        echo "Rating: " . $row['rating'] . "/5<br>";
        echo "Comment: " . $row['comment'] . "</p><hr>";
    }
} else {
    echo "<p>No feedback found.</p>";
}
?>
</body>
</html>