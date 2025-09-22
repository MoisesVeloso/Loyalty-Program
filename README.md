# PHP Loyalty & Transaction Tracker

A simple **Customer Loyalty & Transaction Tracking System** built with PHP, MySQL, and TailwindCSS (with DaisyUI).  
This project allows small businesses to manage customers, record transactions, and track loyalty rewards using invoice numbers.  

👉 Built with the help of **AI assistance (ChatGPT)**.  

---

## ✨ Features

- 👤 **Customer Management**  
  - Add new customers with details (name, phone, address, birthday, gender, marital status).  
  - Validate phone number (must be 11 digits).  
  - Delete customers with **SweetAlert2 confirmation**.  

- 🧾 **Transactions**  
  - Add transactions with invoice number, items purchased, and "answered by".  
  - Search customers by **invoice number** (since loyalty card only stores invoice).  
  - Tracks number of purchases and shows **badge for rewards eligibility** (🎁).  

- 🛒 **Items Purchased**  
  - Dynamically loaded from `items` table.  
  - Checkbox auto-selects if quantity is entered.  

- 🎨 **UI/UX**  
  - Built with **TailwindCSS + DaisyUI**.  
  - Hover effects on rows.  
  - Clean responsive layout.  

---

## 🗄️ Database Schema (simplified)

- **customers**  
  - id, name, phone, email, address, birthday, age, gender, marital_status  

- **transactions**  
  - id, customer_id, invoice, date, answered_by  

- **items**  
  - id, model, status  

- **transaction_items**  
  - id, transaction_id, item_id, quantity  

