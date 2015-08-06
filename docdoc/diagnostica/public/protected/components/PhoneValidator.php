<?php
class PhoneValidator extends CValidator {

	public function validateAttribute($object, $attribute) {
		$value = $object->$attribute;
		$value = preg_replace('/[^\d]/', '', $value);

		$object->$attribute = $value;
	}

}