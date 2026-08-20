import os 

# --- Paths ---
BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__))) 
MODEL_DIR = os.path.join(BASE_DIR, "models") 
MODEL_PATH = os.path.join(MODEL_DIR, "risk_model_v1.joblib") 
ENCODER_PATH = os.path.join(MODEL_DIR, "label_encoder_v1.joblib") 

#note joblib is a library that allows us to store and load large ml models and encoders efficently.

# --- Feature schema: single source of truth ---
# features.py builds these columns, model.py trains on them,
# and later, Flask validates incoming requests against this exact list.
FEATURE_COLUMNS = [
    "avg_loan_amount",
    "avg_installments",
    "loan_count",
    "avg_days_late",
    "max_days_late",
    "pct_overdue",
]

# --- Risk labels, fixed order so encoding is always consistent ---
LABEL_ORDER = ["low", "medium", "high", "very high"]

MODEL_VERSION = "v1"

#This is our extensibility mechanism.** When time comes to add `employment_type`, we add one line to `FEATURE_COLUMNS`, teach `features.py` how to build it, and retrain. Nothing else in the system needs to be rewritten 