

#2.3.3 — `features.py` — rebuilding notebook sections 4.1–4.5**

#This is the exact logic from your notebook's grouping/aggregation steps, just moved into a reusable function instead of scattered cells.

"""
features.py
Turns raw loan/repayment rows into one row per member
(the same logic as notebook sections 4.1-4.5).
"""

import pandas as pd


def build_member_features(bnpl: pd.DataFrame) -> pd.DataFrame:
    """
    Input:  raw loan-level dataframe, same shape as `bnpl` in the notebook
            (one row per loan; repayment columns may be NaN).
    Output: one row per MemberId with aggregated features.
    """
    # 1. Collapse multiple repayments per loan into one summary row per loan
    agg_repayments = bnpl.groupby("Id").agg(
        TotalRepaid=("RepaymentAmount", "sum"),
        NumberOfInstallments=("RepaymentAmount", "count"),
        MostRecentRepaymentDate=("RepaymentDate", "max"),
    ).reset_index()

    bnpl_summary = (
        bnpl.drop(["RepaymentAmount", "RepaymentDate", "RepaymentType"], axis=1)
            .drop_duplicates(subset="Id")
            .merge(agg_repayments, on="Id", how="left")
    )

    # 2. How many loans has each member ever taken?
    bnpl_summary["Loan_count"] = bnpl.groupby("MemberId")["Id"].transform("count")

    # 3. Drop loans with no repayment history yet (can't compute lateness)
    with_repayment = bnpl_summary[bnpl_summary["MostRecentRepaymentDate"].notnull()].copy()

    # 4. Days late (negative = paid early)
    repayment_dt = pd.to_datetime(
        with_repayment["MostRecentRepaymentDate"], format="ISO8601", utc=True
    )
    maturity_dt = pd.to_datetime(
        with_repayment["MaturityDate"], format="ISO8601", utc=True
    )
    with_repayment["DaysLate"] = (repayment_dt - maturity_dt).dt.total_seconds() / 86400

    # 5. Aggregate to one row per member
    features = with_repayment.groupby("MemberId").agg(
        avg_loan_amount=("LoanAmount", "mean"),
        avg_installments=("NumberOfInstallments", "mean"),
        loan_count=("Loan_count", "max"),
        avg_days_late=("DaysLate", "mean"),
        max_days_late=("DaysLate", "max"),
        pct_overdue=("LoanStatus", lambda s: (s == "overdue").mean()),
    ).reset_index()

    return features

# This is literally your notebook's cells 4.1 through 4.5, unchanged in logic — just wrapped in a function called build_member_features()` so it can be called from anywhere instead of run cell-by-cell.

