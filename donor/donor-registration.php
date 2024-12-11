<?php 
session_start();
error_reporting(0);
include('includes/dbconnection.php');

$msg = '';

if (isset($_POST['submit'])) {
    $fname = mysqli_real_escape_string($con, $_POST['name']);
    $mobno = mysqli_real_escape_string($con, $_POST['mobilnumber']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $ret = mysqli_query($con, "SELECT Email FROM tbldonor WHERE Email='$email'");
    $result = mysqli_fetch_array($ret);
    
    if ($result > 0) {
        $msg = "This email is already associated with another account.";
    } else {
        $query = mysqli_query($con, "INSERT INTO tbldonor (FullName, MobileNumber, Email, Password) VALUES ('$fname', '$mobno', '$email', '$password')");
        $msg = ($query) ? "You have successfully registered." : "Something went wrong. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FWMS | Donor Registration</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <link href="css/style-responsive.css" rel="stylesheet">
    <link href="css/font-awesome.css" rel="stylesheet">
    <script src="js/jquery2.0.3.min.js"></script>
    <script>
        function checkpass() {
            const password = document.signup.password.value;
            const repeatPassword = document.signup.repeatpassword.value;
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%?&])[A-Za-z\d@$!%?&]{8,}$/;

            // Check password strength
            if (!passwordRegex.test(password)) {
                alert("Password must be at least 8 characters long, including uppercase, lowercase, a number, and a special character.");
                document.signup.password.focus();
                return false;
            }

            // Check if passwords match
            if (password !== repeatPassword) {
                alert("Passwords do not match.");
                document.signup.repeatpassword.focus();
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <div class="reg-w3">
        <div class="w3layouts-main">
            <h2>Register Now</h2>
            <p style="font-size:16px; color:red" align="center"><?php echo $msg ?? ''; ?></p>

            <form name="signup" action="#" method="post" onsubmit="return checkpass();">
                <label for="name">Full Name *</label>
                <input type="text" class="ggg" name="name" placeholder="Enter your name" required pattern="[A-Za-z\s]+" title="Only letters and spaces allowed.">
                
                <label for="email">Email *</label>
                <input type="email" class="ggg" name="email" placeholder="Enter your email" required>
                
                <label for="mobilnumber">Phone Number *</label>
                <input type="text" class="ggg" name="mobilnumber" placeholder="Enter 10-digit phone number" required pattern="[6-9][0-9]{9}" maxlength="10" title="Must start with 6, 7, 8, or 9 and be 10 digits long.">

                <label for="password">Password *</label>
                <input type="password" class="ggg" name="password" placeholder="Create a strong password" required>

                <label for="repeatpassword">Repeat Password *</label>
                <input type="password" class="ggg" name="repeatpassword" placeholder="Repeat your password" required>

                <h4>
                    <input type="checkbox" required> I agree to the Terms of Service and Privacy Policy
                </h4>

                <input type="submit" value="Register" name="submit">
            </form>

            <p>Already Registered? <a href="login.php">Login</a></p>
        </div>
    </div>

    <script src="js/bootstrap.js"></script>
    <script src="js/jquery.dcjqaccordion.2.7.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/jquery.slimscroll.js"></script>
    <script src="js/jquery.nicescroll.js"></script>
    <script src="js/jquery.scrollTo.js"></script>
</body>
</html>
