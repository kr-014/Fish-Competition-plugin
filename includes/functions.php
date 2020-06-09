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
    register_rest_route( 'fishappapi/v1', '/social_signup/', array(
       'methods' => 'POST',
       'callback' => 'fishappapi_social_signup_func',
    ));
    register_rest_route( 'fishappapi/v1', '/forgot_password/', array(
       'methods' => 'POST',
       'callback' => 'fishappapi_forgot_password_func',
    ));
    register_rest_route( 'fishappapi/v1', '/reset_password/', array(
       'methods' => 'POST',
       'callback' => 'fishappapi_reset_password_func',
    ));

    register_rest_route( 'fishappapi/v1', '/signup_opt/', array(
       'methods' => 'POST',
       'callback' => 'fishappapi_signup_OTP_func',
    ));
    register_rest_route( 'fishappapi/v1', '/forget_otp/', array(
       'methods' => 'POST',
       'callback' => 'fishappapi_forget_OTP_func',
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
            'code'      => 400,
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
                    $response['code'] = 200;
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
            'code'      => 400,
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
                'code'      => 400,
                'message'       => 'Email Already Exist',
                'success'    => false
            );
        } else {
            $otp = generateNumericOTP(4);
            $mailsend = mail_for_OTP_signup($_POST['email'], $otp);
            if($mailsend==1){
                
                $token = uniqid();
                $user_id = wp_create_user( $_POST['email'], $_POST['password'], $_POST['email'] );
                update_user_meta( $user_id , 'auth_token', $token );
                update_user_meta( $user_id , 'auth_OTP_signup', $otp );
                update_user_meta( $user_id , 'auth_OTP_passed', 0 );

                $getsignupOPTget = get_user_meta( $user_id , 'auth_OTP_signup', true );
                /* Return generated token and user ID*/
                $response['success'] = true;
                $response['code'] = 200;
                $response['data'] = array(
                    'auth_token'    =>  $token,
                    'OTP'     => $getsignupOPTget,
                    'user_id'       =>  $user_id,
                    'user_login'    =>  $_POST['email']
                );
                $response['message'] = 'Thank you for Signup. Please check mail and verify OTP';
            } else {
                $response = array(
                  'data'      => array(),
                  'code'      => 400,
                  'message'       => 'Not able to send mail.',
                  'success'    => false
                );
            }
        }
        return $response;
}   



function fishappapi_social_signup_func( $data ) {
    require_once($_SERVER['DOCUMENT_ROOT']."/wp-load.php");
    header("Access-Control-Allow-Origin: *");
    $rest_json = file_get_contents("php://input");
    $_POST = json_decode($rest_json, true);
    http_response_code(200);
        $response = array(
            'data'      => array(),
            'code'      => 400,
            'message'       => 'Please required all Field.',
            'success'    => false
        );
        foreach($_POST as $k => $value){
            $_POST[$k] = sanitize_text_field($value);
        }

        $user = get_user_by( 'email', $_POST['email'] );
        if ( get_user_by( 'email', $_POST['email'] ) || get_user_by( 'login', $_POST['email'] ) ){

            if ( $user ) {
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
                        $response['code'] = 200;
                        $response['data'] = array(
                            'auth_token'    =>  $token,
                            'user_id'       =>  $user->ID,
                            'user_login'    =>  $user->user_login
                        );
                        $response['message'] = 'Successfully Authenticated';
                    }
                }
            } else {
                $response = array(
                    'data'      => array(),
                    'code'      => 400,
                    'message'       => 'Please check credentials.',
                    'success'    => false
                );
            }
        } else {
            $token = uniqid();
            $user_id = wp_create_user( $_POST['email'], $_POST['password'], $_POST['email'] );
            update_user_meta( $user_id , 'auth_token', $token );
            // update_user_meta( $user_id , 'auth_OTP_signup', $otp );
            // if(isset($_POST['type']) && !empty($_POST['type'])){
            // 	update_user_meta( $user_id , 'auth_social_type', $_POST['type'] );
            // }
            // update_user_meta( $user_id , 'auth_OTP_passed', 0 );

            $getsignupOPTget = get_user_meta( $user_id , 'auth_OTP_signup', true );
            /* Return generated token and user ID*/
            $response['success'] = true;
            $response['code'] = 200;
            $response['data'] = array(
                'auth_token'    =>  $token,
                'user_id'       =>  $user_id,
                'user_login'    =>  $_POST['email']
            );
            $response['message'] = 'Thank you for Signup.';
            
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
            'code'      => 400,
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
                        update_user_meta( $user->ID , 'auth_OTP_passed_forg', 0 );

                        $response['success'] = true;
                        $response['code'] = 200;
                        $response['data'] = array(
                              'rest'    =>  true,
                              'OTP'     => $otp,
                              'user_id'       =>  $user->ID,
                              'user_login'    =>  $user->user_login
                        );
                        $response['message'] = 'Successfully Authenticated';
                    } else  {
                      $response = array(
                          'data'      => array('rest'    =>  false),
                          'code'      => 400,
                          'message'       => 'Not able to send mail.',
                          'success'    => false
                      );
                    }
        } else {
            $response = array(
              'data'      => array('rest'    =>  false),
              'code'      => 400,
              'message'       => 'Invalid Email',
              'success'    => false
            );
        }
        return $response;
}

