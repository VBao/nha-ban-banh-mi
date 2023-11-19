<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bake_shop";

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
$sql = "SELECT id, name, price, image FROM product ORDER BY id DESC";
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

// if (isset($_GET['buynow'])) {

//    $servername = "localhost";
//    $username = "root";
//    $password = "";
//    $dbname = "bake_shop";
//    // Create connection

//    $conn = mysqli_connect($servername, $username, $password, $dbname);


//    $itemId = $_GET['buynow'];
//    // Check if the item is already in the cart
//    $key = array_search($itemId, array_column($_SESSION['cart'], 'id'));

//    if ($key !== false) {
//       // If the item is already in the cart, update the quantity
//       $_SESSION['cart'][$key]['quantity'] += 1;
//    } else {

//       $sql = "SELECT id, name, price, image FROM product WHERE id = $itemId";
//       $result = mysqli_query($conn, $sql);
//       $row = mysqli_fetch_assoc($result);
//       // If the item is not in the cart, add it with a quantity of 1
//       $_SESSION['cart'][] = array('id' => $itemId, 'quantity' => 1, 'name' => $row['name'], 'price' => $row['price']);
//    }

// }
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
               <li><a href="contact.html">Review</a></li>
               <li><a href="#">Blog</a></li>
               <li><a href="#">Cửa Hàng</a>
                  <ul>
                     <li><a href="#">Khu vực phía bắc</a></li>
                     <li><a href="#">Khu vực phía nam</a></li>
                  </ul>
               </li>
               <li><a href="contact.html">Liên Hệ</a></li>
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

   <!--Sản phẩm-->
   <section class="doan" id="doan">
      <div class="container">

         <h3 class="title"> Sản Phẩm Bán chạy </h3>

         <div class="products-container">
            <?php foreach ($products as $product): ?>

               <div class="product">
                  <img src="assets/<?= $product['image'] ?>" alt="">
                  <h3>
                     <?= $product['name'] ?>
                  </h3>
                  <div class="price">
                     <?= "$" . $product['price'] ?>
                  </div>
                  <div class="product-btn">
                     <div class="add-cart-link">
                        <button>
                           <?php echo "<a href='#' class='add-to-cart' data-item-id='{$product['id']}'>Add to Cart</a>"; ?>
                        </button>
                     </div>
                     <div class="buy-now-link">
                        <button>
                           <?php echo "<a class='by-now' href='http://localhost/cart.php?buynow={$product['id']}' data-item-id='{$product['id']}'>Buy Now</a>"; ?>
                        </button>
                     </div>
                  </div>
               </div>
            <?php endforeach; ?>
         </div>

   </section>
   <!--Thông Tin về chúng tôi-->
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
               Chúng Tôi Không Chỉ Đơn Thuần Là Một Tiệm Bánh. Chúng Tôi Là Một Gia Đình, Là Một Cộng Đồng Yêu Thích
               Bánh Ngọt Và Nghệ Thuật Làm Bánh. Được Thành Lập Bởi Một Nhóm Những Người Đam Mê Với Việc Làm Bánh Và
               Khao Khát Mang Đến Cho Mọi Người Những Trải Nghiệm Đặc Biệt, Chúng Tôi Đã Biến Ước Mơ Thành Hiện Thực
            </p>

            <div class="footer-media">
               <a href="#"><i class="fa fa-youtube"></i></a>
               <a href="#"><i class="fa fa-facebook"></i></a>
               <a href="#"><i class="fa fa-twitter"></i></a>
               <a href="#"><i class="fa fa-instagram"></i></a>
               <a href="#"><i class="fa fa-linkedin"></i></a>
            </div>
         </div>

      </footer>
      <script src="app.js"></script>

</body>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
   $(document).ready(function () {
      // Add to Cart click event
      $('.add-to-cart').on('click', function (e) {
         e.preventDefault();
         // Get the item ID from the data attribute
         var itemId = $(this).data('item-id');
         console.log(itemId);
         // AJAX request to add item to cart
         $.ajax({
            type: 'GET',
            url: '?addToCart=' + itemId,
            success: function (response) {
               // Display a confirmation message (you can customize this part)
               alert('Item added to cart!');
               // window.location.reload();
            },
            error: function () {
               // Handle errors if needed
               alert('Error adding item to cart.');
            }
         });
      });
   });
   // $(document).ready(function () {
   //    // Add to Cart click event
   //    $('.buy-now').on('click', function (e) {
   //       e.preventDefault();
   //       // Get the item ID from the data attribute
   //       var itemId = $(this).data('item-id');
   //       console.log(itemId);
   //       // AJAX request to add item to cart
   //       $.ajax({
   //          type: 'GET',
   //          url: '?buynow=' + itemId,
   //          success: function (response) {
   //             // Display a confirmation message (you can customize this part)
   //             alert('Buynow !');
   //             window.location.href('http://localhost/cart.php');
   //          },
   //          error: function () {
   //             // Handle errors if needed
   //             alert('Error adding item to cart.');
   //          }
   //       });
   //    });
   // });
</script>

</html>