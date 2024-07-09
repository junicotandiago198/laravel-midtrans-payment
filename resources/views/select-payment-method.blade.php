<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Select Payment Method</title>
  @vite('resources/css/app.css')
</head>
    <body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6">Select Payment Method</h1>
        <form id="payment-form" class="space-y-4">
        <div>
            <label for="amount" class="block text-sm font-medium text-gray-700">Amount:</label>
            <input type="number" id="amount" name="amount" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
        <div>
            <label for="payment_type" class="block text-sm font-medium text-gray-700">Payment Type:</label>
            <div class="grid grid-cols-2 gap-4 mt-1">
            <label class="block">
                <input type="radio" name="payment_type" value="gopay" class="hidden peer" required>
                <div class="peer-checked:ring-2 peer-checked:ring-indigo-500 p-3 border border-gray-300 rounded-md shadow-sm">
                <img src="{{ asset('images/payment-methods/gopay.png') }}" alt="GoPay" class="w-16 h-16 mx-auto">
                <span class="block text-center mt-2">GoPay</span>
                </div>
            </label>
            <label class="block">
                <input type="radio" name="payment_type" value="bank_transfer" class="hidden peer" required>
                <div class="peer-checked:ring-2 peer-checked:ring-indigo-500 p-3 border border-gray-300 rounded-md shadow-sm">
                <img src="{{ asset('images/payment-methods/bank-transfer.png') }}" alt="Bank Transfer" class="w-16 h-16 mx-auto">
                <span class="block text-center mt-2">Bank Transfer</span>
                </div>
            </label>
            </div>
        </div>
        <div id="bank-selection" style="display: none;">
            <label for="bank" class="block text-sm font-medium text-gray-700">Bank:</label>
            <select id="bank" name="bank" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            <option value="bni">BNI</option>
            <option value="mandiri">Mandiri</option>
            <option value="bri">BRI</option>
            <option value="bca">BCA</option>
            </select>
        </div>
        <div>
            <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Next</button>
        </div>
        </form>
        <div id="qr-container" class="mt-6" style="display: none;">
        <h2 class="text-xl font-bold mb-4">Scan QR Code to Pay</h2>
        <div id="qr-code" class="flex justify-center"></div>
        </div>
        <div id="va-container" class="mt-6" style="display: none;">
        <h2 class="text-xl font-bold mb-4">Virtual Account Information</h2>
        <div id="va-details" class="flex flex-col items-center"></div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        const paymentForm = document.getElementById('payment-form');
        const paymentTypeInputs = document.querySelectorAll('input[name="payment_type"]');
        const bankSelection = document.getElementById('bank-selection');
        
        paymentTypeInputs.forEach(input => {
            input.addEventListener('change', function() {
            if (this.value === 'bank_transfer') {
                bankSelection.style.display = 'block';
            } else {
                bankSelection.style.display = 'none';
            }
            });
        });

        paymentForm.addEventListener('submit', async function(event) {
            event.preventDefault();

            const formData = new FormData(paymentForm);
            const data = {};
            formData.forEach((value, key) => data[key] = value);

            const response = await fetch('/api/process-payment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
            });

            const result = await response.json();

            if (data.payment_type === 'gopay' && result.qr) {
            const qrContainer = document.getElementById('qr-container');
            const qrCode = document.getElementById('qr-code');
            qrCode.innerHTML = `<img src="${result.qr}" alt="QR Code" class="w-32 h-32">`;
            qrContainer.style.display = 'block';
            } else if (data.payment_type === 'bank_transfer' && result.va_numbers) {
            const vaContainer = document.getElementById('va-container');
            const vaDetails = document.getElementById('va-details');
            vaDetails.innerHTML = result.va_numbers.map(va => `
                <p>Bank: ${va.bank}</p>
                <p>VA Number: ${va.va_number}</p>
            `).join('');
            vaContainer.style.display = 'block';
            }
        });
        });
    </script>
    </body>
</html>
