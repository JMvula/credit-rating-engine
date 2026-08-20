# Hobbition Credit Rating Engine

A credit scoring system developed to predict the credit risk of an individual before a loan is issued.

The system uses a machine learning model trained on historical BNPL (Buy Now, Pay Later) transaction and repayment data. It classifies applicants into four risk categories:

- Low
- Medium
- High
- Very High

The application is divided into three main components: a PHP frontend, a PHP REST API gateway, and a Python credit scoring service.


## System Architecture

The system follows a service-based architecture:

1. Frontend (PHP / HTML)

The user interacts with a simple web form built in PHP and HTML.

Input data (loan amount, installments, etc.) is collected here.

When submitted, the form sends the data as JSON.

2. PHP REST API Gateway

Acts as the middle layer between the frontend and the backend service.

Receives JSON from the frontend.

Uses the ApiClient class to forward requests to the Python service.

Handles errors and ensures clean responses for the frontend.

3. Python Credit Scoring Service (Flask)

Exposes REST endpoints such as /api/score.

Accepts JSON input from the gateway.

Performs feature validation and passes the data to the machine learning model.

Returns predictions in JSON format.

4. XGBoost Model

The trained machine learning model that performs the actual risk scoring.

Takes applicant features and outputs a risk category (e.g., low, medium, high).

Provides probability values for each risk level.

The model version is included in the response for traceability.

5. Data Flow

User fills in the form → Frontend sends JSON → Gateway forwards JSON → Flask service processes → XGBoost model predicts → Flask returns JSON → Gateway relays JSON → Frontend displays result.

**Project Structure**

Hobbition-credit-scoring/
│
├── credit_scoring_service/
│   │
│   ├── credit_scoring/
│   │   ├── __init__.py
│   │   ├── config.py
│   │   ├── features.py
│   │   ├── labels.py
│   │   └── model.py
│   │
│   ├── data/
│   │   └── bnpl_raw_export.csv
│   │
│   ├── models/
│   │   ├── risk_model_v1.joblib
│   │   └── label_encoder_v1.joblib
│   │
│   ├── app.py
│   ├── train.py
│   ├── test_predict.py
│   └── requirements.txt
│
├── credit-gateway/
│   ├── handlers/
│   │   └── score.php
│   ├── .htaccess
│   ├── index.php
│   ├── config.php
│   └── ApiClient.php
│
├── credit-frontend/
│   ├── controllers/
│   │   └── ScoreController.php
│   ├── models/
│   │   └── ScoringGatewayClient.php
│   ├── views/
│   │   ├── form.php
│   │   └── result.php
│   ├── css/
│   ├── index.php
│   ├── config.php
│   └── ApiClient.php
│
├── .gitignore
└── README.md

**Credit Scoring Model**

The current version of the model uses six features:

1. avg_loan_amount
2. avg_installments
3. loan_count
4. avg_days_late
5. max_days_late
6. pct_overdue

These features are derived from the applicant's previous BNPL loan and repayment information.

The current machine learning model uses XGBoost classification.

The model predicts one of four risk categories: low, medium, high, very high. The model also returns the probability associated with each risk category.

**Model Training**

The current training pipeline is implemented in Python.

The general process is:
Raw BNPL Data->Feature Engineering->Risk Labels->XGBoost Training->Trained Model->Joblib Model Artifacts

**The training script is:**

credit_scoring_service/train.py

**The trained model and label encoder are stored in:**

credit_scoring_service/models/

**Python Credit Scoring Service**

The Python service provides the machine learning prediction API.

It is implemented using Flask.

Health Endpoint
Endpoint:
GET /v1/health

**Example response:**

{
    "status": "ok",
    "model_version": "v1"
}

This endpoint can be used to verify that the Python service and trained model are available.

**Prediction API**

Endpoint:
POST /v1/predict
The endpoint accepts applicant information in JSON format.

**Example request:**

{
    "avg_loan_amount": 1500,
    "avg_installments": 1,
    "loan_count": 3,
    "avg_days_late": 1,
    "max_days_late": 4,
    "pct_overdue": 0.0
}

**Example response:**

