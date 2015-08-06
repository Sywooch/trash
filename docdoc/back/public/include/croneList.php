<?php
class croneList
{
	private static $croneSMS = array("name" => "croneSMS", "file" => "smsSender.conf");
	private static $croneEmail = array("name" => "croneEmail", "file" => "email.conf");
	private static $croneDoctorRating = array("name" => "croneDoctorRating", "file" => "doctorRating.conf");
	private static $croneRequestListener = array("name" => "croneRequestListener", "file" => "requestListener.conf");
	private static $croneRequestLKListener = array(
		"name" => "croneRequestLKListener",
		"file" => "requestLKListener.conf"
	);
	private static $croneLKNotifier = array(
		"name" => "croneLKNotifier",
		"file" => "croneLKNotifier.conf"
	);
	private static $croneUpdateRequestForPartners = array(
		"name" => "croneUpdateRequestForPartners",
		"file" => "updateRequestForPartners.conf"
	);


	public $cronList = array(
		'croneSMS',
		'croneEmail',
		'croneDoctorRating',
		'croneRequestListener',
		'croneRequestLKListener',
		'croneLKNotifier',
	);

	public static function getConfig($name)
	{
		return self::$$name;
	}
}
