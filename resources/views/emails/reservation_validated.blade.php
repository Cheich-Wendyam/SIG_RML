<!DOCTYPE html>
<html>
<head>
    <title>Confirmation de votre réservation</title>
</head>
<body>
    <h1>Bonjour,</h1>
    <p>Votre réservation (Code: {{ $reservation->code }}) a été validée avec succès.</p>
    <p>Détails de la réservation :</p>
    <ul>
        <li>Date de début : {{ $reservation->date_debut }}</li>
        <li>Date de fin : {{ $reservation->date_fin }}</li>
        <li>Équipement : {{ $reservation->equipement->nom }}</li>
    </ul>
    <p>Veuillez contacter le responsable de l'Équipement pour entrer en posses de l'Équipement: {{ $reservation->equipement->laboratoire->responsable->phone }} ou {{ $reservation->equipement->laboratoire->responsable->email }}</p>
    <p>Merci de votre confiance !</p>
</body>
</html>
