DROP PROCEDURE IF EXISTS createGasolinera;

DELIMITER $$

CREATE PROCEDURE createGasolinera(IN gasPlaceId MEDIUMINT UNSIGNED, IN gasName VARCHAR(128), IN gasLocation POINT)
	BEGIN
	   DECLARE gasMunicipioId TINYINT UNSIGNED;
	   DECLARE gasPlaceExists TINYINT UNSIGNED;

	   SELECT COUNT(placeId) INTO gasPlaceExists FROM mexGasStation WHERE placeId = gasPlaceId;
	   IF gasPlaceExists = 0 THEN
			SELECT municipioId INTO gasMunicipioId FROM municipio WHERE ST_Contains(shape, gasLocation) = 1;
			IF gasMunicipioId IS NOT NULL THEN
				INSERT INTO mexGasStation(placeId, municipioId, name, location) VALUES(gasPlaceId, gasMunicipioId, gasName, gasLocation);
			END IF;
	   END IF;

	END $$
DELIMITER ;