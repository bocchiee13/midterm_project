# 📊 Sales Management System

## 🎯 Project Title
**DPWH Tech Corp. - Sales and Inventory Management System**

A modern, feature-rich web application built with Laravel for managing sales transactions and inventory with real-time calculations and comprehensive reporting.

---

## 📝 Description / Overview

This Sales and Inventory Management System is a full-stack web application developed using Laravel framework. It provides an intuitive interface for managing product inventory and sales transactions with advanced features like real-time stock tracking, automatic calculations, CSV import/export capabilities, and detailed analytics dashboard.

The system features a modern glassmorphism UI design with smooth animations, responsive layouts, and user-friendly navigation. It's designed to help businesses efficiently track their inventory, record sales, monitor stock levels, and generate comprehensive reports.

---

## 🎓 Objectives

The main goals and learning outcomes of this project include:

1. **Master Laravel Framework** - Implement MVC architecture, routing, controllers, and Eloquent ORM
2. **Database Management** - Design and implement relational database schemas with proper relationships
3. **CRUD Operations** - Create, Read, Update, and Delete functionality for both inventory and sales
4. **Form Validation** - Implement comprehensive server-side and client-side validation
5. **Real-time Calculations** - Use JavaScript for dynamic calculations and user interactions
6. **Data Import/Export** - Handle CSV file operations for bulk data management
7. **Modern UI/UX Design** - Create responsive, accessible, and visually appealing interfaces
8. **Git Version Control** - Practice collaborative development using GitHub
9. **Business Logic Implementation** - Handle stock management, sales tracking, and inventory updates
10. **Reporting & Analytics** - Generate meaningful statistics and transaction histories

---

## ✨ Features / Functionality

### 🏪 Inventory Management
- ✅ Add, edit, view, and delete products
- 📊 Real-time stock level monitoring with alerts
- 🏷️ Product categorization and supplier tracking
- 💰 Automatic stock value calculations
- 📈 Inventory performance metrics
- ⚠️ Low stock and out-of-stock warnings
- 📥 CSV import/export functionality
- 🗑️ Bulk delete with confirmation
- 📅 Last restocked date tracking

### 💳 Sales Management
- 🛒 Create and manage sales transactions
- 🔄 Automatic stock deduction on sales
- 💵 Real-time price and total calculations
- 👤 Customer and sales person tracking
- 📊 Transaction status (Pending/Completed/Cancelled)
- 📉 Revenue and sales analytics
- 📜 Complete transaction history
- 🔙 Stock restoration on sale cancellation
- 📥 CSV import/export for sales data

### 🎨 UI/UX Features
- 🌟 Modern glassmorphism design
- 🎭 Smooth animations and transitions
- 📱 Fully responsive layout (mobile, tablet, desktop)
- 🎨 Gradient buttons with hover effects
- 📊 Statistics cards with icons
- 🔔 Alert notifications for actions
- 🎯 Intuitive navigation with dropdowns
- ✨ Custom scrollbar styling

### 🔧 Technical Features
- 🔐 Form validation (client and server-side)
- 🔗 Relational database design
- 📄 Pagination for large datasets
- 🔍 Search and filter capabilities
- 📊 Dynamic data visualization
- ⚡ AJAX for real-time updates
- 🎯 Status indicators with badges
- 📱 Bootstrap 5 integration

---

## 🚀 Installation Instructions

### Prerequisites
- PHP >= 8.0
- Composer
- MySQL/MariaDB
- Node.js & NPM (optional, for asset compilation)
- Git

### Step 1: Clone the Repository
```bash
git clone https://github.com/bocchiee13/midterm_project.git
cd midterm_project/salesApp
```

### Step 2: Install Dependencies
```bash
composer install
```

### Step 3: Configure Environment
```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Configure Database
Edit the `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sales_management
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Step 5: Run Migrations
```bash
# Create database tables
php artisan migrate

# (Optional) Seed sample data
php artisan db:seed
```

### Step 6: Start Development Server
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

### Step 7: Access the Application
- **Inventory Management**: `http://localhost:8000/inventory`
- **Sales Management**: `http://localhost:8000/sales`

---

## 📖 Usage

### Managing Inventory

