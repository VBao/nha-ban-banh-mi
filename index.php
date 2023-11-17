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
$sql = "SELECT id, name, price, image FROM product ORDER BY id DESC LIMIT 5";
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
   $temp = $_GET['addToCart'];
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

   <!--Sản phẩm-->
   <section class="doan" id="doan">
      <div class="container">

         <h3 class="title"> Sản Phẩm Bán chạy </h3>

         <div class="products-container">

            <div class="product" data-name="p-1">
               <img src="assets/<?= $products[0]['image'] ?>" alt="">
               <h3>
                  <?= $products[0]['name'] ?>
               </h3>
               <div class="price">
                  <?= "$" . $products[0]['price'] ?>
               </div>
               <div class="product-btn">
                  <div class="add-cart-link">
                     <button>
                        <?php echo "<a href='#' class='add-to-cart' data-item-id='{$products[0]['id']}'>Add to Cart</a>"; ?>
                     </button>
                  </div>
                  <div class="buy-now-link">
                     <button>
                        <?php echo "<a class='by-now' href='http://localhost/cart.php?buynow={$products[0]['id']}' data-item-id='{$products[0]['id']}'>Buy Now</a>"; ?>
                     </button>
                  </div>
               </div>
            </div>

            <div class="product" data-name="p-2">
               <img src="assets/<?= $products[1]['image'] ?>" alt="">
               <h3>
                  <?= $products[1]['name'] ?>
               </h3>
               <div class="price">
                  <?= "$" . $products[1]['price'] ?>
               </div>
               <div class="product-btn">
                  <div class="add-cart-link">
                     <button>
                        <?php echo "<a href='#' class='add-to-cart' data-item-id='{$products[1]['id']}'>Add to Cart</a>"; ?>
                     </button>
                  </div>
                  <div class="buy-now-link">
                     <button>
                        <?php echo "<a class='by-now' href='http://localhost/cart.php?buynow={$products[1]['id']}' data-item-id='{$products[1]['id']}'>Buy Now</a>"; ?>
                     </button>
                  </div>
               </div>
            </div>
            <div class="product" data-name="p-3">
               <img src="assets/<?= $products[2]['image'] ?>" alt="">
               <h3>
                  <?= $products[2]['name'] ?>
               </h3>
               <div class="price">
                  <?= "$" . $products[2]['price'] ?>
               </div>
               <div class="product-btn">
                  <div class="add-cart-link">
                     <button>
                        <?php echo "<a href='#' class='add-to-cart' data-item-id='{$products[2]['id']}'>Add to Cart</a>"; ?>
                     </button>
                  </div>
                  <div class="buy-now-link">
                     <button>
                        <?php echo "<a class='by-now' href='http://localhost/cart.php?buynow={$products[2]['id']}' data-item-id='{$products[2]['id']}'>Buy Now</a>"; ?>
                     </button>
                  </div>
               </div>
            </div>
            <div class="product" data-name="p-4">
               <img src="assets/<?= $products[3]['image'] ?>" alt="">
               <h3>
                  <?= $products[3]['name'] ?>
               </h3>
               <div class="price">
                  <?= "$" . $products[3]['price'] ?>
               </div>
               <div class="product-btn">
                  <div class="add-cart-link">
                     <button>
                        <?php echo "<a href='#' class='add-to-cart' data-item-id='{$products[3]['id']}'>Add to Cart</a>"; ?>
                     </button>
                  </div>
                  <div class="buy-now-link">
                     <button>
                        <?php echo "<a class='by-now' href='http://localhost/cart.php?buynow={$products[3]['id']}' data-item-id='{$products[3]['id']}'>Buy Now</a>"; ?>
                     </button>
                  </div>
               </div>
            </div>
            <div class="product" data-name="p-5">
               <img src="assets/<?= $products[4]['image'] ?>" alt="">
               <h3>
                  <?= $products[4]['name'] ?>
               </h3>
               <div class="price">
                  <?= "$" . $products[4]['price'] ?>
               </div>
               <div class="product-btn">
                  <div class="add-cart-link">
                     <button>
                        <?php echo "<a href='#' class='add-to-cart' data-item-id='{$products[4]['id']}'>Add to Cart</a>"; ?>
                     </button>
                  </div>
                  <div class="buy-now-link">
                     <button>
                        <?php echo "<a class='by-now' href='http://localhost/cart.php?buynow={$products[4]['id']}' data-item-id='{$products[4]['id']}'>Buy Now</a>"; ?>
                     </button>
                  </div>
               </div>
            </div>
         </div>
   </section>
   <!--Thông Tin về chúng tôi-->
   <section class="about" id="about">
      <h3 class="sub-heading">About Us</h3>
      <h1 class="heading">why you choose us ? </h1>
      <div class="row">
         <div class="image">
            <img src="img/banh1.webp" alt="">
         </div>
         <div class="content">
            <h2>TOUS les JOURS</h2>
            <br><br>
            <p>Chúng tôi không chỉ đơn thuần là một tiệm bánh. Chúng tôi là một gia đình, là một cộng đồng yêu thích
               bánh ngọt và nghệ thuật làm bánh. Được thành lập bởi một nhóm những người đam mê với việc làm bánh và
               khao khát mang đến cho mọi người những trải nghiệm đặc biệt, chúng tôi đã biến ước mơ thành hiện thực.
            </p>
            <br><br>
            <div class="icons-container">
               <div class="icons">
                  <img src="assets/truck-fast-solid.svg" alt="Free Shipping">
                  <span>Free Delivery</span>
               </div>
               <div class="icons">
                  <img src="assets/credit-card-solid.svg" alt="Easy Payment">
                  <span>Easy Payment</span>
               </div>
            </div>
            <br>
            <a href="#" class="btn">Learn more </a>
         </div>
      </div>
   </section>
   <section class="review" id="review"><!--Review của khách hàng-->
      <div id="comment">
         <h2>NHẬN XÉT CỦA KHÁCH HÀNG</h2>
         <div id="comment-body">
            <div class="prev">
               <a href="#">
                  <img src="assets/prev.png" alt="Avatar">
               </a>
            </div>
            <ul id="list-comment">
               <li class="hinhanh">
                  <div class="avatar">
                     <img src="img/riziu.png" alt="Avatar">
                  </div>
                  <div class="stars">
                     <span>
                        <img src="assets/star.png" alt="Star">
                     </span>
                     <span>
                        <img src="assets/star.png" alt="Star">
                     </span>
                     <span>
                        <img src="assets/star.png" alt="Star">
                     </span>
                     <span>
                        <img src="assets/star.png" alt="Star">
                     </span>
                     <span>
                        <img src="assets/star.png" alt="Star">
                     </span>
                  </div>
                  <div class="name">Trần Ngọc Sơn</div>
                  <div class="text">
                     <p>
                        Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                        Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,
                        when an unknown printer took a galley of type and scrambled it to make a type
                        specimen book.
                     </p>
                  </div>
               </li>

               <li class="hinhanh">
                  <div class="avatar">
                     <img src="img/riziu.png" alt="Avatar">
                  </div>
                  <div class="stars">
                     <span>
                        <img src="assets/star.png" alt="Star">
                     </span>
                     <span>
                        <img src="assets/star.png" alt="Star">
                     </span>
                     <span>
                        <img src="assets/star.png" alt="Star">
                     </span>
                     <span>
                        <img src="assets/star.png" alt="Star">
                     </span>
                     <span>
                        <img src="assets/star.png" alt="Star">
                     </span>
                  </div>
                  <div class="name">Trần Đắc Phú</div>
                  <div class="text">
                     <p>
                        Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                        Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,
                        when an unknown printer took a galley of type and scrambled it to make a type
                        specimen book.
                     </p>
                  </div>
               </li>

            </ul>
            <div class="next">
               <a href="#">
                  <img src="assets/next.png" alt="Next">
               </a>
            </div>
         </div>
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
</script>

</html>