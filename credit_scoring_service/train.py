#2.3.6 — `train.py` — the script that ties it all together**

#This lives outside the package, at the project root. It's the thing you actually *run* to produce a trained model file.

"""
train.py
Run this once (and again any time you retrain) to produce the
model artifacts that Flask will load.

    python train.py
"""

import pandas as pd

from credit_scoring import config
from credit_scoring import features as feat
from credit_scoring import labels as lbl
from credit_scoring import model as mdl


def load_raw_data() -> pd.DataFrame: #this is used just to tell someone who might read this code that we return a df from this function
#this function basically loads the csv, we then put it in a varable in the next function ie its loke df=pd.read_csv(bnpl) without the df part
    """
    Swap this out for your real DB connection later
    (same query as notebook section 3). For now, load from a CSV export.
    """

    raw = pd.read_csv("data/bnpl_raw_export.csv")
    for col in ("TransactionDate", "MaturityDate", "RepaymentDate"):
        raw[col] = pd.to_datetime(raw[col], format="ISO8601", utc=True)
    return raw



def main():
    print("1. Loading raw data...")
    raw = load_raw_data()


    print("2. Building features...")
    features = feat.build_member_features(raw)#we call the features.py note: inside it has the function build_member_features, feed it the csv

    print("3. Applying rules-based labels...")
    features["risk_points"] = features.apply(lbl.calculate_risk_points, axis=1) #note we now call label.py(lbl)to predict the risk. features is the new df, we add to columns to it as we predict risk ie risk_points and risk.
    features["Risk"] = features["risk_points"].apply(lbl.points_to_risk)

    print("4. Training model...")
    model, label_encoder = mdl.train_model(features, features["Risk"])#we call the model.py which has the train model funtion in it(we give it the features and risk levels) we return the model and label encoder

    print("5. Saving model artifacts...")
    mdl.save_model(model, label_encoder)
#we then savw the mode and label encoder
    print("Done. Model saved to:", config.MODEL_PATH)


if __name__ == "__main__":
    main()