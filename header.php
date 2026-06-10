<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KP Documentation Services</title>
    
    <!-- Favicon / Tab Logo -->
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
 <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
       
</head>
<body>
    
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary: #1a56db;
            --primary-dark: #1e40af;
            --secondary: #64748b;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --dark: #1e293b;
            --light: #f8fafc;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            color: var(--dark);
        }
        
        /* Navbar */
        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 1rem 2rem;
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 70px;
            width: 280px;
            height: calc(100% - 70px);
            background: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 100;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .sidebar-item {
            padding: 12px 24px;
            margin: 4px 12px;
            border-radius: 12px;
            transition: all 0.3s;
        }
        
        .sidebar-item a {
            color: var(--secondary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }
        
        .sidebar-item a i {
            width: 24px;
            font-size: 1.2rem;
        }
        
        .sidebar-item:hover {
            background: var(--light);
        }
        
        .sidebar-item.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        }
        
        .sidebar-item.active a {
            color: white;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            margin-top: 70px;
            padding: 30px;
            min-height: calc(100vh - 70px);
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background: white;
            border-bottom: 2px solid var(--light);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            border-radius: 20px 20px 0 0;
        }
        
        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(26,86,219,0.3);
        }
        
        /* Form Controls */
        .form-control, .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 10px 16px;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(26,86,219,0.1);
        }
        
        /* Tables */
        .dataTable {
            border-radius: 16px;
            overflow: hidden;
        }
        
        .dataTable thead th {
            background: var(--light);
            color: var(--dark);
            font-weight: 600;
            padding: 15px;
            border: none;
        }
        
        /* Badges */
        .badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-weight: 500;
        }
        
        .badge-completed { background: var(--success); color: white; }
        .badge-pending { background: var(--warning); color: white; }
        .badge-incomplete { background: var(--danger); color: white; }
        
        /* Footer */
        .footer {
            background: white;
            padding: 1.5rem;
            text-align: center;
            color: var(--secondary);
            border-top: 1px solid #e2e8f0;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>