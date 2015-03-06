<html>
  <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
  <meta content="utf-8" http-equiv="encoding">

  <title>CPSC 304 Project</title>
<!--
    A simple stylesheet is provided so you can modify colours, fonts, etc.
  -->
  <link href="customer.css" rel="stylesheet" type="text/css">

<!--
    Javascript to submit a title_id as a POST form, used with the "delete" links
  -->
  <script>
  function formSubmit(titleId) {
    'use strict';
    if (confirm('Are you sure you want to delete this item?')) {
      // Set the value of a hidden HTML element in this form
      var form = document.getElementById('delete');
      form.title_id.value = titleId;
      // Post this form
      form.submit();
    }
  }
  </script>


<div class="header">
  <h1>THE "WE SELL THINGS!!" STORE</h1>
  <p3>View:</p3>
 </div>
<div class="header-cont">
  <div class="header">
  <p>THE "WE SELL THINGS!!" STORE</p>
  <p3>View:</p3><p2><li><a href="manager.php">Manager</a></li>
  <li><a href="clerk.php">Clerk</a></li>
  <li><a href="customer_login.php">Customer Login</a></li>
  <li><a href="customer.php"><now>Customer</now></a></li>
  <li><a href="customer_purchase.php">Customer Purchase</a></li></p2>
   </div>
</div>
 
<body>
<div class="content">
  <?php
  $connection = new mysqli("localhost", "root", "b1j8", "practice");

  if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["submit3"]) && $_POST["submit3"] == "Add To Cart!") {

      $quantity_post = $_POST["add_quantity"];
      $addupc = $_POST["add_upc"];

      $stmt = $connection->prepare("SELECT * FROM item WHERE upc = ?");
      $stmt->bind_param("s", $addupc);
      $stmt->execute();
      $result = $stmt->get_result();
      $row = $result->fetch_assoc();

      $upc = $row['upc'];
      $title = $row['title'];
      $itype = $row['itype'];
      $category = $row['category'];
      $company = $row['company'];
      $iyear = $row['iyear'];
      $price = $row['price'];
      $stock = $row['stock'];

      if ($stock < $quantity_post) {
        $quantity_post = $stock;
      } 

      $stmt = $connection->prepare("SELECT upc FROM shoppingCart WHERE upc = ?");
      $stmt->bind_param("s", $addupc);
      $stmt->execute();
      $stmt->bind_result($result);
      $stmt->fetch();

      if ($result == NULL) {
        $stmt->close();
        $stmt = $connection->prepare("INSERT INTO shoppingCart (upc,title,itype,category,company,iyear,price,stock) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssssssss", $addupc, $title, $itype, $category, $company, $iyear, $price, $quantity_post);
        $stmt->execute();
          
        if($stmt->error) {       
          //printf("<b>Error: %s.</b>\n", $stmt->error);
          printf("<b>Item is not available!</b>");
        } else {
          echo "<b>Successfully added ".$title."</b>";
        }
      } else {
        $stmt->close();
        $stmt = $connection->prepare("UPDATE shoppingCart SET shoppingCart.stock = shoppingCart.stock + ? WHERE shoppingCart.upc = ?");
        $stmt->bind_param("ss", $quantity_post, $addupc);
        $stmt->execute();
          
        if($stmt->error) {
          printf("<b>Error: %s.</b>\n", $stmt->error);
        } else {
          echo "<b>Successfully Added To Shopping Cart!</b>";
        }
      }

      $stmt = $connection->prepare("UPDATE item SET item.stock = item.stock - ? WHERE item.upc = ?");
      $stmt->bind_param("ss", $quantity_post, $addupc);
      $stmt->execute();

    } else if (isset($_POST["submitDelete"]) && $_POST["submitDelete"] == "DELETE") {

      $deleteTitleID = $_POST['title_id'];

      $stmt = $connection->prepare("SELECT * FROM shoppingCart WHERE upc = ?");
      $stmt->bind_param("s", $deleteTitleID);
      $stmt->execute();
      $result = $stmt->get_result();
      $row = $result->fetch_assoc();

      $quantity = $row['stock'];

      $stmt = $connection->prepare("DELETE FROM shoppingCart WHERE upc = ?");
      //$deleteTitleID = $_POST['title_id'];
      $stmt->bind_param("s", $deleteTitleID);
      $stmt->execute();

      $stmt->close();

      $stmt = $connection->prepare("UPDATE item SET item.stock = item.stock + ? WHERE item.upc = ?");
      $stmt->bind_param("ss", $quantity, $deleteTitleID);
      $stmt->execute();
          
      if($stmt->error) {
        printf("<b>Error: %s.</b>\n", $stmt->error);
      } else {
        echo "<b>Successfully deleted</b>";
      }
    }
  }
  ?>
  <h2>Search For Items</h2>
  <form id="add" name="add" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table>
      <tr><td>UPC</td><th><input type="text" size=30 name="new_upc"</th></tr>
      <tr><td>Title</td><th><input type="text" size=30 name="new_title"</th></tr>
      <tr><td>Type</td><th> <input type="text" size=5 name="new_itype"></th></tr>
      <tr><td>Category</td><th><input type="text" size=10 name="new_category"</th></tr>
      <tr><td>Company</td><th><input type="text" size=30 name="new_company"</th></tr>
      <tr><td></td><th><input type="submit" name="submit" border=0 value="Search!"></th></tr>
    </table>
  </form>
  
