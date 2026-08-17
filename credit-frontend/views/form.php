<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= APP_NAME ?></title>

    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/styles.css">

</head>


<body>

    <div class="page-container">


        <!-- ==================================================
             LEFT SIDE
             ================================================== -->

        <div class="image-panel">

            <!-- ==================================================
                 HOBBITON LOGO

                 CHANGE THIS FILE IF YOUR LOGO HAS A DIFFERENT NAME

                 Current expected location:
                 images/logo.png
                 ================================================== -->

            <img
                src="images/hobLogo-82HXwzio.png"
                alt="Hobbiton Logo"
                class="hobbiton-logo"
            >


            <!-- Text underneath the logo -->

            <div class="brand-text">

                <h2>Credit Rating Engine</h2>

                <p>Smart credit risk assessment</p>

            </div>

        </div>


        <!-- ==================================================
             RIGHT SIDE - GLASS FORM
             ================================================== -->

        <div class="form-panel">


            <h1><?= APP_NAME ?></h1>

            <p class="subtitle">
                Enter applicant information to assess credit risk.
            </p>


            <!-- Error message -->

            <?php if (!empty($viewData['error'])): ?>

                <div class="error-message">

                    <strong>Error:</strong>

                    <?= htmlspecialchars($viewData['error']) ?>

                </div>

            <?php endif; ?>


            <!-- ==================================================
                 CREDIT SCORING FORM
                 ================================================== -->

            <form method="POST" action="index.php">

                <div class="form-grid">


                    <!-- Average Loan Amount -->

                    <div class="form-group">

                        <label for="avg_loan_amount">
                            Average Loan Amount
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="avg_loan_amount"
                            id="avg_loan_amount"
                            placeholder="e.g. 1500"
                            required
                        >

                    </div>


                    <!-- Average Installments -->

                    <div class="form-group">

                        <label for="avg_installments">
                            Average Installments
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="avg_installments"
                            id="avg_installments"
                            placeholder="e.g. 1"
                            required
                        >

                    </div>


                    <!-- Loan Count -->

                    <div class="form-group">

                        <label for="loan_count">
                            Loan Count
                        </label>

                        <input
                            type="number"
                            step="1"
                            name="loan_count"
                            id="loan_count"
                            placeholder="e.g. 3"
                            required
                        >

                    </div>


                    <!-- Average Days Late -->

                    <div class="form-group">

                        <label for="avg_days_late">
                            Average Days Late
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="avg_days_late"
                            id="avg_days_late"
                            placeholder="e.g. 1"
                            required
                        >

                    </div>


                    <!-- Maximum Days Late -->

                    <div class="form-group">

                        <label for="max_days_late">
                            Maximum Days Late
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="max_days_late"
                            id="max_days_late"
                            placeholder="e.g. 4"
                            required
                        >

                    </div>


                    <!-- Percentage Overdue -->

                    <div class="form-group">

                        <label for="pct_overdue">
                            Percentage Overdue
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            max="1"
                            name="pct_overdue"
                            id="pct_overdue"
                            placeholder="0.0 - 1.0"
                            required
                        >

                    </div>


                </div>


                <!-- Submit button -->

                <button type="submit">
                    Get Credit Score
                </button>


            </form>

        </div>

    </div>

</body>

</html>