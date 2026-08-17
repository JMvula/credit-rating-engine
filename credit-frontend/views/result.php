<?php
// views/result.php

$result = $viewData['result'] ?? null;
$error  = $viewData['error'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars(APP_NAME) ?> - Result</title>

    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="result-page">

<div class="result-card">

    <!-- =========================
         LEFT BRANDING PANEL
         ========================= -->

    <div class="result-brand-panel">

        <!--
            ================================
            HOBBITON LOGO
            ================================

            Put your logo here.

            Example:
            credit-frontend/
            ├── images/
            │   └── hobbiton-logo.png

            If your logo has a different filename,
            change the src below.
        -->

        <img
            src="images/hobLogo-82HXwzio.png"
            alt="Hobbiton Logo"
            class="result-logo"
        >

        <h2>Credit Rating Engine</h2>

        <p>Smart credit risk assessment</p>

    </div>


    <!-- =========================
         RIGHT RESULTS PANEL
         ========================= -->

    <div class="result-content">

        <?php if ($error): ?>

            <!-- ERROR -->

            <div class="result-error">

                <h1>Something went wrong</h1>

                <p>
                    <?= htmlspecialchars($error) ?>
                </p>

                <a href="index.php" class="result-button">
                    Back to Form
                </a>

            </div>


        <?php elseif ($result): ?>

            <!-- SUCCESS -->

            <div class="result-header">

                <h1>Credit Assessment</h1>

                <p>
                    Applicant credit risk assessment result
                </p>

            </div>


            <!-- =========================
                 RISK RESULT
                 ========================= -->

            <?php
                $risk = strtolower($result['risk'] ?? 'unknown');

                // Convert "very high" into a CSS-friendly class
                $riskClass = str_replace(' ', '-', $risk);
            ?>

            <div class="risk-section">

                <p class="risk-label">
                    Predicted Risk
                </p>

                <div class="risk-badge risk-<?= htmlspecialchars($riskClass) ?>">
                    <?= htmlspecialchars(ucwords($risk)) ?>
                </div>

            </div>


            <!-- =========================
                 PROBABILITIES
                 ========================= -->

            <div class="probability-section">

                <h2>Risk Probabilities</h2>

                <?php foreach ($result['probabilities'] as $label => $prob): ?>

                    <?php
                        $percentage = $prob * 100;
                        $probabilityClass = str_replace(' ', '-', strtolower($label));
                    ?>

                    <div class="probability-row">

                        <div class="probability-info">

                            <span>
                                <?= htmlspecialchars(ucwords($label)) ?>
                            </span>

                            <span>
                                <?= round($percentage, 1) ?>%
                            </span>

                        </div>

                        <div class="probability-bar">

                            <div
                                class="probability-fill probability-<?= htmlspecialchars($probabilityClass) ?>"
                                style="width: <?= min(100, max(0, $percentage)) ?>%;"
                            ></div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>


            <!-- =========================
                 MODEL INFORMATION
                 ========================= -->

            <div class="model-info">

                <span>Model Version</span>

                <strong>
                    <?= htmlspecialchars($result['model_version'] ?? 'Unknown') ?>
                </strong>

            </div>


            <!-- =========================
                 BACK BUTTON
                 ========================= -->

            <a href="index.php" class="result-button">
                Assess Another Applicant
            </a>


        <?php else: ?>

            <!-- NO RESULT -->

            <div class="result-error">

                <h1>No Result Available</h1>

                <p>
                    No credit assessment result was returned.
                </p>

                <a href="index.php" class="result-button">
                    Back to Form
                </a>

            </div>

        <?php endif; ?>

    </div>

</div>

</body>
</html>