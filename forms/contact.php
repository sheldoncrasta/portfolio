<?php
// Make sure errors are displayed during development
ini_set('display_errors', 0);  // Hide errors from users
ini_set('log_errors', 1);      // Enable error logging
ini_set('error_log', 'errors.log');  // Set the log file path

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Collect form data and sanitize
    $name = isset($_POST['name']) ? filter_var($_POST['name'], FILTER_SANITIZE_STRING) : '';
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
    $subject = isset($_POST['subject']) ? filter_var($_POST['subject'], FILTER_SANITIZE_STRING) : '';
    $message = isset($_POST['message']) ? filter_var($_POST['message'], FILTER_SANITIZE_STRING) : '';

    // Check if all required fields are filled out
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        echo "All fields are required!";
        exit;
    }

    // Replace contact@example.com with your actual receiving email address
    $receiving_email_address = 'sendmailtosheldon@gmail.com';

    // Include the PHP email form library
    if (file_exists($php_email_form = '../assets/vendor/php-email-form/php-email-form.php')) {
        include($php_email_form);
    } else {
        error_log('Unable to load the "PHP Email Form" Library!');
        die('An error occurred, please try again later.');
    }

    // Create a new contact form object
    $contact = new PHP_Email_Form;
    $contact->ajax = true;

    // Set the recipient and other fields
    $contact->to = $receiving_email_address;
    $contact->from_name = $name;
    $contact->from_email = $email;
    $contact->subject = $subject;

    // Set up SMTP server credentials using environment variables
    $contact->smtp = array(
        'host' => 'smtp.gmail.com', // Example for Gmail
        'username' => getenv('EMAIL_USERNAME'), // GitHub Secrets SMTP_USERNAME
        'password' => getenv('EMAIL_PASSWORD'), // GitHub Secrets SMTP_PASSWORD
        'port' => '587'
    );

    // Add message content
    $contact->add_message($name, 'From');
    $contact->add_message($email, 'Email');
    $contact->add_message($message, 'Message', 10);

    // Send the email
    if ($contact->send()) {
        echo 'Message sent successfully!';
    } else {
        // If the email fails to send, log the error and show a generic message
        error_log('Failed to send email from ' . $name . ' (' . $email . ')');
        echo 'An error occurred. Please try again later.';
    }
} else {
    error_log('Form not submitted correctly');
    echo 'An error occurred. Please try again later.';
}
?>