#### Adding a New Product
1. Navigate to **Inventory → Add Product**
2. Fill in the product details:
   - Product Code (unique identifier)
   - Product Name
   - Description
   - Unit Price
   - Stock Quantity
   - Category
   - Supplier (optional)
   - Last Restocked Date (optional)
3. Click **Save Product**

#### Viewing Inventory
- Go to **Inventory → All Products** to view all items
- Use the statistics cards to monitor:
  - Total Products
  - Total Inventory Value
  - Low Stock Items
  - Out of Stock Items

#### Importing Products via CSV
1. Click **Import CSV** button
2. Select your CSV file with columns:
   ```
   Product Code, Product Name, Description, Unit Price, Stock Quantity, Category, Supplier, Last Restocked
   ```
3. Click **Import**

### Managing Sales

#### Creating a Sale
1. Navigate to **Sales → Add New Sale**
2. Enter transaction details:
   - Transaction ID
   - Customer Name
   - Select Product (dropdown shows available stock)
   - Unit Price (auto-filled from product)
   - Quantity (validates against stock)
   - Total Amount (calculated automatically)
   - Sale Date
   - Sales Person
   - Status
3. Click **Save Sale**

**Note**: The system automatically:
- Validates stock availability
- Calculates total amount in real-time
- Updates inventory stock levels
- Shows stock warnings if quantity is low

#### Viewing Sales Analytics
- Go to **Sales → Transaction History** to view:
  - Total Transactions
  - Completed/Pending/Cancelled Sales
  - Total Revenue
  - Average Sale Value

### Exporting Data
Both Inventory and Sales modules support CSV export:
1. Click **Export CSV** button
2. The file will download with all current data
3. Open in Excel or any spreadsheet application

---

## 📸 Screenshots or Code Snippets

### Glassmorphism Navigation Design
```css
.navbar {
    background: rgba(255, 255, 255, 0.9) !important;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
}
```

### Real-time Sale Calculation (JavaScript)
```javascript
function calculateTotal() {
    const price = parseFloat(unitPriceInput.value) || 0;
    const quantity = parseInt(quantityInput.value) || 0;
    const total = price * quantity;
    
    totalAmountInput.value = total.toFixed(2);
    displayTotal.textContent = '$' + total.toFixed(2);
    calculationDisplay.textContent = `${quantity} units × $${price.toFixed(2)}`;
}
```

### Inventory Model Relationship
```php
// In Sale.php model
public function inventory()
{
    return $this->belongsTo(Inventory::class);
}

// In Inventory.php model
public function sales()
{
    return $this->hasMany(Sale::class);
}
```

### Route Definitions
```php
// Inventory Routes
Route::resource('inventory', InventoryController::class);
Route::get('/inventory/export', [InventoryController::class, 'export'])->name('inventory.export');
Route::post('/inventory/import', [InventoryController::class, 'import'])->name('inventory.import');

// Sales Routes
Route::resource('sales', SaleController::class);
Route::get('/sales/history', [SaleController::class, 'history'])->name('sales.history');
Route::get('/sales/export', [SaleController::class, 'export'])->name('sales.export');
```

### Database Schema

**Inventory Table**
```sql
CREATE TABLE inventory (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    product_code VARCHAR(255) UNIQUE,
    product_name VARCHAR(255),
    description TEXT,
    unit_price DECIMAL(10,2),
    stock_quantity INT,
    category VARCHAR(255),
    supplier VARCHAR(255),
    last_restocked DATE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Sales Table**
```sql
CREATE TABLE sales (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    transaction_id VARCHAR(255) UNIQUE,
    customer_name VARCHAR(255),
    inventory_id BIGINT,
    product_name VARCHAR(255),
    unit_price DECIMAL(10,2),
    quantity INT,
    total_amount DECIMAL(10,2),
    sale_date DATE,
    sales_person VARCHAR(255),
    status ENUM('pending', 'completed', 'cancelled'),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (inventory_id) REFERENCES inventory(id)
);
```

---

## 🎨 Design System

### Color Palette
- **Primary Gradient**: `#667eea → #764ba2`
- **Success**: `#48bb78 → #38a169`
- **Danger**: `#f56565 → #e53e3e`
- **Warning**: `#fbbf24 → #f59e0b`
- **Info**: `#4299e1 → #3182ce`

