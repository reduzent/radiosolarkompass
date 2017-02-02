WHERE TO GET GEOGRAPHIC DATA
============================

Cities:      http://download.geonames.org/export/dump/cities1000.zip
Countries:   http://download.geonames.org/export/dump/countryInfo.txt


HOW TO CREATE CITIES TABLE
===================================

REATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `name` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `asciiname` varchar(200) NOT NULL,
  `alternatenames` varchar(5000) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `coord` point NOT NULL,
  `feature_class` char(1) NOT NULL,
  `feature_code` char(10) NOT NULL,
  `country_code` char(2) NOT NULL,
  `cc2` varchar(60) DEFAULT NULL,
  `admin1_code` varchar(20) DEFAULT NULL,
  `admin2_code` varchar(80) DEFAULT NULL,
  `admin3_code` varchar(20) DEFAULT NULL,
  `admin4_code` varchar(20) DEFAULT NULL,
  `population` bigint(20) DEFAULT '0',
  `elevation` int(11) DEFAULT '0',
  `gtopo30` int(11) DEFAULT '0',
  `timezone` varchar(32) NOT NULL,
  `mod_date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_countries` (`country_code`),
  CONSTRAINT `fk_countries` FOREIGN KEY (`country_code`) REFERENCES `countries` (`iso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8


HOW TO IMPORT DATA FROM FILE
============================

load data
  local infile '/home/akita/radiosolarkompass/mysql/cities1000.txt' 
  into table cities (
    `id`, 
    `name`, 
    `asciiname`,
    `alternatenames`,
    @coordx,
    @coordy,
    `feature_class`,
    `feature_code`,
    `country_code`,
    `cc2`,
    `admin1_code`,
    `admin2_code`,
    `admin3_code`,
    `admin4_code`,
    `population`,
    `elevation`,
    `gtopo30`,
    `timezone`,
    `mod_date`
  ) 
  set 
    coord = geomfromtext(concat("point(", @coordx, " ", @coordy, ")"));


HOW TO CREATE COUNTRIES TABLE
======================================

CREATE TABLE `countries` (
  `iso` char(2) NOT NULL,
  `iso3` char(3) NOT NULL,
  `isonum` char(3) NOT NULL,
  `fips` char(2) DEFAULT NULL,
  `name` varchar(60) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `capital` varchar(60) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `area` bigint(20) DEFAULT '0',
  `population` bigint(20) DEFAULT '0',
  `continent` char(2) NOT NULL,
  `tld` char(5) DEFAULT NULL,
  `currency_code` char(3) DEFAULT NULL,
  `currency_name` varchar(30) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `phone` char(20) DEFAULT NULL,
  `postal_code_format` varchar(200) DEFAULT NULL,
  `postal_code_regex` varchar(200) DEFAULT NULL,
  `languages` varchar(120) DEFAULT NULL,
  `id` int(11) NOT NULL,
  `neighbours` varchar(60) DEFAULT NULL,
  `equivfipscode` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `iso_key` (`iso`),
  UNIQUE KEY `name_key` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

HOW TO IMPORT DATA FROM FILE
============================

load data
  local infile '/home/akita/radiosolarkompass/mysql/countryInfo.txt' 
  into table countries (
    iso, 
    iso3,
    isonum,
    fips,
    name,
    capital,
    area,
    population,
    continent,
    tld,
    currency_code,
    currency_name,
    phone,
    postal_code_format,
    postal_code_regex,
    languages,
    id,
    neighbours,
    equivfipscode
  );

