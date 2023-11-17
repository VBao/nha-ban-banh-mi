<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bake_shop";
$is_connect = "FALSE";

$temp = "";

session_start();

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql = "SELECT id, name, price, image FROM product LIMIT 12";
$result = mysqli_query($conn, $sql);

$products = array();
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $product = array(
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => $row['price'],
            'image' => $row['image']
        );
        array_push($products, $product);
    }
}

if (isset($_GET['addToCart'])) {
    $temp = "adjawjhdbkaw";

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "bake_shop";
    // Create connection

    $conn = mysqli_connect($servername, $username, $password, $dbname);


    $itemId = $_GET['addToCart'];
    // Check if the item is already in the cart
    $key = array_search($itemId, array_column($_SESSION['cart'], 'id'));

    if ($key !== false) {
        // If the item is already in the cart, update the quantity
        $_SESSION['cart'][$key]['quantity'] += 1;
    } else {

        $sql = "SELECT id, name, price, image FROM product WHERE id = $itemId";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        // If the item is not in the cart, add it with a quantity of 1
        $_SESSION['cart'][] = array('id' => $itemId, 'quantity' => 1, 'name' => $row['name'], 'price' => $row['price']);
    }

    // // Close the database connection
    // $conn->close();

    // Return a JSON response to indicate success
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit();
}



// Function to remove an item from the cart based on its ID
function removeFromCart($itemId)
{
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $cartItem) {
            if ($cartItem['id'] == $itemId) {
                // Remove the item from the cart
                unset($_SESSION['cart'][$key]);
                // Reindex the array to avoid gaps in the array keys
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                break; // Exit the loop once the item is found and removed
            }
        }
    }
}

