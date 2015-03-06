<html>
  <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
  <meta content="utf-8" http-equiv="encoding">

  <title>CPSC 304 Project</title>
<!--
    A simple stylesheet is provided so you can modify colours, fonts, etc.
  -->
  <link href="customer_login.css" rel="stylesheet" type="text/css">

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
  <p>THE "WE SELL THINGS!!" STORE</p>
  <p3>View:</p3><p2><li><a href="manager.php">Manager</a></li>
  <li><a href="clerk.php">Clerk</a></li>
  <li><a href="customer_login.php"><now>Customer Login</now></a></li>
  <li><a href="customer.php">Customer</a></li>
  <li><a href="customer_purchase.php">Customer Purchase</a></li></p2>
 </div>
 </div>
 
 
 <body>
 
 <div class="content">
 <div2>
  <?php
  $connection = new mysqli("localhost", "root", "b1j8", "practice");

  if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["submit"]) && $_POST["submit"] == "LOG IN") {

          $cid = $_POST["new_cid"];
          $Cpassword = $_POST["new_Cpassword"];

          $stmt = $connection->prepare("SELECT cid FROM customer WHERE cid = ? AND Cpassword = ?");

          $stmt->bind_param("ss", $cid, $Cpassword);

          $stmt->execute();

          $stmt->bind_result($login_result);

          $stmt->fetch();

          if ($login_result == NULL) {
		    echo("<h2>Dialogue Box: </h2>");
            echo "<b>Invalid ID or Password ".$deleteTitleID."</b>";
          }
          else {

            header("Location: http://localhost/customer.php");

          }

       } elseif (isset($_POST["submit2"]) && $_POST["submit2"] ==  "Register!") {

        $cid = $_POST["new_cid"];
        $Cpassword = $_POST["new_Cpassword"];
        $Cname = $_POST["new_Cname"];
        $address = $_POST["new_address"];
        $phone = $_POST["new_phone"];

        $stmt = $connection->prepare("SELECT cid FROM customer Where cid = ?");

        $stmt->bind_param("s", $cid);

        $stmt->execute();

        $stmt->bind_result($register_result);

        $stmt->fetch();

        if ($register_result != NULL) {
		      //echo("<h2>Dialogue Box: </h2>");
          echo("<b>Provided ID Already Exists! Try Another ID!</b>");
        }
        else {

          $stmt->close();

          $stmt = $connection->prepare("INSERT INTO customer (cid, Cpassword, Cname, address, phone) VALUES (?,?,?,?,?)");
          
          $stmt->bind_param("sssss", $cid, $Cpassword, $Cname, $address, $phone);

          $stmt->execute();

          if($stmt->error) {   
			      //echo("<h2>Dialogue Box: </h2>");
            //printf("<b>Error: %s.</b>\n", $stmt->error);
            echo "<b>Register Failed!</b>";
          } else {
		        //echo("<h2>Dialogue Box: </h2>");
            echo "<b>Successfully registered!</b>";
          }
        }
      } 
    }
    ?>



  <h2>Log In!</h2>

  <form id="add" name="add" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table>
      <tr><td>ID</td><th><input type="text" size=30 name="new_cid"</th></tr>
      <tr><td>Password</td><th><input type="text" size=30 name="new_Cpassword"</th></tr>
      <tr><td></td><th><input type="submit" name="submit" border=0 value="LOG IN"></th></tr>
    </table>
  </form>
</div2>

 <h2>Register for Online Shopping!</h2>
  <form id="add" name="add" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table>
      <tr><td>ID</td><th><input type="text" size=30 name="new_cid"</th></tr>
      <tr><td>Password</td><th><input type="text" size=30 name="new_Cpassword"</th></tr>
      <tr><td>Name</td><th><input type="text" size=5 name="new_Cname"</th></tr>
      <tr><td>Address</td><th><input type="text" size=10 name="new_address"</th></tr>
      <tr><td>Phone Number</td><th><input type="text" size=30 name="new_phone"</th></tr>
      <tr><td></td><th><input type="submit" name="submit2" border=0 value="Register!"></th></tr>
    </table>
  </form>

</div>

</body>
 
 </html>