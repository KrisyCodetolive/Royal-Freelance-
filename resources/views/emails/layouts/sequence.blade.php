<!DOCTYPE html>
<html lang="fr" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            background-color: #f8fafc;
            color: #334155;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            word-break: break-word;
        }
        .wrapper {
            width: 100%;
            background-color: #f8fafc;
            padding: 40px 0;
        }
        .main {
            background-color: #ffffff;
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
        }
        .header {
            padding: 30px 40px;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
            background: #ffffff;
        }
        .content {
            padding: 40px;
            font-size: 16px;
            line-height: 1.6;
            color: #475569;
        }
        .content h1 { color: #0f172a; font-size: 24px; font-weight: bold; margin-top: 0; margin-bottom: 20px; }
        .content h2 { color: #1e293b; font-size: 20px; font-weight: bold; margin-top: 0; margin-bottom: 16px; }
        .content h3 { color: #334155; font-size: 18px; font-weight: bold; margin-top: 0; margin-bottom: 12px; }
        .content p { margin-top: 0; margin-bottom: 16px; }
        .content a { color: #f59e0b; text-decoration: none; font-weight: 500;}
        .content a:hover { text-decoration: underline; }
        .content blockquote {
            border-left: 4px solid #f59e0b;
            background-color: #fefce8;
            margin: 20px 0;
            padding: 15px 20px;
            font-style: italic;
            border-radius: 0 8px 8px 0;
            color: #854d0e;
        }
        .content ul, .content ol { margin-top: 0; margin-bottom: 16px; padding-left: 24px; }
        .footer {
            padding: 30px 40px;
            text-align: center;
            background-color: #f8fafc;
            font-size: 13px;
            color: #64748b;
        }
        .btn {
            display: inline-block;
            background-color: #f59e0b;
            color: #ffffff !important;
            padding: 12px 28px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
        @media screen and (max-width: 600px) {
            .main { border-radius: 0 !important; }
            .header, .content, .footer { padding: 20px !important; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <table class="main" width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
            @if(isset($tenant_name))
            <tr>
                <td class="header">
                    <h2 style="margin: 0; color: #0f172a; font-size: 24px;">{{ $tenant_name }}</h2>
                </td>
            </tr>
            @endif

            <tr>
                <td class="content">
                    {!! $content !!}
                </td>
            </tr>

            <tr>
                <td class="footer">
                    <p style="margin: 0 0 10px 0;">Cet email vous a été envoyé par <strong>{{ $tenant_name ?? 'notre plateforme' }}</strong>.</p>
                    @if(isset($unsubscribeUrl))
                    <p style="margin: 0;">
                        <a href="{{ $unsubscribeUrl }}" style="color: #94a3b8; text-decoration: underline; font-weight: normal;">Se désabonner de nos séquences</a>
                    </p>
                    @endif
                    @if(isset($pixelUrl))
                        <img src="{{ $pixelUrl }}" width="1" height="1" style="display:none;" alt="">
                    @endif
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