function fishappapi_signup_OTP_func( $data ) {
    require_once($_SERVER['DOCUMENT_ROOT']."/wp-load.php");
    header("Access-Control-Allow-Origin: *");
    $rest_json = file_get_contents("php://input");
    $_POST = json_decode($rest_json, true);
    http_response_code(200);
        $response = array(
            'data'      => array(),
            'code'      => 400,
            'message'       => 'Please required all Field.',
            'success'    => false
        );
        foreach($_POST as $k => $value){
            $_POST[$k] = sanitize_text_field($value);
        }

        $user = get_user_by( 'email', $_POST['email'] );


        if ( get_user_by( 'email', $_POST['email'] ) || get_user_by( 'login', $_POST['email'] ) ){

                $getsignupOPT = get_user_meta( $user->ID , 'auth_OTP_signup', true );
                if($getsignupOPT && ($_POST['Code'] == $getsignupOPT)){
                    update_user_meta( $user->ID , 'auth_OTP_passed', 1 );
                    $token = uniqid();
                    update_user_meta( $user->ID , 'auth_token', $token );
                    /* Return generated token and user ID*/
                    $response['success'] = true;
                    $response['code'] = 200;
                    $response['data'] = array(
                        'auth_token'    =>  $token,
                        'user_id'       =>  $user->ID,
                        'user_login'    =>  $_POST['email']
                    );
                    $response['message'] = 'Thank you for Signup.';
                } else {
                    $response = array(
                        'data'      => array(),
                        'code'      => $getsignupOPT,
                        'message'       => 'OTP Not Match',
                        'success'    => false
                    );
                }
            
        } else {
           $response = array(
                        'data'      => array(),
                        'code'      => 400,
                        'message'       => 'Eamil not Found',
                        'success'    => false
                    );     
            
        }
        return $response;
}


function fishappapi_forget_OTP_func( $data ) {
    require_once($_SERVER['DOCUMENT_ROOT']."/wp-load.php");
    header("Access-Control-Allow-Origin: *");
    $rest_json = file_get_contents("php://input");
    $_POST = json_decode($rest_json, true);
    http_response_code(200);
        $response = array(
            'data'      => array(),
            'code'      => 400,
            'message'       => 'Please required all Field.',
            'success'    => false
        );
        foreach($_POST as $k => $value){
            $_POST[$k] = sanitize_text_field($value);
        }
        $user = get_user_by( 'email', $_POST['email'] );
        if ( get_user_by( 'email', $_POST['email'] ) || get_user_by( 'login', $_POST['email'] ) ){
                $getsignupOPT = get_user_meta( $user->ID , 'auth_OTP', true );
                if($getsignupOPT && ($_POST['Code'] == $getsignupOPT)){
                    update_user_meta( $user->ID , 'auth_OTP_passed_forg', 1 );
                    $token = uniqid();
                    update_user_meta( $user->ID , 'auth_token', $token );
                    /* Return generated token and user ID*/
                    $response['success'] = true;
                    $response['code'] = 200;
                    $response['data'] = array(
                        'auth_token'    =>  $token,
                        'OTP'     => true,
                        'user_id'       =>  $user->ID,
                        'user_login'    =>  $_POST['email']
                    );
                    $response['message'] = 'Thank you for Signup.';
                } else {
                    $response = array(
                        'data'      => array(),
                        'code'      => 400,
                        'message'       => 'OTP Not Match',
                        'success'    => false
                    );
                }
            
        } else {
           $response = array(
                        'data'      => array(),
                        'code'      => 400,
                        'message'       => 'Eamil not Found',
                        'success'    => false
                    );     
            
        }
        return $response;
}


