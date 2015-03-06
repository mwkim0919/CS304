<html>
  <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
  <meta content="utf-8" http-equiv="encoding">

  <title>CPSC 304 Project</title>
<!--
    A simple stylesheet is provided so you can modify colours, fonts, etc.
  -->
  <link href="customer_purchase.css" rel="stylesheet" type="text/css">

<!--
    Javascript to submit a title_id as a POST form, used with the "delete" links
  -->
  <script>
  function formSubmit(titleId) {
    'use strict';
    if (confirm('Are you sure you want to delete this title?')) {
      // Set the value of a hidden HTML element in this form
      var form = document.getElementById('delete');
      form.title_id.value = titleId;
      // Post this form
      form.submit();
    }
  }
  </script>



<div class = "header">
  <p>THE "WE SELL THINGS!!" STORE</p>
  </div>
   <div class="header-cont">
 <div class="header">
  <p>THE "WE SELL THINGS!!" STORE</p>
  <p3>View:</p3><p2><li><a href="manager.php">Manager</a></li>
  <li><a href="clerk.php">Clerk</a></li>
  <li><a href="customer_login.php">Customer Login</a></li>
  <li><a href="customer.php">Customer</a></li>
  <li><a href="customer_purchase.php"><now>Customer Purchase</now></a></li></p2>
 </div>
 </div>
 
  
  <body>
  <div class = "content">
  <?php
  $connection = new mysqli("localhost", "root", "b1j8", "practice");

  if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["submit"]) && $_POST["submit"] == "Pay!") {
      $cardNum = $_POST["new_cardNum"];
      $expiryDate = $_POST["new_expiryDate"];
      $id = $_POST["new_ID"];
      $password = $_POST["new_password"];

      //$receiptId = rand(10000000,99999999);

      if (!$result = $connection->query("SELECT MAX(expectedDate) AS result2 FROM Corder WHERE expectedDate > date(now()) GROUP BY expectedDate HAVING COUNT(*) < 3")) {
        die('There was an error running the query [' . $db->error . ']');
      }

      $row = $result->fetch_assoc();
      $expected_date = $row['result2'];
      //printf($expected_date);

      if (!$result = $connection->query("SELECT MAX(expectedDate + 1) AS result3 FROM Corder WHERE expectedDate > date(now())")) {
        die('There was an error running the query [' . $db->error . ']');
      }

      $row = $result->fetch_assoc();
      $tom_date = $row['result3'];
      //printf($tom_date);

      if ($tom_date == NULL) {
        if (!$result = $connection->query("SELECT (date(now())+1) AS result4 FROM Corder")) {
          die('There was an error running the query [' . $db->error . ']');
        }

        $row = $result->fetch_assoc();
        $tom_date = $row['result4'];
      }
      //printf($tom_date);

      if ($expected_date == NULL) {
        $expected_date = $tom_date;
      }
      //printf($expected_date);

      $result = $connection->query("SELECT * FROM shoppingCart");
      $row = $result->fetch_assoc();

      $receiptId = rand(10000000,99999999);
      $upc = $row['upc'];
      $title = $row['title'];
      $itype = $row['itype'];
      $category = $row['category'];
      $company = $row['company'];
      $iyear = $row['iyear'];
      $price = $row['price'];
      $stock = $row['stock'];
      $deliveredDate = NULL;

      $stmt = $connection->prepare("INSERT INTO Corder (receiptId, Odate, cid, cardNum, expiryDate, expectedDate, deliveredDate) VALUES (?, date(now()), ?, ?, ?, ?, ?)");
      $stmt->bind_param("ssssss", $receiptId, $id, $cardNum, $expiry_date, $expected_date, $deliveredDate);
      $stmt->execute();

      $stmt = $connection->prepare("INSERT INTO purchaseItem (receiptId, upc, quantity) VALUES (?,?,?)");
      $stmt->bind_param("sss", $receiptId, $upc, $stock);
      $stmt->execute();

      while($row = $result->fetch_assoc()){
        $receiptId = rand(10000000,99999999);
        $upc = $row['upc'];
        $title = $row['title'];
        $itype = $row['itype'];
        $category = $row['category'];
        $company = $row['company'];
        $iyear = $row['iyear'];
        $price = $row['price'];
        $stock = $row['stock'];

        $stmt = $connection->prepare("INSERT INTO Corder (receiptId, Odate, cid, cardNum, expiryDate, expectedDate, deliveredDate) VALUES (?, date(now()), ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $receiptId, $id, $cardNum, $expiry_date, $expected_date, $deliveredDate);
        $stmt->execute();

        $stmt = $connection->prepare("INSERT INTO purchaseItem (receiptId, upc, quantity) VALUES (?,?,?)");
        $stmt->bind_param("sss", $receiptId, $upc, $stock);
        $stmt->execute();

        //printf("sucess");
      }


      printf("<b>If you order now, your expected delivery date would be $expected_date!</b>");

    } else if (isset($_POST["submitDelete"]) && $_POST["submitDelete"] == "DELETE") {
      $stmt = $connection->prepare("DELETE FROM shoppingCart WHERE upc = ?");
      $deleteTitleID = $_POST['title_id'];
      $stmt->bind_param("s", $deleteTitleID);
      $stmt->execute();
          
      if($stmt->error) {
        printf("<b>Error: %s.</b>\n", $stmt->error);
      } else {
        echo "<b>Successfully deleted ".$deleteTitleID."</b>";
      }
    }
  }
  ?>

