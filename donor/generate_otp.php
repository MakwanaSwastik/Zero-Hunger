<?php
session_start();
include('includes/dbconnection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $email = $input['email'];

    // Check email existence in the database
    $query = mysqli_query($con, "SELECT ID FROM tbldonor WHERE Email='$email'");
    $ret = mysqli_fetch_array($query);

    if ($ret) {
        $otp = rand(100000, 999999);
        $otpExpiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        // Save OTP and expiry in database
        mysqli_query($con, "UPDATE tbldonor SET otp='$otp', otp_expiry='$otpExpiry' WHERE ID='" . $ret['ID'] . "'");

        // Send OTP email
        $to = $email;
        $subject = "Your OTP for ZeroHunger Login";
        $message = "Your OTP is $otp. It is valid for 5 minutes.";
        $headers = "From: no-reply@zerohunger.com";

        if (mail($to, $subject, $message, $headers)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send OTP email.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Email not found.']);
    }
}
?>
