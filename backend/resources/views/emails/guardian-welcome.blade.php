<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>KidWatch Guardian Account</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f7fa; font-family:Arial, sans-serif; color:#333;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f7fa; padding:40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background-color:#003366; padding:20px; text-align:center; color:#ffffff;">
                            <h1 style="margin:0; font-size:24px;">KidWatch</h1>
                            <p style="margin:0; font-size:14px;">Guardian Account Created</p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px;">
                            <h2 style="color:#003366; margin-top:0;">Welcome to KidWatch!</h2>
                            <p>Dear <strong>{{ $guardian->first_name }}</strong>,</p>
                            <p>Your guardian account has been successfully created for student
                               <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>.</p>

                            <p>You can log in using the following credentials:</p>
                            <table cellpadding="8" cellspacing="0" width="100%" style="background:#f9fbfd; border:1px solid #e0e6ed; border-radius:8px; margin:20px 0;">
                                <tr>
                                    <td style="font-weight:bold; width:150px;">Email:</td>
                                    <td>{{ $guardian->user->email }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight:bold;">Temporary Password:</td>
                                    <td>{{ $password }}</td>
                                </tr>
                            </table>

                            <p style="color:#d9534f; font-weight:bold;">⚠ Please log in and change your password immediately for security.</p>

                            <!-- Call to Action -->
                            <div style="text-align:center; margin:30px 0;">
                                <a href="{{ url('/') }}"
                                   style="background-color:#003e6d; color:#ffffff; text-decoration:none; padding:12px 24px; border-radius:6px; font-weight:bold; display:inline-block;">
                                   Log In to KidWatch
                                </a>
                            </div>

                            <p>Thank you for being part of KidWatch.<br>
                               <em>— The KidWatch System Team</em></p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f4f7fa; padding:15px; text-align:center; font-size:12px; color:#888;">
                            © {{ date('Y') }} KidWatch System. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
