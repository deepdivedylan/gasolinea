DROP PROCEDURE IF EXISTS getCurrentPrices;

DELIMITER $$
CREATE PROCEDURE getCurrentPrices(OUT currExchangeRate FLOAT, OUT latestDate DATE)
	BEGIN
	   DECLARE galToLiter FLOAT DEFAULT 3.78541;

		SELECT DISTINCT priceDate INTO latestDate
		FROM usaGasPrice
		WHERE priceDate IN(SELECT priceDate FROM mexGasPrice)
		ORDER BY priceDate DESC
		LIMIT 1;

		SELECT exchangeRate INTO currExchangeRate
		FROM exchangeRate
	   ORDER BY exchangeDate DESC
	   LIMIT 1;

		SELECT AVG(price) AS price, municipio.name AS municipio, gasType.name AS gasType
		FROM mexGasPrice
			INNER JOIN mexGasStation ON mexGasPrice.placeId = mexGasStation.placeId
			INNER JOIN gasType ON mexGasPrice.gasTypeId = gasType.gasTypeId
			INNER JOIN municipio ON mexGasStation.municipioId = municipio.municipioId
		WHERE priceDate = latestDate
		GROUP BY municipio, gasType
		UNION SELECT ((currExchangeRate * price) / galToLiter) AS price, 'San Diego' AS municipio, gasType.name AS gasType
		FROM usaGasPrice
			INNER JOIN gasType ON usaGasPrice.gasTypeId = gasType.gasTypeId
		WHERE priceDate = latestDate;

	END $$

DELIMITER  ;