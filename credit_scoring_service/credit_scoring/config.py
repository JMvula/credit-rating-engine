import os #we import os to interact with the operating system and get file paths

# --- Paths ---
BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__))) #__file__ gives the path of the current file ie C:\Users\Joshua\Desktop\credit_scoring_service\credit_scoring\config.py. os.path.abspath(__file__) makes sure its the complete path not just config.py. os.path.dirname(...) removes file name ie we remove config.py from path. os.path.dirname removes credit_scoring. therefore we have the base directory.
MODEL_DIR = os.path.join(BASE_DIR, "models") #we join the base directory with models ie C:\Users\Joshua\Desktop\credit_scoring_service\models. This is where we will store our trained model and encoder.
MODEL_PATH = os.path.join(MODEL_DIR, "risk_model_v1.joblib") # this is the path for the trained model.
ENCODER_PATH = os.path.join(MODEL_DIR, "label_encoder_v1.joblib") #path for the label encoder. We will use this to encode categorical variables before making predictions.

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

#This is your extensibility mechanism.** When you're ready to add `employment_type`, you add one line to `FEATURE_COLUMNS`, teach `features.py` how to build it, and retrain. Nothing else in the system needs to be rewritten — you'll see exactly why once Flask is built in Step 2.