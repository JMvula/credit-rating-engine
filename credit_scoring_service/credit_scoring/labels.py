#the rules engine from notebook section 5**


"""
labels.py
The rules-based risk scoring engine (notebook section 5).
This is what the model is currently being trained to imitate.
"""


def calculate_risk_points(row) -> int:
    points = 0

    # Repayment timeliness
    if row["avg_days_late"] <= 0:
        points += 0
    elif row["avg_days_late"] <= 7:
        points += 1
    elif row["avg_days_late"] <= 30:
        points += 2
    else:
        points += 4

    # Currently overdue loans
    if row["pct_overdue"] == 0:
        points += 0
    elif row["pct_overdue"] < 1 / 3:
        points += 1
    elif row["pct_overdue"] < 2 / 3:
        points += 2
    else:
        points += 3

    # Loan history depth
    if row["loan_count"] >= 20:
        points -= 1
    elif row["loan_count"] <= 3:
        points += 1

    # Loan size / exposure
    if row["avg_loan_amount"] >= 5000:
        points += 1

    return points


def points_to_risk(points: int) -> str:
    if points <= 0:
        return "low"
    elif points <= 2:
        return "medium"
    elif points <= 4:
        return "high"
    else:
        return "very high"