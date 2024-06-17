<?php

declare(strict_types=1);

namespace app\controllers\auth;


use app\core\Controller;
use app\models\Auth;
use app\models\Mails;
use app\models\Password_Reset as ModelsPassword_Reset;
use app\models\User;

class Password_reset extends Controller
{

    private $jsonData;
    private $class;

    public function __construct()
    {
        $this->jsonData = file_get_contents("php://input");
        $this->jsonData = json_decode($this->jsonData);
        // $this->class = get_class();
    }

    public function index()
    {
        //if (!Auth::logged_in())  $this->redirect('login');

        return $this->view('change-password', ['pageTitle' => "Change Password", 'class' => $this->class]);
    }

    public function forgetPassword()
    {
        // if (Auth::logged_in()) return $this->view('dashboard');

        unset($_SESSION['email']);
        unset($_SESSION['userID']);

        //? uread messages for footer
        return $this->view('forgot-password');
    }

    public function resetPassword()
    {
        // if (Auth::logged_in()) return $this->view('dashboard');

        unset($_SESSION['email']);

        if (isset($_SESSION['userID'])) return $this->view('reset-password', ['userID' => $_SESSION['userID']]);


        return $this->view('forgot-password');
    }


    public function reset()
    {
        $user = new User();
        if ($this->jsonData) {

            $password = $this->jsonData->password;
            $confirm_pass = $this->jsonData->confirm_password;
            $userID = $this->jsonData->userID;

            $userData = $user->where('userID', $userID);

            if ($userData && !empty($userData)) {
                // validate data
                $data['password'] = $password;
                $data['confirm_password'] = $confirm_pass;

                $validated = $this->validate($data);

                if (is_array($validated)) {
                    // send json data
                    return $this->sendJsonResponse(STATUS_ERROR, 'Validation is incorrect', $validated);
                }

                // echo "All correct"; die;
                $_SESSION['passwordReset'] = true;

                // update password
                $this->updatePassword($userData, $password);
                return $this->sendJsonResponse(STATUS_SUCCESS, 'Password changed successfully', $user->errors);
            }
        } else {
            // send json data
            return $this->sendJsonResponse(STATUS_ERROR, 'Validation failed', $user->errors);
        }
    }


    public function otp()
    {
        // if (Auth::logged_in()) return $this->view('dashboard');

        // check if session exist
        // if (isset($_SESSION['email'])) return $this->view('otp', ['email' => $_SESSION['email']]);

        return $this->view('otp', ['pageTitle' => 'Otp Page']);
    }

    public function sendOtp()
    {
        // if (Auth::logged_in()) return $this->view('dashboard');

        if ($this->jsonData) {
            // show($this->jsonData); die;
            // check if email exist
            $user = new User();
            $userData = $user->where('email', $this->jsonData->email);

            if ($userData) {
                $userData = $userData[0];
                $passwordReset = new ModelsPassword_Reset();
                $email = $userData->email;

                // check if otp exist
                $otpExist = $this->checkOtpExist(['userID' => $userData->userID]);
                if ($otpExist) {
                    $passwordReset->delete($otpExist->id);
                }

                // generate OTP
                $otp = $this->generateOTP();
                $expiryTime = time() + OTP_EXPIRY_DURATION;
                $expiryDate = date('F j, Y g:i:s A', $expiryTime);

                $data['userID'] = $userData->userID;
                $data['otp_code'] = $otp;
                $data['expiry_time'] = $expiryTime;

                // store in otp table
                $passwordReset->insert($data);

                // Mail text 
                $text = "We have received a request to reset the password for your {$userData->fullName} account. To complete the password reset process, use the following one-time password (OTP):<br><br>Your OTP is: {$otp}<br><br>
                Please this OTP will expire by {$expiryDate}, use it to securely reset your password. Do not share this OTP with anyone for security reasons.<br><br>
                If you did not request this password reset or have any concerns about your account security, please contact our support team immediately.<br><br>
                Best regards,<br>
                Risky-T Team
                ";

                // Send mail
                $this->sendMail($userData->fullName, $userData->email, 'Password Reset OTP for Risky-T', 'PASSWORD RESET', $text);

                // create a new sesion to save email
                $_SESSION['email'] = $userData->email;

                $this->sendJsonResponse(STATUS_SUCCESS, 'OTP sent successfully');
                return;
            }

            // return to front end with data
            return  $this->sendJsonResponse(STATUS_ERROR, 'Email not found');
        }
    }

