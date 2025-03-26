<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Payment Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Bahij TheSansArabic', sans-serif;
            background-color: white;
            margin: 0;
            padding: 20px;
        }
        .btn {
            text-decoration: none; /* Removes underline from links */
        }
        .container {
            max-width: 100%;
            margin: 0 auto;
            text-align: center;
        }

        /* Back button */
        .back-button {
            position: relative;
            display: inline-block;
            width: 38px;
            height: 38px;
            margin-bottom: 20px;
        }

        .back-button-box {
            width: 100%;
            height: 100%;
            background: white;
            box-shadow: 5px 10px 20px rgba(211, 209, 216, 0.30);
            border-radius: 12px;
        }

        .back-arrow {
            position: absolute;
            width: 5px;
            height: 9.5px;
            left: 12px;
            top: 14px;
            transform: rotate(180deg);
            border: 1.5px solid #111719;
        }

        /* Product image */
        .product-image {
            width: 100%;
            max-width: 300px;
            height: auto;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        /* Purchase label */
        .purchase-label {
            color: black;
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 10px;
        }

        /* Button Styles */
        .btn-applepay, .btn-savedcard {
            display: inline-block;
            width: 100%;
            max-width: 500px;
            padding: 15px;
            margin: 10px 0;
            border-radius: 12px;
            font-size: 20px;
            font-weight: 500;
            line-height: 24px;
            text-align: center;
            cursor: pointer;
        }

        .btn-applepay {
            background-color: black;
            color: white;
            font-size: 18px;
        }

        .btn-applepay i {
            margin-right: 8px;
        }

        .btn-savedcard {
            background-color: white;
            color: #9B9B9B;
            font-size: 16px;
            border: 1.5px solid #9B9B9B;
        }

        .btn-savedcard i {
            margin-left: 8px;
        }

        /* Responsive adjustments */
        @media (max-width: 1024px) {
            .btn-applepay, .btn-savedcard {
                max-width: 400px;
            }
        }

        @media (max-width: 768px) {
            .btn-applepay, .btn-savedcard {
                max-width: 300px;
            }

            .btn-applepay {
                font-size: 16px;
            }

            .btn-savedcard {
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .btn-applepay, .btn-savedcard {
                max-width: 250px;
            }

            .btn-applepay {
                font-size: 14px;
            }

            .btn-savedcard {
                font-size: 12px;
            }

            .back-button {
                width: 30px;
                height: 30px;
            }

            .back-arrow {
                left: 9px;
                top: 12px;
                width: 4px;
                height: 8px;
            }

            .product-image {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Back Button -->
    <!-- Product Image -->
    <img class="product-image" src="https://makank.s3.amazonaws.com/settings/February2023/UhjGYaV497tQjOtqCNx8.jpg" alt="Product Image">
    <br>
    <br>
    <!-- Purchase Label -->
    <div class="purchase-label" style="float: right;">: الشراء بـ</div>
    <br>
    <br>
    <br>
    @if($device_type == 'ios')
        <a href="{{ url('/applepay/payment/' . $id . '/' . $code . '/' . $userid . '/' . $payment_id )}}" class="btn btn-applepay">
            <i class="fab fa-apple"></i> Pay
        </a>
    @endif


    <a href="{{ url('/send/payment/' . $id . '/' . $code . '/' . $userid . '/' . $payment_id . '/' . 1) }}" class="btn btn-savedcard">
        <i class="fas fa-credit-card"></i> الدفع بـ بطاقة محفوظة
    </a>

    <a href="{{ url('/send/payment/' . $id . '/' . $code . '/' . $userid . '/' . $payment_id . '/' . 0) }}" class="btn btn-savedcard">
        <i class="fas fa-plus"></i> إضافة بطاقة جديدة
    </a>
</div>
</body>
</html>
