<?php
/*******************************************************************************
 * Copyright (c) 2018, WP Popup Maker
 ******************************************************************************/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_action( 'rest_api_init', function () {
   
    register_rest_route( 'fishappapi/v1', '/login/', array(
       'methods' => 'POST',
       'callback' => 'fishappapi_login_func',
    ));
    register_rest_route( 'fishappapi/v1', '/signup/', array(
       'methods' => 'POST',
       'callback' => 'fishappapi_signup_func',
    ));
    register_rest_route( 'fishappapi/v1', '/forgot_password/', array(
       'methods' => 'POST',
       'callback' => 'fishappapi_forgot_password_func',
    ));
    register_rest_route( 'fishappapi/v1', '/reset_password/', array(
       'methods' => 'POST',
       'callback' => 'fishappapi_reset_password_func',
    ));

    
});


function fishappapi_login_func( $data ) {
    require_once($_SERVER['DOCUMENT_ROOT']."/wp-load.php");
    header("Access-Control-Allow-Origin: *");
    $rest_json = file_get_contents("php://input");
    $_POST = json_decode($rest_json, true);
    http_response_code(200);
        $response = array(
            'data'      => array(),
            'message'       => 'Invalid email or password',
            'success'    => false
        );
        foreach($_POST as $k => $value){
            $_POST[$k] = sanitize_text_field($value);
        }

        $user = get_user_by( 'email', $_POST['email'] );

        if ( $user ){
            //$password_check = wp_check_password( $_REQUEST['password'], $user->user_passs, $user->ID );
            $password_check = wp_authenticate($user->data->user_login,$_POST['password'] );
            //return $password_check;
            if ( $password_check->data ){

                /* Generate a unique auth token */
                $token = uniqid();

                /* Store / Update auth token in the database */
                if( update_user_meta( $user->ID, 'auth_token', $token ) ){

                    /* Return generated token and user ID*/
                    $response['success'] = true;
                    $response['data'] = array(
                        'auth_token'    =>  $token,
                        'user_id'       =>  $user->ID,
                        'user_login'    =>  $user->user_login
                    );
                    $response['message'] = 'Successfully Authenticated';
                }
            }
        }
        return $response;
}

function fishappapi_signup_func( $data ) {
    require_once($_SERVER['DOCUMENT_ROOT']."/wp-load.php");
    header("Access-Control-Allow-Origin: *");
    $rest_json = file_get_contents("php://input");
    $_POST = json_decode($rest_json, true);
    http_response_code(200);
        $response = array(
            'data'      => array(),
            'message'       => 'Please required all Field.',
            'success'    => false
        );
        foreach($_POST as $k => $value){
            $_POST[$k] = sanitize_text_field($value);
        }

        $user = get_user_by( 'email', $_POST['email'] );

        if ( get_user_by( 'email', $_POST['email'] ) || get_user_by( 'login', $_POST['email'] ) ){

            $response = array(
                'data'      => array(),
                'message'       => 'Email Already Exist',
                'success'    => false
            );
        } else {
           
                /* Generate a unique auth token */
                $token = uniqid();
                $user_id = wp_create_user( $_POST['email'], $_POST['password'], $_POST['email'] );
                update_user_meta( $user_id , 'auth_token', $token );
                update_user_meta( $user_id , 'user_phone', $_POST['phone'] );

               

                    /* Return generated token and user ID*/
                    $response['success'] = true;
                    $response['data'] = array(
                        'auth_token'    =>  $token,
                        'user_id'       =>  $user_id,
                        'user_login'    =>  $_POST['email'],
                        'phone'         => get_user_meta($user_id,'user_phone',true)
                    );
                    $response['message'] = 'Thank you for Login.';
        }
        return $response;
}

function fishappapi_forgot_password_func( $data ) {
    require_once($_SERVER['DOCUMENT_ROOT']."/wp-load.php");
    header("Access-Control-Allow-Origin: *");
    $rest_json = file_get_contents("php://input");
    $_POST = json_decode($rest_json, true);
    http_response_code(200);
        $response = array(
            'data'      => array('rest'    =>  false),
            'message'       => 'Invalid Request',
            'success'    => false
        );
        foreach($_POST as $k => $value){
            $_POST[$k] = sanitize_text_field($value);
        }

        $user = get_user_by( 'email', $_POST['email'] );

        if ( $user ){
                    $otp = generateNumericOTP(4);
                    $mailsend = mail_for_OTP($_POST['email'], $otp);
                    if($mailsend==1){
                      update_user_meta( $user->ID , 'auth_OTP', $otp );
                      $response['success'] = true;
                      $response['data'] = array(
                          'rest'    =>  true,
                          'user_id'       =>  $user->ID,
                          'user_login'    =>  $user->user_login
                      );
                      $response['message'] = 'Successfully Authenticated';
                    } else  {
                      $response = array(
                          'data'      => array('rest'    =>  false),
                          'message'       => 'Not able to send mail.'.$otp,
                          'success'    => false
                      );
                    }
        } else {
            $response = array(
              'data'      => array('rest'    =>  false),
              'message'       => 'Invalid Email',
              'success'    => false
            );
        }
        return $response;
}




