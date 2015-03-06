<html>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">

<title>CPSC 304 Project</title>
<!--
    A simple stylesheet is provided so you can modify colours, fonts, etc.
-->
    <link href="manager.css" rel="stylesheet" type="text/css">

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
  <p3>View:</p3>
 </div>
 <div class="header-cont">
 <div class="header">
  <p>DATABASE MANAGEMENT SYSTEM</p>
  <p3>View:</p3><p2><li><a href="manager.php"><now>Manager</now></a></li>
  <li><a href="clerk.php">Clerk</a></li>
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
         echo "<b>Successfully deleted ".$deleteTitleID."</b>";
       }
            
      } elseif (isset($_POST["submit"]) && $_POST["submit"] ==  "ADD") {       

        $upc = $_POST["new_upc"];
        $title = $_POST["new_title"];
        $type = $_POST["new_type"];
        $category = $_POST["new_category"];
        $company = $_POST["new_company"];
        $year = $_POST["new_year"];
        $price = $_POST["new_price"];
        $stock = $_POST["new_stock"];


        $stmt = $connection->prepare("SELECT upc FROM purchaseItem Where upc = ?");

        $stmt->bind_param("s", $upc);

        $stmt->execute();

        $stmt->bind_result($upc_result);

        $stmt->fetch();

        if ($upc_result == NULL) {

        $stmt->close();
          
        $stmt = $connection->prepare("INSERT INTO item (upc, title, itype, category, company, iyear, price, stock) VALUES (?,?,?,?,?,?,?,?)");

        $stmt->bind_param("sssssiss", $upc, $title, $type, $category, $company, $year, $price, $stock);

        $stmt->execute();
          
        if($stmt->error) {       
          printf("<b>Error: %s.</b>\n", $stmt->error);
        } else {
          echo "<b>Successfully added ".$title."</b>";
        }

      } else {

        $stmt->close();

        if ($price == NULL) {

          $stmt = $connection->prepare("UPDATE item SET item.stock = item.stock + ? WHERE item.upc = ?");

          $stmt->bind_param("ss", $stock, $upc);

          $stmt->execute();

          if($stmt->error) {       
            printf("<b>Error: %s.</b>\n", $stmt->error);
        } else {
            echo "<b>Successfully added (updated) ".$title."</b>";
        }

        } else {
          
          $stmt = $connection->prepare("UPDATE item SET item.stock = item.stock + ?, item.price = ? WHERE item.upc = ?");

          $stmt->bind_param("sss", $stock, $price, $upc);

          $stmt->execute();

          if($stmt->error) {       
            printf("<b>Error: %s.</b>\n", $stmt->error);
        } else {
            echo "<b>Successfully added (updated) ".$title."</b>";
        }
      }
      }

      } elseif (isset($_POST["submit3"]) && $_POST["submit3"] ==  "UPDATE") {       

        $receiptId = $_POST["new_receiptId"];
        $expectedDate = $_POST["new_expectedDate"];

        $stmt = $connection->prepare("SELECT receiptId FROM Corder Where receiptId = ?");

        $stmt->bind_param("s", $receiptId);

        $stmt->execute();

        $stmt->bind_result($receiptId_result);

        $stmt->fetch();

        if ($receiptId_result == NULL) {
          echo "<b>No Matching Item Found! ".$title."</b>";
        }
        else {

        $stmt->close();
          
        $stmt = $connection->prepare("UPDATE Corder SET expectedDate = ? WHERE receiptId = ?");
          
        $stmt->bind_param("ss", $expectedDate, $receiptId);

        $stmt->execute();
          
        if($stmt->error) {       
          printf("<b>Error: %s.</b>\n", $stmt->error);
        } else {
          echo "<b>Successfully added</b>";
        }
      }
      }
   }
?>

<h2>Items in stock ordered by UPC</h2>
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

    if (!$result = $connection->query("SELECT upc, title, itype, category, company, iyear, price, stock FROM item ORDER BY upc")) {
        die('There was an error running the query [' . $db->error . ']');
    }

    echo "<form id=\"delete\" name=\"delete\" action=\"";
    echo htmlspecialchars($_SERVER["PHP_SELF"]);
    echo "\" method=\"POST\">";

    echo "<input type=\"hidden\" name=\"title_id\" value=\"-1\"/>";
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
?>

</table>

<h2>Add a new item to stock</h2>

