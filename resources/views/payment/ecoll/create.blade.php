<h1>Make a Payment</h1>
<form action="{{ route('ecoll.payment.redirect') }}" method="POST">
    @csrf
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" value="{{ old('name') }}" required>
    <br>
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
    <br>
    <label for="amount">Amount (SGD):</label>
    <input type="text" id="amount" name="amount" value="{{ old('amount', '10.50') }}" required>
    <br>
    <button type="submit">Pay Now</button>
</form>