    public function validateOtp()
    {
        if ($this->jsonData) {
            $user = new User();

            // get otp code and email;
            $otp = $this->jsonData->otp;
            $email = $this->jsonData->email;

            // find userID
            $userID = $user->where('email', $email)[0]->userID;

            // check if otp code is correct
            $otpExist = $this->checkOtpExist(['userID' => $userID, 'otp_code' => $otp]);
            if ($otpExist) {

                // if time has ellapsed
                $time = time();
                $timeCheck = $this->checkTimeElappse($otpExist->otp_code, $time);

                if ($timeCheck) {
                    // create a new sesion to save userID
                    $_SESSION['userID'] = $userID;

                    // delete OTP and check
                    $passwordReset = new ModelsPassword_Reset();
                    $passwordReset->delete($otpExist->id);

                    return $this->sendJsonResponse(STATUS_SUCCESS, 'Otp check passed');
                }

                return $this->sendJsonResponse(STATUS_ERROR, 'OTP is expired, resend OTP');
            } else {
                return $this->sendJsonResponse(STATUS_ERROR, 'OTP code is invalid');
            }
        }
    }

    public function changePassword()
    {
        $user = new User();
        $userDetails = Auth::user();

        if ($this->jsonData) {

            $oldPass = $this->jsonData->oldPass;
            $password = $this->jsonData->password;
            $confirm_pass = $this->jsonData->confirm_password;

            $userData = $user->where('userID', Auth::user()->userID);

            if (is_array($userData) && !empty($userData)) {
                $userData = $userData[0];

                // if (!password_verify($oldPass, $userData->password)) {
                if (hash_equals(hash('sha256', $oldPass), $userData->password)) {
                    $user->errors['current_password'] = "Your current password is incorrect";

                    return $this->sendJsonResponse(STATUS_ERROR, 'Validation incorrect', $user->errors);
                } else {
                    // validate data
                    $data['password'] = $password;
                    $data['confirm_password'] = $confirm_pass;

                    $validated = $this->validate($data);

                    if (is_array($validated)) {
                        // send json data
                        return $this->sendJsonResponse(STATUS_ERROR, 'Validation is incorrect', $validated);
                    }

                    // update password and send json data
                    $this->updatePassword($userDetails, $password);
                    return $this->sendJsonResponse(STATUS_SUCCESS, 'Password changed successfully', $user->errors);
                }
            }
        } else {
            // send json data
            $this->sendJsonResponse(STATUS_ERROR, 'Validation failed', $user->errors);
            return;
        }
    }

    private function validate($data)
    {
        $user = new User();
        $validate = $user->validate($data);

        if (!$validate) return $user->errors;

        return true;
    }

    private function generateOTP()
    {
        $otp = mt_rand(1000, 9999);
        $otp = sprintf("%04d", $otp);
        return $otp;
    }


    private function checkOtpExist(array $data)
    {
        $password_reset = new ModelsPassword_Reset();
        $otpCheck  = $password_reset->row_exist($data);

        if ($otpCheck) {
            $otpCheck = $otpCheck[0];
            // show($otpCheck); die;
            return $otpCheck;
        }

        return false;
    }

    public function checkTimeElappse(string $otp, int $currentTime): bool
    {
        $password_reset = new ModelsPassword_Reset();
        $expiryTime = $password_reset->where('otp_code', $otp)[0]->expiry_time;

        if ((int) $expiryTime >= $currentTime) return true;

        return false;
    }

    private function sendMail(string $clientName, array|string $receiverEmail, string $subject, string $heading, string $text)
    {
        try {
            $mail = new Mails();
            $message = $mail->MailTemplate($clientName, $heading, ASSETS, $text);
            $mail->send_mail(COMPANY_MAIL, 'Ricky-T', $receiverEmail, $message, $subject);
        } catch (Exception $e) {
            error_log("Error sending email. Mailer Error: {$e->getMessage()}");
            return false;
        }

        return true;
    }


    private function updatePassword($userDetails, $newPassword)
    {
        $user = new User();
        $userDetails =  Auth::user() ? Auth::user() : $userDetails[0];

        // update password
        $user->update($userDetails->id, ['password' => $newPassword]);

        // mail text
        $text = "Your password has been changed successfully, Risky-T cares.";

        // send mail
        $this->sendMail($userDetails->fullName, $userDetails->email, 'Password Reset for Risky-T', 'PASSWORD RESET', $text);

        // send json data
        $this->sendJsonResponse(STATUS_SUCCESS, 'Password changed successfully', $user->errors);
        return;
    }
}
