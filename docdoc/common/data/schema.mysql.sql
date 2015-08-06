-- MySQL dump 10.13  Distrib 5.6.15, for Linux (i686)
--
-- Host: localhost    Database: docdoc_stage
-- ------------------------------------------------------
-- Server version	5.6.15-63.0-log
SET foreign_key_checks = 0;

--
-- Table structure for table `SMSQuery`
--
CREATE TABLE `SMSQuery` (
	`idMessage` int(11) NOT NULL AUTO_INCREMENT,
	`phoneTo` varchar(12) NOT NULL,
	`typeSMS` int(11) NOT NULL DEFAULT '0',
	`message` varchar(500) NOT NULL,
	`crDate` datetime NOT NULL,
	`sendDate` datetime DEFAULT NULL,
	`priority` tinyint(4) NOT NULL DEFAULT '99',
	`status` enum('new','in_process','sended','deleted','canceled','error','error_gate','error_connect','delivered') DEFAULT 'new',
	`systemId` varchar(255) DEFAULT NULL,
	`gateId` tinyint(4) DEFAULT '1',
	`ttl` int(11),
	PRIMARY KEY (`idMessage`),
	INDEX `status_idx` (`status` ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='������� �������� SMS';

--
-- Table structure for table `SMStype`
--
CREATE TABLE `SMStype` (
	`id_type` int(11) NOT NULL,
	`title` varchar(50) NOT NULL,
	PRIMARY KEY (`id_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='���� SMS (����������)';

--
-- Table structure for table `academic_degree`
--
CREATE TABLE `academic_degree` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(512) NOT NULL COMMENT 'название учёной степени',
	`weight` int(11) DEFAULT '0' COMMENT 'приоритет учёной степени',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='учёные степени';

--
-- Table structure for table `admin_4_clinic`
--
CREATE TABLE `admin_4_clinic` (
	`clinic_admin_id` int(11) NOT NULL DEFAULT '0',
	`clinic_id` int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`clinic_admin_id`,`clinic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Администраторы для клиники';

--
-- Table structure for table `api_clinic`
--
CREATE TABLE `api_clinic` (
	`id` varchar(50) NOT NULL,
	`name` varchar(45) NOT NULL,
	`phone` char(11) DEFAULT NULL,
	`city` varchar(20) DEFAULT NULL,
	`enabled` tinyint(1) NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`),
	KEY `enabled_index` (`enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Информация о клиниках, получаемая из интеграционного шлюза';

--
-- Table structure for table `api_doctor`
--
CREATE TABLE `api_doctor` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `api_doctor_id` varchar(50) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `api_clinic_id` varchar(50) DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `api_resource_type` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `doctor_in_clinic` (`api_clinic_id`,`api_doctor_id`),
  KEY `api_clinic_id_fk` (`api_clinic_id`),
  KEY `enabled_index` (`enabled`),
  CONSTRAINT `api_clinic_id_fk` FOREIGN KEY (`api_clinic_id`) REFERENCES `api_clinic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Информация о крачах, получаемая из интеграционного шлюза';


--
-- Table structure for table `area`
--
CREATE TABLE `area` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`rewrite_name` varchar(512) NOT NULL,
	`name` varchar(512) NOT NULL,
	`full_name` varchar(512) DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='округа';

--
-- Table structure for table `area_moscow`
--
CREATE TABLE `area_moscow` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`rewrite_name` varchar(512) NOT NULL,
	`name` varchar(512) NOT NULL,
	`full_name` varchar(512) DEFAULT NULL,
	`seo_text` text,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `area_underground_station`
--
CREATE TABLE `area_underground_station` (
	`area_id` int(11) NOT NULL,
	`station_id` int(11) NOT NULL,
	PRIMARY KEY (`area_id`,`station_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `article`
--
CREATE TABLE `article` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`disabled` int(1) NOT NULL COMMENT 'не выводить',
	`name` varchar(512) NOT NULL COMMENT 'название статьи',
	`description` text COMMENT 'аннотация статьи',
	`text` text NOT NULL COMMENT 'текст статьи',
	`rewrite_name` varchar(512) DEFAULT NULL,
	`title` varchar(512) DEFAULT NULL,
	`meta_keywords` varchar(512) DEFAULT NULL,
	`meta_description` varchar(512) DEFAULT NULL,
	`is_memo` int(1) NOT NULL DEFAULT '0',
	`article_section_id` int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `article_section_id` (`article_section_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='статьи';


--
-- Table structure for table `booking`
--
CREATE TABLE `booking` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`request_id` int(11) unsigned NOT NULL,
	`slot_id` varchar(255) DEFAULT NULL,
	`status` tinyint(2) NOT NULL,
	`date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`external_id` varchar(50) DEFAULT NULL,
	`start_time` timestamp NULL DEFAULT NULL,
	`finish_time` timestamp NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `request_id_idx` (`request_id`),
	KEY `slot_id_idx` (`slot_id`),
	CONSTRAINT `booking_request_fk` FOREIGN KEY (`request_id`) REFERENCES `request` (`req_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

--
-- Table structure for table `article_section`
--
CREATE TABLE `article_section` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(512) NOT NULL,
	`rewrite_name` varchar(512) NOT NULL,
	`text` text,
	`title` varchar(512) DEFAULT NULL,
	`meta_keywords` varchar(512) DEFAULT NULL,
	`meta_description` varchar(512) DEFAULT NULL,
	`sector_id` int(11) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `call4diagnostica`
--
CREATE TABLE `call4diagnostica` (
	`idCall` int(11) NOT NULL AUTO_INCREMENT,
	`crDate` datetime DEFAULT NULL,
	`numberFrom` varchar(12) DEFAULT NULL,
	`numberTo` varchar(12) DEFAULT NULL,
	`duration` int(11) NOT NULL DEFAULT '0',
	`payDuration` int(11) NOT NULL DEFAULT '0',
	`id_clinic` int(11) NOT NULL DEFAULT '0',
	`clinicName3ThSoft` varchar(100) DEFAULT NULL,
	`price` float(9,2) DEFAULT '0.00',
	PRIMARY KEY (`idCall`),
	KEY `idx_clinic_id` (`id_clinic`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='������ �� �����������';

--
-- Table structure for table `category_dict`
--
CREATE TABLE `category_dict` (
	`category_id` tinyint(4) NOT NULL AUTO_INCREMENT,
	`title` varchar(150) NOT NULL,
	PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Справочник категорий врачей';

--
-- Table structure for table `city`
--
CREATE TABLE `city` (
  `id_city` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `rewrite_name` varchar(50) DEFAULT NULL,
  `long` varchar(20) DEFAULT NULL,
  `lat` varchar(20) DEFAULT NULL,
  `prefix` varchar(8) NOT NULL,
  `title_genitive` varchar(50) DEFAULT NULL,
  `title_prepositional` varchar(50) DEFAULT NULL,
  `title_dative` varchar(50) NOT NULL,
  `has_diagnostic` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `site_phone` char(12) DEFAULT NULL,
  `site_office` char(12) DEFAULT NULL,
  `site_YA` varchar(20) DEFAULT NULL,
  `site_GA`            VARCHAR(20)          DEFAULT NULL,
  `search_type` int(11) NOT NULL DEFAULT '1',
  `has_mobile` tinyint(1) DEFAULT '0',
  `diagnostic_site_YA` varchar(20) DEFAULT NULL,
  `diagnostic_site_GA` VARCHAR(20)          DEFAULT NULL,
  `opinion_phone` char(12) DEFAULT NULL,
  `gtm`                VARCHAR(20) NOT NULL,
  `diagnostic_gtm`     VARCHAR(20) NOT NULL,
  `time_zone`          TINYINT(4)  NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_city`),
  KEY `city_is_active` (`is_active`),
  KEY `prefix` (`prefix`),
  KEY `site_phone_fk` (`site_phone`),
  KEY `opinion_phone_fk` (`opinion_phone`),
  CONSTRAINT `opinion_phone_fk` FOREIGN KEY (`opinion_phone`) REFERENCES `phone` (`number`)
    ON UPDATE CASCADE,
  CONSTRAINT `site_phone_fk` FOREIGN KEY (`site_phone`) REFERENCES `phone` (`number`)
    ON UPDATE CASCADE
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 10
  DEFAULT CHARSET = utf8
  COMMENT = 'Города';

--
-- Table structure for table `city_dict`
--
CREATE TABLE `city_dict` (
	`city_id` int(11) NOT NULL AUTO_INCREMENT,
	`title` varchar(50) NOT NULL,
	PRIMARY KEY (`city_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Справочник городов';

--
-- Table structure for table `client`
--
CREATE TABLE `client` (
	`clientId` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) DEFAULT NULL,
	`first_name` varchar(50) DEFAULT NULL,
	`last_name` varchar(50) DEFAULT NULL,
	`middle_name` varchar(50) DEFAULT NULL,
	`email` varchar(50) DEFAULT NULL,
	`phone` varchar(20) DEFAULT NULL,
	`registered_in_mixpanel` TINYINT(1) DEFAULT 0,
	PRIMARY KEY (`clientId`),
	UNIQUE KEY `phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Клиенты';

--
-- Table structure for table `clinic`
--
CREATE TABLE `clinic` (
  `id`                   INT(11)                                        NOT NULL           AUTO_INCREMENT,
  `created`              TIMESTAMP                                      NOT NULL           DEFAULT CURRENT_TIMESTAMP,
  `status`               INT(1)                                         NOT NULL,
  `rating`               FLOAT                                                             DEFAULT '0',
  `phone`                VARCHAR(512)                                                      DEFAULT NULL,
  `asterisk_phone`       VARCHAR(50)                                                       DEFAULT NULL,
  `asterisk_phone_2`     CHAR(12)                                                          DEFAULT NULL,
  `phone_appointment`    VARCHAR(512)                                                      DEFAULT NULL,
  `email`                VARCHAR(512)                                                      DEFAULT NULL,
  `url`                  VARCHAR(512)                                                      DEFAULT NULL,
  `name`                 VARCHAR(512)                                   NOT NULL,
  `short_name`           VARCHAR(100)                                                      DEFAULT NULL,
  `rewrite_name`         VARCHAR(100)                                                      DEFAULT NULL,
  `contact_name`         VARCHAR(512)                                                      DEFAULT NULL,
  `text`                 TEXT,
  `attach`               VARCHAR(512)                                                      DEFAULT NULL,
  `note`                 TEXT,
  `password`             VARCHAR(512)                                                      DEFAULT NULL,
  `parent_clinic_id`     INT(11)                                        NOT NULL           DEFAULT '0',
  `city_id`              INT(11)                                                           DEFAULT '1',
  `age_selector`         ENUM('multy', 'child', 'adult')                                   DEFAULT 'multy',
  `status_new`           ENUM('enable', 'disable', 'manual', 'request') NOT NULL           DEFAULT 'request',
  `city`                 VARCHAR(50)                                                       DEFAULT NULL,
  `street`               VARCHAR(100)                                                      DEFAULT NULL,
  `house`                VARCHAR(50)                                                       DEFAULT NULL,
  `aliase`               VARCHAR(50)                                                       DEFAULT NULL,
  `latitude`             DOUBLE(13, 10)                                                    DEFAULT NULL,
  `longitude`            DOUBLE(13, 10)                                                    DEFAULT NULL,
  `description`          TEXT,
  `operator_comment`     TEXT,
  `logoPath`             VARCHAR(100)                                                      DEFAULT NULL,
  `shortDescription`     TEXT,
  `isDiagnostic`         ENUM('yes', 'no')                              NOT NULL           DEFAULT 'no',
  `isClinic`             ENUM('yes', 'no')                              NOT NULL           DEFAULT 'yes',
  `isPrivatDoctor`       ENUM('yes', 'no')                              NOT NULL           DEFAULT 'no',
  `weekdays_open`        VARCHAR(50)                                                       DEFAULT NULL,
  `weekend_open`         VARCHAR(50)                                                       DEFAULT NULL,
  `saturday_open`        VARCHAR(50)                                                       DEFAULT NULL,
  `sunday_open`          VARCHAR(50)                                                       DEFAULT NULL,
  `sort4commerce`        TINYINT(4)                                                        DEFAULT '99',
  `open_4_yandex`        ENUM('no', 'yes')                                                 DEFAULT 'yes',
  `schedule_state`       ENUM('disable', 'enable')                      NOT NULL           DEFAULT 'disable'
  COMMENT 'enable, disable',
  `sendSMS`              ENUM('yes', 'no')                                                 DEFAULT 'yes',
  `rating_1`             FLOAT(9, 2)                                    NOT NULL           DEFAULT '1.00',
  `rating_2`             FLOAT(9, 2)                                    NOT NULL           DEFAULT '1.00',
  `rating_3`             FLOAT(9, 2)                                    NOT NULL           DEFAULT '1.00',
  `rating_4`             FLOAT(9, 2)                                    NOT NULL           DEFAULT '1.00',
  `rating_total`         FLOAT(9, 2)                                    NOT NULL           DEFAULT '1.00',
  `settings_id`          INT(11)                                                           DEFAULT NULL,
  `diag_settings_id`     INT(11)                                        NOT NULL           DEFAULT '0',
  `external_id`          VARCHAR(50)                                                       DEFAULT NULL
  COMMENT 'Идентификатор клиники в МИС',
  `show_in_advert`       TINYINT(1)                                                        DEFAULT '0',
  `district_id`          INT(11)                                                           DEFAULT NULL,
  `street_id`            INT(11)                                                           DEFAULT NULL,
  `online_booking`       TINYINT(1)                                     NOT NULL           DEFAULT '0',
  `conversion`           DECIMAL(6, 3)                                                     DEFAULT NULL,
  `hand_factor`          DOUBLE                                         NOT NULL           DEFAULT '0',
  `admission_cost`       DECIMAL(8, 5)                                  NOT NULL           DEFAULT '0.00000',
  `validate_phone`       TINYINT(4)                                     NOT NULL           DEFAULT '1',
  `contract_signed`      TINYINT(1)                                     NOT NULL           DEFAULT '0',
  `notify_emails`        VARCHAR(255)                                                      DEFAULT NULL,
  `notify_phones`        VARCHAR(255)                                                      DEFAULT NULL,
  `way_on_foot`          TEXT,
  `way_on_car`           TEXT,
  `min_price`            INT(11)                                                           DEFAULT NULL,
  `max_price`            INT(11)                                                           DEFAULT NULL,
  `count_reviews`        INT(11)                                        NOT NULL           DEFAULT '0',
  `rating_show`          FLOAT(9, 2)                                                       DEFAULT NULL,
  `scheduleForDoctors`   TINYINT(1)                                     NOT NULL           DEFAULT '1',
  `email_reconciliation` VARCHAR(255)                                                      DEFAULT NULL,
  `discount_online_diag` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `rewrite` (`rewrite_name`),
  KEY `parent_clinic_id` (`parent_clinic_id`),
  KEY `fk_district_id_idx` (`district_id`),
  KEY `name_idx` (`name`(100)),
  KEY `street_id_key` (`street_id`),
  KEY `city_idx` (`city_id`),
  KEY `fk_api_clinic_id` (`external_id`),
  KEY `clinic_min_price` (`min_price`),
  KEY `clinic_count_reviews` (`count_reviews`),
  KEY `clinic_rating_show` (`rating_show`),
  KEY `asterisk_phone_fk` (`asterisk_phone`),
  CONSTRAINT `asterisk_phone_fk` FOREIGN KEY (`asterisk_phone`) REFERENCES `phone` (`number`)
    ON UPDATE CASCADE,
  CONSTRAINT `fk_api_clinic_id` FOREIGN KEY (`external_id`) REFERENCES `api_clinic` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_district_id` FOREIGN KEY (`district_id`) REFERENCES `district` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `street_id_key` FOREIGN KEY (`street_id`) REFERENCES `street_dict` (`street_id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 5762
  DEFAULT CHARSET = utf8;

--
-- Table structure for table `clinic_billing`
--
CREATE TABLE `clinic_billing` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`clinic_id` INT(11) NOT NULL,
	`billing_date` DATE NOT NULL  COMMENT 'Дата отчетного периода',
	`status` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'статус получения денег',
	`clinic_contract_id` INT(11) NOT NULL COMMENT 'контракт',
	`start_sum` INT(11) NOT NULL COMMENT 'сумма биллинга на 1 число периода',
	`start_requests` INT(11) NOT NULL COMMENT 'заявок в биллинге на 1 число периода',
	`agreed_sum` INT(11) NULL COMMENT 'Согласованная сумма',
	`agreed_requests` INT(11) NULL  COMMENT 'Согласовано заявок',
	`today_sum` INT(11) NULL COMMENT 'Сумма на сегодня',
	`today_requests` INT(11) NULL  COMMENT 'Заявок на сегодня',
	`recieved_sum` FLOAT(10,2) NULL DEFAULT 0 COMMENT 'Полученная сумма',
	`changedata_date` DATETIME NULL  COMMENT 'Дата обновления информации по согласованным заявкам',
	PRIMARY KEY (`id`),
	INDEX `billing_date_idx` (`billing_date` ASC))
	ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Биллинг клиник';

--
-- Table structure for table `clinic_payment`
--
CREATE TABLE `clinic_payment` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`clinic_billing_id` INT(11) NOT NULL,
	`payment_date` DATE NOT NULL  COMMENT 'Дата перевода',
	`sum` float(10,2) NOT NULL DEFAULT 1 COMMENT 'сумма',
	PRIMARY KEY (`id`),
	INDEX `clinic_billing_idx` (`clinic_billing_id` ASC))
	ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Поступления';

--
-- Table structure for table `clinic_4_remote_api`
--
CREATE TABLE `clinic_4_remote_api` (
	`clinic_id` int(11) NOT NULL DEFAULT '0',
	`clinic_api_id` varchar(50) DEFAULT NULL,
	`api_id` int(11) DEFAULT NULL,
	UNIQUE KEY `clinic_id` (`clinic_id`,`clinic_api_id`),
	UNIQUE KEY `clinic` (`clinic_id`,`api_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `clinic_address`
--
CREATE TABLE `clinic_address` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`clinic_id` int(11) NOT NULL,
	`address` text NOT NULL,
	`isNew` enum('yes','no') NOT NULL DEFAULT 'no',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `clinic_admin`
--
CREATE TABLE `clinic_admin` (
	`clinic_admin_id` int(11) NOT NULL AUTO_INCREMENT,
	`email` varchar(50) NOT NULL,
	`fname` varchar(50) DEFAULT NULL,
	`lname` varchar(50) DEFAULT NULL,
	`mname` varchar(50) DEFAULT NULL,
	`phone` varchar(50) DEFAULT NULL,
	`cell_phone` varchar(50) DEFAULT NULL,
	`passwd` varchar(50) DEFAULT NULL,
	`admin_comment` text,
	`status` enum('enable','disable') NOT NULL DEFAULT 'enable',
	PRIMARY KEY (`clinic_admin_id`),
	UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Администратор клиники';

--
-- Table structure for table `clinic_contract`
--
CREATE TABLE `clinic_contract` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`clinic_id` INT(11) NOT NULL,
	`contract_id` TINYINT(3) NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `fk_clinic_contract_1_idx` (`clinic_id` ASC),
	INDEX `fk_clinic_contract_2_idx` (`contract_id` ASC),
	CONSTRAINT `fk_clinic_contract_1`
		FOREIGN KEY (`clinic_id`)
		REFERENCES `clinic` (`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
	CONSTRAINT `fk_clinic_contract_2`
		FOREIGN KEY (`contract_id`)
		REFERENCES `contract_dict` (`contract_id`)
		ON DELETE RESTRICT
		ON UPDATE RESTRICT)
ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='контракты клиники';


--
-- Table structure for table `clinic_contract_cost`
--
CREATE TABLE `clinic_contract_cost` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`service_id` INT(11) NULL COMMENT 'sector_id/diagnostic_id в зависимости от kind',
	`cost` FLOAT(6,2) NULL COMMENT 'стоимость заявки',
	`clinic_contract_id` INT(11) NULL COMMENT 'id контракта клиники',
	`from_num` SMALLINT(5) NULL COMMENT 'начальное количество заявок',
	`is_active` tinyint(1) NOT NULL DEFAULT '1',
	`group_uid` varchar(32) NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `fk_clinic_contract_cost_1_idx` (`clinic_contract_id` ASC),
	KEY `is_active` (`is_active`),
	CONSTRAINT `fk_clinic_contract_cost_1`
		FOREIGN KEY (`clinic_contract_id`)
		REFERENCES `clinic_contract` (`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE)
ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='стоимость услуг в контракте клиники';

--
-- Table structure for table `clinic_request_limit`
--
CREATE TABLE `clinic_request_limit` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`group_uid` int(11) DEFAULT NULL,
	`limit` smallint(5) DEFAULT 0,
	`date_notice` date DEFAULT NULL,
	`clinic_contract_id` int(11) DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `date_notice` (`date_notice`),
	CONSTRAINT `fk_clinic_request_limit_1`
		FOREIGN KEY (`clinic_contract_id`)
		REFERENCES `clinic_contract` (`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE)
ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='лимиты на кол-во заявок по клиникам';

--
-- Table structure for table `clinic_phone`
--
CREATE TABLE `clinic_phone` (
	`phone_id` int(11) NOT NULL AUTO_INCREMENT,
	`number_p` varchar(20) NOT NULL,
	`label` varchar(20) DEFAULT 'Основной',
	`clinic_id` int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`phone_id`),
	KEY `clinicId` (`clinic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='телефоны клиники';

--
-- Table structure for table `clinic_schedule`
--
CREATE TABLE `clinic_schedule` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`week_day` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 - workday, 1-monday,7-sunday',
	`clinic_id` int(11) NOT NULL DEFAULT '0',
	`start_time` time NOT NULL DEFAULT '08:00:00',
	`end_time` time NOT NULL DEFAULT '20:00:00',
	PRIMARY KEY (`id`),
	KEY `idx_clinic_id` (`clinic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Расписание работы клиники';

--
-- Table structure for table `clinic_settings`
--
CREATE TABLE `clinic_settings` (
	`settings_id` int(11) NOT NULL AUTO_INCREMENT,
	`contract_id` tinyint(4) NOT NULL DEFAULT '0',
	`price_1` float(9,2) NOT NULL DEFAULT '0.00' COMMENT 'default',
	`price_2` float(9,2) NOT NULL DEFAULT '0.00' COMMENT 'specialization stomatologia',
	`price_3` float(9,2) NOT NULL DEFAULT '0.00' COMMENT 'specialization plast hirurg',
	`show_billing` enum('show','hide') DEFAULT 'show',
	`lk_start_history_date` datetime DEFAULT NULL,
	PRIMARY KEY (`settings_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Доп. настройки для клиники';

--
-- Table structure for table `closest_district`
--
CREATE TABLE `closest_district` (
	`district_id`  DOUBLE NOT NULL,
	`closest_district_id` DOUBLE NOT NULL,
	`priority` TINYINT(3) DEFAULT NULL,
	PRIMARY KEY (`district_id`, `closest_district_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `closest_station`
--
CREATE TABLE `closest_station` (
	`station_id`  DOUBLE NOT NULL,
	`closest_station_id` DOUBLE NOT NULL,
	`priority` TINYINT(3) DEFAULT NULL,
	PRIMARY KEY (`station_id`, `closest_station_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `contract_dict`
--
CREATE TABLE `contract_dict` (
	`contract_id` tinyint(4) NOT NULL,
	`title` varchar(100) NOT NULL,
	`description` text,
	`isClinic` enum('yes','no') DEFAULT 'yes',
	`isDiagnostic` enum('yes','no') DEFAULT 'no',
	`kind` tinyint(1) DEFAULT 0,
	PRIMARY KEY (`contract_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Справочник контрактов с клиниками';

--
-- Table structure for table `contract_group`
--
CREATE TABLE `contract_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `kind` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `contract_group_kind` (`kind`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Table structure for table `contract_group_service`
--
CREATE TABLE `contract_group_service` (
  `contract_group_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  PRIMARY KEY (`contract_group_id`,`service_id`),
  KEY `contract_group_id` (`contract_group_id`),
  KEY `service_id` (`service_id`),
  CONSTRAINT `contract_group_service_ibfk_1` FOREIGN KEY (`contract_group_id`) REFERENCES `contract_group` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `ctr`
--
CREATE TABLE `ctr` (
	`clinic_id` int(11) NOT NULL,
	`year` smallint(4) unsigned NOT NULL,
	`month` tinyint(2) unsigned NOT NULL,
	`value` float(4,2) DEFAULT '0.00',
	PRIMARY KEY (`clinic_id`,`year`,`month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `degree_dict`
--
CREATE TABLE `degree_dict` (
	`degree_id` tinyint(4) NOT NULL AUTO_INCREMENT,
	`title` varchar(150) NOT NULL,
	PRIMARY KEY (`degree_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Справочник ученой степени';

--
-- Table structure for table `diagnostic_center`
--
CREATE TABLE `diagnostic_center` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`date_created` int(11) NOT NULL,
	`date_blocked` int(11) DEFAULT NULL,
	`name` varchar(512) NOT NULL,
	`rewrite_name` varchar(512) DEFAULT NULL,
	`view_count` int(11) NOT NULL DEFAULT '0',
	`status` int(11) NOT NULL DEFAULT '1',
	`contact_name` varchar(512) NOT NULL,
	`contact_phone` varchar(64) NOT NULL,
	`center_phone` varchar(64) NOT NULL,
	`additional_phone` varchar(64) DEFAULT NULL,
	`address` varchar(512) NOT NULL,
	`weekdays_open` varchar(512) NOT NULL,
	`weekend_open` varchar(512) NOT NULL,
	`saturday_open` varchar(512) NOT NULL,
	`sunday_open` varchar(512) NOT NULL,
	`url` varchar(512) NOT NULL,
	`short_description` text NOT NULL,
	`full_description` text NOT NULL,
	`map_latitude` varchar(64) NOT NULL,
	`map_longitude` varchar(64) NOT NULL,
	`logo` varchar(512) DEFAULT NULL,
	`address_map` varchar(512) DEFAULT NULL,
	`isCommerceSort` tinyint(4) DEFAULT '9',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `diagnostic_center_address`
--
CREATE TABLE `diagnostic_center_address` (
	`diagnostic_center_id` int(11) NOT NULL,
	`underground_station_id` int(11) NOT NULL,
	KEY `diagnostic_center_id` (`diagnostic_center_id`,`underground_station_id`),
	KEY `underground_station_id` (`underground_station_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `diagnostic_center_diagnostica`
--
CREATE TABLE `diagnostic_center_diagnostica` (
	`diagnostic_center_id` int(11) NOT NULL,
	`diagnostica_id` int(11) NOT NULL,
	`price` int(11) DEFAULT NULL,
	`special_price` float DEFAULT NULL,
	PRIMARY KEY (`diagnostic_center_id`,`diagnostica_id`),
	KEY `diagnostic_center_id` (`diagnostic_center_id`),
	KEY `diagnostica_id` (`diagnostica_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `diagnostic_center_image`
--
CREATE TABLE `diagnostic_center_image` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`diagnostic_center_id` int(11) NOT NULL,
	`image` varchar(512) NOT NULL,
	`image_description` varchar(512) DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `diagnostic_center_id` (`diagnostic_center_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `diagnostica`
--
CREATE TABLE `diagnostica` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(512) NOT NULL,
	`rewrite_name` varchar(512) NOT NULL,
	`title` varchar(512) DEFAULT NULL,
	`meta_keywords` text,
	`meta_desc` text,
	`meta_description` text,
	`parent_id` int(11) NOT NULL DEFAULT '0',
	`reduction_name` varchar(512) DEFAULT NULL,
	`sort` int(11) DEFAULT '999',
	`sort_in_subtype` tinyint(3) DEFAULT NULL,
	`diagnostica_subtype_id` int(11) DEFAULT NULL,
	`accusative_name` varchar(512) NOT NULL,
	`genitive_name` varchar(512) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `parent_id` (`parent_id`),
	KEY `fk_diagnostica_subtype_idx` (`diagnostica_subtype_id`),
	KEY `rewrite_name_idx` (`rewrite_name`),
	CONSTRAINT `fk_diagnostica_subtype_id` FOREIGN KEY (`diagnostica_subtype_id`) REFERENCES `diagnostica_subtype` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `diagnostica_subtype`
--
CREATE TABLE `diagnostica_subtype` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`diagnostica_id` int(11) NOT NULL,
	`priority` tinyint(3) DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `fk_diagnostica_idx` (`diagnostica_id`),
	CONSTRAINT `fk_diagnostica_id` FOREIGN KEY (`diagnostica_id`) REFERENCES `diagnostica` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='подвиды диагностик';

--
-- Table structure for table `diagnostica4clinic`
--
CREATE TABLE `diagnostica4clinic` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`diagnostica_id` int(11) NOT NULL DEFAULT '0',
	`clinic_id` int(11) NOT NULL DEFAULT '0',
	`price` float(9,2) NOT NULL DEFAULT '0.00',
	`special_price` float(9,2) DEFAULT '0.00',
  `price_for_online` float(9,2) DEFAULT '0.00',
	PRIMARY KEY (`id`),
	UNIQUE KEY `d4c_unique_idx` (`diagnostica_id`, `clinic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Исследования для клиники';

--
-- Table structure for table `diagnostica_settings`
--
CREATE TABLE `diagnostica_settings` (
	`settings_id` int(11) NOT NULL AUTO_INCREMENT,
	`contract_id` tinyint(4) NOT NULL DEFAULT '0',
	`price` float(9,2) NOT NULL DEFAULT '0.00' COMMENT 'default',
	`show_billing` enum('show','hide') DEFAULT 'show',
	`lk_start_history_date` datetime DEFAULT NULL,
	PRIMARY KEY (`settings_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Доп. настройки для диагностических центров';

--
-- Table structure for table `district`
--
CREATE TABLE `district` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(50) DEFAULT NULL,
	`rewrite_name` varchar(50) DEFAULT NULL,
	`id_city` int(11) NOT NULL DEFAULT '0',
	`id_area` int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Районы';

--
-- Table structure for table `district_has_underground_station`
--
CREATE TABLE `district_has_underground_station` (
	`id_district` int(11) NOT NULL DEFAULT '0',
	`id_station` int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id_district`,`id_station`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `doctor`
--
CREATE TABLE `doctor` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`clinic_id` int(11) DEFAULT NULL,
	`created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`departure` int(1) DEFAULT NULL,
	`rating` float DEFAULT '0',
	`rating_education` float NOT NULL DEFAULT '0',
	`rating_ext_education` float NOT NULL DEFAULT '0',
	`rating_experience` float NOT NULL DEFAULT '0',
	`rating_academic_degree` float NOT NULL DEFAULT '0',
	`rating_clinic` float NOT NULL DEFAULT '0',
	`rating_opinion` float NOT NULL DEFAULT '0',
	`total_rating` float NOT NULL DEFAULT '0',
	`rating_internal` float NOT NULL DEFAULT '0',
	`price` int(11) DEFAULT NULL,
	`special_price` int(11) DEFAULT NULL,
	`experience_year` int(11) DEFAULT NULL,
	`status` int(11) NOT NULL,
	`view_count` int(11) DEFAULT '0',
	`name` varchar(512) NOT NULL,
	`rewrite_name` varchar(512) DEFAULT NULL,
	`image` varchar(1024) DEFAULT NULL,
	`phone` varchar(512) DEFAULT NULL,
	`phone_appointment` varchar(512) DEFAULT NULL,
	`text` text,
	`text_degree` varchar(512) DEFAULT NULL,
	`category_id` int(11) DEFAULT '0',
	`degree_id` int(11) DEFAULT '0',
	`rank_id` int(11) DEFAULT '0',
	`text_education` text,
	`text_association` text,
	`text_spec` text,
	`text_course` text,
	`text_experience` text,
	`attach` varchar(512) DEFAULT NULL,
	`note` text,
	`openNote` text,
	`sex` int(1) NOT NULL DEFAULT '0',
	`email` varchar(512) DEFAULT NULL,
	`password` varchar(512) DEFAULT NULL,
	`interval_appointment` int(11) NOT NULL DEFAULT '15',
	`addNumber` int(4) DEFAULT NULL COMMENT 'ASTERISK number',
	`schedule_state` enum('disable','enable') NOT NULL DEFAULT 'enable' COMMENT 'enable, disable',
	`doctor_list_state` enum('show','hide') NOT NULL DEFAULT 'show' COMMENT 'show, hide',
	`kids_reception` int(1) NOT NULL DEFAULT 0,
	`kids_age_from` int(4),
	`kids_age_to` int(4),
	`conversion` decimal(6,3) DEFAULT NULL,
	`update_tips` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
	PRIMARY KEY (`id`),
	KEY `clinic_id` (`clinic_id`),
	KEY `addNumber` (`addNumber`),
	KEY `name_idx` (`name`(100)),
	KEY `rewrite` (`rewrite_name`(255)),
	KEY `kids_reception` (`kids_reception`),
	KEY `update_tips_key` (`update_tips`),
	CONSTRAINT `doctor_ibfk_17` FOREIGN KEY (`clinic_id`) REFERENCES `clinic` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='врачи';

--
-- Table structure for table `doctor_4_clinic`
--
CREATE TABLE `doctor_4_clinic` (
	`doctor_id` int(11) NOT NULL,
	`clinic_id` int(11) NOT NULL,
	`schedule_step` tinyint(4) DEFAULT '60' COMMENT 'In minute',
	`has_slots` tinyint(1) DEFAULT '0',
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`doc_external_id` int(11) DEFAULT NULL,
	`schedule_rule` text,
	`last_slots_update` timestamp NULL DEFAULT NULL COMMENT 'время последней загрузки слотов',
	`type` tinyint(4) NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`),
	UNIQUE KEY `external_id_idx` (`doc_external_id`),
	KEY `last_slots_update_idx` (`last_slots_update`),
	KEY `doctor_id_clinic_id_type_index` (`doctor_id`,`clinic_id`,`type`),
	KEY `fk_clinic` (`clinic_id`),
	CONSTRAINT `fk_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT `fk_clinic` FOREIGN KEY (`clinic_id`) REFERENCES `clinic` (`id`) ON UPDATE CASCADE,
	CONSTRAINT `doctor_4_clinic_external_fk` FOREIGN KEY (`doc_external_id`) REFERENCES `api_doctor` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Врачи в клиниках';

--
-- Table structure for table `doctor_4_remote_api`
--
CREATE TABLE `doctor_4_remote_api` (
	`doctor_id` int(11) NOT NULL,
	`doctor_api_id` varchar(50) NOT NULL,
	`api_id` int(11) NOT NULL,
	PRIMARY KEY (`doctor_id`),
	UNIQUE KEY `doctor` (`doctor_id`,`api_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='������������� ������ ��� ������� ���';

--
-- Table structure for table `doctor_appointment`
--
CREATE TABLE `doctor_appointment` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`doctor_id` int(11) NOT NULL,
	`created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`done` int(1) NOT NULL,
	`name` varchar(512) NOT NULL,
	`phone` varchar(512) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `doctor` (`doctor_id`),
	CONSTRAINT `doctor_appointment_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='заявки на запись';

--
-- Table structure for table `doctor_opinion`
--
CREATE TABLE `doctor_opinion` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`doctor_id` int(11) NOT NULL COMMENT 'врач',
	`created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`allowed` int(1) NOT NULL COMMENT 'разрешён к публикации',
	`rating_qualification` int(11) DEFAULT NULL COMMENT 'рейтинг - квалификация',
	`age` int(11) DEFAULT NULL COMMENT 'возраст пациента',
	`phone` varchar(512) NOT NULL DEFAULT '',
	`rating_attention` int(11) DEFAULT NULL COMMENT 'рейтинг - внимание',
	`rating_room` int(11) DEFAULT NULL COMMENT 'рейтинг - кабинет',
	`name` varchar(512) NOT NULL COMMENT 'имя пациента',
	`text` text COMMENT 'текст отзыва',
	`request_id` int(11) DEFAULT NULL,
	`lk_status` int(11) NOT NULL DEFAULT '1',
	`date_publication` int(11) DEFAULT NULL,
	`is_fake` tinyint(1) DEFAULT '1',
	`author` char(4) NOT NULL,
	`rating_color` tinyint(4) DEFAULT NULL,
	`origin` enum('editor','combine','original') NOT NULL DEFAULT 'original',
	`status` enum('enable','disable','hidden') DEFAULT 'disable',
	`operatorComment` text,
	PRIMARY KEY (`id`),
	KEY `doctor` (`doctor_id`),
	KEY `allowed` (`allowed`,`doctor_id`),
	CONSTRAINT `doctor_opinion_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='отзывы пациентов';

--
-- Table structure for table `doctor_request`
--
CREATE TABLE `doctor_request` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`sector_id` int(11) DEFAULT NULL,
	`doctor_id` int(11) DEFAULT NULL,
	`created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`done` int(11) DEFAULT NULL,
	`name` varchar(512) NOT NULL,
	`phone` varchar(512) NOT NULL,
	`departure` int(1) DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `doctor` (`doctor_id`),
	KEY `sector` (`sector_id`),
	CONSTRAINT `doctor_request_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT `doctor_request_ibfk_2` FOREIGN KEY (`sector_id`) REFERENCES `sector` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='запросы рекомендаций ("порекомендуйте врача")';

--
-- Table structure for table `doctor_request_address`
--
CREATE TABLE `doctor_request_address` (
	`doctor_request_id` int(11) NOT NULL,
	`underground_station_id` int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`doctor_request_id`,`underground_station_id`),
	KEY `underground_station_id` (`underground_station_id`),
	CONSTRAINT `doctor_request_address_ibfk_1` FOREIGN KEY (`doctor_request_id`) REFERENCES `doctor_request` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT `doctor_request_address_ibfk_2` FOREIGN KEY (`underground_station_id`) REFERENCES `underground_station` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='связи между запросами рекомендаций и станциями метро';

--
-- Table structure for table `doctor_sсhedule_on_day`
--
CREATE TABLE `doctor_sсhedule_on_day` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`on_date_schedule` date DEFAULT NULL,
	`doctor_id` int(11) NOT NULL DEFAULT '0',
	`clinic_id` int(11) NOT NULL DEFAULT '0',
	`start_time` time DEFAULT NULL,
	`end_time` time DEFAULT NULL,
	`type_state` enum('absence','presence','request','reserve','external_data') DEFAULT NULL,
	`external_data` varchar(255) DEFAULT NULL COMMENT 'request_id or external data',
	`request_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'request_id - denormalize',
	PRIMARY KEY (`id`),
	KEY `search_doc_clinic_date` (`on_date_schedule`,`doctor_id`,`clinic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Текущие записи в расписании врача';

--
-- Table structure for table `doctor_schedule_presence`
--
CREATE TABLE `doctor_schedule_presence` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`week_day` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 - workday, 1-monday,7-sunday',
	`doctor_id` int(11) NOT NULL DEFAULT '0',
	`clinic_id` int(11) NOT NULL DEFAULT '0',
	`start_time` time NOT NULL,
	`end_time` time NOT NULL,
	PRIMARY KEY (`id`),
	KEY `search_doc_clinic_date` (`week_day`,`doctor_id`,`clinic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Расписание врача - присутствие по дням недели';

--
-- Table structure for table `doctor_sector`
--
CREATE TABLE `doctor_sector` (
	`doctor_id` int(11) NOT NULL,
	`sector_id` int(11) NOT NULL,
	PRIMARY KEY (`doctor_id`,`sector_id`),
	KEY `doctor_sector_ibfk_1` (`sector_id`),
	CONSTRAINT `doctor_sector_ibfk_1` FOREIGN KEY (`sector_id`) REFERENCES `sector` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT `doctor_sector_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='связи между врачами и направленями + год начала практики';

--
-- Table structure for table `education`
--
CREATE TABLE `education` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(512) NOT NULL,
	`acronym` varchar(512) DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='образование';

--
-- Table structure for table `education_4_doctor`
--
CREATE TABLE `education_4_doctor` (
	`doctor_id` int(11) NOT NULL DEFAULT '0',
	`education_id` int(11) NOT NULL DEFAULT '0',
	`year` year(4) DEFAULT NULL,
	PRIMARY KEY (`doctor_id`,`education_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Образование врача';

--
-- Table structure for table `education_dict`
--
CREATE TABLE `education_dict` (
	`education_id` int(11) NOT NULL AUTO_INCREMENT,
	`title` varchar(255) NOT NULL,
	`type` enum('none','college','university','internship','traineeship','graduate') DEFAULT 'none',
	PRIMARY KEY (`education_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Справочник учебных заведений';

--
-- Table structure for table `illness`
--
CREATE TABLE `illness` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`sector_id` int(11) NOT NULL,
	`name` varchar(512) NOT NULL,
	`rewrite_name` varchar(512) DEFAULT NULL,
	`full_name` varchar(512) DEFAULT NULL,
	`text_desc` text NOT NULL,
	`text_symptom` text NOT NULL,
	`text_treatment` text NOT NULL,
	`text_other` text,
	`title` text,
	`meta_keywords` text,
	`meta_desc` text,
	`is_hidden` int(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `is_hidden` (`is_hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `img_clinic`
--
CREATE TABLE `img_clinic` (
	`img_id` int(11) NOT NULL AUTO_INCREMENT,
	`clinic_id` int(11) NOT NULL DEFAULT '0',
	`imgPath` varchar(100) NOT NULL,
	`description` text,
	PRIMARY KEY (`img_id`),
	KEY `clinicId` (`clinic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Иображения для клиники';

--
-- Table structure for table `log`
--
CREATE TABLE `log` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`user_id` int(11) NOT NULL,
	`created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`model` varchar(512) NOT NULL,
	`action` varchar(512) NOT NULL,
	`item_id` int(11) DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='логи';

--
-- Table structure for table `log_ami`
--
CREATE TABLE `log_ami` (
	`log_id` int(11) NOT NULL AUTO_INCREMENT,
	`date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`request` text,
	`response` text,
	PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `log_back_user`
--
CREATE TABLE `log_back_user` (
	`log_id` bigint(20) NOT NULL AUTO_INCREMENT,
	`crDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`message` varchar(255) DEFAULT NULL,
	`log_code_id` char(5) NOT NULL DEFAULT '0',
	`user_id` int(11) DEFAULT '0',
	PRIMARY KEY (`log_id`),
	KEY `code` (`log_code_id`),
	KEY `crDate` (`crDate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Логи действий пользователя';

--
-- Table structure for table `log_dict`
--
CREATE TABLE `log_dict` (
	`log_code_id` char(5) NOT NULL,
	`title` varchar(50) DEFAULT NULL,
	PRIMARY KEY (`log_code_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Справочник действий пользователя';

--
-- Table structure for table `log_sms`
--
CREATE TABLE `log_sms` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`request_id` int(11) DEFAULT NULL,
	`sms_id` int(11) DEFAULT NULL,
	`phone` varchar(11) NOT NULL,
	`message` varchar(512) NOT NULL,
	`status` int(4) DEFAULT NULL,
	`datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`service_params` varchar(512) DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `idx_request_id` (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `mailQuery`
--
CREATE TABLE `mailQuery` (
	`idMail` int(11) NOT NULL AUTO_INCREMENT,
	`emailTo` varchar(50) NOT NULL,
	`subj` varchar(255) NOT NULL,
	`message` text,
	`resendCount` tinyint(4) DEFAULT NULL,
	`status` enum('new','resend','sended','deleted') DEFAULT 'new',
	`crDate` datetime NOT NULL,
	`sendDate` datetime DEFAULT NULL,
	`priority` int(11) NOT NULL DEFAULT '99',
	`reply` varchar(255) NOT NULL,
	PRIMARY KEY (`idMail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='������� ��������';

--
-- Table structure for table `page`
--
CREATE TABLE `page` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`url` VARCHAR(1024) NOT NULL  COMMENT 'Url страницы',
	`h1` VARCHAR(1024) NOT NULL  COMMENT 'Основной заголовок страницы',
	`title` VARCHAR(1024) NOT NULL  COMMENT 'title страницы',
	`keywords` TEXT NULL  COMMENT 'meta-keywords для страницы',
	`description` TEXT NULL  COMMENT 'meta-description для страницы',
	`seo_text_top` TEXT NULL  COMMENT 'верхний seo текст',
	`seo_text_bottom` TEXT NULL  COMMENT 'нижний seo текст',
	`is_show` TINYINT(1) NULL DEFAULT 1  COMMENT 'Флаг показывать/не показывать',
	`id_city` INT(10) NULL DEFAULT 0  COMMENT 'ID города',
	`site` TINYINT(3) NULL DEFAULT 1  COMMENT 'Сайт 1 - docdoc, 2 - diagnostica',
	PRIMARY KEY (`id`),
	INDEX `search_idx` (`site` ASC, `is_show` ASC, `url`(100) ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='SEO описание страниц';


--
-- Table structure for table `partner`
--
CREATE TABLE `partner` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(64) NOT NULL,
	`login` varchar(16) DEFAULT NULL,
	`password` varchar(32) DEFAULT NULL,
	`contact_name` varchar(64) DEFAULT NULL,
	`contact_phone` varchar(64) DEFAULT NULL,
	`contact_email` varchar(64) DEFAULT NULL,
	`city_id` tinyint(4) NOT NULL,
	`password_salt` varchar(16) DEFAULT NULL,
	`offer_accepted` smallint(6) DEFAULT NULL,
	`offer_accepted_timestamp` timestamp NULL DEFAULT NULL,
	`offer_accepted_from_addresses` varchar(45) DEFAULT NULL,
	`cost_per_request` int(11) DEFAULT NULL,
	`param_client_uid_name` varchar(50) DEFAULT NULL,
	`use_special_price` tinyint(1) NOT NULL DEFAULT '0',
	`request_kind` int(11) DEFAULT '0',
	`send_sms` tinyint(1) NOT NULL DEFAULT '0',
	`send_sms_to_clinic` tinyint(1) DEFAULT '1',
	`not_merged_requests` int(11) NOT NULL,
 	`show_clinics_with_contracts` tinyint(1) NOT NULL DEFAULT '0',
	`json_params` TEXT NULL,
	`phone_queue` varchar(100) DEFAULT 'partnerq',
  `show_watermark` tinyint(1) NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`),
	UNIQUE KEY `contact_email_unq` (`contact_email`),
	UNIQUE KEY `login_unq` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Справочник партнеров';

--
-- Table structure for table `partner_widget`
--
CREATE TABLE `partner_widget` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`partner_id` INT(11) NOT NULL,
	`widget` varchar(50) NOT NULL,
	`json_config` text DEFAULT NULL,
	`is_used` tinyint(1) DEFAULT 1,
	PRIMARY KEY (`id`),
	INDEX `partner_widget_idx` (`partner_id` ASC, `widget` ASC),
	KEY `partner_fk` (`partner_id`),
	CONSTRAINT `partner_fk` FOREIGN KEY (`partner_id`) REFERENCES `partner` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Настройки партнерских виджетов';

--
-- Table structure for table `phone`
--
CREATE TABLE `phone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` char(12) DEFAULT NULL,
  `provider_id` int(11) NOT NULL DEFAULT '1',
  `model_name` VARCHAR(255)   DEFAULT NULL,
  `partner_id` INT(11)        DEFAULT NULL,
  `comment`    VARCHAR(255)   DEFAULT NULL,
  `mtime`      TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `muser_id`   INT(11)        DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `number` (`number`),
  KEY `fk_provider_id` (`provider_id`),
  CONSTRAINT `fk_provider_id` FOREIGN KEY (`provider_id`) REFERENCES `phone_provider` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `phone_provider`
--
CREATE TABLE `phone_provider` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `price_range`
--
CREATE TABLE `price_range` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`price_from` int(11) DEFAULT NULL,
	`price_to` int(11) DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='диапазоны цен';

--
-- Table structure for table `promo_text`
--
CREATE TABLE `promo_text` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`disabled` int(1) NOT NULL,
	`name` varchar(512) NOT NULL,
	`text` text NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='промо-блоки';

--
-- Table structure for table `promo_text_zone`
--
CREATE TABLE `promo_text_zone` (
	`promo_zone_id` int(11) NOT NULL,
	`promo_text_id` int(11) NOT NULL,
	PRIMARY KEY (`promo_zone_id`,`promo_text_id`),
	KEY `promo_text_zone_ibfk_2` (`promo_text_id`),
	CONSTRAINT `promo_text_zone_ibfk_1` FOREIGN KEY (`promo_zone_id`) REFERENCES `promo_zone` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT `promo_text_zone_ibfk_2` FOREIGN KEY (`promo_text_id`) REFERENCES `promo_text` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='связи между промо-блоками и промо-зонами';

--
-- Table structure for table `promo_zone`
--
CREATE TABLE `promo_zone` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`code` varchar(16) NOT NULL,
	`disabled` int(1) NOT NULL,
	`name` varchar(512) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='промо-зоны';

--
-- Table structure for table `queue`
--
CREATE TABLE `queue` (
	`SIP` int(11) NOT NULL,
	`startTime` datetime NOT NULL,
	`user_id` int(11) NULL DEFAULT NULL,
	`asteriskPool` varchar(20) DEFAULT NULL,
	`status` tinyint(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`SIP`),
	KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Очередь Asterisk';

--
-- Table structure for table `rank_dict`
--
CREATE TABLE `rank_dict` (
	`rank_id` tinyint(4) NOT NULL AUTO_INCREMENT,
	`title` varchar(150) NOT NULL,
	PRIMARY KEY (`rank_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Справочник званий врачей';

--
-- Table structure for table `reg_city`
--
CREATE TABLE `reg_city` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`rewrite_name` varchar(255) NOT NULL,
	`city_id` int(11) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Областные города';

--
-- Table structure for table `remote_api`
--
CREATE TABLE `remote_api` (
	`api_id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`login` varchar(255) NOT NULL,
	`password` varchar(255) NOT NULL,
	`url` varchar(255) NOT NULL,
	PRIMARY KEY (`api_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='������� ���';

--
-- Table structure for table `request`
--
CREATE TABLE `request` (
	`req_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`clientId` int(11) DEFAULT NULL,
	`id_city` int(11) DEFAULT '1',
	`client_name` varchar(255) NOT NULL,
	`client_phone` varchar(255) NOT NULL,
	`req_comments` text,
	`req_created` int(10) unsigned NOT NULL,
	`req_status` tinyint(3) unsigned NOT NULL DEFAULT '1',
	`req_user_id` int(11) DEFAULT NULL,
	`req_departure` tinyint(1) DEFAULT '0',
	`req_sector_id` int(11) DEFAULT '0',
	`diagnostics_id` int(4) DEFAULT '0',
	`diagnostics_other` varchar(100) DEFAULT NULL,
	`req_doctor_id` int(11) NOT NULL DEFAULT '0',
	`req_type` tinyint(4) unsigned NOT NULL,
	`kind` tinyint(1) NOT NULL DEFAULT '0',
	`source_type` int(3) DEFAULT '1',
	`doctor_request_id` int(11) DEFAULT NULL,
	`clinic_id` int(11) DEFAULT NULL,
	`req_client_stations` tinyint(4) NOT NULL DEFAULT '0',
	`opinion_id` int(11) DEFAULT NULL,
	`record` varchar(512) DEFAULT NULL,
	`record1` varchar(512) DEFAULT NULL,
	`record2` varchar(512) DEFAULT NULL,
	`url_record` varchar(512) DEFAULT NULL,
	`records` varchar(50) DEFAULT NULL,
	`lk_status` int(11) NOT NULL DEFAULT '1',
	`is_transfer` tinyint(4) DEFAULT '0' COMMENT '0 - no, 1- ok',
	`date_admission` int(11) DEFAULT NULL,
	`appointment_time` int(11) DEFAULT NULL,
	`appointment_status` int(11) NOT NULL DEFAULT '0',
	`request_cost` int(11) NULL DEFAULT NULL,
	`call_occured` tinyint(4) NOT NULL DEFAULT '0',
	`call_later_time` int(11) DEFAULT NULL,
	`clinic_address_id` int(11) DEFAULT NULL,
	`status_sms` tinyint(4) NOT NULL DEFAULT '0',
	`actionpay_id` varchar(255) DEFAULT NULL,
	`transferred` enum('0','1') NOT NULL DEFAULT '0',
	`age_selector` enum('multy','child','adult') DEFAULT 'multy',
	`client_comments` text,
	`last_call_id` varchar(20) DEFAULT NULL,
	`partner_id` int(11) DEFAULT NULL,
	`reject_reason` tinyint(4) NOT NULL DEFAULT '0',
	`destination_phone_id` int(11) DEFAULT NULL,
	`date_record` datetime DEFAULT NULL,
	`add_client_phone` char(12) DEFAULT NULL,
	`transferred_clinic_id` int(11) NOT NULL DEFAULT '0',
	`is_hot` tinyint(1) DEFAULT '0',
	`for_listener` tinyint(1) DEFAULT '0',
	`enter_point` varchar(20) DEFAULT NULL COMMENT 'Точка входа, в которой была создана заявка',
	`partner_cost` decimal(10,6) NOT NULL DEFAULT '0.000000',
	`billing_status` tinyint NOT NULL DEFAULT '0',
	`partner_status` tinyint NOT NULL DEFAULT '0',
	`validation_code` varchar(6) DEFAULT NULL,
	`date_billing` datetime DEFAULT NULL,
	`processing_time` int NOT NULL DEFAULT '0',
	`token` char(32) DEFAULT NULL,
	`expire_time` datetime DEFAULT NULL,
	`queue` varchar(15) DEFAULT NULL,
	PRIMARY KEY (`req_id`),
	KEY `doctor` (`req_doctor_id`),
	KEY `sector` (`req_sector_id`),
	KEY `status` (`req_status`),
	KEY `type` (`req_type`),
	KEY `crDate` (`req_created`),
	KEY `destination_phone_id` (`destination_phone_id`),
	KEY `date_record` (`date_record`),
	KEY `client_phone` (`client_phone`),
	KEY `kind` (`kind`),
	KEY `diagnostics` (`diagnostics_id`),
	KEY `partner_id_index` (`partner_id`),
	KEY `add_client_phone_index` (`add_client_phone`),
	KEY `billing_status_key` (`billing_status`),
	KEY `partner_status_index` (`partner_status`),
	KEY `token` (`token`),
	KEY `date_billing` (`date_billing`),
	CONSTRAINT `request_ibfk_1` FOREIGN KEY (`destination_phone_id`) REFERENCES `phone` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=COMPACT;

--
-- Table structure for table `request_4_remote_api`
--
CREATE TABLE `request_4_remote_api` (
	`request_id` int(11) NOT NULL,
	`request_api_id` varchar(50) NOT NULL,
	`api_id` int(11) NOT NULL,
	`update_status` enum('no','yes') NOT NULL DEFAULT 'no',
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`doctor_schedule_ids` varchar(255) NOT NULL,
	PRIMARY KEY (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='������������� ������ ��� ������� ���';

--
-- Table structure for table `request_history`
--
CREATE TABLE `request_history` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`request_id` int(11) NOT NULL,
	`created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`action` tinyint(4) NOT NULL,
	`user_id` int(11) NOT NULL,
	`text` text NOT NULL,
	PRIMARY KEY (`id`),
	KEY `idx_request_id` (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `request_partner`
--
CREATE TABLE `request_partner` (
	`request_id` int(11) NOT NULL,
	`partner_id` varchar(64) NOT NULL,
	`external_status` tinyint(4) NOT NULL,
	`updated_status` enum('no','yes') NOT NULL DEFAULT 'no',
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	UNIQUE KEY `request_id` (`request_id`,`partner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `request_record`
--
CREATE TABLE `request_record` (
  `request_id` int(11) NOT NULL,
  `record_id` int(11) NOT NULL AUTO_INCREMENT,
  `record` varchar(100) DEFAULT NULL,
  `crDate` datetime DEFAULT NULL,
  `duration` int(11) DEFAULT '0',
  `comments` tinytext,
  `isOpinion` enum('yes','no') DEFAULT 'no',
  `isAppointment` enum('yes','no') DEFAULT 'no',
  `isVisit` enum('yes','no') DEFAULT 'no',
  `source` tinyint(3) NOT NULL DEFAULT '0',
  `clinic_id` int(11) NOT NULL DEFAULT '0',
  `year` smallint(4) unsigned DEFAULT '0',
  `month` tinyint(2) unsigned DEFAULT '0',
  `type` tinyint(1) unsigned DEFAULT '0',
  `replaced_phone` varchar(15) DEFAULT NULL,
  `external_call_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`record_id`),
  KEY `request_id` (`request_id`,`record`),
  KEY `clinic_id` (`clinic_id`),
  KEY `cr_date` (`crDate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `request_spam`
--
CREATE TABLE `request_spam` (
  `req_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_city` int(11) DEFAULT '1',
  `client_name` varchar(255) NOT NULL,
  `client_phone` varchar(255) NOT NULL,
  `req_created` int(10) unsigned NOT NULL,
  `req_status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `req_departure` tinyint(1) DEFAULT '0',
  `req_sector_id` int(11) DEFAULT '0',
  `diagnostics_id` int(4) DEFAULT '0',
  `req_doctor_id` int(11) NOT NULL DEFAULT '0',
  `req_type` tinyint(4) unsigned NOT NULL,
  `kind` tinyint(1) NOT NULL DEFAULT '0',
  `source_type` int(3) DEFAULT '1',
  `clinic_id` int(11) DEFAULT NULL,
  `date_admission` int(11) DEFAULT NULL,
  `appointment_time` int(11) DEFAULT NULL,
  `age_selector` enum('multy','child','adult') DEFAULT 'multy',
  `client_comments` text,
  `partner_id` int(11) DEFAULT NULL,
  `date_record` datetime DEFAULT NULL,
  `is_hot` tinyint(1) DEFAULT '0',
  `enter_point` varchar(20) DEFAULT NULL COMMENT 'Точка входа, в которой была создана заявка',
  `token` char(32) DEFAULT NULL,
  PRIMARY KEY (`req_id`),
  KEY `client_phone` (`client_phone`),
  KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `request_station`
--
CREATE TABLE `request_station` (
	`request_id` int(10) unsigned NOT NULL,
	`station_id` int(10) unsigned NOT NULL,
	UNIQUE KEY `request_id` (`request_id`,`station_id`),
	KEY `station_id` (`station_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=COMPACT;

--
-- Table structure for table `right_4_user`
--
CREATE TABLE `right_4_user` (
	`right_id` tinyint(4) NOT NULL DEFAULT '0',
	`user_id` int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`right_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Права пользователя';

--
-- Table structure for table `schedule_day_pool`
--
CREATE TABLE `schedule_day_pool` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`doctor_id` int(11) NOT NULL DEFAULT '0',
	`clinic_id` int(11) NOT NULL DEFAULT '0',
	`on_date_schedule` date NOT NULL,
	`start_time` time DEFAULT NULL,
	`end_time` time DEFAULT NULL,
	`time_interval_array` varchar(2048) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `search_doc_clinic_date` (`doctor_id`,`clinic_id`,`on_date_schedule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Буфер для хранения расписания';

--
-- Table structure for table `sector`
--
CREATE TABLE `sector` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`doctor_count_active` int(11) NOT NULL DEFAULT '0',
	`doctor_count_all` int(11) NOT NULL DEFAULT '0',
	`name` varchar(512) NOT NULL,
	`name_genitive` varchar(64) NOT NULL,
	`name_plural` varchar(64) NOT NULL,
	`name_plural_genitive` varchar(64) NOT NULL,
	`rewrite_name` varchar(512) NOT NULL,
	`spec_name` varchar(512) DEFAULT NULL,
	`rewrite_spec_name` varchar(512) DEFAULT NULL,
	`hidden_in_menu` tinyint(4) NOT NULL DEFAULT '0',
	`clinic_seo_title` varchar(255) DEFAULT NULL,
	`sector_seo_title` varchar(255) DEFAULT NULL,
	`is_double` tinyint(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `rewrite_name` (`rewrite_name`(255)),
	KEY `is_double_idx` (`is_double`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='направления';

--
-- Table structure for table `related_specialty`
--
CREATE TABLE `related_specialty` (
	`specialty_id` int(11) NOT NULL,
	`related_specialty_id` int(11) NOT NULL,
	PRIMARY KEY (`specialty_id`,`related_specialty_id`),
	KEY `related_specialty_id_fk` (`related_specialty_id`),
	CONSTRAINT `related_specialty_id_fk` FOREIGN KEY (`related_specialty_id`) REFERENCES `sector` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT `specialty_id_fk` FOREIGN KEY (`specialty_id`) REFERENCES `sector` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `sector_seo_text`
--
CREATE TABLE `sector_seo_text` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`disabled` int(1) NOT NULL,
	`position` int(11) NOT NULL DEFAULT '1',
	`page_type` tinyint(4) DEFAULT '1',
	`name` varchar(512) NOT NULL,
	`text` text NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='сео-блоки для направлений';

--
-- Table structure for table `sector_seo_text_sector`
--
CREATE TABLE `sector_seo_text_sector` (
	`sector_id` int(11) NOT NULL,
	`sector_seo_text_id` int(11) NOT NULL,
	PRIMARY KEY (`sector_id`,`sector_seo_text_id`),
	KEY `sector_seo_text_sector_ibfk_2` (`sector_seo_text_id`),
	CONSTRAINT `sector_seo_text_sector_ibfk_1` FOREIGN KEY (`sector_id`) REFERENCES `sector` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT `sector_seo_text_sector_ibfk_2` FOREIGN KEY (`sector_seo_text_id`) REFERENCES `sector_seo_text` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='связи между сео-блоками и направлениями';

--
-- Table structure for table `sip_channels`
--
CREATE TABLE `sip_channels` (
	`sip` int(4) unsigned NOT NULL,
	`channel` varchar(50) NOT NULL,
	`ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`request_id` int(11) unsigned NULL,
	`active` tinyint(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`sip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `site_problrem`
--
CREATE TABLE `site_problrem` (
	`problem_id` int(11) NOT NULL AUTO_INCREMENT,
	`user_id` int(11) DEFAULT NULL,
	`subj` varchar(255) DEFAULT NULL,
	`page` varchar(255) DEFAULT NULL,
	`problem_text` text,
	`cr_date` datetime NOT NULL,
	`status` enum('new','open','resolve','reject','close') DEFAULT 'new',
	`support_user_id` int(11) DEFAULT NULL,
	`is_critical` enum('yes','no') DEFAULT 'no',
	PRIMARY KEY (`problem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Ошибки';

--
-- Table structure for table `slot`
--
CREATE TABLE `slot` (
	`id` bigint(15) NOT NULL AUTO_INCREMENT,
	`doctor_4_clinic_id` int(11) NOT NULL,
	`start_time` timestamp NULL DEFAULT NULL,
	`finish_time` timestamp NULL DEFAULT NULL,
	`external_id` varchar(50) NOT NULL,
	`ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	KEY `doctor_4_clinic_idx` (`doctor_4_clinic_id`),
	KEY `slot_time_interval_idx` (`start_time`, `finish_time`),
	KEY `slot_external_id_idx` (`external_id`)
) ENGINE=InnoDB AUTO_INCREMENT=268561 DEFAULT CHARSET=utf8;

--
-- Table structure for table `sms_4_request`
--
CREATE TABLE `sms_4_request` (
	`smsQueryMessageId` int(11) NOT NULL DEFAULT '0',
	`request_id` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`smsQueryMessageId`,`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='��� ��������� � ��������';

--
-- Table structure for table `source_dict`
--
CREATE TABLE `source_dict` (
	`source_id` int(3) NOT NULL AUTO_INCREMENT,
	`title` varchar(64) NOT NULL,
	`description` varchar(255) DEFAULT NULL,
	PRIMARY KEY (`source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `street_dict`
--
CREATE TABLE `street_dict` (
	`street_id` int(11) NOT NULL AUTO_INCREMENT,
	`city_id` int(11) DEFAULT NULL,
	`title` varchar(100) NOT NULL,
	`rewrite_name` VARCHAR(100),
	`bound_left` FLOAT(12,8) DEFAULT '0.00000000',
	`bound_right` FLOAT(12,8) DEFAULT '0.00000000',
	`bound_top` FLOAT(12,8) DEFAULT '0.00000000',
	`bound_bottom` FLOAT(12,8) DEFAULT '0.00000000',
	`type` SMALLINT,
	`search_title` VARCHAR(100) DEFAULT NULL,
	PRIMARY KEY (`street_id`),
	UNIQUE KEY `rewrite_name` (`city_id`, `rewrite_name`),
	UNIQUE KEY `search_title` (`city_id`, `type`, `search_title`),
	KEY `bound` (`bound_left`, `bound_right`, `bound_top`, `bound_bottom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Справочник улиц';

--
-- Table structure for table `traffic_params`
--
CREATE TABLE `traffic_params` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `obj_id` int(11) NOT NULL,
  `obj_type` tinyint(4) NOT NULL,
  `param_id` int(11) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `traffic_params_obj_id` (`obj_id`),
  KEY `traffic_params_obj_type` (`obj_type`),
  KEY `traffic_params_param_id` (`param_id`),
  CONSTRAINT `traffic_params_ibfk_1` FOREIGN KEY (`param_id`) REFERENCES `traffic_params_dict` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

--
-- Table structure for table `traffic_params_dict`
--
CREATE TABLE `traffic_params_dict` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Table structure for table `underground_line`
--
CREATE TABLE `underground_line` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(512) NOT NULL,
	`color` varchar(16) NOT NULL,
	`city_id` int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ветки метро';

--
-- Table structure for table `underground_station`
--
CREATE TABLE `underground_station` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(512) NOT NULL,
	`underground_line_id` int(11) NOT NULL,
	`index` int(11) DEFAULT NULL,
	`rewrite_name` varchar(512) DEFAULT NULL,
	`longitude` float(14,8) DEFAULT '0.00000000',
	`latitude` float(14,8) DEFAULT '0.00000000',
	PRIMARY KEY (`id`),
	KEY `line_id` (`underground_line_id`),
	CONSTRAINT `underground_station_ibfk_1` FOREIGN KEY (`underground_line_id`) REFERENCES `underground_line` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='станции метро';

--
-- Table structure for table `underground_station_4_clinic`
--
CREATE TABLE `underground_station_4_clinic` (
	`undegraund_station_id` int(11) NOT NULL DEFAULT '0',
	`clinic_id` int(11) NOT NULL DEFAULT '0',
	`distance` INT(6) UNSIGNED NULL,
	PRIMARY KEY (`undegraund_station_id`,`clinic_id`),
	KEY `clinic_idx` (`clinic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Станции метро для клиник';

--
-- Table structure for table `underground_station_4_reg_city`
--
CREATE TABLE `underground_station_4_reg_city` (
	`reg_city_id` int(11) NOT NULL,
	`station_id` int(11) NOT NULL,
	`sort` int(11) NOT NULL DEFAULT '99',
	PRIMARY KEY (`reg_city_id`,`station_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `user`
--
CREATE TABLE `user` (
	`user_id` int(11) NOT NULL AUTO_INCREMENT,
	`user_login` varchar(255) NOT NULL,
	`user_password` varchar(255) NOT NULL,
	`user_role` tinyint(3) unsigned NOT NULL,
	`user_fname` varchar(255) NOT NULL,
	`user_lname` varchar(255) NOT NULL,
	`user_mname` varchar(255) DEFAULT NULL,
	`user_email` varchar(255) DEFAULT NULL,
	`user_status` tinyint(4) NOT NULL DEFAULT '0',
	`status` enum('enable','disable','block') NOT NULL DEFAULT 'enable',
	`phone` varchar(50) DEFAULT NULL,
	`skype` varchar(50) DEFAULT NULL,
	`operator_stream` tinyint NOT NULL DEFAULT '0',
	PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `user_right_dict`
--
CREATE TABLE `user_right_dict` (
	`right_id` tinyint(4) NOT NULL AUTO_INCREMENT,
	`title` varchar(50) NOT NULL,
	`code` char(3) NOT NULL,
	PRIMARY KEY (`right_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Справочник прав пользователя';

--
-- Table structure for table `visitor_settings`
--
CREATE TABLE `visitor_settings` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`visitor_id` varchar(50) NOT NULL,
	`abtest_option` int(4) DEFAULT NULL,
	`city_id` int(11) NOT NULL,
	`url` text NOT NULL,
	`session_id` varchar(255) DEFAULT NULL,
	`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET foreign_key_checks = 1;

--
-- Table structure for table `partner_cost`
--
CREATE TABLE `partner_cost` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`partner_id` int(11) DEFAULT NULL,
	`service_id` int(11) DEFAULT NULL,
	`cost` decimal(10,6) NOT NULL,
	`city_id` int(11) DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `partner_id` (`partner_id`),
	CONSTRAINT `partner_cost_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `partner` (`id`),
	CONSTRAINT `partner_cost_city_id` FOREIGN KEY (`city_id`) REFERENCES `city` (`id_city`),
	UNIQUE INDEX `partner_cost_unique` (`partner_id`, `service_id`, `city_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `partner_sector_mapping`
--
CREATE TABLE `partner_sector_mapping` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`partner_id` INT(11) NOT NULL,
	`sector_id` INT(11) NOT NULL,
	`partner_sector` VARCHAR(50) NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `partner_sector` (`partner_sector` ASC),
	INDEX `partner_id` (`partner_id` ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `clinic_partner_phone`
--
CREATE TABLE `clinic_partner_phone` (
  `clinic_id`       INT(11) NOT NULL,
  `partner_id`      INT(11) NOT NULL,
  `phone_id`        INT(11) NOT NULL,
  `clinic_phone_id` INT(11) DEFAULT NULL,
  PRIMARY KEY (`clinic_id`, `partner_id`),
  KEY `clinic_id` (`clinic_id`),
  KEY `partner_id` (`partner_id`),
  KEY `phone_id` (`phone_id`),
  KEY `clinic_partner_phone_ibfk_4` (`clinic_phone_id`),
  CONSTRAINT `clinic_partner_phone_ibfk_4` FOREIGN KEY (`clinic_phone_id`) REFERENCES `phone` (`id`),
  CONSTRAINT `clinic_partner_phone_ibfk_1` FOREIGN KEY (`clinic_id`) REFERENCES `clinic` (`id`),
  CONSTRAINT `clinic_partner_phone_ibfk_2` FOREIGN KEY (`partner_id`) REFERENCES `partner` (`id`),
  CONSTRAINT `clinic_partner_phone_ibfk_3` FOREIGN KEY (`phone_id`) REFERENCES `phone` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `call_log`
--
CREATE TABLE `call_log` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`ext_id` varchar(20) NOT NULL,
	`start_time` datetime NOT NULL,
	`duration` time NOT NULL,
	`ani` varchar(12) NOT NULL,
	`did` varchar(12) NOT NULL,
	`tariff_duration` time NOT NULL,
	`tariff` decimal(10,4) NOT NULL,
	`cost` decimal(10,4) NOT NULL,
	`application_type_id` varchar(10) NOT NULL,
	`sort` varchar(10) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `id` (`id`),
	UNIQUE KEY `unique_ext_id` (`ext_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `comagic_log` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `numa` varchar(20),
	`numb` varchar(20) NOT NULL,
	`ac_id` int(11) NOT NULL,
	`call_date` datetime(6) NOT NULL,
	`wait_time` int(11) NOT NULL,
	`duration` int(11) NOT NULL,
	`status` varchar(20) NOT NULL,
	`utm_source` varchar(30) NOT NULL,
	`utm_medium` varchar(50) NOT NULL,
	`utm_term` varchar(50) NOT NULL,
	`utm_content` varchar(50) NOT NULL,
	`utm_campaign` varchar(50) NOT NULL,
	`os_service_name` varchar(50) NOT NULL,
	`os_campaign_id` varchar(50) NOT NULL,
	`os_ad_id` varchar(50) NOT NULL,
	`os_source_id` varchar(50) NOT NULL,
	`session_start` varchar(50) NOT NULL,
	`visitor_id` int(11) NOT NULL,
	`search_engine` varchar(50) NOT NULL,
	`search_query` varchar(255) NOT NULL,
	`file_link` varchar(255) NOT NULL,
	`ua_client_id` int(11) NOT NULL,
	`page_url` varchar(255) NOT NULL,
	`referrer` varchar(255) NOT NULL,
	`ef_id` varchar(255) NOT NULL,
	`request_id` int(11) DEFAULT NULL,
	`checked_time` datetime(6) DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `id` (`id`),
	UNIQUE KEY `numa_call_date_index` (`numa`,`call_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `moderation`
--
CREATE TABLE `moderation` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`entity_class` varchar(50) NOT NULL,
	`entity_id` bigint(20) NOT NULL,
	`data` text NOT NULL,
	`is_new` tinyint(1) NOT NULL DEFAULT '0',
	`is_delete` tinyint(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	UNIQUE KEY `entity_key` (`entity_class`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `rating_strategy`
--
CREATE TABLE `rating_strategy` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`chance` int(11) NOT NULL DEFAULT '0',
	`params` varchar(255) NOT NULL,
	`type` varchar(255) NULL,
	`for_object` tinyint(1) NULL DEFAULT '0',
  `needs_to_recalc` tinyint(1) NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `rating`
--
CREATE TABLE `rating` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `object_id` int(11) NOT NULL,
  `object_type` int(11) NOT NULL,
  `strategy_id` bigint(20) unsigned NOT NULL,
  `rating_value` decimal(10,5) NOT NULL DEFAULT '0.00000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `object_id_object_type_strategy_id_index` (`object_id`,`object_type`,`strategy_id`),
  KEY `rating_ibfk_1` (`strategy_id`),
  KEY `rating_value_index` (`rating_value`),
  CONSTRAINT `rating_ibfk_1` FOREIGN KEY (`strategy_id`) REFERENCES `rating_strategy` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `tips`
--
CREATE TABLE `tips` (
	`id` int unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(50) NOT NULL,
	`category` tinyint NOT NULL,
	`weight` int unsigned NOT NULL,
	`color` char(7) NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `name_key` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `tips_message`
--
CREATE TABLE `tips_message` (
	`tips_id` int unsigned NOT NULL,
	`record_id` int NOT NULL,
	`weight` tinyint NOT NULL,
	`params` varchar(500) NULL,
	PRIMARY KEY (`record_id`, `tips_id`),
	CONSTRAINT `tips_id_key` FOREIGN KEY (`tips_id`) REFERENCES `tips` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `google_big_query`
--
CREATE TABLE `google_big_query` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`token` VARCHAR(255) DEFAULT NULL,
	`mtime` TIMESTAMP NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `auth_token`
--
CREATE TABLE `auth_token` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`token` VARCHAR(50) NOT NULL,
	`type` VARCHAR(10) NOT NULL,
	`expired` TIMESTAMP NOT NULL,
	`using` TINYINT NOT NULL DEFAULT '0',
	`user_id` INT UNSIGNED DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `auth_token_token_idx` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for `partner_phones`
--
CREATE TABLE `partner_phones` (
  `partner_id` INT(11) NOT NULL,
  `city_id`    INT(11) NOT NULL,
  `phone_id`   INT(11) NOT NULL,
  PRIMARY KEY (`partner_id`, `city_id`, `phone_id`),
  KEY `partner_phones_city_id` (`city_id`),
  KEY `partner_phones_phone_id` (`phone_id`),
  CONSTRAINT `partner_phones_phone_id` FOREIGN KEY (`phone_id`) REFERENCES `phone` (`id`),
  CONSTRAINT `partner_phones_city_id` FOREIGN KEY (`city_id`) REFERENCES `city` (`id_city`),
  CONSTRAINT `partner_phones_partner_id` FOREIGN KEY (`partner_id`) REFERENCES `partner` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
