<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rappel de remboursement - FinTrack</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9ff;
            color: #0b1c30;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(11, 28, 48, 0.05);
            border: 1px border #e5eeff;
        }
        .header {
            background-color: #00288e;
            color: #ffffff;
            padding: 32px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .content {
            padding: 32px;
            line-height: 1.6;
        }
        .details-box {
            background-color: #f0f4ff;
            border-left: 4px solid #00288e;
            padding: 20px;
            border-radius: 8px;
            margin: 24px 0;
        }
        .details-box p {
            margin: 8px 0;
            font-size: 15px;
        }
        .footer {
            background-color: #f8f9ff;
            text-align: center;
            padding: 24px;
            font-size: 12px;
            color: #757684;
            border-t: 1px solid #e5eeff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>FinTrack</h1>
            <p style="margin: 4px 0 0 0; opacity: 0.9;">Rappel de remboursement amical</p>
        </div>
        <div class="content">
            <p>Bonjour <strong>{{ $loan->personne }}</strong>,</p>
            
            <p>Nous vous contactons pour vous transmettre un rappel concernant le prêt financier accordé par <strong>{{ $loan->user->name }}</strong>.</p>
            
            <div class="details-box">
                <p><strong>Détails du prêt :</strong></p>
                <p>• Montant initial : <strong>{{ number_format($loan->montant_initial, 2, ',', ' ') }} FCFA</strong></p>
                <p>• Montant remboursé : <strong style="color: #006c49;">{{ number_format($loan->montant_rembourse, 2, ',', ' ') }} FCFA</strong></p>
                <p>• Reste à payer : <strong style="color: #ba1a1a;">{{ number_format($loan->montant_restant, 2, ',', ' ') }} FCFA</strong></p>
                @if($loan->date_echeance)
                <p>• Date limite convenue : <strong>{{ $loan->date_echeance->format('d/m/Y') }}</strong></p>
                @endif
            </div>

            <p>Merci de bien vouloir régulariser ou contacter <strong>{{ $loan->user->name }}</strong> dès que possible pour finaliser ce remboursement.</p>
            
            <p style="margin-top: 32px;">Cordialement,<br>L'équipe <em>FinTrack</em></p>
        </div>
        <div class="footer">
            Ceci est un message automatisé envoyé de la part de l'utilisateur de FinTrack.
        </div>
    </div>
</body>
</html>
