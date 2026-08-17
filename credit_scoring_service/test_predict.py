from credit_scoring import model as mdl

result = mdl.predict_one({
    "avg_loan_amount": 1500,
    "avg_installments": 1,
    "loan_count": 3,
    "avg_days_late": 1,
    "max_days_late": 4,
    "pct_overdue": 0.0,
})

print(result)