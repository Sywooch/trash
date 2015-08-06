<?php

class m150324_152548_dd_1006_alter_table_phone extends CDbMigration
{
	public function safeUp()
	{
		$this->execute('ALTER TABLE phone ADD COLUMN provider_id INT NOT NULL DEFAULT 1;');
		$this->execute("ALTER TABLE phone ADD CONSTRAINT fk_provider_id FOREIGN KEY (provider_id) REFERENCES phone_provider (id)");

		$this->execute('ALTER TABLE phone ADD COLUMN model_name VARCHAR(255) NULL;');
		$this->execute('ALTER TABLE phone ADD COLUMN partner_id INT NULL;');
		$this->execute('ALTER TABLE phone ADD COLUMN comment VARCHAR(255) NULL;');

		$this->execute(
			"UPDATE phone p
   				JOIN clinic c ON p.number = c.asterisk_phone
			SET
  				p.model_name = 'clinic'"
		);

		$this->execute(
			"UPDATE phone p
  				JOIN clinic_partner_phone cpp ON p.id = cpp.phone_id
    			JOIN clinic c ON c.id = cpp.clinic_id
			SET
			  p.model_name = 'clinic_partner_phone',
			  p.partner_id = cpp.partner_id;"
		);

		$this->execute(
			"UPDATE phone p
  				JOIN partner_phones ph ON p.id = ph.phone_id
    		SET
      			p.model_name = 'partner_phones', p.partner_id = ph.partner_id;"
		);

		$this->execute(
			"UPDATE phone p
				JOIN city c ON c.site_phone = p.number
			SET
  				p.model_name = 'city', p.partner_id = NULL;

			UPDATE phone p
  				JOIN city c ON c.opinion_phone = p.number
			SET
  				p.model_name = 'city', p.partner_id = NULL;"
		);

		$this->execute('ALTER TABLE phone ADD COLUMN mtime TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP');
		$this->execute('ALTER TABLE phone ADD COLUMN muser_id INT NULL DEFAULT NULL');
	}

	public function safeDown()
	{
		$this->dropForeignKey('fk_provider_id', 'phone');
		$this->dropColumn('phone', 'provider_id');
		$this->dropColumn('phone', 'model_name');
		$this->dropColumn('phone', 'partner_id');
		$this->dropColumn('phone', 'comment');
		$this->dropColumn('phone', 'mtime');
		$this->dropColumn('phone', 'muser_id');
	}
}