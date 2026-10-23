# 🚀 AWS Cloud-Native 2-Tier Registration Application

A modern, responsive 2-tier user registration web application deployed on **AWS EC2** with a serverless **AWS DynamoDB** database backend.

## 🌟 Key Features
- **Modern UI:** Glassmorphism design with Google Fonts (Poppins) and FontAwesome icons.
- **Serverless Database:** Directly integrates with AWS DynamoDB via AWS CLI and PHP.
- **Secure IAM Authentication:** Uses EC2 IAM Roles (`ec2-dynamodb-role` with `AmazonDynamoDBFullAccess`) to avoid hardcoding access keys.
- **High Scalability:** NoSQL DynamoDB table capable of handling high-throughput user registrations.

## 🛠️ Tech Stack
- **Frontend & Backend:** HTML5, CSS3, PHP
- **Cloud Infrastructure:** AWS EC2 (Amazon Linux, Apache Web Server, Region: `ap-south-1`)
- **Database:** AWS DynamoDB (Table: `user`, Partition Key: `username`)
- **Security:** AWS IAM Roles
- 