### Typography
- **Font Family**: Inter, -apple-system, BlinkMacSystemFont, sans-serif
- **Weights**: 300, 400, 500, 600, 700

### Components
- **Cards**: Glassmorphism with backdrop blur
- **Buttons**: Gradient backgrounds with shadow on hover
- **Forms**: Modern inputs with focus states
- **Tables**: Hover effects with smooth transitions
- **Badges**: Rounded with icon indicators

---

## 👥 Contributors

### Development Team
- **Bryan** - Lead Developer
  - GitHub: [@bocchiee13](https://github.com/bocchiee13)
  - Role: Full-stack development, UI/UX design, database architecture

- **Nagi** - Collaborator
  - Role: Testing, documentation, feature development

### Special Thanks
- **DPWH Tech Corp.** - Project sponsor and requirements provider
- **Laravel Community** - Framework and documentation
- **Bootstrap Team** - UI framework

---

## 📋 Project Structure

```
salesApp/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── InventoryController.php
│   │       └── SaleController.php
│   └── Models/
│       ├── Inventory.php
│       └── Sale.php
├── database/
│   └── migrations/
│       ├── create_inventory_table.php
│       └── create_sales_table.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php
│       ├── inventory/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   ├── show.blade.php
│       │   └── history.blade.php
│       └── sales/
│           ├── index.blade.php
│           ├── create.blade.php
│           ├── edit.blade.php
│           ├── show.blade.php
│           └── history.blade.php
├── routes/
│   └── web.php
└── public/
    ├── css/
    └── js/
```

---

## 🔧 Technologies Used

### Backend
- **Laravel 10.x** - PHP Framework
- **MySQL** - Database
- **Eloquent ORM** - Database queries

### Frontend
- **Blade Templates** - Laravel templating engine
- **Bootstrap 5.3.2** - CSS Framework
- **Font Awesome 6.4.0** - Icons
- **Vanilla JavaScript** - Real-time calculations
- **CSS3** - Custom styling and animations

### Development Tools
- **Git & GitHub** - Version control
- **Composer** - PHP dependency manager
- **VS Code** - Code editor

---

## 📊 Key Metrics

- **Total Files**: 12+ Blade templates
- **Database Tables**: 2 (Inventory, Sales)
- **Features Implemented**: 20+
- **Lines of Code**: ~2,500+
- **Responsive Breakpoints**: 3 (Mobile, Tablet, Desktop)

---

## 🐛 Known Issues & Future Improvements

### Known Issues
- None reported at this time

### Future Enhancements
- [ ] User authentication and role-based access
- [ ] Advanced reporting with charts
- [ ] Product image uploads
- [ ] Barcode scanning support
- [ ] Email notifications for low stock
- [ ] Multi-currency support
- [ ] Dark mode theme
- [ ] API development for mobile app
- [ ] Advanced search and filtering
- [ ] Inventory forecasting

---

## 📄 License

This project is licensed under the **MIT License**.

```
MIT License

Copyright (c) 2025 DPWH Tech Corp.

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

---

## 📞 Contact & Support

For questions, issues, or contributions:

- **GitHub Repository**: [https://github.com/bocchiee13/midterm_project](https://github.com/bocchiee13/midterm_project)
- **Issues**: Create an issue on GitHub
- **Email**: support@dpwhtech.com (if applicable)

---

## 🙏 Acknowledgments

- Laravel Documentation Team
- Bootstrap Community
- Font Awesome Icons
- Stack Overflow Community
- Our instructors and mentors

---

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Bootstrap Documentation](https://getbootstrap.com/docs)
- [Git Documentation](https://git-scm.com/doc)
- [PHP Official Documentation](https://www.php.net/docs.php)

---

## 📝 Changelog

### Version 1.0.0 (2025-10-27)
- ✅ Initial release
- ✅ Inventory management module
- ✅ Sales management module
- ✅ CSV import/export
- ✅ Real-time calculations
- ✅ Responsive design
- ✅ Transaction history
- ✅ Statistics dashboard

---

**Made with ❤️ by Bryan | DPWH Tech Corp. © 2025**