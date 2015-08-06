use likefifa_test;

DROP TABLE IF EXISTS payments_operation;
DROP TABLE IF EXISTS payments_invoice;
DROP TABLE IF EXISTS payments_processor;
DROP TABLE IF EXISTS payments_account;

CREATE TABLE payments_account
(
	id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
	amount_real BIGINT NOT NULL DEFAULT 0,
	amount_fake BIGINT NOT NULL DEFAULT 0,
	comment VARCHAR (255)
) ENGINE=InnoDB AUTO_INCREMENT=10000 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO payments_account (id, comment)
	VALUES
		(1, 'Система'),
		(2, 'Бонусы'),
		(1000, 'Робокасса')
	;

CREATE TABLE payments_processor
(
	`id` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`key` VARCHAR(64) NOT NULL,
	`account_id` INTEGER NOT NULL,

	CONSTRAINT `key` UNIQUE INDEX (`key`),
	CONSTRAINT `account_id` UNIQUE INDEX (`account_id`),

	FOREIGN KEY (account_id)
		REFERENCES payments_account(id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO payments_processor (`key`, `account_id`)
	VALUES
		('robokassa', 1000)
	;

CREATE TABLE payments_invoice
(
	`id` CHAR(36) NOT NULL PRIMARY KEY,
	`create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`amount_real` BIGINT NOT NULL,
	`amount_fake` BIGINT NOT NULL,
	`processor_id` INTEGER NOT NULL,
	`account_to` INTEGER NOT NULL,
	`message` TEXT NOT NULL,
	`status` INTEGER NOT NULL,
	`status_date` TIMESTAMP NOT NULL,
	`email` VARCHAR(255) NOT NULL,

	INDEX (`account_to`),

	FOREIGN KEY (`account_to`)
		REFERENCES payments_account(`id`),
	FOREIGN KEY (`processor_id`)
		REFERENCES payments_processor(`id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE payments_operation
(
	`id` CHAR(36) NOT NULL,
	`create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`amount_real` BIGINT NOT NULL,
	`amount_fake` BIGINT NOT NULL,
	`account_from` INTEGER NOT NULL,
	`account_to` INTEGER NOT NULL,
	`type` INTEGER NOT NULL,
	`message` TEXT NOT NULL,
	`income` INTEGER NOT NULL,
	`invoice_id` CHAR(36),

	PRIMARY KEY (`id`, `account_from`),
	INDEX (`create_date`),

	FOREIGN KEY (`account_to`)
		REFERENCES payments_account(`id`),
	FOREIGN KEY (`account_from`)
		REFERENCES payments_account(`id`),
	FOREIGN KEY (`invoice_id`)
		REFERENCES payments_invoice(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