// Check confirm to remove from cart
// Check if the "OK" button is clicked and an item ID is provided
if (isset($_POST['okButton']) && isset($_POST['itemId'])) {
    // Get the item ID from the POST data
    $itemId = $_POST['itemId'];

    // Call the function to remove the item from the cart
    removeFromCart($itemId);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get order information from the form
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $note = $_POST['note'];

    // Get cart items from the session (replace with your actual session variable)
    $cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();

    // Call the function to save order information to the database
    saveOrderToDatabase($name, $address, $phone, $note, $cartItems);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

function saveOrderToDatabase($name, $address, $phone, $note, $cartItems)
{

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "bake_shop";
    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Assuming you have an 'orders' table with columns: id, name, address, phone
    $sql = "INSERT INTO orders (name, address, phone, note) VALUES ('$name', '$address', '$phone', '$note')";

    // Perform the query and check for errors
    if ($conn->query($sql) === TRUE) {
        // Get the order ID of the inserted row
        $orderID = $conn->insert_id;

        // Insert each item from the cart into the 'order_items' table
        foreach ($cartItems as $cartItem) {
            $productId = $cartItem['id'];
            $quantity = $cartItem['quantity'];
            $sql = "INSERT INTO order_items (order_id, product_id, quantity) VALUES ('$orderID', '$productId', '$quantity')";
            $conn->query($sql);
        }

        // Clear the cart in the session after the order is saved
        $_SESSION['cart'] = array();
        echo "Order placed successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }


    // // Close the database connection
    // $conn->close();
}

// Function to get all orders with order items from the database
function getAllOrdersWithItems()
{

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "bake_shop";
    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Create a connection to the database
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Assuming you have 'orders' and 'order_items' tables
    $sql = "SELECT o.*, o.name as cname ,oi.product_id, oi.quantity, p.name, p.price 
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN product p ON oi.product_id = p.id
            ORDER BY o.id";

    // Perform the query and check for errors
    $result = $conn->query($sql);

    if ($result === false) {
        die("Error in the query: " . $conn->error);
    }

    // Transform the result into an associative array
    $orders = array();
    while ($row = $result->fetch_assoc()) {
        $orderId = $row['id'];
        if (!isset($orders[$orderId])) {
            $orders[$orderId] = array(
                'id' => $orderId,
                'name' => $row['cname'],
                'address' => $row['address'],
                'phone' => $row['phone'],
                'items' => array()
            );
        }

        $orders[$orderId]['items'][] = array(
            'product_id' => $row['product_id'],
            'product_name' => $row['name'],
            'price' => $row['price'],
            'quantity' => $row['quantity']
        );
    }

    // // Close the database connection
    // $conn->close();

    return $orders;
}

// Get all orders with items
$allOrders = getAllOrdersWithItems();


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <div class="header-container">
        <header class="header">
            <a href="" class="logo">
                <img src="assets/logooo.jpg" alt="">
            </a>
            <nav class="navbar">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="menu.php">Thực Đơn</a></li>
                    <li><a href="#">Review</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Cửa Hàng</a>
                        <ul>
                            <li><a href="#">Khu vực phía bắc</a></li>
                            <li><a href="#">Khu vực phía nam</a></li>
                        </ul>
                    </li>
                    <li><a href="#">Liên Hệ</a></li>
                </ul>
            </nav>
            <div class="Icon">
                <a href="cart.php">
                    <img src="assets/cart-shopping-solid.svg" alt="Biểu tượng tài khoản">
                </a>
                <a href="#">
                    <img src="assets/user-solid.svg" alt="Biểu tượng tài khoản">
                </a>
                <!-- <a href="#">
                    <img src="assets/basket-shopping-solid.svg" alt="Biểu tượng Mua">
                </a> -->
            </div>
        </header>
    </div>
</head>

<body>
    <div class="slider"><!--Phần ảnh header (Chuyển ảnh)-->
        <div class="list">
            <div class="item">
                <img src="assets/barner5.jpg" alt="">
            </div>
            <div class="item">
                <img src="assets/barner3.jpg" alt="">
            </div>
            <div class="item">
                <img src="assets/barner1.jpg" alt="">
            </div>
            <div class="item">
                <img src="assets/barner2.jpg" alt="">
            </div>
            <div class="item">
                <img src="assets/barner3.jpg" alt="">
            </div>
        </div>
        <div class="buttons">
            <button id="prev">
                << /button>
                    <button id="next">></button>
        </div>
        <ul class="dots">
            <li class="active"></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
        </ul>
    </div>

    <section class="order">
        <h1 class="heading"> Shopping Cart </h1>
        <!-- Shopping Cart Table -->
        <div>
            <table class="table-order">
                <thead>
                    <tr>
                        <th scope="col-order">Product</th>
                        <th scope="col-order">Price</th>
                        <th scope="col-order">Quantity</th>
                        <th scope="col-order">Total</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $cartItem):
                        ?>
                        <tr>
                            <td>
                                <?php echo $cartItem['name']; ?>
                            </td>
                            <td>
                                <?php echo $cartItem['price']; ?>
                            </td>
                            <td>
                                <?php echo $cartItem['quantity']; ?>
                            </td>
                            <td>
                                <?php echo $cartItem['quantity'] * $cartItem['price']; ?>
                            </td>
                            <?php echo "<td><button onclick='removeFromCart({$cartItem['id']})' data-item-id='{$cartItem['id']}'>Remove</button></td>"; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Order Form -->
        <div class="order-form">
            <h2>Order Information</h2>
            <form action="#" method="post">
                <div class="mb-3">
                    <label for="name" class="form-label">Name:</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address:</label>
                    <textarea class="form-control" id="address" name="address" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number:</label>
                    <input type="tel" class="form-control" id="phone" name="phone" required>
                </div>
                <div>
                    <label for="phone" class="form-label">Note:</label>
                    <textarea name="note" class="box" placeholder="Note" id="" cols="30" rows="10"></textarea>
                </div>
                <button type="submit" class="btn btn-success">Place Order</button>
            </form>
        </div>
    </section>

    <section class="order-list">
        <table border="1">
            <tr>
                <th>Order ID</th>
                <th>Name</th>
                <th>Address</th>
                <th>Phone</th>
            </tr>
            <?php foreach ($allOrders as $order): ?>
                <tr class="collapsible" data-order-id="<?php echo $order['id']; ?>">
                    <td>
                        <?php echo $order['id']; ?>
                    </td>
                    <td>
                        <?php echo $order['name']; ?>
                    </td>
                    <td>
                        <?php echo $order['address']; ?>
                    </td>
                    <td>
                        <?php echo $order['phone']; ?>
                    </td>
                </tr>
                <tr class="hidden-row" data-order-id="<?php echo $order['id']; ?>">
                    <td colspan="4">
                        <!-- Display order items in a nested table -->
                        <table border="1">
                            <tr>
                                <th>Product ID</th>
                                <th>Product Name</th>
                                <th>Price</th>
                                <th>Quantity</th>
                            </tr>
                            <?php foreach ($order['items'] as $item): ?>
                                <tr>
                                    <td>
                                        <?php echo $item['product_id']; ?>
                                    </td>
                                    <td>
                                        <?php echo $item['product_name']; ?>
                                    </td>
                                    <td>
                                        <?php echo $item['price']; ?>
                                    </td>
                                    <td>
                                        <?php echo $item['quantity']; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </section>


    <div id="footer">
        <footer class="footer">
            <div class="footer-left">
                <h3>Payment Method</h3>
                <div class="credit-cards">
                    <img src="img/visa.png" alt="">
                    <img src="img/mastercard.png" alt="">
                    <img src="img/paypal.png" alt="">
                </div>
                <p class="footer-copyright">2023 Mandankoding</p>
            </div>

            <div class="footer-center">
                <div>
                    <i class="fa fa-map-marker"></i>
                    <p><span>VietNam</span>GoVap district,HoChiMinh</p>
                </div>
                <div>
                    <i class="fa fa-phone"></i>
                    <p>+84 077-777-77</p>
                </div>
                <div>
                    <i class="fa fa-envelope"></i>
                    <p><a href="#">support@gmail.com</a></p>
                </div>
            </div>

            <div class="footer-right">
                <p class="footer-about">
                    <span>About</span>
                    Chúng Tôi Không Chỉ Đơn Thuần Là Một Tiệm Bánh. Chúng Tôi Là Một Gia Đình, Là Một Cộng Đồng Yêu
                    Thích
                    Bánh Ngọt Và Nghệ Thuật Làm Bánh. Được Thành Lập Bởi Một Nhóm Những Người Đam Mê Với Việc Làm Bánh
                    Và
                    Khao Khát Mang Đến Cho Mọi Người Những Trải Nghiệm Đặc Biệt, Chúng Tôi Đã Biến Ước Mơ Thành Hiện
                    Thực
                </p>

                <div class="footer-media">
                    <a href="#"><i class="fa fa-youtube"></i></a>
                    <a href="https://www.facebook.com/TousLesJoursUSA/"><i class="fa fa-facebook"></i></a>
                    <a href="#"><i class="fa fa-twitter"></i></a>
                    <a href="https://www.instagram.com/touslesjoursusa/"><i class="fa fa-instagram"></i></a>
                    <a href="#"><i class="fa fa-linkedin"></i></a>
                </div>
            </div>

        </footer>
        <script src="app.js"></script>

</body>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    function removeFromCart(itemId) {
        var confirmRemove = confirm("Are you sure you want to remove this item from the cart?");
        if (confirmRemove) {
            // Call the PHP script to remove the item from the cart
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "remove_from_cart.php", true); // Replace with the correct URL
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Reload the page after successful removal
                    window.location.reload();
                }
            };
            xhr.send("okButton=1&itemId=" + itemId);
        }
    }

    // Collasp order detail
    document.addEventListener("DOMContentLoaded", function () {
        var collapsibleRows = document.querySelectorAll(".collapsible");

        collapsibleRows.forEach(function (row) {
            row.addEventListener("click", function () {
                var orderId = row.getAttribute("data-order-id");
                var hiddenRow = document.querySelector(".hidden-row[data-order-id='" + orderId + "']");

                if (hiddenRow) {
                    hiddenRow.classList.toggle("show");
                }
            });
        });
    });
</script>

</html>