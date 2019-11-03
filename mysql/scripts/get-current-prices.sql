DROP PROCEDURE IF EXISTS getCurrentPrices;

DELIMITER $$
CREATE PROCEDURE getCurrentPrices()
	BEGIN
	   DECLARE currPrice FLOAT;
	   DECLARE currCity VARCHAR(32);
	   DECLARE currGasType VARCHAR(8);
		DECLARE latestDate DATE;
	   DECLARE done BOOLEAN DEFAULT FALSE;
	   DECLARE gasCursor CURSOR FOR SELECT AVG(price) AS price, municipio.name AS municipio, gasType.name AS gasType
											FROM mexGasPrice
														INNER JOIN mexGasStation ON mexGasPrice.placeId = mexGasStation.placeId
														INNER JOIN gasType ON mexGasPrice.gasTypeId = gasType.gasTypeId
														INNER JOIN municipio ON mexGasStation.municipioId = municipio.municipioId
											WHERE priceDate = latestDate
											GROUP BY municipio, gasType
											UNION SELECT price, 'San Diego' AS municipio, gasType.name AS gasType
											FROM usaGasPrice
														INNER JOIN gasType ON usaGasPrice.gasTypeId = gasType.gasTypeId
											WHERE priceDate = latestDate;
		DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

		SELECT DISTINCT priceDate INTO latestDate
		FROM usaGasPrice
		WHERE priceDate IN(SELECT priceDate FROM mexGasPrice)
		ORDER BY priceDate DESC
		LIMIT 1;

		DROP TEMPORARY TABLE IF EXISTS priceSummary;
		CREATE TEMPORARY TABLE priceSummary (
			price FLOAT NOT NULL,
			city VARCHAR(32) NOT NULL,
			gasType VARCHAR(8) NOT NULL
		);

		OPEN gasCursor;

		mexLoop: LOOP
			FETCH gasCursor INTO currPrice, currCity, currGasType;
			INSERT INTO priceSummary(price, city, gasType) VALUES(currPrice, currCity, currGasType);
		END LOOP;

		SELECT price, city, gasType FROM priceSummary;

	END $$

DELIMITER  ;