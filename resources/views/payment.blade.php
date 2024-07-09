<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Payment</title>
    <script>
        async function processPayment() {
            const amount = document.getElementById('amount').value;
            const response = await fetch('api/process-payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ amount: amount }),
            });
            const data = await response.json();
            if (response.status === 200) {
                document.getElementById('qr-code').src = data.qr;
            } else {
                alert(data.message || 'Something went wrong');
            }
        }
    </script>
</head>
<body>
    <h1>QR Code Payment</h1>
    <form onsubmit="event.preventDefault(); processPayment();">
        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" required>
        <button type="submit">Pay</button>
    </form>
    <div id="qr-code-container">
        <img id="qr-code" src="" alt="QR Code will be displayed here" />
    </div>
</body>
</html>
