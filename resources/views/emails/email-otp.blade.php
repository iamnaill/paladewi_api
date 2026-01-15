<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Email</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family: Arial, Helvetica, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8; padding:20px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:480px; background-color:#ffffff; border-radius:8px; padding:24px; box-shadow:0 2px 8px rgba(0,0,0,0.05);">
                    
                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding-bottom:16px;">
                            <h2 style="margin:0; color:#333333;">Verifikasi Email</h2>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="color:#555555; font-size:14px; line-height:1.6;">
                            <p>Halo 👋</p>
                            <p>
                                Gunakan kode OTP berikut untuk memverifikasi email kamu:
                            </p>
                        </td>
                    </tr>

                    <!-- OTP Box -->
                    <tr>
                        <td align="center" style="padding:20px 0;">
                            <div style="
                                display:inline-block;
                                padding:14px 24px;
                                font-size:28px;
                                font-weight:bold;
                                letter-spacing:6px;
                                color:#0d6efd;
                                background-color:#f1f5ff;
                                border-radius:6px;
                            ">
                                {{ $otp }}
                            </div>
                        </td>
                    </tr>

                    <!-- Info -->
                    <tr>
                        <td style="color:#555555; font-size:14px; line-height:1.6;">
                            <p>
                                Kode ini berlaku selama <strong>5 menit</strong>.
                                Jangan bagikan kode ini kepada siapa pun.
                            </p>
                            <p>
                                Jika kamu tidak merasa melakukan pendaftaran, silakan abaikan email ini.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding-top:20px; font-size:12px; color:#999999; text-align:center;">
                            <p style="margin:0;">
                                © {{ date('Y') }} Paladewi App<br>
                                Email ini dikirim otomatis, mohon tidak membalas.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
