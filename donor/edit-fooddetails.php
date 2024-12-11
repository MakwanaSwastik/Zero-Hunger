<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['pgasoid']==0)) {
    header('location:logout.php');
} else {

    if(isset($_POST['submit'])) {
        $eid=$_GET['editid'];
        $donorid=$_SESSION['pgasoid'];
        $statename=$_POST['state'];
        $cityname=$_POST['city'];
        $description=$_POST['description'];
        $pdate=$_POST['pdate'];
        $padd=$_POST['address'];
        $contactperson=$_POST['contactperson'];
        $cpmobnum=$_POST['cpmobnum'];
        $fitem=$_POST["fitem"]; 
        $fitemarray = implode(",", $fitem);
        
        // Image Upload Handling
        if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
            $allowed_extensions = array('jpeg', 'jpg', 'png', 'gif');
            $file_name = $_FILES['image']['name'];
            $file_temp = $_FILES['image']['tmp_name'];
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            if (in_array($file_extension, $allowed_extensions)) {
                $target_dir = "images/";
                $new_image_name = time() . '.' . $file_extension;
                if (move_uploaded_file($file_temp, $target_dir . $new_image_name)) {
                    // Image uploaded successfully
                    $image = $new_image_name;
                } else {
                    // Error during image upload
                    echo "<script>alert('Error uploading the image. Please try again.');</script>";
                }
            } else {
                echo "<script>alert('Only jpeg, jpg, png, and gif images are allowed.');</script>";
            }
        } else {
            // No image uploaded, use existing one
            $image = $_POST['existing_image'];
        }

        // Update food details in the database
        $query = mysqli_query($con, "UPDATE tblfood SET FoodItems='$fitemarray', StateName='$statename', CityName='$cityname', Description='$description', PickupDate='$pdate', PickupAddress='$padd', ContactPerson='$contactperson', CPMobNumber='$cpmobnum', Image='$image' WHERE ID='$eid'");
        
        if ($query) {
            echo "<script>alert('Donating food detail has been updated successfully');</script>";
            echo "<script>window.location.href = 'add-food-details.php';</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<head>
    <title>ZeroHunger | Food Updation</title>
    <script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <link href="css/style-responsive.css" rel="stylesheet"/>
    <!-- Font CSS -->
    <link href='//fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="css/font.css" type="text/css"/>
    <link href="css/font-awesome.css" rel="stylesheet"> 
    <!-- jQuery -->
    <script src="js/jquery2.0.3.min.js"></script>
    <script>
        function getCity(val) { 
            $.ajax({
                type: "POST",
                url: "get-city.php",
                data: 'sateid=' + val,
                success: function(data){
                    $("#city").html(data);
                }
            });
        }
    </script>
    <script>
        $(document).ready(function(){
            var i=1;
            $('#add').click(function(){
                i++;
                $('#dynamic_field').append('<tr id="row'+i+'"><td><input type="text" name="fitem[]" placeholder="Enter Food Items" class="form-control name_list" title="Enter a food item" required /></td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>');
            });
        
            $(document).on('click', '.btn_remove', function(){
                var button_id = $(this).attr("id"); 
                $('#row'+button_id+'').remove();
            });
        });
    </script>
</head>
<body>
<section id="container">
    <!--header start-->
    <?php include_once('includes/header.php');?>
    <!--header end-->
    <!--sidebar start-->
    <?php include_once('includes/sidebar.php');?>
    <!--sidebar end-->
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper">
            <div class="form-w3layouts">
                <div class="row">
                    <div class="col-lg-12">
                        <section class="panel">
                            <header class="panel-heading">
                                Update Food Details
                            </header>
                            <div class="panel-body">
                                <?php if($msg){ ?>
                                    <div class="alert alert-info" role="alert">
                                        <strong>Message !</strong>  
                                        <?php echo $msg;}  ?>
                                    </div>

                                    <?php
                                    $cid=$_GET['editid'];
                                    $ret=mysqli_query($con,"select * from tblfood where ID='$cid'");
                                    $cnt=1;
                                    while ($row=mysqli_fetch_array($ret)) {
                                    ?>
                                    <form class="form-horizontal bucket-form" method="post" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Food Item</label>
                                            <div class="col-sm-6">
                                                <table class="table table-bordered" id="dynamic_field">
                                                    <tr>
                                                        <td><input type="text" name="fitem[]" value="<?php  echo $row['FoodItems'];?>" class="form-control name_list" title="Enter a food item" required /></td>
                                                        <td><button type="button" name="add" id="add" class="btn btn-success">Add More</button></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Description</label>
                                            <div class="col-sm-6">
                                                <textarea class="form-control" id="description" name="description" type="text" required title="Please enter a description"><?php echo $row['Description'];?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Pickup Date</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" id="pdate" name="pdate" type="date" required title="Please select a pickup date" value="<?php echo $row['PickupDate'];?>" min="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label col-lg-3" for="inputSuccess">Choose State</label>
                                            <div class="col-lg-6">
                                                <select class="form-control m-bot15" name="state" id="state" onChange="getCity(this.value);" required title="Please select a state">
                                                    <option value="<?php echo $row['StateName'];?>"><?php echo $row['StateName'];?></option>
                                                    <?php 
                                                    $query1=mysqli_query($con,"select * from tblstate");
                                                    while($row1=mysqli_fetch_array($query1)) { ?>    
                                                    <option value="<?php echo $row1['StateName'];?>"><?php echo $row1['StateName'];?></option>
                                                    <?php } ?> 
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label col-lg-3" for="inputSuccess">Choose City</label>
                                            <div class="col-lg-6">
                                                <select class="form-control m-bot15" name="city" id="city" required title="Please select a city">
                                                    <option value="<?php echo $row['CityName'];?>"><?php echo $row['CityName'];?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Pickup Address</label>
                                            <div class="col-sm-6">
                                                <textarea type="text" class="form-control" name="address" id="address" required title="Please enter the pickup address"><?php echo $row['PickupAddress'];?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class=" col-sm-3 control-label">Pictures</label>
                                            <div class="col-lg-6">
                                                <img src="images/<?php echo $row['Image'];?>" width="200" height="150" />
                                                <input type="file" class="form-control" name="image" id="image" title="Upload an image of the food donation">
                                                <input type="hidden" name="existing_image" value="<?php echo $row['Image'];?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Contact Person</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" id="contactperson" name="contactperson" type="text" required title="Please provide a contact person name" value="<?php echo $row['ContactPerson'];?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Contact Person Mobile Number</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" id="cpmobnum" name="cpmobnum" type="text" required title="Mobile number must start with 6, 7, 8, or 9 and be exactly 10 digits long." value="<?php echo $row['CPMobNumber'];?>" pattern="\d{10}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-6 col-sm-offset-3">
                                                <button type="submit" class="btn btn-success" name="submit">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                <?php } ?>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </section>
    </section>
    <!--main content end-->
</section>
<!--footer start-->
<?php include_once('includes/footer.php');?>
<!--footer end-->
</body>
</html>
