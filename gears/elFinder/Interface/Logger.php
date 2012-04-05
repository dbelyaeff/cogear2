<?php
interface elFinder_Interface_Logger {
	public function log($cmd, $ok, $context, $err='', $errorData = array());
}