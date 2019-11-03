DROP PROCEDURE IF EXISTS addPrice;

DELIMITER $$
CREATE PROCEDURE addPrice(IN typeId TINYINT UNSIGNED, IN gasPlaceId MEDIUMINT UNSIGNED, gasPrice FLOAT)
	BEGIN
	   DECLARE gasPriceExists TINYINT UNSIGNED;
		DECLARE gasStationExists TINYINT UNSIGNED;

		SELECT COUNT(placeId) INTO gasStationExists FROM mexGasStation WHERE placeId = gasPlaceId;
		IF gasStationExists = 1 THEN
		   SELECT COUNT(gasTypeId) INTO gasPriceExists FROM mexGasPrice WHERE gasTypeId = typeId AND placeId = gasPlaceId AND priceDate = CURRENT_DATE();
		   IF gasPriceExists = 0  THEN
				INSERT INTO mexGasPrice(gasTypeId, placeId, priceDate, price) VALUES(typeId, gasPlaceId, CURRENT_DATE(), gasPrice);
			END IF;
		END IF;

	END $$
DELIMITER  ;