<form id="add" name="add" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table>
        <tr><td>UPC</td><th><input type="text" size=30 name="new_upc"</th></tr>
        <tr><td>Title</td><th><input type="text" size=30 name="new_title"</th></tr>
        <tr><td>Type</td><th> <input type="text" size=5 name="new_type"></th></tr>
        <tr><td>Category</td><th><input type="text" size=10 name="new_category"</th></tr>
        <tr><td>Company</td><th><input type="text" size=30 name="new_company"</th></tr>
        <tr><td>Year</td><th><input type="text" size=5 name="new_year"</th></tr>
        <tr><td>Price</td><th><input type="text" size=10 name="new_price"</th></tr>
        <tr><td>Stock</td><th><input type="text" size=5 name="new_stock"</th></tr>
        <tr><td></td><th><input type="submit" name="submit" border=0 value="ADD"></th></tr>
    </table>
</form>






<h2>Expected Delivery Dates</h2>
<!-- Set up a table to view the book titles -->
<table>
<!-- Create the table column headings -->

<tr valign=center>
<td class=rowheader>receiptId</td>
<td class=rowheader>Date</td>
<td class=rowheader>cid</td>
<td class=rowheader>Card #</td>
<td class=rowheader>Expiry Date</td>
<td class=rowheader>Expected Date</td>
<td class=rowheader>Delivered Date</td>
</tr>

<?php

     if (!$result = $connection->query("SELECT receiptId, odate, cid, cardNum, expiryDate, expectedDate, deliveredDate FROM Corder ORDER BY receiptId")) {
        die('There was an error running the query [' . $db->error . ']');
    }

    while($row = $result->fetch_assoc()){
        
       echo "<th>".$row['receiptId']."</th>";
       echo "<th>".$row['odate']."</th>";
       echo "<th>".$row['cid']."</th>";
       echo "<th>".$row['cardNum']."</th>";
       echo "<th>".$row['expiryDate']."</th>";
       echo "<th>".$row['expectedDate']."</th>";
       echo "<th>".$row['deliveredDate']."</th>";

       echo "</td></tr>";
        
    }
    echo "</form>";

?>

</table>

<h2>Set Expected Delivery Date</h2>

<form id="add" name="add" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table>
        <tr><td>Receipt ID</td><th><input type="text" size=30 name="new_receiptId"</th></tr>
        <tr><td>Expected Date</td><th><input type="text" size=30 name="new_expectedDate"</th></tr>
        <tr><td></td><th><input type="submit" name="submit3" border=0 value="UPDATE"></th></tr>
    </table>
</form>




<h2>Daily Report</h2>
<!-- Set up a table to view the book titles -->
<table>
<!-- Create the table column headings -->

<tr valign=center>
<td class=rowheader>UPC</td>
<td class=rowheader>Category</td>
<td class=rowheader>Unit Price</td>
<td class=rowheader>Units</td>
<td class=rowheader>Total Value</td>
</tr>

