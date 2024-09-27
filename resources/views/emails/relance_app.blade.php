<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Vos informations de connexion</title>
</head>
<body>
    <h2>Bonjour,</h2>

    <p>Vous trouverez ci-dessous vos informations de connexion à notre système :</p>

    <p><strong>Matricule :</strong> {{ $matricule }}</p>
    <p><strong>Email :</strong> {{ $email }}</p>
    <p><strong>Mot de passe par défaut :</strong> {{ $password }}</p>

    <p>Vous pouvez vous connecter en cliquant sur le lien suivant :</p>
    <a href="{{ $loginLink }}">Se connecter</a>

    @if (!empty($qrcode))
        <p>Veuillez utiliser le QR code ci-dessous pour accéder à certaines fonctionnalités :</p>
        <img src="data:image/png;base64,{{ base64_encode($qrcode) }}" alt="QR Code">
    @else
        <p>Le QR code n'est pas disponible pour le moment.</p>
    @endif

    <p>Merci,</p>
    <p>L'équipe de gestion pédagogique</p>
</body>
</html>
