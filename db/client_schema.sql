-- SQL CREATE TABLE statements for client profile system
-- All tables prefixed with client_

CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    app_id INT NOT NULL UNSIGNED,
    app_name VARCHAR(255) NOT NULL,
    address VARCHAR(255),
    contact_email VARCHAR(255),
    contact_phone VARCHAR(50),
    status ENUM('active','inactive','archived') DEFAULT 'active',
    geo_location VARCHAR(100),
    latitude DECIMAL(10,7),
    longitude DECIMAL(10,7),
    blacklisted ENUM('yes','no') DEFAULT 'no',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT
);

CREATE TABLE client_projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    location VARCHAR(255),
    status ENUM('active','inactive','completed','archived') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,
    FOREIGN KEY (customer_id) REFERENCES clients(id)
);

CREATE TABLE client_system_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    system_power VARCHAR(50),
    system_size VARCHAR(50),
    number_of_panels INT,
    system_type VARCHAR(50),
    rate_class VARCHAR(50),
    panel_type VARCHAR(50),
    roof_inclination VARCHAR(50),
    inspection_date DATE,
    system_size_remarks VARCHAR(255),
    energization_date DATE,
    plan_type VARCHAR(50),
    inverter_brand VARCHAR(50),
    inverter_type VARCHAR(50),
    inverter_size VARCHAR(50),
    inverter_quantity INT,
    status ENUM('active','inactive','archived') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,
    FOREIGN KEY (project_id) REFERENCES client_projects(id)
);

CREATE TABLE client_materials_used (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    item VARCHAR(100),
    qty INT,
    unit VARCHAR(20),
    status ENUM('installed','used','pending','archived') DEFAULT 'installed',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,
    FOREIGN KEY (project_id) REFERENCES client_projects(id)
);

CREATE TABLE client_finance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    downpayment_amount DECIMAL(12,2),
    lease_months INT,
    status ENUM('active','inactive','archived') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,
    FOREIGN KEY (project_id) REFERENCES client_projects(id)
);

CREATE TABLE client_maintenance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    service_id VARCHAR(50),
    date DATE,
    description VARCHAR(255),
    status ENUM('completed','pending','scheduled','archived') DEFAULT 'completed',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,
    FOREIGN KEY (project_id) REFERENCES client_projects(id)
);

CREATE TABLE client_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    author VARCHAR(100),
    note TEXT,
    status ENUM('active','archived') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,
    FOREIGN KEY (project_id) REFERENCES client_projects(id)
);

CREATE TABLE client_billing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    year INT,
    month VARCHAR(20),
    due_date DATE,
    principal DECIMAL(12,2),
    interest DECIMAL(12,2),
    amount DECIMAL(12,2),
    status ENUM('paid','passed_due','unpaid','archived') DEFAULT 'unpaid',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,
    FOREIGN KEY (project_id) REFERENCES client_projects(id)
);

CREATE TABLE client_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    fileurl VARCHAR(255),
    name VARCHAR(255),
    description VARCHAR(255),
    comply TINYINT(1) DEFAULT 0,
    status ENUM('active','archived') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,
    FOREIGN KEY (project_id) REFERENCES client_projects(id)
);

CREATE TABLE client_legal_actions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    status ENUM('none','initiated','in_progress','resolved','archived') DEFAULT 'none',
    last_update DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,
    FOREIGN KEY (project_id) REFERENCES client_projects(id)
);

CREATE TABLE client_warranty (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    component VARCHAR(100),
    warranty VARCHAR(50),
    notes VARCHAR(100),
    status ENUM('active','expired','archived') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,
    FOREIGN KEY (project_id) REFERENCES client_projects(id)
);
