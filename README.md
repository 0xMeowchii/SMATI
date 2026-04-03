# SMATI Online Grade Viewing System

A web-based academic records platform developed for **St. Michael Archangel Technological Institute, Inc. (SMATI)**, designed to modernize grade distribution and simplify academic management for CSIT First Year students.

> **Capstone Project** — Datamex College of Saint Adeline, College of Information Technology  
> Deployed at: [smati.education](https://smati.education)

---

## 📖 Table of Contents

- [About the Project](#about-the-project)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [User Roles](#user-roles)
- [Project Scope](#project-scope)

---

## About the Project

SMATI previously relied on a manual, paper-based grading process — printed report cards, spreadsheets, and face-to-face grade distribution. This led to misplaced files, encoding errors, delayed grade access, and limited transparency for students and parents.

This system replaces that workflow with a **centralized, secure, web-based platform** that allows students, teachers, registrars, and administrators to access and manage academic records efficiently and in real time.

**Key goals:**
- Eliminate manual grade distribution delays
- Provide secure, role-based access to academic records
- Reduce teacher workload 
- Improve communication between students, faculty, and administration
- Establish a scalable foundation for SMATI's digital transformation

---

## Features

### Student Portal
- View Prelim, Midterm, and Final grades through a personal account
- Monitor academic standing continuously throughout the semester
- Submit grade-related concerns or correction requests via the **Concern Form**
- View institutional announcements and academic updates from the **Announcement Bulletin**
- Automated average grade calculation displayed in real time

### Teacher Dashboard
- Encode, modify, and manage student grades securely
- View and respond to student concerns through the **Teacher Concern Dashboard**
- Submit grade change requests for admin review
- Receive automated reminders before grade submission deadlines
- Activity logs track all faculty grade-related actions

### Admin Panel
- Full control over user accounts, access permissions, and system configuration
- Manage teacher and student records
- Post announcements to the entire academic community
- Review, approve, or reject teacher grade change requests
- Monitor grade submission compliance across departments
- Manual and automated database backup management

### Registrar Module
- View individual student grades across subjects
- Generate downloadable PDF reports with embedded e-signatures for official documentation

### Announcement Bulletin
- Admin-managed bulletin board visible to students, teachers, and the registrar
- Post grade release schedules, enrollment reminders, deadlines, and institutional updates

### Backup System
- Automated weekly database backup every Monday at 2:00 AM via Hostinger cron job
- Manual backup option with authentication prompt, generating a downloadable SQL file
- Secondary backup maintained on GitHub for version control and data redundancy

---

## Tech Stack

| Layer | Technology |
|---|---|
| **Backend** | PHP |
| **Frontend** | Bootstrap (responsive, mobile-friendly UI) |
| **Database** | MySQL (via XAMPP for local development) |
| **IDE** | Visual Studio Code |
| **Version Control** | GitHub |
| **Hosting** | Hostinger (Domain + Web Hosting) |
| **Domain** | smati.education |

---

### Key Components
- Role-based dashboards (Admin, Teacher, Student, Registrar)
- SMATI Database Schema with structured academic period tables (Prelim, Midterm, Finals)
- Concern management routing system
- PDF report generation with e-signature integration
- Automated grade average calculation engine

---

## User Roles

| Role | Key Capabilities |
|---|---|
| **Admin** | Full system control, user management, announcements, backup, grade change approvals |
| **Teacher** | Grade entry and management, concern resolution, submission deadline tracking |
| **Student** | Grade viewing, concern submission, announcement access |
| **Registrar** | Grade viewing, PDF report generation with e-signature |

---

## Project Scope

### Included
- Online grade viewing for CSIT First Year students
- Grade entry and management for faculty
- Multi-role dashboards (Admin, Teacher, Student, Registrar)
- Student concern management and resolution tracking
- Announcement bulletin system
- PDF report generation with registrar e-signature
- Automated grade average calculation
- Database backup and recovery
- Deployment under the official SMATI domain

### Not Included (Current Version)
- Automated computation of raw scores (quizzes, exams, activities) — only final computed grades per period are recorded
- Mobile application (iOS/Android)
- Multi-school or multi-campus support
- Integration with third-party LMS or academic platforms
- Direct parent-teacher messaging
- Offline/offline-mode access
- Printing of physical report cards

---

### Production Deployment
The system is deployed on **Hostinger** under the domain [smati.education](https://smati.education). 
