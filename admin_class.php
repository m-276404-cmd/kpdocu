<?php
// NO session_start() here - session is already started in index.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

Class Action {
	private $db;

	public function __construct() {
		ob_start();
		include 'db_connect.php';
		$this->db = $conn;
	}
	
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

	function login(){
    $email = $this->db->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    
    // First check if user exists
    $qry = $this->db->query("SELECT * FROM users WHERE email = '$email'");
    
    if($qry->num_rows > 0){
        $user = $qry->fetch_array();
        
        // Check password - supports both plain text AND hashed passwords
        $password_valid = false;
        
        // Check if password is hashed (using password_verify)
        if(password_verify($password, $user['password'])) {
            $password_valid = true;
        }
        // Check if password is plain text
        elseif($password === $user['password']) {
            $password_valid = true;
            // Optional: Convert plain text to hash for security
            $new_hash = password_hash($password, PASSWORD_DEFAULT);
            $this->db->query("UPDATE users SET password = '$new_hash' WHERE id = {$user['id']}");
        }
        
        if($password_valid){
            foreach ($user as $key => $value) {
                if($key != 'password' && !is_numeric($key))
                    $_SESSION['login_'.$key] = $value;
            }
            return 1;
        } else {
            return 3;
        }
    } else {
        return 3;
    }
}
	
	function logout(){
		session_destroy();
		header("location:login.php");
	}

	function save_user(){
		$firstname = isset($_POST['firstname']) ? $_POST['firstname'] : '';
		$lastname = isset($_POST['lastname']) ? $_POST['lastname'] : '';
		$email = isset($_POST['email']) ? $_POST['email'] : '';
		$password = isset($_POST['password']) ? $_POST['password'] : '';
		$contact = isset($_POST['contact']) ? $_POST['contact'] : '';
		$address = isset($_POST['address']) ? $_POST['address'] : '';
		$type = isset($_POST['type']) ? (int)$_POST['type'] : 2;
		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		
		if(empty($firstname) || empty($lastname) || empty($email) || empty($password)) {
			return 0;
		}
		
		$check = $this->db->query("SELECT * FROM users WHERE email = '$email' " . (!empty($id) ? " AND id != {$id}" : ""));
		if($check->num_rows > 0){
			return 2;
		}
		
		$firstname = $this->db->real_escape_string($firstname);
		$lastname = $this->db->real_escape_string($lastname);
		$email = $this->db->real_escape_string($email);
		$password_hash = password_hash($password, PASSWORD_DEFAULT);
		$contact = $this->db->real_escape_string($contact);
		$address = $this->db->real_escape_string($address);
		
		$avatar = '';
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '' && $_FILES['img']['error'] == 0){
			$ext = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
			$fname = time() . '_' . rand(1000, 9999) . '.' . $ext;
			if(move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/'. $fname)){
				$avatar = ", avatar = '$fname'";
			}
		}
		
		if(empty($id)){
			$sql = "INSERT INTO users (firstname, lastname, email, password, contact, address, type) 
					VALUES ('$firstname', '$lastname', '$email', '$password_hash', '$contact', '$address', $type)";
		} else {
			$sql = "UPDATE users SET 
					firstname = '$firstname',
					lastname = '$lastname', 
					email = '$email',
					password = '$password_hash',
					contact = '$contact',
					address = '$address',
					type = $type
					WHERE id = $id";
		}
		
		$save = $this->db->query($sql);
		
		if($save){
			return 1;
		}
		return 0;
	}
	
	function update_user(){
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if($id <= 0) return 0;
    
    $email = isset($_POST['email']) ? $this->db->real_escape_string($_POST['email']) : '';
    $check = $this->db->query("SELECT id FROM users WHERE email = '$email' AND id != $id");
    if($check && $check->num_rows > 0) return 2;
    
    $update_fields = array();
    
    if(isset($_POST['firstname'])) {
        $update_fields[] = "firstname = '".$this->db->real_escape_string($_POST['firstname'])."'";
    }
    if(isset($_POST['lastname'])) {
        $update_fields[] = "lastname = '".$this->db->real_escape_string($_POST['lastname'])."'";
    }
    if(isset($_POST['email'])) {
        $update_fields[] = "email = '".$this->db->real_escape_string($_POST['email'])."'";
    }
    if(isset($_POST['contact'])) {
        $update_fields[] = "contact = '".$this->db->real_escape_string($_POST['contact'])."'";
    }
    if(isset($_POST['address'])) {
        $update_fields[] = "address = '".$this->db->real_escape_string($_POST['address'])."'";
    }
    
    if(isset($_POST['password']) && !empty($_POST['password'])){
        $update_fields[] = "password = '".password_hash($_POST['password'], PASSWORD_DEFAULT)."'";
    }
    
    if(isset($_POST['type']) && $_SESSION['login_type'] == 1){
        $update_fields[] = "type = ".(int)$_POST['type'];
    }
    
    if(isset($_FILES['img']) && $_FILES['img']['error'] == 0){
        $ext = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . rand(1000, 9999) . '.' . $ext;
        if(move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/'.$filename)){
            $update_fields[] = "avatar = '$filename'";
        }
    }
    
    if(empty($update_fields)) return 0;
    
    $sql = "UPDATE users SET " . implode(", ", $update_fields) . " WHERE id = $id";
    $save = $this->db->query($sql);
    
    if($save){
        // ONLY update session if the edited user is the CURRENT logged-in user
        if($id == $_SESSION['login_id']){
            $updated = $this->db->query("SELECT * FROM users WHERE id = $id")->fetch_assoc();
            foreach($updated as $key => $value){
                if($key != 'password' && !is_numeric($key)){
                    $_SESSION['login_'.$key] = $value;
                }
            }
        }
        return 1;
    }
    return 0;
}
	
	function delete_user(){
		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		$delete = $this->db->query("DELETE FROM users where id = $id");
		return $delete ? 1 : 0;
	}
	
	function save_document_multipage(){
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title = isset($_POST['title']) ? $this->db->real_escape_string($_POST['title']) : '';
    
    if(empty($title)){
        return "Title is required";
    }
    
    // Business fields
    $business_reg_no = isset($_POST['business_reg_no']) ? $this->db->real_escape_string($_POST['business_reg_no']) : '';
    $business_type = isset($_POST['business_type']) ? $this->db->real_escape_string($_POST['business_type']) : 'sole';
    $business_address = isset($_POST['business_address']) ? $this->db->real_escape_string($_POST['business_address']) : '';
    $marital_status = isset($_POST['marital_status']) ? $this->db->real_escape_string($_POST['marital_status']) : 'single';
    $marriage_date = isset($_POST['marriage_date']) ? $this->db->real_escape_string($_POST['marriage_date']) : '';
    
    // Spouse and children fields
    $spouse_count = isset($_POST['spouse_count']) ? (int)$_POST['spouse_count'] : 0;
    $spouse_data = isset($_POST['spouse_data']) ? $this->db->real_escape_string($_POST['spouse_data']) : '[]';
    $child_count = isset($_POST['child_count']) ? (int)$_POST['child_count'] : 0;
    $children_data = isset($_POST['children_data']) ? $this->db->real_escape_string($_POST['children_data']) : '[]';
    
    // Financial fields
    $total_sales = isset($_POST['total_sales']) ? $this->db->real_escape_string($_POST['total_sales']) : '';
    $net_profit = isset($_POST['net_profit']) ? $this->db->real_escape_string($_POST['net_profit']) : '';
    $capital = isset($_POST['capital']) ? $this->db->real_escape_string($_POST['capital']) : '';
    $assets = isset($_POST['assets']) ? $this->db->real_escape_string($_POST['assets']) : '';
    $status = isset($_POST['status']) ? $this->db->real_escape_string($_POST['status']) : 'pending';
    $description = isset($_POST['description']) ? $this->db->real_escape_string($_POST['description']) : '';
    $user_id = $_SESSION['login_id'];
    
    // Payment fields
    $payment_received = isset($_POST['payment_received']) ? $this->db->real_escape_string($_POST['payment_received']) : '';
    $payment_total = isset($_POST['payment_total']) ? $this->db->real_escape_string($_POST['payment_total']) : '';
    $payment_deposit = isset($_POST['payment_deposit']) ? $this->db->real_escape_string($_POST['payment_deposit']) : '';
    $payment_balance = isset($_POST['payment_balance']) ? $this->db->real_escape_string($_POST['payment_balance']) : '';
    
    // OWNERS DATA
    $owner_count = isset($_POST['owner_count']) ? (int)$_POST['owner_count'] : 1;
    $owners_data = isset($_POST['owners_data']) ? $this->db->real_escape_string($_POST['owners_data']) : '[]';
    
    if (!is_dir('assets/uploads')) {
        mkdir('assets/uploads', 0777, true);
    }
    
    // ============================================
    // FILE HANDLING - Store original names
    // ============================================
    
    // Handle SSM files
    $ssm_files = [];
    if(isset($_FILES['ssm_files']) && !empty($_FILES['ssm_files']['name'][0])) {
        for($i = 0; $i < count($_FILES['ssm_files']['name']); $i++) {
            if($_FILES['ssm_files']['error'][$i] == 0) {
                $original_name = $_FILES['ssm_files']['name'][$i];
                $ext = pathinfo($original_name, PATHINFO_EXTENSION);
                $generated_name = 'ssm_' . time() . '_' . rand(1000, 9999) . '_' . $i . '.' . $ext;
                move_uploaded_file($_FILES['ssm_files']['tmp_name'][$i], 'assets/uploads/' . $generated_name);
                $ssm_files[] = [
                    'original' => $original_name,
                    'generated' => $generated_name
                ];
            }
        }
    }
    
    // Handle Tax files
    $tax_files = [];
    if(isset($_FILES['tax_files']) && !empty($_FILES['tax_files']['name'][0])) {
        for($i = 0; $i < count($_FILES['tax_files']['name']); $i++) {
            if($_FILES['tax_files']['error'][$i] == 0) {
                $original_name = $_FILES['tax_files']['name'][$i];
                $ext = pathinfo($original_name, PATHINFO_EXTENSION);
                $generated_name = 'tax_' . time() . '_' . rand(1000, 9999) . '_' . $i . '.' . $ext;
                move_uploaded_file($_FILES['tax_files']['tmp_name'][$i], 'assets/uploads/' . $generated_name);
                $tax_files[] = [
                    'original' => $original_name,
                    'generated' => $generated_name
                ];
            }
        }
    }
    
    // Handle Bank files
    $bank_files = [];
    if(isset($_FILES['bank_files']) && !empty($_FILES['bank_files']['name'][0])) {
        for($i = 0; $i < count($_FILES['bank_files']['name']); $i++) {
            if($_FILES['bank_files']['error'][$i] == 0) {
                $original_name = $_FILES['bank_files']['name'][$i];
                $ext = pathinfo($original_name, PATHINFO_EXTENSION);
                $generated_name = 'bank_' . time() . '_' . rand(1000, 9999) . '_' . $i . '.' . $ext;
                move_uploaded_file($_FILES['bank_files']['tmp_name'][$i], 'assets/uploads/' . $generated_name);
                $bank_files[] = [
                    'original' => $original_name,
                    'generated' => $generated_name
                ];
            }
        }
    }
    
    // Handle Financial files
    $financial_files = [];
    if(isset($_FILES['financial_files']) && !empty($_FILES['financial_files']['name'][0])) {
        for($i = 0; $i < count($_FILES['financial_files']['name']); $i++) {
            if($_FILES['financial_files']['error'][$i] == 0) {
                $original_name = $_FILES['financial_files']['name'][$i];
                $ext = pathinfo($original_name, PATHINFO_EXTENSION);
                $generated_name = 'financial_' . time() . '_' . rand(1000, 9999) . '_' . $i . '.' . $ext;
                move_uploaded_file($_FILES['financial_files']['tmp_name'][$i], 'assets/uploads/' . $generated_name);
                $financial_files[] = [
                    'original' => $original_name,
                    'generated' => $generated_name
                ];
            }
        }
    }
    
    // Handle Other files
    $other_files = [];
    if(isset($_FILES['other_files']) && !empty($_FILES['other_files']['name'][0])) {
        for($i = 0; $i < count($_FILES['other_files']['name']); $i++) {
            if($_FILES['other_files']['error'][$i] == 0) {
                $original_name = $_FILES['other_files']['name'][$i];
                $ext = pathinfo($original_name, PATHINFO_EXTENSION);
                $generated_name = 'other_' . time() . '_' . rand(1000, 9999) . '_' . $i . '.' . $ext;
                move_uploaded_file($_FILES['other_files']['tmp_name'][$i], 'assets/uploads/' . $generated_name);
                $other_files[] = [
                    'original' => $original_name,
                    'generated' => $generated_name
                ];
            }
        }
    }
    
    // Get files to delete from edit mode
    $delete_ssm = isset($_POST['delete_files_ssm']) ? $_POST['delete_files_ssm'] : [];
    $delete_tax = isset($_POST['delete_files_tax']) ? $_POST['delete_files_tax'] : [];
    $delete_bank = isset($_POST['delete_files_bank']) ? $_POST['delete_files_bank'] : [];
    $delete_financial = isset($_POST['delete_files_financial']) ? $_POST['delete_files_financial'] : [];
    $delete_other = isset($_POST['delete_files_other']) ? $_POST['delete_files_other'] : [];
    
    // Delete physical files
    foreach($delete_ssm as $file) { 
        if(file_exists('assets/uploads/'.$file)) unlink('assets/uploads/'.$file); 
    }
    foreach($delete_tax as $file) { 
        if(file_exists('assets/uploads/'.$file)) unlink('assets/uploads/'.$file); 
    }
    foreach($delete_bank as $file) { 
        if(file_exists('assets/uploads/'.$file)) unlink('assets/uploads/'.$file); 
    }
    foreach($delete_financial as $file) { 
        if(file_exists('assets/uploads/'.$file)) unlink('assets/uploads/'.$file); 
    }
    foreach($delete_other as $file) { 
        if(file_exists('assets/uploads/'.$file)) unlink('assets/uploads/'.$file); 
    }
    
    // For Edit mode - keep existing files
    if($id > 0) {
        $existing = $this->db->query("SELECT * FROM documents WHERE id = $id")->fetch_assoc();
        if($existing) {
            // SSM existing files
            if(empty($ssm_files) && !empty($existing['ssm_files']) && $existing['ssm_files'] != '[]') {
                $existing_ssm = json_decode($existing['ssm_files'], true);
                if(is_array($existing_ssm)) {
                    $ssm_files = $existing_ssm;
                    if(!empty($delete_ssm)) {
                        $ssm_files = array_filter($ssm_files, function($file) use ($delete_ssm) {
                            $filename = is_array($file) ? $file['generated'] : $file;
                            return !in_array($filename, $delete_ssm);
                        });
                        $ssm_files = array_values($ssm_files);
                    }
                }
            }
            
            // Tax existing files
            if(empty($tax_files) && !empty($existing['tax_files']) && $existing['tax_files'] != '[]') {
                $existing_tax = json_decode($existing['tax_files'], true);
                if(is_array($existing_tax)) {
                    $tax_files = $existing_tax;
                    if(!empty($delete_tax)) {
                        $tax_files = array_filter($tax_files, function($file) use ($delete_tax) {
                            $filename = is_array($file) ? $file['generated'] : $file;
                            return !in_array($filename, $delete_tax);
                        });
                        $tax_files = array_values($tax_files);
                    }
                }
            }
            
            // Bank existing files
            if(empty($bank_files) && !empty($existing['bank_files']) && $existing['bank_files'] != '[]') {
                $existing_bank = json_decode($existing['bank_files'], true);
                if(is_array($existing_bank)) {
                    $bank_files = $existing_bank;
                    if(!empty($delete_bank)) {
                        $bank_files = array_filter($bank_files, function($file) use ($delete_bank) {
                            $filename = is_array($file) ? $file['generated'] : $file;
                            return !in_array($filename, $delete_bank);
                        });
                        $bank_files = array_values($bank_files);
                    }
                }
            }
            
            // Financial existing files
            if(empty($financial_files) && !empty($existing['financial_files']) && $existing['financial_files'] != '[]') {
                $existing_financial = json_decode($existing['financial_files'], true);
                if(is_array($existing_financial)) {
                    $financial_files = $existing_financial;
                    if(!empty($delete_financial)) {
                        $financial_files = array_filter($financial_files, function($file) use ($delete_financial) {
                            $filename = is_array($file) ? $file['generated'] : $file;
                            return !in_array($filename, $delete_financial);
                        });
                        $financial_files = array_values($financial_files);
                    }
                }
            }
            
            // Other existing files
            if(empty($other_files) && !empty($existing['other_files']) && $existing['other_files'] != '[]') {
                $existing_other = json_decode($existing['other_files'], true);
                if(is_array($existing_other)) {
                    $other_files = $existing_other;
                    if(!empty($delete_other)) {
                        $other_files = array_filter($other_files, function($file) use ($delete_other) {
                            $filename = is_array($file) ? $file['generated'] : $file;
                            return !in_array($filename, $delete_other);
                        });
                        $other_files = array_values($other_files);
                    }
                }
            }
        }
    }
    
    // Convert to JSON
    $ssm_files_json = json_encode($ssm_files);
    $tax_files_json = json_encode($tax_files);
    $bank_files_json = json_encode($bank_files);
    $financial_files_json = json_encode($financial_files);
    $other_files_json = json_encode($other_files);
    
    $ssm_files_json = $this->db->real_escape_string($ssm_files_json);
    $tax_files_json = $this->db->real_escape_string($tax_files_json);
    $bank_files_json = $this->db->real_escape_string($bank_files_json);
    $financial_files_json = $this->db->real_escape_string($financial_files_json);
    $other_files_json = $this->db->real_escape_string($other_files_json);
    
    if($id > 0){
        $sql = "UPDATE documents SET 
            title='$title', 
            business_reg_no='$business_reg_no', 
            business_type='$business_type',
            business_address='$business_address', 
            marital_status='$marital_status',
            marriage_date='$marriage_date',
            spouse_count=$spouse_count, 
            spouse_data='$spouse_data',
            child_count=$child_count, 
            children_data='$children_data',
            total_sales='$total_sales', 
            net_profit='$net_profit',
            capital='$capital', 
            assets='$assets', 
            status='$status',
            description='$description',
            ssm_files='$ssm_files_json', 
            tax_files='$tax_files_json',
            bank_files='$bank_files_json', 
            financial_files='$financial_files_json',
            other_files='$other_files_json',
            payment_received='$payment_received', 
            payment_total='$payment_total', 
            payment_deposit='$payment_deposit', 
            payment_balance='$payment_balance',
            owner_count=$owner_count,
            owners_data='$owners_data',
            date_updated=NOW()
            WHERE id=$id";
    } else {
        $sql = "INSERT INTO documents SET 
            title='$title', 
            business_reg_no='$business_reg_no', 
            business_type='$business_type',
            business_address='$business_address', 
            marital_status='$marital_status',
            marriage_date='$marriage_date',
            spouse_count=$spouse_count, 
            spouse_data='$spouse_data',
            child_count=$child_count, 
            children_data='$children_data',
            total_sales='$total_sales', 
            net_profit='$net_profit',
            capital='$capital', 
            assets='$assets', 
            status='$status',
            description='$description',
            ssm_files='$ssm_files_json', 
            tax_files='$tax_files_json',
            bank_files='$bank_files_json', 
            financial_files='$financial_files_json',
            other_files='$other_files_json',
            user_id=$user_id, 
            payment_received='$payment_received', 
            payment_total='$payment_total', 
            payment_deposit='$payment_deposit', 
            payment_balance='$payment_balance',
            owner_count=$owner_count,
            owners_data='$owners_data',
            date_created=NOW()";
    }
    
    if($this->db->query($sql)){
        return 1;
    }
    return "DB Error: " . $this->db->error;
}
	
	function delete_file(){
		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		$doc = $this->db->query("SELECT * FROM documents where id = $id")->fetch_array();
		$delete = $this->db->query("DELETE FROM documents where id = $id");
		if($delete){
			$file_fields = ['ssm_files', 'tax_files', 'bank_files', 'financial_files', 'other_files'];
			foreach($file_fields as $field) {
				if(!empty($doc[$field])){
					$files = json_decode($doc[$field], true);
					if(is_array($files)){
						foreach($files as $file){
							$filename = is_array($file) ? $file['generated'] : $file;
							if(!empty($filename) && file_exists('assets/uploads/'.$filename)){
								unlink('assets/uploads/'.$filename);
							}
						}
					}
				}
			}
			return 1;
		}
		return 0;
	}
	
	function upload_file(){
		if(isset($_FILES['file']) && $_FILES['file']['tmp_name'] != ''){
			$fname = time() . '_' . $_FILES['file']['name'];
			move_uploaded_file($_FILES['file']['tmp_name'], 'assets/uploads/' . $fname);
			return json_encode(array("status"=>1,"fname"=>$fname));
		}
		return json_encode(array("status"=>0));
	}
	
	function remove_file(){
		$fname = $_POST['fname'];
		if(file_exists('assets/uploads/'.$fname)){
			unlink('assets/uploads/'.$fname);
		}
		return 1;
	}
	
	function update_profile(){
		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		
		if($id != $_SESSION['login_id']){
			return 0;
		}
		
		$email = isset($_POST['email']) ? $this->db->real_escape_string($_POST['email']) : '';
		
		$check = $this->db->query("SELECT id FROM users WHERE email = '$email' AND id != $id");
		if($check && $check->num_rows > 0) return 2;
		
		$update_fields = array();
		
		if(isset($_POST['firstname'])) {
			$update_fields[] = "firstname = '".$this->db->real_escape_string($_POST['firstname'])."'";
		}
		if(isset($_POST['lastname'])) {
			$update_fields[] = "lastname = '".$this->db->real_escape_string($_POST['lastname'])."'";
		}
		if(isset($_POST['email'])) {
			$update_fields[] = "email = '".$this->db->real_escape_string($_POST['email'])."'";
		}
		if(isset($_POST['contact'])) {
			$update_fields[] = "contact = '".$this->db->real_escape_string($_POST['contact'])."'";
		}
		if(isset($_POST['address'])) {
			$update_fields[] = "address = '".$this->db->real_escape_string($_POST['address'])."'";
		}
		
		if(isset($_POST['password']) && !empty($_POST['password'])){
			$update_fields[] = "password = '".password_hash($_POST['password'], PASSWORD_DEFAULT)."'";
		}
		
		if(isset($_FILES['img']) && $_FILES['img']['error'] == 0){
			$ext = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
			$filename = time() . '_' . rand(1000, 9999) . '.' . $ext;
			if(move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/'.$filename)){
				$update_fields[] = "avatar = '$filename'";
			}
		}
		
		if(empty($update_fields)) return 0;
		
		$sql = "UPDATE users SET " . implode(", ", $update_fields) . " WHERE id = $id";
		$save = $this->db->query($sql);
		
		if($save){
			$updated = $this->db->query("SELECT * FROM users WHERE id = $id")->fetch_assoc();
			foreach($updated as $key => $value){
				if($key != 'password' && !is_numeric($key)){
					$_SESSION['login_'.$key] = $value;
				}
			}
			return 1;
		}
		return 0;
	}
}
?>