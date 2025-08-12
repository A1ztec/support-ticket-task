<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Ticket Reply</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 300;
        }

        .content {
            padding: 30px;
        }

        .ticket-info {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .ticket-info h3 {
            margin-top: 0;
            color: #667eea;
            font-size: 18px;
        }

        .ticket-details {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin: 15px 0;
        }

        .ticket-detail {
            flex: 1;
            min-width: 150px;
        }

        .ticket-detail strong {
            display: block;
            color: #555;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .ticket-detail span {
            font-size: 14px;
            color: #333;
        }

        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status.open {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status.in_progress {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .status.closed {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message-box {
            background-color: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }

        .message-box h4 {
            margin-top: 0;
            color: #667eea;
            font-size: 16px;
        }

        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 500;
            margin: 20px 0;
            transition: transform 0.2s;
        }

        .button:hover {
            transform: translateY(-2px);
        }

        .footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            border-top: 1px solid #e9ecef;
        }

        .footer p {
            margin: 5px 0;
        }

        .divider {
            height: 1px;
            background-color: #e9ecef;
            margin: 25px 0;
        }

        @media (max-width: 600px) {
            .container {
                margin: 10px;
                border-radius: 0;
            }

            .header,
            .content {
                padding: 20px;
            }

            .ticket-details {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="header">
            <h1>🎫 Support Ticket Reply</h1>
            <p>We've responded to your support request</p>
        </div>


        <div class="content">
            <p>Hello <strong>{{ $ticket->user->name }}</strong>,</p>

            <p>Great news! We have replied to your support ticket. Here are the details:</p>

            
            <div class="ticket-info">
                <h3>{{ $ticket->subject }}</h3>

                <div class="ticket-details">
                    <div class="ticket-detail">
                        <strong>Ticket ID</strong>
                        <span>#{{ $ticket->id }}</span>
                    </div>
                    <div class="ticket-detail">
                        <strong>Status</strong>
                        <span class="status {{ $ticket->status->value }}">
                            {{ ucwords(str_replace('_', ' ', $ticket->status->value)) }}
                        </span>
                    </div>
                    <div class="ticket-detail">
                        <strong>Created</strong>
                        <span>{{ $ticket->created_at->format('M j, Y g:i A') }}</span>
                    </div>
                    <div class="ticket-detail">
                        <strong>Last Update</strong>
                        <span>{{ $ticket->updated_at->format('M j, Y g:i A') }}</span>
                    </div>
                </div>
            </div>


            <div class="message-box">
                <h4>📝 Your Original Message:</h4>
                <p>{{ $ticket->message }}</p>
            </div>


            @if(isset($latestMessage) && $latestMessage)
                <div class="message-box">
                    <h4>💬 Latest Reply from Support Team:</h4>
                    <p>{{ $latestMessage->message }}</p>
                    <p style="color: #6c757d; font-size: 12px; margin-top: 15px;">
                        <strong>Replied by:</strong> {{ $latestMessage->user->name }}
                        • {{ $latestMessage->created_at->format('M j, Y g:i A') }}
                    </p>
                </div>
            @endif

            <div class="divider"></div>



            <div
                style="margin-top: 30px; padding: 20px; background-color: #f8f9fa; border-radius: 8px; border-left: 4px solid #17a2b8;">
                <h4 style="color: #17a2b8; margin-top: 0;">💡 Quick Tips:</h4>
                <ul style="margin-bottom: 0; color: #495057;">
                    <li>You can reply to this ticket by responding to this email</li>
                    <li>Keep your ticket ID (#{{ $ticket->id }}) for reference</li>
                    <li>Our support team typically responds within 24 hours</li>
                </ul>
            </div>
        </div>


        <div class="footer">
            <p><strong>{{ config('app.name', 'Support System') }}</strong></p>
            <p>Thank you for contacting our support team!</p>
            <p style="font-size: 12px; color: #868e96;">
                This is an automated email. Please do not reply directly to this email address.
            </p>
            <p style="font-size: 12px; color: #868e96;">
                © {{ date('Y') }} {{ config('app.name', 'Support System') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>

</html>