<?php

     if ($_SERVER["REQUEST_METHOD"] == "POST") {

      if (isset($_POST["submit4"]) && $_POST["submit4"] == "DailyReport") {

        $stmt = $connection->prepare("UPDATE purchaseItem INNER JOIN item ON item.upc = purchaseItem.upc SET purchaseItem.totalValue = item.price * purchaseItem.quantity");
          
        //$stmt->bind_param();

        $stmt->execute();

        $stmt->close();
   
        if (!$result = $connection->query("SELECT * FROM item inner join purchaseItem on purchaseItem.upc = item.upc INNER JOIN Corder on Corder.receiptId = purchaseItem.receiptId WHERE item.category = 'Rock' AND Corder.Odate = date(now()) order by item.upc")) {
          die('There was an error running the query [' . $db->error . ']');
        }

        while($row = $result->fetch_assoc()){

        echo "<th>".$row['upc']."</th>";
        echo "<th>".$row['category']."</th>";
        echo "<th>".$row['price']."</th>";
        echo "<th>".$row['quantity']."</th>";
        echo "<th>".$row['totalValue']."</th>";
       
        echo "</td></tr>";
        }
        echo "</form>";

        if (!$result = $connection->query("SELECT SUM(totalValue) FROM purchaseItem INNER JOIN item ON item.upc = purchaseItem.upc INNER JOIN Corder on Corder.receiptId = purchaseItem.receiptId WHERE category = 'Rock' AND Corder.Odate = date(now())")) {
          die('There was an error running the query [' . $db->error . ']');
        }

        while($row = $result->fetch_assoc()){
          if ($row['SUM(totalValue)'] != NULL) {

            echo "<td>".$row[0]."</td>";
            echo "<td>".$row[1]."</td>";
            echo "<td>TOTAL</td>";
            echo "<th>".$row[3]."</th>";
            echo "<th>".$row['SUM(totalValue)']."</th>";

            echo "</td></tr>";

          }
          echo "</form>";
          }



        if (!$result = $connection->query("SELECT * FROM item inner join purchaseItem on purchaseItem.upc = item.upc INNER JOIN Corder on Corder.receiptId = purchaseItem.receiptId WHERE item.category = 'Pop' AND Corder.Odate = date(now()) order by item.upc")) {
         die('There was an error running the query [' . $db->error . ']');
        }

        while($row = $result->fetch_assoc()){

        echo "<th>".$row['upc']."</th>";
        echo "<th>".$row['category']."</th>";
        echo "<th>".$row['price']."</th>";
        echo "<th>".$row['quantity']."</th>";
        echo "<th>".$row['totalValue']."</th>";

        echo "</td></tr>";
        }
        echo "</form>";

        if (!$result = $connection->query("SELECT SUM(totalValue) FROM purchaseItem INNER JOIN item ON item.upc = purchaseItem.upc INNER JOIN Corder on Corder.receiptId = purchaseItem.receiptId WHERE category = 'Pop' AND Corder.Odate = date(now())")) {
          die('There was an error running the query [' . $db->error . ']');
        }


        while($row = $result->fetch_assoc()){
          if ($row['SUM(totalValue)'] != NULL) {

            echo "<td>".$row[0]."</td>";
            echo "<td>".$row[1]."</td>";
            echo "<td>TOTAL</td>";
            echo "<th>".$row[3]."</th>";
            echo "<th>".$row['SUM(totalValue)']."</th>";

            echo "</td></tr>";
          }
          echo "</form>";
        }



        if (!$result = $connection->query("SELECT * FROM item inner join purchaseItem on purchaseItem.upc = item.upc INNER JOIN Corder on Corder.receiptId = purchaseItem.receiptId WHERE item.category = 'Rap' AND Corder.Odate = date(now()) order by item.upc")) {
          die('There was an error running the query [' . $db->error . ']');
        }

        while($row = $result->fetch_assoc()){

        echo "<th>".$row['upc']."</th>";
        echo "<th>".$row['category']."</th>";
        echo "<th>".$row['price']."</th>";
        echo "<th>".$row['quantity']."</th>";
        echo "<th>".$row['totalValue']."</th>";
       
        echo "</td></tr>";
        }
        echo "</form>";

        if (!$result = $connection->query("SELECT SUM(totalValue) FROM purchaseItem INNER JOIN item ON item.upc = purchaseItem.upc INNER JOIN Corder on Corder.receiptId = purchaseItem.receiptId WHERE category = 'Rap' AND Corder.Odate = date(now())")) {
          die('There was an error running the query [' . $db->error . ']');
        }

        while($row = $result->fetch_assoc()){
          if ($row['SUM(totalValue)'] != NULL) {

            echo "<td>".$row[0]."</td>";
            echo "<td>".$row[1]."</td>";
            echo "<td>TOTAL</td>";
            echo "<th>".$row[3]."</th>";
            echo "<th>".$row['SUM(totalValue)']."</th>";

            echo "</td></tr>";

          }
          echo "</form>";
          }



        if (!$result = $connection->query("SELECT * FROM item inner join purchaseItem on purchaseItem.upc = item.upc INNER JOIN Corder on Corder.receiptId = purchaseItem.receiptId WHERE item.category = 'Country' AND Corder.Odate = date(now()) order by item.upc")) {
          die('There was an error running the query [' . $db->error . ']');
        }

        while($row = $result->fetch_assoc()){

        echo "<th>".$row['upc']."</th>";
        echo "<th>".$row['category']."</th>";
        echo "<th>".$row['price']."</th>";
        echo "<th>".$row['quantity']."</th>";
        echo "<th>".$row['totalValue']."</th>";
       
        echo "</td></tr>";
        }
        echo "</form>";

        if (!$result = $connection->query("SELECT SUM(totalValue) FROM purchaseItem INNER JOIN item ON item.upc = purchaseItem.upc INNER JOIN Corder on Corder.receiptId = purchaseItem.receiptId WHERE category = 'Country' AND Corder.Odate = date(now())")) {
          die('There was an error running the query [' . $db->error . ']');
        }

        while($row = $result->fetch_assoc()){
          if ($row['SUM(totalValue)'] != NULL) {

            echo "<td>".$row[0]."</td>";
            echo "<td>".$row[1]."</td>";
            echo "<td>TOTAL</td>";
            echo "<th>".$row[3]."</th>";
            echo "<th>".$row['SUM(totalValue)']."</th>";

            echo "</td></tr>";

          }
          echo "</form>";
          }



        if (!$result = $connection->query("SELECT * FROM item inner join purchaseItem on purchaseItem.upc = item.upc INNER JOIN Corder on Corder.receiptId = purchaseItem.receiptId WHERE item.category = 'Classical' AND Corder.Odate = date(now()) order by item.upc")) {
          die('There was an error running the query [' . $db->error . ']');
        }

        while($row = $result->fetch_assoc()){

        echo "<th>".$row['upc']."</th>";
        echo "<th>".$row['category']."</th>";
        echo "<th>".$row['price']."</th>";
        echo "<th>".$row['quantity']."</th>";
        echo "<th>".$row['totalValue']."</th>";
       
        echo "</td></tr>";
        }
        echo "</form>";

        if (!$result = $connection->query("SELECT SUM(totalValue) FROM purchaseItem INNER JOIN item ON item.upc = purchaseItem.upc INNER JOIN Corder on Corder.receiptId = purchaseItem.receiptId WHERE category = 'Classical' AND Corder.Odate = date(now())")) {
          die('There was an error running the query [' . $db->error . ']');
        }

        while($row = $result->fetch_assoc()){
          if ($row['SUM(totalValue)'] != NULL) {

            echo "<td>".$row[0]."</td>";
            echo "<td>".$row[1]."</td>";
            echo "<td>TOTAL</td>";
            echo "<th>".$row[3]."</th>";
            echo "<th>".$row['SUM(totalValue)']."</th>";

            echo "</td></tr>";

          }
          echo "</form>";
          }



        if (!$result = $connection->query("SELECT * FROM item inner join purchaseItem on purchaseItem.upc = item.upc INNER JOIN Corder on Corder.receiptId = purchaseItem.receiptId WHERE item.category = 'New Age' AND Corder.Odate = date(now()) order by item.upc")) {
          die('There was an error running the query [' . $db->error . ']');
        }

        while($row = $result->fetch_assoc()){

        echo "<td>".$row['upc']."</td>";
        echo "<td>".$row['category']."</td>";
        echo "<td>".$row['price']."</td>";
        echo "<td>".$row['quantity']."</td>";
        echo "<td>".$row['totalValue']."</td>";
       
        echo "</td></tr>";
        }
        echo "</form>";

        if (!$result = $connection->query("SELECT SUM(totalValue) FROM purchaseItem INNER JOIN item ON item.upc = purchaseItem.upc INNER JOIN Corder on Corder.receiptId = purchaseItem.receiptId WHERE category = 'New Age' AND Corder.Odate = date(now())")) {
          die('There was an error running the query [' . $db->error . ']');
        }

        while($row = $result->fetch_assoc()){
          if ($row['SUM(totalValue)'] != NULL) {

            echo "<td>".$row[0]."</td>";
            echo "<td>".$row[1]."</td>";
            echo "<td>TOTAL</td>";
            echo "<th>".$row[3]."</th>";
            echo "<th>".$row['SUM(totalValue)']."</th>";

            echo "</td></tr>";

          }
          echo "</form>";
          }



        if (!$result = $connection->query("SELECT * FROM item inner join purchaseItem on purchaseItem.upc = item.upc INNER JOIN Corder on Corder.receiptId = purchaseItem.receiptId WHERE item.category = 'Instrumental' AND Corder.Odate = date(now()) order by item.upc")) {
          die('There was an error running the query [' . $db->error . ']');
        }

        while($row = $result->fetch_assoc()){

        echo "<th>".$row['upc']."</th>";
        echo "<th>".$row['category']."</th>";
        echo "<th>".$row['price']."</th>";
        echo "<th>".$row['quantity']."</th>";
        echo "<th>".$row['totalValue']."</th>";
       
        echo "</td></tr>";
        }
        echo "</form>";

        if (!$result = $connection->query("SELECT SUM(totalValue) FROM purchaseItem INNER JOIN item ON item.upc = purchaseItem.upc INNER JOIN Corder on Corder.receiptId = purchaseItem.receiptId WHERE category = 'Instrumental' AND Corder.Odate = date(now())")) {
          die('There was an error running the query [' . $db->error . ']');
        }

        while($row = $result->fetch_assoc()){
          if ($row['SUM(totalValue)'] != NULL) {

            echo "<td>".$row[0]."</td>";
            echo "<td>TOTAL</td>";
            echo "<th>".$row[2]."</th>";
            echo "<th>".$row[3]."</th>";
            echo "<th>".$row['SUM(totalValue)']."</th>";

            echo "</td></tr>";

          }
          echo "</form>";
          }



        if (!$result = $connection->query("SELECT SUM(totalValue) FROM purchaseItem INNER JOIN item ON item.upc = purchaseItem.upc INNER JOIN Corder on Corder.receiptId = purchaseItem.receiptId WHERE Corder.Odate = date(now())")) {
          die('There was an error running the query [' . $db->error . ']');
        }

        while($row = $result->fetch_assoc()){
          if ($row['SUM(totalValue)'] != NULL) {

            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";

            echo "</td></tr>";

          }
          echo "</form>";
          }



        if (!$result = $connection->query("SELECT SUM(totalValue) FROM purchaseItem INNER JOIN item ON item.upc = purchaseItem.upc INNER JOIN Corder on Corder.receiptId = purchaseItem.receiptId WHERE Corder.Odate = date(now())")) {
          die('There was an error running the query [' . $db->error . ']');
        }

        while($row = $result->fetch_assoc()){
          if ($row['SUM(totalValue)'] != NULL) {

            echo "<td>".$row[0]."</td>";
            echo "<td>TOTAL</td>";
            echo "<th>".$row[2]."</th>";
            echo "<th>".$row[3]."</th>";
            echo "<th>".$row['SUM(totalValue)']."</th>";

            echo "</td></tr>";

          }
          echo "</form>";
          }
        }
      }



    // Close the connection to the database once we're done with it.
    //mysqli_close($connection);

 ?>

