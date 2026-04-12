# Student Discipline & Violation Management System (pelanggaran_uk)

A web-based application designed to digitize student discipline records, automate point calculations based on violations, and streamline administrative tasks such as generating warning letters and parent summons.

## 🚀 Features

- **Role-Based Access Control**: Separate dashboards and permissions for Admin, BK Teachers (School Counselors), Subject Teachers, and Students.
- **Automated Point System**: Real-time calculation of student discipline points based on violation categories.
- **Document Automation**: Generate and print official letters (Parent Summons, Agreements, Statements, and Transfer Letters) directly from the system.
- **Smart Data Entry**: Chained dropdowns for efficient and accurate student selection.
- **Comprehensive Reporting**: Detailed logs of student violations and administrative actions.

## 🛠️ Tech Stack

- **Backend**: PHP 8.x (Native)
- **Database**: MySQL / MariaDB
- **Frontend**: Tailwind CSS 4, DaisyUI 5
- **Icons/UI**: SVG-based iconography, Responsive Layouts

## 📂 Project Structure

```text
pelanggaran_uk/
├── auth/               # Login, Logout, and Process scripts
├── config/             # Database connection and global constants
├── includes/           # Reusable components and layout pieces
│   ├── ui/             # UI Components (Alerts, Header, Sidebar, Pagination)
│   └── helpers.php     # Common utility functions
├── layout/             # Main application layout wrapper
├── middleware/         # Auth and Role-based access security
├── pages/              # Module-specific pages
│   ├── dashboard/      # Role-specific dashboards
│   ├── jenis_pelanggaran/ # Violation category management
│   ├── pelanggaran/    # Violation recording and logs
│   ├── siswa/          # Student management
│   └── surat*/         # Document generation modules
├── src/                # Frontend assets (Tailwind CSS, Images, SVGs)
└── index.php           # Main entry point (Router/Dispatcher)
```

## ⚙️ Installation & Setup

### Prerequisites

- PHP 8.0 or higher
- MySQL / MariaDB
- Node.js & pnpm (for frontend asset compilation)
- A local server environment like Laragon (recommended), XAMPP, or MAMP.

### Steps

1. **Clone the Repository**

    ```bash
    git clone https://github.com/St4rkXc/uk-pelanggaran-siswa
    cd pelanggaran_uk
    ```

2. **Database Setup**
    - Create a new database named `pelanggaran_siswa_new`.
    - Import the database schema (look for a `.sql` file, or check `database.sqlite` if migrating).
    - Configure your database credentials in `config/database.php`.

3. **Configure Environment**
    - Open `config/database.php` and update `BASE_URL`:
        ```php
        define('BASE_URL', 'http://localhost/pelanggaran_uk');
        ```

4. **Install Frontend Dependencies**
    - If you plan to modify styles, install the required packages:
        ```bash
        pnpm install
        ```

5. **Build Styles (Optional)**
    - To compile Tailwind CSS:
        ```bash
        pnpm run build
        ```
    - For development (watching changes):
        ```bash
        pnpm run watch
        ```

6. **Run the Project**
    - Access the project via your browser at `http://localhost/pelanggaran_uk`.

## 🔐 Security Note

The system uses middleware located in the `middleware/` folder (`auth.php` and `role.php`) to ensure that only authorized users can access specific pages. Ensure these are included at the top of your controller or page files.

## 🖨️ Printing Guidelines

For the best results when printing letters:

- Use a modern browser (Chrome/Edge recommended).
- Set **Margins** to `None` in the print settings.
- Enable `Background Graphics` if required by the design.

---

Developed by Dhiyo Wikantara