<h2>Your Shopping Cart</h2>
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

  while($row = $result->fetch_assoc()) {

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

  if (!$result = $connection->query("SELECT * FROM shoppingCart")) {
    die('There was an error running the query [' . $db->error . ']');
  }
 
 
           echo "<td>".$row[0]."</td>";
            echo "<td>".$row[1]."</td>";
            echo "<td>".$row[2]."</td>";
            echo "<td>".$row[3]."</td>";
            echo "<td>".$row[4]."</td>";
            echo "<td>".$row[5]."</td>";
            echo "<td>".$row[6]."</td>";
			echo "<td>".$row[7]."</td>";
			echo "<td>".$row[8]."</td>";


            echo "</td></tr>";
			echo "</form>";

  if (!$result = $connection->query("SELECT SUM(price*stock) AS result FROM shoppingCart")) {
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

  $row = $result->fetch_assoc();
            echo "<td>TOTAL</td>";
/*
            echo "<th>".$row[1]."</th>";
            echo "<th>".$row[2]."</th>";
            echo "<th>".$row[3]."</th>";
            echo "<th>".$row[4]."</th>";
            echo "<th>".$row[5]."</th>";
      			echo "<th>".$row[6]."</th>";
      			echo "<th>".$row[7]."</th>";
*/
			echo "<th>$".$row['result']."</th>";
            echo "</th></tr>";

          echo "</form>";

  ?>

</table>

<h2>Check Out</h2>
<form id="add" name="add" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
  <table>
    <tr><td>Credit Card Number</td><th><input type="text" size=30 name="new_cardNum"</th></tr>
    <tr><td>Expiry Date</td><th><input type="text" size=30 name="new_expiryDate"</th></tr>
    <tr><td>ID</td><th><input type="text" size=30 name="new_ID"</th></tr>
    <tr><td>Password</td><th><input type="text" size=30 name="new_password"</th></tr>
    <tr><td></td><th><input type="submit" name="submit" border=0 value="Pay!"></th></tr>
  </table>
</form>

<h2>Items Purchased So Far</h2>
  <!-- Set up a table to view the book titles -->
  <table>

  <!-- Create the table column headings -->

  <tr valign=center>
  <td class=rowheader>ReceiptId</td>
  <td class=rowheader>Odate</td>
  <td class=rowheader>cid</td>
  <td class=rowheader>cardNum</td>
  <td class=rowheader>expiryDate</td>
  <td class=rowheader>expectedDate</td>
  <td class=rowheader>deliveredDate</td>
  <td class=rowheader>upc</td>
  <td class=rowheader>quantity</td>
  </tr>

  <?php
  if (!$result = $connection->query("SELECT * FROM Corder INNER JOIN purchaseItem WHERE purchaseItem.receiptId = Corder.receiptId")) {
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

    echo "<th>".$row['receiptId']."</th>";
    echo "<th>".$row['Odate']."</th>";
    echo "<th>".$row['cid']."</th>";
    echo "<th>".$row['cardNum']."</th>";
    echo "<th>".$row['expiryDate']."</th>";
    echo "<th>".$row['expectedDate']."</th>";
    echo "<th>".$row['deliveredDate']."</th>";
    echo "<th>".$row['upc']."</th>";
    echo "<th>".$row['quantity']."</th>";

    echo "</td></tr>";

  }
  echo "</form>";

  // Close the connection to the database once we're done with it.
  mysqli_close($connection);
  ?>

</table>


</div>
</body>
</html>