</table>


<form id="add" name="add" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table>
        <tr><td></td><th><input type="submit" name="submit4" border=0 value="DailyReport"></th><th></th></tr>
    </table>
</form>



<h2>Your Search Result</h2>
<!-- Set up a table to view the book titles -->
<table>
<!-- Create the table column headings -->

<tr valign=center>
<td class=rowheader>Title</td>
<td class=rowheader>Company</td>
<td class=rowheader>Current Stock</td>
<td class=rowheader>Number of Copies Sold</td>
</tr>






<?php

     if ($_SERVER["REQUEST_METHOD"] == "POST") {

      if (isset($_POST["submit5"]) && $_POST["submit5"] == "Search") {

        $date = $_POST["new_date"];
        $numItem = $_POST["new_numItem"];

        $stmt = $connection->prepare("SELECT * FROM item INNER JOIN purchaseItem on purchaseItem.upc = item.upc INNER JOIN Corder on Corder.receiptId = purchaseItem.receiptId WHERE Corder.Odate = ? ORDER BY purchaseItem.quantity DESC LIMIT ?");
          
        $stmt->bind_param("ss", $date, $numItem);

        $stmt->execute();

        $result = $stmt->get_result();

        while($row = $result->fetch_assoc()){

        echo "<th>".$row['title']."</th>";
        echo "<th>".$row['company']."</th>";
        echo "<th>".$row['stock']."</th>";
        echo "<th>".$row['quantity']."</th>";
       
        echo "</td></tr>";
        }
        echo "</form>";
        }


        mysqli_close($connection);
    }



?>
</table>

<h2>Search For Top Selling Items</h2>
<form id="add" name="add" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table>
	
      <tr><td>Date</td><th><input type="text" size=30 name="new_date"</th></tr>
      <tr><td>Number of Items</td><th> <input type="text" size=5 name="new_numItem"></th></tr>
      <tr><td></td><th><input type="submit" name="submit5" border=0 value="Search"></th></tr>
    </table>
</form>






</div>

</body>
</html>


