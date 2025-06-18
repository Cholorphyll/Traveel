<?php 
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class sendEmail extends Controller
{


public function sendmail(Request $request)
{
    //   $request->validate([
    //     'to' => 'required|email',
    //     'subject' => 'required|string',
    //     'body' => 'required|string',
    // ]);

    $to = 'priyathakur141997@gmail.com';
    $subject = 'Test Email Subject';
    $body = 'This is a test email body.';

      // Send the email using Laravel's Mail facade
      // Mail::raw($body, function ($message) use ($to, $subject) {
      //     $message->to($to)
      //             ->subject($subject);
      // });

      // return response()->json(['message' => 'Email sent successfully'], 200);


      try {
        // Attempt to send the email
        Mail::raw($body, function ($message) use ($to, $subject) {
            $message->to($to)
                    ->subject($subject);
        });

        // Email sent successfully
        return response()->json(['message' => 'Email sent successfully'], 200);
    } catch (\Exception $e) {
	  \Log::error('Failed to send email: ' . $e->getMessage());

    // Email sending failed
    return response()->json(['message' => 'Failed to send email: ' . $e->getMessage()], 500);
    }
  }
}