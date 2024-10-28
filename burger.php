<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Nếu chưa đăng nhập, chuyển hướng đến trang đăng nhập
    exit;
}

$user_id = $_SESSION['user_id']; // Lấy ID người dùng từ session

// Kết nối cơ sở dữ liệu
$host = 'localhost';
$dbname = 'web';
$dbuser = 'root';
$dbpass = 'password';
$conn = new mysqli($host, $dbuser, $dbpass, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Khởi tạo biến để lưu thông tin đơn hàng
$order_summary = "";
$order_success = false; // Biến để kiểm tra trạng thái lưu đơn hàng

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $protein = $_POST['protein'];
    $patties = $_POST['patties'];
    $cook_level = $_POST['cook_level'];
    $toppings = isset($_POST['toppings']) ? implode(", ", $_POST['toppings']) : 'None';
    $cheese = $_POST['cheese'];
    $bun_type = $_POST['bun_type'];
    $sauce = $_POST['sauce'];
    $additional = $_POST['additional'];

    // Chuẩn bị câu lệnh SQL để lưu thông tin đơn hàng
    $stmt = $conn->prepare("INSERT INTO burgers (user_id, protein, patties, cook_level, toppings, cheese, bun_type, sauce, additional) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ississsss", $user_id, $protein, $patties, $cook_level, $toppings, $cheese, $bun_type, $sauce, $additional);

    if ($stmt->execute()) {
        // Tạo tóm tắt đơn hàng
        $order_summary = "<h3>Đơn hàng của bạn đã được lưu thành công!</h3>";
        $order_summary .= "<p>Protein: $protein</p>";
        $order_summary .= "<p>Patties: $patties</p>";
        $order_summary .= "<p>Cook Level: $cook_level</p>";
        $order_summary .= "<p>Toppings: $toppings</p>";
        $order_summary .= "<p>Cheese: $cheese</p>";
        $order_summary .= "<p>Bun Type: $bun_type</p>";
        $order_summary .= "<p>Sauce: $sauce</p>";
        $order_summary .= "<p>Additional: $additional</p>";
        $order_success = true; // Đánh dấu rằng đơn hàng đã lưu thành công
    } else {
        echo "<h3>Đã xảy ra lỗi khi lưu đơn hàng. Vui lòng thử lại.</h3>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Burger Page</title>
    <link rel="stylesheet" href="style1.css">
    <style>
        .success-message {
            display: none; /* Ẩn thông điệp thành công theo mặc định */
            border: 2px solid #4CAF50;
            border-radius: 5px;
            padding: 20px;
            margin-top: 20px;
            background-color: #f9f9f9;
        }

        .success-message.visible {
            display: block; /* Hiển thị thông điệp khi có lớp 'visible' */
        }
    </style>
</head>
<body>
    <h1>Chào mừng bạn đến với trang làm burger!</h1>
    
    <!-- Nút đăng xuất -->
    <form action="logout.php" method="POST">
        <input type="submit" value="Đăng xuất">
    </form>
    
    <form class="class1" action="burger.php" method="POST">
        <h2>Create a burger!</h2>
        
        <label for="protein">What type of protein would you like?</label>
        <input type="text" id="protein" name="protein" required><br>
        
        <label for="patties">How many patties would you like?</label>
        <input type="number" id="patties" name="patties" min="1" required><br>
        
        <p>How do you want the patty cooked?</p>
        Rare <input type="range" name="cook_level" min="1" max="5"> Well-Done<br>
        
        <p>What topping would you like?</p>
        <input type="checkbox" id="topping_lettuce" name="toppings[]" value="Lettuce">
        <label for="topping_lettuce">Lettuce</label>
        <input type="checkbox" id="topping_tomato" name="toppings[]" value="Tomato">
        <label for="topping_tomato">Tomato</label>
        <input type="checkbox" id="topping_onion" name="toppings[]" value="Onion">
        <label for="topping_onion">Onion</label><br>
        
        <p>Would you like to add cheese?</p>
        <input type="radio" id="cheese_yes" name="cheese" value="Yes">
        <label for="cheese_yes">Yes</label>
        <input type="radio" id="cheese_no" name="cheese" value="No" required>
        <label for="cheese_no">No</label><br>
        
        <p>What type of bun would you like?</p>
        <select name="bun_type" required>
            <option value="Sesame">Sesame</option>
            <option value="Meat">Meat</option>
            <option value="Banh cuon">Banh cuon</option>
        </select>
        
        <label for="sauce">What type of sauce would you like?</label>
        <input type="text" id="sauce" name="sauce"><br>
        
        <label for="additional">Anything else you want to add?</label>
        <input type="text" id="additional" name="additional"><br>
        
        <input type="submit" value="Submit">
    </form>

    <!-- Hiển thị tóm tắt đơn hàng nếu đã lưu thành công -->
    <div class="success-message <?php if ($order_success) echo 'visible'; ?>">
        <?php if ($order_success) echo $order_summary; ?>
    </div>
</body>
</html>
