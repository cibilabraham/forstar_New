DELIMITER $$

DROP FUNCTION IF EXISTS `GetFY`$$
CREATE FUNCTION `GetFY` (dt DATE) RETURNS INT
BEGIN
declare yr int;
declare mn int;
set yr = YEAR(dt);
set mn = MONTH(dt);

IF mn < 4 THEN
	set yr = yr -1;
END IF;
return yr;
END$$

DELIMITER ;
