DROP PROCEDURE IF EXISTS createGasolinera;

DELIMITER $$

CREATE PROCEDURE createGasolinera(IN gasPlaceId MEDIUMINT UNSIGNED, IN gasName VARCHAR(128), IN gasLocation POINT)
	BEGIN
	   DECLARE gasMunicipioId TINYINT UNSIGNED;
	   -- DECLARE gasLocation POINT DEFAULT POINT(gasLocationX, gasLocationY);

	   SELECT municipioId INTO gasMunicipioId FROM municipio WHERE ST_Contains(shape, gasLocation) = 1;
	   IF gasMunicipioId IS NOT NULL THEN
			INSERT INTO mexGasStation(placeId, municipioId, name, location) VALUES(gasPlaceId, gasMunicipioId, gasName, gasLocation);
		END IF;

	END $$
DELIMITER ;