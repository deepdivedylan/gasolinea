DROP PROCEDURE IF EXISTS addPrice;

DELIMITER $$
CREATE PROCEDURE addPrice(IN gasTypeId TINYINT UNSIGNED, IN gasPlaceId MEDIUMINT UNSIGNED, gasPrice FLOAT)
	BEGIN
		DECLARE gasStationExists TINYINT UNSIGNED;

		SELECT COUNT(placeId) INTO gasStationExists FROM mexGasStation WHERE placeId = gasPlaceId;
		IF gasStationExists = 1 THEN
			INSERT INTO mexGasPrice(gasTypeId, placeId, priceDate, price) VALUES(gasTypeId, gasPlaceId, NOW(), gasPrice);
		END IF;

	END $$
DELIMITER  ;