function fishappapi_reset_password_func( $data ) {
    require_once($_SERVER['DOCUMENT_ROOT']."/wp-load.php");
    header("Access-Control-Allow-Origin: *");
    $rest_json = file_get_contents("php://input");
    $_POST = json_decode($rest_json, true);
    http_response_code(200);
    $response = array(
          'data'      => array(),
           'code'      => 400,
          'message'       => 'Please required all Field.',
          'success'    => false
      );
    foreach($_POST as $k => $value){
          $_POST[$k] = sanitize_text_field($value);
    }

    $user = get_user_by( 'email', $_POST['email'] );
    if ( get_user_by( 'email', $_POST['email'] ) || get_user_by( 'login', $_POST['email'] ) ){
        if(isset($_POST['New_Password']) && isset($_POST['Confirm_Password'])) {
            if($_POST['New_Password']==$_POST['Confirm_Password']){
                $forgett = get_user_meta( $user->ID , 'auth_OTP_passed_forg', true );
                if($forgett==1){
                    $token = uniqid();
                    update_user_meta( $user->ID , 'auth_token', $token );
                    wp_set_password($_POST['New_Password'],$user->ID);
                    $response['success'] = true;
                    $response['code'] = 200;
                    $response['data'] = array(
                        'auth_token'    =>  $token,
                        'user_id'       =>  $user->ID,
                        'user_login'    =>  $_POST['email']
                    );
                    $response['message'] = 'Password Changed Successfully.';
                } else {
                    $response = array(
                    'data'      => array(),
                     'code'      => $forgett,
                    'message'       => 'OTP Process Required',
                    'success'    => false
                  );
                }

              } else {
                  $response = array(
                    'data'      => array(),
                     'code'      => 400,
                    'message'       => 'Password Not Match',
                    'success'    => false
                  );
              }
        } else {
          $response = array(
            'data'      => array(),
            'code'      => 400,
            'message'       => 'Please required all Field.',
            'success'    => false
          );
        }
    } else {
       $response = array(
                    'data'      => array(),
                    'code'      => 400,
                    'message'       => 'Eamil not Found',
                    'success'    => false
                );     
        
    }
    return $response;   
}




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
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= 'From: '.$from."\r\n".
        'Reply-To: '.$from."\r\n" .
        'X-Mailer: PHP/' . phpversion();
    $message = '<html><body>';
    $message .= '<h2>Hi!</h2>';
    $message .= '<p>Seems like you forgot your password for Fishing Zone.<br> 
                  If this is true please type OTP.<br> 
                  <b>OTP: '.$otp.'</b><br>
                  if you don not forget. Please ignore</p>';
    $message .= '</body></html>';
    if(mail($to, $subject, $message, $headers)){
      return 1;
    } else{
      return 0;
    }
}
function mail_for_OTP_signup($email, $otp){
    $to = $email;
    $subject = 'Signup Request';
    $from = 'noreply@i3techs.com';
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= 'From: '.$from."\r\n".
        'Reply-To: '.$from."\r\n" .
        'X-Mailer: PHP/' . phpversion();
    $message = '<html><body>';
    $message .= '<h2>Hi!</h2>';
    $message .= '<p>Seems like you Signup for Fishing Zone.<br> 
                  If this is true please type OTP.<br> 
                  <b>OTP: '.$otp.'</b><br>
                  if you don not forget. Please ignore</p>';
    $message .= '</body></html>';
    if(mail($to, $subject, $message, $headers)){
      return 1;
    } else{
      return 0;
    }
}

