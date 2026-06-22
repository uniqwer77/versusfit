<!DOCTYPE html>
<html>
<head>
    <title>Authorize</title>
</head>
<body>

<h1>Authorize Application</h1>

<p>{{ $client->name }}</p>

<form method="POST" action="{{ route('passport.authorizations.approve') }}">
    @csrf
    <input type="hidden" name="state" value="{{ $request->state }}">
    <input type="hidden" name="auth_token" value="{{ $authToken }}">
    <button type="submit">Approve</button>
</form>

<form method="POST" action="{{ route('passport.authorizations.deny') }}">
    @csrf
    @method('DELETE')
    <input type="hidden" name="state" value="{{ $request->state }}">
    <input type="hidden" name="auth_token" value="{{ $authToken }}">
    <button type="submit">Deny</button>
</form>

</body>
</html>
