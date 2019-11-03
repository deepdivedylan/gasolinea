USE gasolinea;

DROP TABLE IF EXISTS mexGasPrice;
DROP TABLE IF EXISTS mexGasStation;
DROP TABLE IF EXISTS usaGasPrice;
DROP TABLE IF EXISTS gasType;
DROP TABLE IF EXISTS municipio;
DROP TABLE IF EXISTS exchangeRate;

CREATE TABLE exchangeRate (
   exchangeDate DATETIME NOT NULL,
   exchangeRate DECIMAL(9,6) NOT NULL,
   INDEX(exchangeDate)
);

CREATE TABLE municipio (
   municipioId TINYINT UNSIGNED AUTO_INCREMENT NOT NULL,
   name VARCHAR(32) NOT NULL,
   shape POLYGON,
   PRIMARY KEY(municipioId)
);

CREATE TABLE gasType (
  gasTypeId TINYINT UNSIGNED NOT NULL,
  name VARCHAR(16) NOT NULL,
  PRIMARY KEY(gasTypeId)
);

CREATE TABLE usaGasPrice (
   gasTypeId TINYINT UNSIGNED NOT NULL,
   priceDate DATE NOT NULL,
   price DECIMAL(5,3) NOT NULL,
   INDEX(gasTypeId),
   INDEX(priceDate),
   FOREIGN KEY(gasTypeId) REFERENCES gasType(gasTypeId)
);

CREATE TABLE mexGasStation (
   placeId MEDIUMINT UNSIGNED NOT NULL,
   municipioId TINYINT UNSIGNED NOT NULL,
   name VARCHAR(128) NOT NULL,
   location POINT NOT NULL,
   INDEX(municipioId),
   FOREIGN KEY(municipioId) REFERENCES municipio(municipioId),
   PRIMARY KEY(placeId)
);

CREATE TABLE mexGasPrice (
   gasTypeId TINYINT UNSIGNED NOT NULL,
	placeId MEDIUMINT UNSIGNED NOT NULL,
	priceDate DATE NOT NULL,
	price DECIMAL(4,2) NOT NULL,
   INDEX(gasTypeId),
	INDEX(placeId),
	FOREIGN KEY(gasTypeId) REFERENCES gasType(gasTypeId),
	FOREIGN KEY(placeId) REFERENCES mexGasStation(placeId)
);

INSERT INTO gasType(gasTypeId, name) VALUES(1, 'Regular');
INSERT INTO gasType(gasTypeId, name) VALUES(2, 'Premium');
INSERT INTO gasType(gasTypeId, name) VALUES(3, 'Diesel');
