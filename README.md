
# Logistics REST APIs

RESTful API integrations with **Bluedart** and **Shiprocket** logistics platforms to automate **shipment creation, order tracking, label generation, and delivery status updates**.

---

## 🚀 Features

- **Bluedart Integration** → Manage shipments, AWBs, and tracking.
- **Shiprocket Integration** → Automate order fulfillment and shipping.
- **Real-Time Tracking** → Fetch latest shipment statuses.
- **Label Generation** → Generate printable shipping labels.
- **Secure Environment Handling** → Stores credentials in `.env`.

---

## 🛠 Tech Stack

- **Language:**  PHP 
- **APIs:** Bluedart, Shiprocket REST APIs
- **Database:**  PostgreSQL 
- **Authentication:** OAuth / API Key based
- **Tools:** cURL, Axios, JSON handling

---

## 📂 Project Structure

```bash
logistics-rest-apis/
├── bluedart/
│   ├── bluedart_api.js
│   ├── config.js
│   ├── utils.js
│   └── README.md
├── shiprocket/
│   ├── shiprocket_api.js
│   ├── config.js
│   ├── utils.js
│   └── README.md
├── shared/
│   ├── logger.js
│   └── constants.js
├── .env
├── .gitignore
└── README.md



⚙️ Setup & Installation

1️⃣ Clone the Repository

git clone https://github.com/<your-username>/logistics-rest-apis.git
cd logistics-rest-apis

2️⃣ Install Dependencies

composer install

3️⃣ Configure Environment Variables

env
BLUEDART_API_KEY=your_bluedart_key
BLUEDART_CLIENT_ID=your_bluedart_client_id
SHIPROCKET_EMAIL=your_shiprocket_email
SHIPROCKET_PASSWORD=your_shiprocket_password

4️⃣ Run the Project

php shiprocket/shiprocket_api.php

```

📌 API Functionalities
Feature	API	Description
Create Shipment	Bluedart / Shiprocket	Generate shipment requests
Track Orders	Both APIs	Get live tracking updates
Print Labels	Both APIs	Generate PDF/printable labels
Cancel Shipment	Shiprocket	Cancel undelivered orders

🔐 Security Notes
Use .env for API credentials.
Never commit sensitive credentials to GitHub.

📜 License
This project is private and maintained for internal use only.

👨‍💻 Author
Mohd Hussain
Full Stack Developer | API Integrations | Automation Specialist
GitHub • LinkedIn
