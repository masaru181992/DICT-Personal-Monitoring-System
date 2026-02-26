-- Add quantity columns to ipcr_entries table
ALTER TABLE `ipcr_entries`
ADD COLUMN `success_indicators_quantity` INT DEFAULT 1 AFTER `success_indicators`,
ADD COLUMN `actual_accomplishments_quantity` INT DEFAULT 1 AFTER `actual_accomplishments`;

-- Update existing entries to have default quantity of 1
UPDATE `ipcr_entries` 
SET `success_indicators_quantity` = 1,
    `actual_accomplishments_quantity` = 1;

-- Make the columns NOT NULL after setting default values
ALTER TABLE `ipcr_entries`
MODIFY COLUMN `success_indicators_quantity` INT NOT NULL DEFAULT 1,
MODIFY COLUMN `actual_accomplishments_quantity` INT NOT NULL DEFAULT 1;
