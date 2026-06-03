<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Rapport de Transactions - FinTrack</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #0b1c30;
            font-size: 13px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            margin-bottom: 30px;
            border-bottom: 2px solid #00288e;
            padding-bottom: 15px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #00288e;
        }
        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 5px;
            color: #444653;
        }
        .meta-info {
            margin-top: 15px;
            font-size: 11px;
            color: #757684;
        }
        .summary-box {
            background-color: #f8f9ff;
            border: 1px solid #e5eeff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 30px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 4px 8px;
        }
        .summary-label {
            color: #444653;
            font-weight: 500;
        }
        .summary-val {
            font-weight: bold;
            text-align: right;
        }
        .text-green {
            color: #006c49;
        }
        .text-red {
            color: #ba1a1a;
        }
        .text-blue {
            color: #00288e;
        }
        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .transactions-table th {
            background-color: #00288e;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            font-size: 12px;
            text-transform: uppercase;
        }
        .transactions-table td {
            padding: 10px;
            border-bottom: 1px solid #e5eeff;
        }
        .transactions-table tr:nth-child(even) {
            background-color: #f8f9ff;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #c4c5d5;
            border-top: 1px solid #e5eeff;
            padding-top: 10px;
        }
        .amount {
            text-align: right;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <div class="logo">FinTrack</div>
                    <div class="report-title">Rapport de Transactions</div>
                </td>
                <td style="text-align: right; vertical-align: bottom;">
                    <div class="meta-info">
                        <strong>Utilisateur :</strong> {{ $user->name }}<br>
                        <strong>Période :</strong> {{ ucfirst($mois) }} {{ $annee }}<br>
                        <strong>Exporté le :</strong> {{ now()->format('d/m/Y H:i') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="summary-box">
        <table class="summary-table">
            <tr>
                <td class="summary-label">Total Revenus :</td>
                <td class="summary-val text-green">+{{ number_format($totalRevenus, 2, ',', ' ') }} FCFA</td>
                <td class="summary-label" style="padding-left: 40px;">Solde Net Période :</td>
                <td class="summary-val text-blue" style="font-size: 15px;">
                    {{ number_format($totalRevenus - $totalDepenses, 2, ',', ' ') }} FCFA
                </td>
            </tr>
            <tr>
                <td class="summary-label">Total Dépenses :</td>
                <td class="summary-val text-red">-{{ number_format($totalDepenses, 2, ',', ' ') }} FCFA</td>
                <td class="summary-label" style="padding-left: 40px;">Filtré sur :</td>
                <td class="summary-val" style="color: #757684;">{{ $typeLabel ?? 'Toutes' }}</td>
            </tr>
        </table>
    </div>

    <table class="transactions-table">
        <thead>
            <tr>
                <th style="width: 15%;">Date</th>
                <th style="width: 20%;">Type</th>
                <th style="width: 25%;">Catégorie</th>
                <th style="width: 25%;">Note / Description</th>
                <th style="width: 15%; text-align: right;">Montant</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $t)
            <tr>
                <td>{{ $t->date_transaction->format('d/m/Y') }}</td>
                <td>
                    <span class="{{ $t->type->isDebit() ? 'text-red' : 'text-green' }}">
                        {{ $t->type->label() }}
                    </span>
                </td>
                <td>{{ $t->categorie }}</td>
                <td>{{ $t->note ?? '-' }}</td>
                <td class="amount {{ $t->type->isDebit() ? 'text-red' : 'text-green' }}">
                    {{ $t->type->isDebit() ? '-' : '+' }}{{ number_format($t->montant, 2, ',', ' ') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 20px; color: #757684;">
                    Aucune transaction trouvée pour cette période.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        FinTrack - Votre Gestionnaire Financier Intelligent &copy; {{ date('Y') }}
    </div>

</body>
</html>
