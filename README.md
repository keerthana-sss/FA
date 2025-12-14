# TravelHub ✈️  
A Laravel-based travel planner for collaborative trip management, expense tracking, and trip analytics.

---

## Overview
WanderLog is a centralized travel management system that helps users plan, track, and manage trips efficiently. It allows collaborative trip creation, expense sharing, itinerary planning, and provides insights using external APIs for geocoding, nearby places, and currency exchange.

---

## Features
- 👥 **User Management**: Registration, login, roles (Admin, Traveler)  
- 🗺️ **Trip Management**: Create, edit, delete trips with destination, dates, and members  
- 📅 **Itinerary Planner**: Add daily plans (places, times, notes)  
- 💰 **Expense Tracker**: Add, categorize, and split expenses among members  
- 📂 **File Uploads**: Upload receipts, tickets, or photos  
- 🔔 **Notifications**: Notify trip members when new expenses or plans are added  
- 📊 **Reports**: View total trip expenses and who owes whom  
- 🌍 **API Integration**: Geocoding, nearby places, and currency exchange  
- 📈 **Analytics**: Visual trip analytics using charts  
- 📬 **Email Automation**: Weekly trip summary sent via Laravel Scheduler  

---

## Tech Stack
- **Backend:** Laravel 12  
- **Frontend:** Not implemented yet (currently backend-only)  
- **Database:** MySQL / PostgreSQL  
- **APIs Used:**  
  - [Open-Meteo Geocoding API](https://geocoding-api.open-meteo.com/v1/search)  
  - [Geoapify Places API](https://api.geoapify.com/v2/places)  
  - [ExchangeRate API](https://api.exchangerate.host)

---

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/keerthana-sss/FA.git
   cd FA