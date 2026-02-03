TPcountrycity

CREATE TABLE `TPcountrycity` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) ,
  `state_code` VARCHAR(255) ,
  `type` VARCHAR(255) ,
  `countryId` INT,
  `latitude` DECIMAL(8, 6),
  `longitude` DECIMAL(8, 6), 
  `code` INT, 
  `isVariation` INT,
);