// function fishappapi_reset_password_func( $data ) {
//     require_once($_SERVER['DOCUMENT_ROOT']."/wp-load.php");
//     header("Access-Control-Allow-Origin: *");
//     $rest_json = file_get_contents("php://input");
//     $_POST = json_decode($rest_json, true);
//     http_response_code(200);
//       $response = array(
//           'data'      => array(),
//           'message'       => 'Please required all Field.',
//           'success'    => false
//       );
//       foreach($_POST as $k => $value){
//           $_POST[$k] = sanitize_text_field($value);
//       }

//       $user = get_user_by( 'email', $_POST['email'] );
//       if($user) {
//         if(isset($_POST['New_Password']) && isset($_POST['Confirm_Password'])) {
//           if($_POST['New_Password']==$_POST['Confirm_Password']){
//             update_user_meta( $user_id , 'New_Password', $_POST['New_Password'] );


//           } else {
//               $response = array(
//                 'data'      => array(),
//                 'message'       => 'Password Not Match',
//                 'success'    => false
//               );
//           }
//         } else {
//           $response = array(
//             'data'      => array(),
//             'message'       => 'Please required all Field.',
//             'success'    => false
//           );
//         }
//       }
        

//         if($user){
//               $otpval = get_user_meta( $user_id , 'auth_token_change_pass', true );
//               /* Generate a unique auth token */
//               if($otpval==$_POST['“Code”']){
//                 $user_id = wp_create_user( $_POST['email'], $_POST['password'], $_POST['email'] );
//                  /* Return generated token and user ID*/
//                   $response['success'] = true;
//                   $response['data'] = array(
//                       'auth_token'    =>  $token,
//                       'user_id'       =>  $user_id,
//                       'user_login'    =>  $_POST['email'],
//                       'phone'         => get_user_meta($user_id,'user_phone',true)
//                   );
//                   $response['message'] = 'Thank you for Login.';
//               }
                
                
                

               

                   
//         }
    
               
        
//         return $response;
        
// }




// Function to generate OTP 
function generateNumericOTP($n) { 
    $generator = "1357902468"; 
    $result = ""; 
    for ($i = 1; $i <= $n; $i++) { 
        $result .= substr($generator, (rand()%(strlen($generator))), 1); 
    } 
    return $result; 
} 


function mail_for_OTP($email, $otp){
    $to = $email;
    $subject = 'Forgot Password';
    $from = 'noreply@i3techs.com';
     
    // To send HTML mail, the Content-type header must be set
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
     
    // Create email headers
    $headers .= 'From: '.$from."\r\n".
        'Reply-To: '.$from."\r\n" .
        'X-Mailer: PHP/' . phpversion();
     
    // Compose a simple HTML email message
    $message = '<html><body>';
    $message .= '<h1 style="color:#f40;">Hi!</h1>';
    $message .= '<p style="color:#080;font-size:18px;">Seems like you forgot your password for Fishing Zone.<br> 
                  If this is true please type OTP.<br> 
                  <b>OTP: '.$otp.'</b><br>
                  if you don not forget. Please ignore</p>';
    $message .= '</body></html>';
     
    // Sending email
    if(mail($to, $subject, $message, $headers)){

      return 1;
        // echo 'Your mail has been sent successfully.';
    } else{
      return 0;
        //echo 'Unable to send email. Please try again.';
    }
}





    //     $tokenpass_change = '123';
    //             update_user_meta( $user_id , 'auth_token_change_pass', $tokenpass_change );

    //             'reset_code' => $tokenpass_change,




    //              require_once($_SERVER['DOCUMENT_ROOT']."/wp-load.php");
    // header("Access-Control-Allow-Origin: *");
    // $rest_json = file_get_contents("php://input");
    // $_POST = json_decode($rest_json, true);
    // http_response_code(200);
    //     $response = array(
    //         'data'      => array(),
    //         'message'       => 'Please required all Field.',
    //         'success'    => false
    //     );
    //     foreach($_POST as $k => $value){
    //         $_POST[$k] = sanitize_text_field($value);
    //     }

    //     $user = get_user_by( 'email', $_POST['email'] );

    //     if($user){
    //           $otpval = get_user_meta( $user_id , 'auth_token_change_pass', true );
    //           /* Generate a unique auth token */
    //           if($otpval==$_POST['“Code”']){
    //             $user_id = wp_create_user( $_POST['email'], $_POST['password'], $_POST['email'] );
    //              /* Return generated token and user ID*/
    //               $response['success'] = true;
    //               $response['data'] = array(
    //                   'auth_token'    =>  $token,
    //                   'user_id'       =>  $user_id,
    //                   'user_login'    =>  $_POST['email'],
    //                   'phone'         => get_user_meta($user_id,'user_phone',true)
    //               );
    //               $response['message'] = 'Thank you for Login.';
    //           }
                
                
                

               

                   
    //     }
    
               
        
    //     return $response;