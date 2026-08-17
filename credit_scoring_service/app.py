### 3.4 Building `app.py`, point by point

##3.4.1 — Imports and creating the app**

from time import time

from flask import Flask, request, jsonify #flask is the app, requests is where our resquests from clients will land note when we say requests.json they are converted to python, jsonify will be to convert python dict to json

from credit_scoring import config
from credit_scoring import model as mdl

app = Flask(__name__) #this creates the web app. Note flask(__name__) is a constructor that takes the name of our current file in ur case app.py. We construct the app object from the Flask class. This is the main entry point for our application. It will handle incoming requests and route them to the appropriate functions. its similar to index.php, everything first comes here and we then route it to the right function.

##3.4.2 — Load the model ONCE at startup**

##This is important. Loading a model from disk takes time — you do **not** want to do it on every single request. Load it once, when the server starts, and keep it in memory.

MODEL, LABEL_ENCODER = mdl.load_model()

##3.4.3 — The `/v1/predict` route**

#my php api will call this function
#ie Frontend ->PHP API -> Flask (/v1/predict) -> predict_one() -> XGBoost -> JSON Response

@app.route("/v1/predict", methods=["POST"]) #When someone sends a POST request to /v1/predict, run the function below

def predict():
    body = request.get_json(silent=True) # remeber request.get_json in flask gets the incoming request, and converts it into a python dict, we then store this in body. now is the user didnt sent json insted of causing an error we return none by turning silent on

    if body is None:
        return jsonify({"error": "Request body must be valid JSON"}), 400 #we return an error that the user didnt send json, not we send ths message to the php api and front end part in json

    # Validate against the SAME feature list config.py defines.
    # This is where the extensibility pays off: add a field to
    # FEATURE_COLUMNS later, and this check updates itself.
    missing = [f for f in config.FEATURE_COLUMNS if f not in body]
    if missing:
        return jsonify({"error": f"Missing required fields: {missing}"}), 400 #check for missing feature

    try:
        result = mdl.predict_one(body, model=MODEL, label_encoder=LABEL_ENCODER) #if no missing data try to predict
    except Exception as e:
        return jsonify({"error": f"Prediction failed: {str(e)}"}), 500 #else prediction failed, server side issue

    return jsonify(result), 200 #else return prediction in json

#This is a simple function php api uses to check if the model still good and the entire pyhton serice before sending actual requst
@app.route("/v1/health", methods=["GET"])
def health():
    return jsonify({"status": "ok", "model_version": config.MODEL_VERSION}), 200

#Run the server**
#"If this file is being run directly, start the Flask web server.
#note in python when a file runs itself in the terminal its name becomes main therefore we will have main=main then run this file, however if this file was imported by another file its name becomes app therefore app= main therefore flase so this file wont run
if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000, debug=True)#0000 means a pc on thesame network as mine can use this,debug is true gives detailed info of what goes wrong but turn off in final version
