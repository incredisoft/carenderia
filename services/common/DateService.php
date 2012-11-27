<?php
class DateService {
	function getServerDate() {
		date_format(new DateTime(), 'Y-m-d');
	}
}
?>