{
    "risk": "medium",
    "probabilities": {
        "high": 0.03782309219241142,
        "low": 0.019266262650489807,
        "medium": 0.9265684485435486,
        "very high": 0.016342205926775932
    },
    "model_version": "v1"
}

The risk field represents the predicted risk category.
The probabilities object provides the model's probability for each risk category.

**PHP REST API Gateway**

The PHP gateway provides an intermediate API layer between the frontend and Python credit scoring service.
Endpoint:
POST /api/score
The gateway:

Receives applicant data from the frontend.
Validates that the request contains valid JSON.
Sends the applicant data to the Python credit scoring service.
Receives the prediction from Python.
Returns the prediction to the frontend as JSON.

The gateway does not contain the machine learning model.

This separation allows the Python service to remain responsible for the machine learning functionality while PHP handles the API gateway functionality.

**Gateway Example Request**

{
    "avg_loan_amount": 1500,
    "avg_installments": 1,
    "loan_count": 3,
    "avg_days_late": 1,
    "max_days_late": 4,
    "pct_overdue": 0.0
}

**Gateway Example Response**

{
    "success": true,
    "prediction": {
        "risk": "medium",
        "probabilities": {
            "high": 0.03782309219241142,
            "low": 0.019266262650489807,
            "medium": 0.9265684485435486,
            "very high": 0.016342205926775932
        },
        "model_version": "v1"
    }
}

**Frontend**

The frontend provides a web interface for entering applicant information.

The frontend is implemented using PHP and HTML/CSS.
The user enters the applicant's current feature information through the form.

The frontend sends the information to:
POST /api/score

The returned prediction is then displayed to the user, including:
*Predicted risk
*Probability for each risk category
*Model version

**Running the System**

The system currently runs locally using Apache for the PHP components and Flask for the Python credit scoring service.

**1.Python Credit Scoring Service**

Navigate to:

credit_scoring_service/

Activate the Python virtual environment if one is being used.

Install the required packages:
pip install -r requirements.txt

Start the Flask service:
python app.py

The service runs on:
http://localhost:5000

Test the health endpoint:
http://localhost:5000/v1/health

**2.PHP Gateway**

Place the project under the Apache htdocs directory.

For example:
C:\xampp\htdocs\credit-gateway

The gateway is available through Apache on port 80.

The scoring endpoint is:
http://localhost/credit-gateway/api/score

**3.PHP Frontend**

Place the frontend under Apache's htdocs directory.

For example:
C:\xampp\htdocs\credit-frontend

The frontend can then be accessed through:
http://localhost/credit-frontend

**End-to-End Request Flow**
When a user submits the frontend form, the request follows this process:

User -> PHP Frontend -> POST /api/score -> PHP REST Gateway -> POST /v1/predict -> Python Flask Service -> XGBoost Model -> Risk Prediction -> Python JSON Response -> PHP Gateway -> Frontend Result Page

**Extensibility**

The system has been designed so that the machine learning model can be expanded in the future.

The Python service maintains the model feature schema in:
credit_scoring/config.py

The current feature list is maintained in one location:
FEATURE_COLUMNS = [
    "avg_loan_amount",
    "avg_installments",
    "loan_count",
    "avg_days_late",
    "max_days_late",
    "pct_overdue",
]

Future versions may include additional information such as demographic or employment-related features.

The intended approach is to update the Python feature engineering and model training pipeline when new features are introduced rather than making the PHP gateway responsible for the internal model structure.

This allows the machine learning service to evolve independently from the gateway architecture.

**Model Versioning**

The current model is identified as:
v1

The model version is included in API responses so that predictions can be associated with the version of the model that generated them.

Future versions can introduce additional features or changes to the model while maintaining the existing service architecture.

**Technologies Used**

**Machine Learning / Backend**
- Python
- Flask
- Pandas
- Scikit-learn
- XGBoost
- Joblib


**API Gateway**

- PHP
- Apache
- cURL
- JSON
- REST API principles

**Frontend**

- PHP
- HTML
- CSS

**Development Environment**

- Visual Studio Code
- XAMPP
- Git
- GitHub

Author

Joshua Mvula

University of Zambia

Project: Hobbition Credit Rating Engine