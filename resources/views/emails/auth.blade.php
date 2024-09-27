<!DOCTYPE html>
<html>
<head>
    <title>Lien d'authentification</title>
</head>
<body>
    <h1>Ecole du Code Sonatel Academy,</h1>
    <h1>Bonjour {{ $prenom }} {{ $nom }},</h1>
    <p>Votre compte a été créé avec succès !</p>
    <p>Voici vos informations de connexion :</p>
    <p><strong>Email :</strong> {{ $email }}</p>
    <p><strong>Mot de passe :</strong> {{ $password }}</p>
    <p>Merci de vous connecter et de changer votre mot de passe dès que possible.</p>
    <p>Voici votre QR code :</p>
    <img src="{{ $qrCodePath }}" alt="QR Code">
    <p>Pour vous connecter, veuillez suivre ce lien : <a href="{{ url('/login') }}">Connexion</a></p>
    <p>Si vous avez besoin d'aide, n'hésitez pas à contacter le support technique.</p>
</body>
</html>
