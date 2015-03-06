<html>
  <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
  <meta content="utf-8" http-equiv="encoding">

  <title>CPSC 304 DBMS </title>
<!--
    A simple stylesheet is provided so you can modify colours, fonts, etc.
  -->
  <link href="clerk.css" rel="stylesheet" type="text/css">

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
<div class="header">
  <p>DATABASE MANAGEMENT SYSTEM</p>
 </div>
 <div class="header-cont">
 <div class="header">
  <p>DATABASE MANAGEMENT SYSTEM</p>
  <p3>View:</p3><p2><li><a href="manager.php">Manager</a></li>
  <li><a href="clerk.php"><now>Clerk</now></a></li>
  <li><a href="customer_login.php">Customer Login</a></li>
  <li><a href="customer.php">Customer</a></li>
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

    if (isset($_POST["submitDelete"]) && $_POST["submitDelete"] == "DELETE") {

          $stmt = $connection->prepare("DELETE FROM item WHERE upc=?");

          $deleteTitleID = $_POST['title_id'];

          $stmt->bind_param("s", $deleteTitleID);

          $stmt->execute();
          
          if($stmt->error) {
           printf("<b>Error: %s.</b>\n", $stmt->error);
         } else {
		  echo("<h2>Dialogue Box: </h2>");
           echo "<b>Successfully deleted ".$deleteTitleID."</b>";
         }

       } elseif (isset($_POST["submit"]) && $_POST["submit"] ==  "RETURN") {

          $receiptId = $_POST["new_receiptId"];
          $upc = $_POST["new_upc"];
          $quantity = $_POST["new_quantity"];

          $retid = rand(10000000, 99999999);
          //printf($retid);


/*
          while ($break == "true") {
          $retid = rand(10000000, 99999999);
          $stmt = $connection->prepare("SELECT * FROM returnItem INNER JOIN iReturn on iReturn.retid = returnItem.retid WHERE iReturn.retid = ?");
          $stmt->bind_param("s", $retid);
          $stmt->execute();
          $stmt->bind_result($result);
          $stmt->fetch();
          if ($result == NULL) {
          $break = "true";
          }
        }*/


          $stmt = $connection->prepare("SELECT * FROM purchaseItem inner join Corder on Corder.receiptId = purchaseItem.receiptId WHERE purchaseItem.receiptId = ? AND purchaseItem.upc = ?");

          $stmt->bind_param("ss", $receiptId, $upc);

          $stmt->execute();

          $result = $stmt->get_result();

          $row = $result->fetch_assoc();

          $quantity_purchase = $row['quantity'];

          if ($quantity_purchase < $quantity) {
            $quantity = $quantity_purchase;
          } 

        $stmt = $connection->prepare("SELECT upc FROM purchaseItem Where upc = ? AND receiptId = ?");

        $stmt->bind_param("ss", $upc, $receiptId);

        $stmt->execute();

        $stmt->bind_result($upc_result);

        $stmt->fetch();

        if ($upc_result == NULL) {
		 echo("<h2>Dialogue Box: </h2>");
          echo("<b>No matched result found!</b>");
        } else {


          $stmt->close();
          
          $stmt = $connection->prepare("INSERT INTO iReturn (retid, Rdate, receiptId) VALUES (?,date(now()),?)");

          $stmt->bind_param("ss", $retid, $receiptId);

          $stmt->execute();
          
           if($stmt->error) {       
             printf("<b>Error: %s.</b>\n", $stmt->error);
          } else {
		        //echo("<h2>Dialogue Box: </h2>");
            //echo "<b>Successfully added to iReturn</b>";
           }

          $stmt->close();

          $stmt = $connection->prepare("INSERT INTO returnItem (retid, upc, quantity) VALUES (?,?,?)");

          $stmt->bind_param("sss", $retid, $upc, $quantity);

          $stmt->execute();
          
           if($stmt->error) {       
             printf("<b>Error: %s.</b>\n", $stmt->error);
          } else {
		        //echo("<h2>Dialogue Box: </h2>");
            echo "<b>Successfully added to Returned Item</b>";
          }

          $stmt->close();


          $stmt = $connection->prepare("UPDATE purchaseItem SET purchaseItem.quantity = purchaseItem.quantity - ? WHERE purchaseItem.receiptId = ?");

          $stmt->bind_param("ss", $quantity, $receiptId);

          $stmt->execute();

          $stmt->close();

          $stmt = $connection->prepare("DELETE FROM purchaseItem WHERE quantity = 0");

          $stmt->execute();
          
        }

      }
    }
    ?>
	

    <h2>Items Awaiting Return</h2>

    <!-- Set up a table to view the book titles -->
    <table>
      <!-- Create the table column headings -->


      <tr valign=center>
        <td class=rowheader>Receipt ID</td>
        <td class=rowheader>UPC</td>
        <td class=rowheader>Quantity</td>
        <td class=rowheader>OrderDate</td>
        <td class=rowheader>CustomerID</td>
        <td class=rowheader>CardNumber</td>
        <td class=rowheader>ExpiryDate</td>
        <td class=rowheader>ExpectedDate</td>
        <td class=rowheader>DeliveredDate</td>
      </tr>

      <?php

     if (!$result = $connection->query("SELECT * FROM purchaseItem inner join Corder on Corder.receiptId = purchaseItem.receiptId WHERE date(Odate) >= date(subdate(now(), INTERVAL 15 DAY)) AND date(Odate) <= date(now()) order by upc")) {
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
       echo "<th>".$row['upc']."</th>";
       echo "<th>".$row['quantity']."</th>";
       echo "<th>".$row['Odate']."</th>";
       echo "<th>".$row['cid']."</th>";
       echo "<th>".$row['cardNum']."</th>";
       echo "<th>".$row['expiryDate']."</th>";
       echo "<th>".$row['expectedDate']."</th>";
       echo "<th>".$row['deliveredDate']."</th>";
       
       //Display an option to delete this title using the Javascript function and the hidden title_id
       //echo "<a href=\"javascript:formSubmit('".$row['upc']."');\">DELETE</a>";
       echo "</td></tr>";

     }
     echo "</form>";

    // Close the connection to the database once we're done with it.
    //mysqli_close($connection);

     ?>

   </table>

