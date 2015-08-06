<?php

/**
 * Class m150331_131712_DD_1122_add_manager
 *
 * Добавление менеджера клиники
 *
 */
class m150331_131712_DD_1122_add_manager extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function up()
	{
		$this->addColumn('clinic', 'manager_id', 'INT(11) DEFAULT NULL');
		$this->addColumn('clinic_billing', 'manager_id', 'INT(11) DEFAULT NULL');
		$this->addForeignKey('fk_manager_id', 'clinic', 'manager_id', 'user', 'user_id', 'RESTRICT', 'CASCADE');
		$this->addForeignKey('fk_billing_manager_id', 'clinic_billing', 'manager_id', 'user', 'user_id', 'RESTRICT', 'CASCADE');

		$this->insert('user_right_dict', ['right_id' => 8, 'title' => 'Аккаунт', 'code' => 'ACN']);
		$this->insert('right_4_user', ['right_id' => 8, 'user_id' => 125]);
		$this->insert('right_4_user', ['right_id' => 8, 'user_id' => 56]);
		$this->insert('right_4_user', ['right_id' => 8, 'user_id' => 221]);

		//tgorodnicheva
		$this->update('clinic', ['manager_id' => 56], " id IN (1930,
			2265,1294,207,2762,4345,1592,3067,710,2673,2248,4571,2291,2733,26,3093,4209,648,3211,701,3577,1048,617,
			3339,2479,2288,3343,1419,1150,2526,1984,65,533,105,2603,589,3365,46,3251,325,2454,2309,1575,86,193,1592,
			546,703,1930,2265,2997,2317,4235,193,1233,1592,3067,2,154,2673,2715,1294,2758,2248,910,748,3303,4571,124,
			1443,3381,2160,3211,3577,541,44,3339,2479,972,3401,1293,2241,2484,4125,1833,2603,1853,46,3251,324,1338,2454,
			2193,1300,1575,86,175,306,207,84,1592,1189,10,161,1138,1712,4643,912,2647,910,1324,2738,1661,2150,26,162,1267,
			1914,380,102,2679,1109,830,2741,3317,105,1718,1621,736,1963,589,703,198,1884,3327,466,1896,2285,5421,14,2023,344)");

		//lkohanova
		$this->update('clinic', ['manager_id' => 125], " id IN (297,107,110,812,1801,1678,147,1671,946,3369,211,155,1625,1431,182,640,
			3249,1086,206,1583,149,106,3539,116,357,1245,3297,2296,1433,593,370,1343,1918,465,1321,1288,808,810,1272,1282,1615,1375,
			1731,868,2094,3143,2235,355,495,1458,322,743,414,348,1279,1468,1553,3183,866,770,1308,1709,1650,1913,445,112,1318,1397,83,
			1818,829,884,1216,1320,784,1764,1898,1730,1786,2197,502,2120,15,1697,2025,5395,2097,746,4587,685,3144,4051,201,3205,5223,
			2148,1496,98,3145,2066,650,308,1890,1,2442,544,667,525,3215,569,5123,904,1071,13,221,1360,2208,4467,20,55,3163,2025,2097,
			746,4587,685,3144,211,3257,201,116,2148,3259,2044,1496,98,3145,2066,465,650,308,1,2442,544,525,3215,1731,2094,2991,355,495,
			569,5123,322,414,2124,904,1071,2125,866,13,221,1360,2093,2208,83,677,963,784,3695,55,15,1070,1918,1458,1764)");

		//nkarpova
		$this->update('clinic', ['manager_id' => 221], " manager_id IS NULL");

	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->dropForeignKey('fk_manager_id', 'clinic');
		$this->dropForeignKey('fk_billing_manager_id', 'clinic_billing');
		$this->dropColumn('clinic', 'manager_id');
		$this->dropColumn('clinic_billing', 'manager_id');

		$this->execute("DELETE FROM user_right_dict WHERE right_id = 8");
		$this->execute("DELETE FROM right_4_user WHERE right_id = 8");
	}
}