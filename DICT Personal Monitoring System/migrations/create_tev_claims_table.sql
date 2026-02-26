-- Create TEV Claims table
CREATE TABLE IF NOT EXISTS tev_claims (
    id INT AUTO_INCREMENT PRIMARY KEY,
    claim_reference VARCHAR(20) NOT NULL UNIQUE,
    employee_name VARCHAR(255) NOT NULL,
    department VARCHAR(100) NOT NULL,
    claim_date DATE NOT NULL,
    purpose TEXT NOT NULL,
    amount DECIMAL(12, 2) NOT NULL,
    status ENUM('draft', 'submitted', 'under_review', 'approved', 'rejected', 'paid') NOT NULL DEFAULT 'draft',
    project_id INT,
    project_title VARCHAR(255),
    activity_id INT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE SET NULL,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_claim_reference (claim_reference),
    INDEX idx_claim_date (claim_date),
    INDEX idx_employee (employee_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create trigger for generating claim reference numbers
DELIMITER //
CREATE TRIGGER before_tev_claims_insert
BEFORE INSERT ON tev_claims
FOR EACH ROW
BEGIN
    DECLARE year_str CHAR(4);
    DECLARE last_seq INT;
    DECLARE new_seq INT;
    
    -- Only generate reference if not provided
    IF NEW.claim_reference IS NULL THEN
        -- Get current year
        SET year_str = DATE_FORMAT(NOW(), '%Y');
        
        -- Get the highest sequence number for the current year
        SELECT IFNULL(MAX(CAST(SUBSTRING_INDEX(claim_reference, '-', -1) AS UNSIGNED)), 0) INTO last_seq
        FROM tev_claims
        WHERE claim_reference LIKE CONCAT('TEV-', year_str, '-%');
        
        -- Increment the sequence
        SET new_seq = last_seq + 1;
        
        -- Format the reference number: TEV-YYYY-XXXXX (5-digit sequence with leading zeros)
        SET NEW.claim_reference = CONCAT('TEV-', year_str, '-', LPAD(new_seq, 5, '0'));
    END IF;
    
    -- Set default status if not provided
    IF NEW.status IS NULL THEN
        SET NEW.status = 'draft';
    END IF;
END //
DELIMITER ;