<div2>
  <h2>Returned Items</h2>
  <!-- Set up a table to view the book titles -->
  <table>
    <!-- Create the table column headings -->

    <tr valign=center>
      <td class=rowheader>ReturnID</td>
      <td class=rowheader>UPC</td>
      <td class=rowheader>ReturnDate</td>
      <td class=rowheader>ReceiptID</td>
      <td class=rowheader>Quantity</td>
    </tr>


    <?php

     if (!$result = $connection->query("SELECT * FROM iReturn inner join returnItem on returnItem.retid = iReturn.retid")) {
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

       echo "<th>".$row['retid']."</th>";
       echo "<th>".$row['upc']."</th>";
       echo "<th>".$row['Rdate']."</th>";
       echo "<th>".$row['receiptId']."</th>";
       echo "<th>".$row['quantity']."</th>";
       
       //Display an option to delete this title using the Javascript function and the hidden title_id
       //echo "<a href=\"javascript:formSubmit('".$row['upc']."');\">DELETE</a>";
       echo "</tr>";

     }
     echo "</form>";

    // Close the connection to the database once we're done with it.
     mysqli_close($connection);
     ?>

   </table>
</div2>
<div3>
  <h2>Return An Item</h2>
  <form id="add" name="add" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table>
      <tr><td>UPC</td><th><input type="text" size=30 name="new_upc"</th></tr>
      <tr><td>ReceiptID</td><th><input type="text" size=10 name="new_receiptId"</th></tr>
      <tr><td>Quantity</td><th><input type="text" size=30 name="new_quantity"</th></tr>
      <tr><td></td><th><input type="submit" name="submit" border=0 value="RETURN"></th></tr>
    </table>
  </form>
 </div3>
 </div>
 
 </body>
 </html>

