if (isset($_POST['verify_otp'])) {
    $email = $_POST['email'];
    $otp = $_POST['otp'];

    // Check OTP and expiry
    $query = mysqli_query($con, "SELECT * FROM tbldonor WHERE Email='$email'");
    $ret = mysqli_fetch_array($query);

    if ($ret && $ret['otp'] == $otp && strtotime($ret['otp_expiry']) >= time()) {
        $_SESSION['pgasoid'] = $ret['ID'];
        echo "<script>alert('OTP verified successfully! Redirecting to dashboard.');</script>";
        header('location:dashboard.php');
    } else {
        echo "<script>alert('Invalid or expired OTP. Please try again.');</script>";
    }
}
