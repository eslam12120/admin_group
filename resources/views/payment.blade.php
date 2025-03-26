<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قم بالدفع الان </title>
    <style>

        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: white;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .container h1 {
            text-align: center;
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            width: 100px;
        }

        .back-link {
            background-color:  #FBD252;  /* Yellow color */
            color: white;               /* White text */
            padding: 15px 32px;         /* Larger padding to match 'ادفع الآن' button */
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 18px;            /* Larger font size for better visibility */
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
            border: none;               /* No border */
            width: 100%;                /* Make button width responsive */
            box-sizing: border-box;      /* Smooth transition for hover effects */
        }

        /* Smaller devices (phones, 600px and down) */
        @media only screen and (max-width: 600px) {
            .back-link {
                padding: 5px 10px;
                font-size: 14px;
            }
        }

        /* Medium devices (tablets, 768px and up) */
        @media only screen and (min-width: 601px) and (max-width: 768px) {
            .back-link {
                padding: 5px 10px;
                font-size: 15px;
            }
        }

        /* Large devices (desktops, 992px and up) */
        @media only screen and (min-width: 769px) {
            .back-link {
                padding: 5px 10px;;
                font-size: 16px;
            }
        }
        .saved-cards {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .saved-cards h3 {
            font-size: 18px;
            margin-bottom: 15px;
            color: #555;
        }

        .card-option {
            background-color: white; /* Default background */
            border: 2px solid #FFBE00; /* Border color */
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            position: relative;
        }

        /* Styles for the radio button */
        .card-option input[type="radio"] {
            display: none; /* Hide the radio button circle */
        }

        /* Styles when the card option is selected */
        .card-option.selected {
            background-color: #FFFAEB; /* Highlight background when selected */
        }

        /* Ensure the label takes full width for the background color */
        .card-option label {
            flex: 1;
            display: block;
            padding: 10px;
            margin: -10px;
            padding-left: 35px; /* Adjust for radio button space */
        }
        .cvv-input {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
            display: none;
            align-items: center;
            justify-content: space-between;
        }
        .btn-back {
            background-color: #6c757d;
        }
        .cvv-input label {
            font-size: 18px;
            color: #333;
            margin-right: 10px;
            text-align: right;
        }

        .cvv-input input[type="text"] {
            width: 250px;
            height: 40px;
            border: 2px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            padding: 0 10px;
        }

        .new-card h2 {
            text-align: right;
            color: #333;
            font-size: 20px;
            margin-bottom: 20px;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 5px;
            display: none; /* Hidden by default */
        }

        button {
            width: 100%;
            padding: 12px;
            background-color:  #FBD252;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #e6b800;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .container {
                width: 90%;
                padding: 15px;
            }

            .container h1 {
                font-size: 20px;
            }

            .saved-cards h3 {
                font-size: 16px;
            }

            .cvv-input label {
                font-size: 18px;
            }

            button {
                padding: 10px;
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .container {
                width: 95%;
                padding: 10px;
            }

            .container h1 {
                font-size: 18px;
            }

            .saved-cards h3 {
                font-size: 14px;
            }

            .cvv-input label {
                font-size: 16px;
            }

            button {
                padding: 8px;
                font-size: 12px;
            }

            .logo img {
                width: 80px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="logo">
        <img src="https://makank.s3.amazonaws.com/settings/February2023/UhjGYaV497tQjOtqCNx8.jpg" alt="Logo"> <!-- Replace with actual logo path -->
    </div>
    <div class="new-card">
        <h2>    : الشراء بـ     </h2>
        <div id="card-element"></div>
    </div>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <button  id="payButton" onclick="submitPayment()">تنفيذ الطلب   </button>
    <a href="javascript:history.back()" class="back-link">رجوع</a>

</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.payment/3.0.0/jquery.payment.min.js"></script>
<script src="https://sa.myfatoorah.com/cardview/v2/session.js"></script>
<script>
    var lastSelectedRadio = null;
    var config = {
        countryCode: "{{ $data->CountryCode }}",
        sessionId: "{{ $data->SessionId }}",
        cardViewId: "card-element",
        name: "{{$user->name}}",
        email: "{{$user->phone }}",
        order_id: "{{ $order->id }}",
        price: "{{ $order->price }}",
        kind: "{{ $payment->kind }}",
        onCardBinChanged: handleBinChanges,
        style: {
            hideCardIcons: false,
            direction: "ltr",
            cardHeight: 130,
            input: {
                color: "black",
                fontSize: "13px",
                fontFamily: "sans-serif",
                inputHeight: "32px",
                borderColor: "c7c7c7",
                borderWidth: "1px",
                borderRadius: "8px",
            }
        },
    };
    myFatoorah.init(config);
    $(document).ready(function () {
        // Format card number, expiry, and CVV inputs
        $('input[name="cardNumber"]').payment('formatCardNumber');
        $('input[name="expiry"]').payment('formatCardExpiry');
        $('input[name="cvv"]').payment('formatCardCVC');

        // Monitor inputs in the new card section
        $('#card-element input').on('input', function () {
            var isAnyNewCardInputFilled = Array.from($('#card-element input')).some(input => input.value.trim() !== '');

            if (isAnyNewCardInputFilled) {
                // Disable all saved card radio buttons if any new card input is filled
                $('input[name="token"]').prop('checked', false).prop('disabled', true);
                showCvvInput(false);  // Hide CVV input since no saved card is selected
            } else {
                // Enable saved card radio buttons if no new card input is filled
                $('input[name="token"]').prop('disabled', false);
            }
        });
    });
    function submitPayment() {
        var cardView = document.getElementById('card-element');
        var payButton = document.getElementById('payButton');
        payButton.disabled = true;  // Disable the button
        payButton.innerHTML = 'جاري الدفع ...';
        if (cardView.value === '') {
            alert('يرجى إدخال بيانات البطاقة الجديدة.');
            payButton.disabled = false;  // Re-enable the button
            payButton.innerHTML = 'ادفع الان';  // Revert button text
            return;  // Stop the form submission
        }
        // Process new card
        myFatoorah.submit()
            .then(function (response) {
                var sessionId = response.sessionId;
                var cardBrand = response.cardBrand;
                window.location.href = "{{ route('payment.execute') }}" +
                    "?session_id=" + sessionId +
                    "&invoice_value=" + encodeURIComponent(config.price) +
                    "&cardBrand=" + encodeURIComponent(cardBrand) +
                    "&name=" + encodeURIComponent(config.name) +
                    "&email=" + encodeURIComponent(config.email) +
                    "&kind=" + encodeURIComponent(config.kind) +
                    "&order_id=" + encodeURIComponent(config.order_id);
            })
            .catch(function (error) {
                console.log('Payment failed:', error);
                payButton.disabled = false;  // Re-enable the button if payment fails
                payButton.innerHTML = 'ادفع الان';  // Revert button text
            });
    }
    function handleBinChanges(bin) {
        console.log(bin);
    }
</script>
</body>
</html>
