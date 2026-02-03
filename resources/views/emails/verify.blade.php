<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Email Verification</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>
  <style type="text/css">
    body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0;} 
  </style>
  <body>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#DCDADA" align="center" style="margin:0;padding:0">
      <tbody>
        <tr>
          <td align="center">
            <table width="652" cellspacing="0" cellpadding="0" border="0" align="center">
              <tbody>
                <tr>
                  <td align="center">  
                    <table width="652" cellspacing="0" cellpadding="0" border="0">
                      <tbody>
                        <tr>
                          <td height="60" align="center"></td>
                        </tr>
                        <tr>     
                          <td align="center">
                            <a href="{{ url('/') }}">
                              <img src="{{ asset('images/travell-logo.png') }}" width="248" alt="Travell Logo">
                            </a>
                          </td>
                        </tr>
                        <tr>
                          <td height="60" align="center"></td>
                        </tr>
                      </tbody>
                    </table>

                    <table width="652" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF" style="border-radius:40px;">
                      <tbody>
                        <tr>
                          <td height="40" align="center"></td>
                        </tr>
                        <tr>
                          <td style="color:#000000" align="center">
                            <p style="font-family:Helvetica,Arial, sans-serif;font-size:26px;font-weight:500;line-height:31px;margin:0;padding:0" align="center">Hi {{ $user->name ?? 'there' }},<br>please verify your email address</p>
                          </td>
                        </tr>
                        <tr>
                          <td height="36" align="center"></td>
                        </tr>
                        <tr>
                          <td align="center">
                            <p style="color: #6A6A6A;font-size: 18px;font-family:Helvetica,Arial, sans-serif;font-weight: 500;letter-spacing: 0.2px;line-height: 22px;margin: 0;" align="center">Thanks for signing up with Travell. Please click the<br>button below to verify your email address.</p>
                          </td>
                        </tr>
                        <tr>
                          <td height="36" align="center"></td>
                        </tr>
                        <tr>
                          <td align="center">
                            <a style="color: #fff;display: block;font-size: 16px;font-weight: bold;font-family:Helvetica,Arial, sans-serif;letter-spacing: 0.6px;line-height: 51px;border-radius: 12px;background: #FF4B01;text-decoration: none;width: 384px;-webkit-text-size-adjust:none;mso-hide:all;" href="{{ route('varification', ['token' => $token]) }}" target="_blank">Verify Email Address</a>
                          </td>
                        </tr>
                        <tr>
                          <td height="36" align="center"></td>
                        </tr>
                        <tr>
                          <td align="center">
                            <p style="color: #6A6A6A;font-size: 18px;font-family:Helvetica,Arial, sans-serif;font-weight: 500;letter-spacing: 0.2px;line-height: 22px;margin: 0" align="center">If the above button does not work, copy and paste the<br> following into your browser's address bar:</p>
                          </td>
                        </tr>
                        <tr>
                          <td height="18" align="center"></td>
                        </tr>
                        <tr>
                          <td align="center">
                            <a href="{{ route('varification', ['token' => $token]) }}" style="color: #2C4DDE;font-size: 14px;font-family:Helvetica,Arial, sans-serif;font-weight: 500;line-height: 18px;margin: 0 0 15px;" align="center" target="_blank">{{ route('varification', ['token' => $token]) }}</a>
                          </td>
                        </tr>
                        <tr>
                          <td height="30" align="center"></td>
                        </tr>
                      </tbody>
                    </table>

                    <table width="652" cellspacing="0" cellpadding="0" border="0" align="center">
                      <tbody>
                        <tr>
                          <td align="center">
                            <table width="652" cellspacing="0" cellpadding="0" border="0" align="center">
                              <tbody>
                                <tr>
                                  <td height="20" align="center"></td>
                                </tr>
                                <tr>
                                  <td align="center">
                                    <p style="color: #555555;font-size: 16px;font-family:Helvetica,Arial, sans-serif;font-weight: 500;letter-spacing: 0.2px;line-height: 28px;margin: 0;" align="center">If you did not create an account, you can safely<br> ignore this email.</p>
                                  </td>
                                </tr>
                                <tr>
                                  <td height="50" align="center"></td>
                                </tr>
                              </tbody>
                            </table>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
  </body>
</html>
