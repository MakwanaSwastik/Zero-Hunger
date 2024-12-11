<?php
session_start();
include('includes/dbconnection.php');

if (strlen($_SESSION['pgasoid']) == 0) {
    header('location:logout.php');
} else {
    $errorMessage = ""; // Variable to store error message

    if (isset($_POST['submit'])) {
        // Collect form data and sanitize input
        $donorid = $_SESSION['pgasoid'];
        $statename = mysqli_real_escape_string($con, $_POST['state']);
        $cityname = mysqli_real_escape_string($con, $_POST['city']);
        $description = mysqli_real_escape_string($con, $_POST['description']);
        $pdate = $_POST['pdate'];
        $padd = mysqli_real_escape_string($con, $_POST['padd']);
        $contactperson = mysqli_real_escape_string($con, $_POST['contactperson']);
        $cpmobnum = $_POST['cpmobnum'];
        $fid = mt_rand(100000000, 999999999);

        // Handle file upload
        $pic = $_FILES["images"]["name"];
        $picTemp = $_FILES["images"]["tmp_name"];
        $extension = strtolower(pathinfo($pic, PATHINFO_EXTENSION));

        // Allowed file extensions for images
        $allowed_extensions = array("jpg", "jpeg", "png", "gif");

        // Validate image file format
        if (!in_array($extension, $allowed_extensions)) {
            $errorMessage = "Invalid format. Only jpg, jpeg, png, or gif formats are allowed.";
        } elseif ($_FILES["images"]["size"] > 5000000) {  // File size limit: 5MB
            $errorMessage = "File size must be less than 5MB.";
        } elseif (!preg_match('/^[6-9][0-9]{9}$/', $cpmobnum)) {
            $errorMessage = "Mobile number must start with 6, 7, 8, or 9 and be exactly 10 digits long.";
        } elseif (empty($statename) || empty($cityname) || empty($description) || empty($pdate) || empty($padd) || empty($contactperson)) {
            $errorMessage = "Please fill in all the required fields.";
        } else {
            // Generate a unique image name
            $foodpic = md5($pic . time()) . "." . $extension;

            // Move uploaded file to the destination folder
            if (move_uploaded_file($picTemp, "images/" . $foodpic)) {
                $fitem = $_POST["fitem"];
                $fitemarray = implode(",", $fitem);

                // Insert the data into the database
                $query = mysqli_query($con, "INSERT INTO tblfood(DonorID, foodId, StateName, CityName, FoodItems, Description, PickupDate, PickupAddress, ContactPerson, CPMobNumber, Image) 
                        VALUES ('$donorid', '$fid', '$statename', '$cityname', '$fitemarray', '$description', '$pdate', '$padd', '$contactperson', '$cpmobnum', '$foodpic')");

                if ($query) {
                    // Show success alert and redirect
                    echo "<script type='text/javascript'> 
                            alert('Food Detail added successfully.');
                            document.location = 'add-food-details.php'; 
                          </script>";
                } else {
                    // Show error alert if the query fails
                    echo "<script type='text/javascript'> 
                            alert('Something went wrong. Please try again.');
                          </script>";
                }
            } else {
                // Show error alert for image upload failure
                echo "<script type='text/javascript'> 
                        alert('Error uploading the image. Please try again.');
                      </script>";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZeroHunger | Add Food Detail</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <link href="css/style-responsive.css" rel="stylesheet">
    <link href="css/font-awesome.css" rel="stylesheet">
    <script src="js/jquery2.0.3.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const today = new Date().toISOString().split("T")[0];
            document.getElementById("pdate").setAttribute("min", today);
        });

        function getCity(val) {
            $.ajax({
                type: "POST",
                url: "get-city.php",
                data: 'sateid=' + val,
                success: function (data) {
                    $("#city").html(data);
                }
            });
        }
    </script>
</head>
<body>
<section id="container">
    <!-- Header -->
    <?php include_once('includes/header.php'); ?>
    <!-- Sidebar -->
    <?php include_once('includes/sidebar.php'); ?>
    <!-- Main content -->
    <section id="main-content">
        <section class="wrapper">
            <div class="form-w3layouts">
                <div class="row">
                    <div class="col-lg-12">
                        <section class="panel">
                            <header class="panel-heading">
                                List Your Food Details
                            </header>
                            <div class="panel-body">
                                <form class="form-horizontal bucket-form" method="post" enctype="multipart/form-data">
                                    <?php if (!empty($errorMessage)) { ?>
                                        <div class="alert alert-danger">
                                            <strong>Error: </strong><?php echo $errorMessage; ?>
                                        </div>
                                    <?php } ?>
                                    
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Food Item</label>
                                        <div class="col-sm-6">
                                            <table class="table table-bordered" id="dynamic_field">
                                                <tr>
                                                    <td><input type="text" name="fitem[]" placeholder="Enter Food Items" class="form-control name_list" required /></td>
                                                    <td><button type="button" name="add" id="add" class="btn btn-success">Add More</button></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Description</label>
                                        <div class="col-sm-6">
                                            <textarea class="form-control" id="description" name="description" required></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Pickup Date</label>
                                        <div class="col-sm-6">
                                            <input class="form-control" id="pdate" name="pdate" type="date" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Pickup Address</label>
                                        <div class="col-sm-6">
                                            <textarea class="form-control" id="padd" name="padd" required></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Choose State</label>
                                        <div class="col-sm-6">
                                            <select class="form-control m-bot15" name="state" id="state" onChange="getCity(this.value);" required>
                                                <option value="">Choose State</option>
                                                <?php
                                                $query = mysqli_query($con, "SELECT * FROM tblstate");
                                                while ($row = mysqli_fetch_array($query)) {
                                                    echo "<option value='" . $row['StateName'] . "'>" . $row['StateName'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Choose City</label>
                                        <div class="col-sm-6">
                                            <select class="form-control m-bot15" name="city" id="city" required></select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Contact Person</label>
                                        <div class="col-sm-6">
                                            <input class="form-control" id="contactperson" name="contactperson" type="text" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Contact Person Mobile Number</label>
                                        <div class="col-sm-6">
                                            <input class="form-control" id="cpmobnum" name="cpmobnum" type="text" required pattern="^[6-9][0-9]{9}$" title="Mobile number must start with 6, 7, 8, or 9 and be exactly 10 digits long.">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Pictures</label>
                                        <div class="col-sm-6">
                                            <input class="form-control" id="images" name="images" type="file" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-offset-3 col-sm-6">
                                            <button class="btn btn-primary" type="submit" name="submit">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </section>
    </section>
    <!-- Footer -->
    <?php include_once('includes/footer.php'); ?>
</section>

<script src="js/bootstrap.js"></script>
<script src="js/jquery.dcjqaccordion.2.7.js"></script>
<script src="js/scripts.js"></script>
<script src="js/jquery.slimscroll.js"></script>
<script src="js/jquery.nicescroll.js"></script>
<script src="js/jquery.scrollTo.js"></script>
</body>
</html>
