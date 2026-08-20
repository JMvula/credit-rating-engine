#`model.py` — the only file that touches XGBoost**

#Everything else (Flask, later PHP) talks to the model *through* this file's functions. Nothing outside `model.py` needs to know XGBoost exists.


"""
model.py
This file basically does 5 things
1. Import model
2. Train model
3. Save Model
4. load model
5. predict 


"""

import os
import joblib #used for saving the model once we train
import pandas as pd
from sklearn.preprocessing import LabelEncoder #used to convert labels into nos eg low =0, mideum=1 etc since model only works with nos
from xgboost import XGBClassifier

from . import config #note a python package is a folder with only python files, therefore we are just importing this file from the same python folder ie config.py


def train_model(features: pd.DataFrame, labels: pd.Series):
    #This function recives features and labels eg days late/ high risk
    """Trains and returns (model, label_encoder)."""
    label_encoder = LabelEncoder() #create a label incoder
    label_encoder.fit(config.LABEL_ORDER) # we get the labels from the config file and give them nos based on how the list comes

    y_encoded = label_encoder.transform(labels) #we now apply the nos we just made at fit to the labels passed in to the function
    X = features[config.FEATURE_COLUMNS] #we get the features from the config file vs the features from the function, only pick matches

    model = XGBClassifier( #create the model
        n_estimators=100, #build 100 discision trees
        max_depth=3, # 3 levels deep
        learning_rate=0.1,
        random_state=42, #make results replicable
        eval_metric="mlogloss", #Multiclass Logarithmic Loss                                                                                                                                                                                                                                                                                                                                                                                                        
    )
    model.fit(X, y_encoded)

    return model, label_encoder


def save_model(model, label_encoder): #we save the model and encoder from train
    os.makedirs(config.MODEL_DIR, exist_ok=True) #creates models folder
    joblib.dump(model, config.MODEL_PATH) #saves the model in the models folder 
    joblib.dump(label_encoder, config.ENCODER_PATH)# saves the encoder in models as label_encoder_v1.joblib. remeber the path from config.py


def load_model():
    model = joblib.load(config.MODEL_PATH) #loaf the model and encoder from mdels
    label_encoder = joblib.load(config.ENCODER_PATH)
    return model, label_encoder


def predict_one(raw_input: dict, model=None, label_encoder=None) -> dict:
    """
    raw_input: a dictionary of applicant data (keys must match config.FEATURE_COLUMNS).
    model: optional, the trained XGBoost model (if not provided, it will be loaded).
    label_encoder: optional, the fitted encoder (if not provided, it will be loaded).

Returns a dictionary with the risk prediction, probabilities, and model version


    raw_input: dict with the keys in config.FEATURE_COLUMNS,
               e.g. {"avg_loan_amount": 1500, "avg_installments": 1, ...}
    Returns:   {"risk": "medium", "probabilities": {...}, "model_version": "v1"}
    """
    if model is None or label_encoder is None:
        model, label_encoder = load_model() #if no model was passed load one

    # Build a single-row dataframe, columns in the EXACT order the model expects
    row = pd.DataFrame([raw_input])[config.FEATURE_COLUMNS]# make df from inpu tusing config colums as guide notee thdf just has one row since this is a new client we try to predict

    pred_encoded = model.predict(row)[0] #returns no rg 0 for low risk
    risk_label = label_encoder.inverse_transform([pred_encoded])[0] # gives no meaning eg low 0= low risk

    proba = model.predict_proba(row)[0]
    probabilities = {
        label: float(p) for label, p in zip(label_encoder.classes_, proba)
    }#returns a probability of eah class eg if you are low it also shows the prob of all other cclasses

    return {
        "risk": str(risk_label),
        "probabilities": probabilities,
        "model_version": config.MODEL_VERSION,
    }


# Notice `predict_one()` takes a **plain Python dict** in and returns a **plain Python dict** out. That's deliberate — it's the exact shape that will slot into a Flask JSON request/response with almost no extra code