<h2>Your Search Results</h2>
<!-- Set up a table to view the book titles -->
<table>
<!-- Create the table column headings -->
<tr valign=center>
<td class=rowheader>UPC</td>
<td class=rowheader>Title</td>
<td class=rowheader>Type</td>
<td class=rowheader>Category</td>
<td class=rowheader>Company</td>
<td class=rowheader>Year</td>
<td class=rowheader>Price</td>
<td class=rowheader>Stock</td>
</tr>

  <?php
  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["submit"]) && $_POST["submit"] == "Search!") {
      $upc = $_POST["new_upc"];
      $title = $_POST["new_title"];
      $itype = $_POST["new_itype"];
      $category = $_POST["new_category"];
      $company = $_POST["new_company"];

      if ($upc != NULL) {
        $stmt = $connection->prepare("SELECT * FROM item WHERE upc = ?");
        $stmt->bind_param("s", $upc);
        $stmt->execute();
        $result = $stmt->get_result();

        while($row = $result->fetch_assoc()){
          echo "<th>".$row['upc']."</th>";
          echo "<th>".$row['title']."</th>";
          echo "<th>".$row['itype']."</th>";
          echo "<th>".$row['category']."</th>";
          echo "<th>".$row['company']."</th>";
          echo "<th>".$row['iyear']."</th>";
          echo "<th>".$row['price']."</th>";
          echo "<th>".$row['stock']."</th><th>";

          echo "</th></tr>";
        }
      echo "</form>";

      } else {
        $stmt = $connection->prepare("SELECT * FROM item WHERE title = ? OR itype = ? OR category = ? OR company = ?");
        $stmt->bind_param("ssss", $title, $itype, $category, $company);
        $stmt->execute();
        $result = $stmt->get_result();

        while($row = $result->fetch_assoc()) {

          echo "<th>".$row['upc']."</th>";
          echo "<th>".$row['title']."</th>";
          echo "<th>".$row['itype']."</th>";
          echo "<th>".$row['category']."</th>";
          echo "<th>".$row['company']."</th>";
          echo "<th>".$row['iyear']."</th>";
          echo "<th>".$row['price']."</th>";
          echo "<th>".$row['stock']."</th><th>";

          echo "</th></tr>";
        }
        echo "</form>";
      }

    } else if (isset($_POST["submit2"]) && $_POST["submit2"] ==  "Let Me Pay!") {
      header("Location: http://localhost/customer_purchase.php");
    }
  }
  ?>

</table>

<h2>Add An Item To Your Cart</h2>
  <form id="add" name="add" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table>
      <tr><td>UPC</td><th><input type="text" size=30 name="add_upc"</th></tr>
      <tr><td>Quantity</td><th><input type="text" size=30 name="add_quantity"</th></tr>
      <tr><td></td><th><input type="submit" name="submit3" border=0 value="Add To Cart!"></th></tr>
    </table>
  </form>

<h2>Shopping Cart</h2>
  <!-- Set up a table to view the book titles -->
  <table>
    <!-- Create the table column headings -->

  <tr valign=center>
  <td class=rowheader>UPC</td>
  <td class=rowheader>Title</td>
  <td class=rowheader>Type</td>
  <td class=rowheader>Category</td>
  <td class=rowheader>Company</td>
  <td class=rowheader>Year</td>
  <td class=rowheader>Price</td>
  <td class=rowheader>Stock</td>
  <td class=rowheader></td>
  </tr>


  <?php
  if (!$result = $connection->query("SELECT * FROM shoppingCart")) {
    die('There was an error running the query [' . $db->error . ']');
  }

  // Avoid Cross-site scripting (XSS) by encoding PHP_SELF (this page) using htmlspecialchars.
  echo "<form id=\"delete\" name=\"delete\" action=\"";
  echo htmlspecialchars($_SERVER["PHP_SELF"]);
  echo "\" method=\"POST\">";

  // Hidden value is used if the delete link is clicked
  echo "<input type=\"hidden\" name=\"title_id\" value=\"-1\"/>";

  // We need a submit value to detect if delete was pressed 
  echo "<input type=\"hidden\" name=\"submitDelete\" value=\"DELETE\"/>";

  while($row = $result->fetch_assoc()){
    echo "<th>".$row['upc']."</th>";
    echo "<th>".$row['title']."</th>";
    echo "<th>".$row['itype']."</th>";
    echo "<th>".$row['category']."</th>";
    echo "<th>".$row['company']."</th>";
    echo "<th>".$row['iyear']."</th>";
    echo "<th>".$row['price']."</th>";
    echo "<th>".$row['stock']."</th><th>";
    echo "<a href=\"javascript:formSubmit('".$row['upc']."');\">DELETE</a>";
    echo "</th></tr>";
  }

  echo "</form>";

  // Close the connection to the database once we're done with it.
  mysqli_close($connection);
  ?>

</table>

<div class="right">
<che>Check Out!</che>
<form id="add" name="add" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<table>
  <tr><td></td><th><input type="submit" name="submit2" border=0 value="Let Me Pay!"></th><th></th></tr>
</table>
</form>
</div>
</div>
</body>
</html>