<?php

namespace App;

use DD\Exceptions\ValidationException;
use DD\Mailer\Mailer;
use DD\SystemType;
use ErrorException;
use Exception;
use PDOException;
use Throwable;

/**
 * Error and exception handler
 *

 */
class Error
{

	/**
	 * Error handler. Convert all errors to Exceptions by throwing an ErrorException.
	 *
	 * @param int $level Error level
	 * @param string $message Error message
	 * @param string $file Filename the error was raised in
	 * @param int $line Line number in the file
	 *
	 * @return void
	 * @throws ErrorException
	 */
	public static function set_error_handler (int $level, string $message, string $file, int $line): void {

		if (error_reporting () !== 0) {  // to keep the @ operator working
			throw new ErrorException($message, 0, $level, $file, $line);
		}
	}

	/**
	 * Exception handler.
	 *
	 * @param Exception $exception The exception
	 *
	 * @return void
	 */
	public static function set_exception_handler (Throwable $exception): void {

		try {
			// Code is 404 (not found) or 500 (general error)
			$code = $exception->getCode ();
			$code = $code != 404 ? 200 : $code;

			http_response_code ($code);

			if (SHOW_ERRORS) {

				$message = str_replace ('#', '<br>#', $exception->getMessage ());
				$message = str_replace ('Stack trace:', '<br>StackTrace:#######################', $message);
				$message = str_replace (str_replace ('/public', '', $_SERVER['DOCUMENT_ROOT']), '', $message);
				echo "<h1>Fatal error</h1>";
				echo "<p>Uncaught exception: '".get_class ($exception)."'</p>";
				echo "<p>Message: '".$message."'</p>";
				/*echo "<p>Stack trace:<pre>".$exception->getTraceAsString ()."</pre></p>";*/
				echo "<p>Thrown in '".$exception->getFile ()."' on line ".$exception->getLine ()."</p>";
				$array = explode ("#", $exception->getTraceAsString ());
				foreach ($array as $row) {
					$row = str_replace (str_replace ('/public', '', $_SERVER['DOCUMENT_ROOT']), '', $row);
					echo "#".$row."<br>";
				}

			} else {

				$log = dirname (__DIR__).'/logs/'.date ('Y-m-d').'.txt';
				ini_set ('error_log', $log);

				$message = "Uncaught exception: '".get_class ($exception)."'";
				$message .= " with message '".$exception->getMessage ()."'";
				$message .= "\nStack trace: ".$exception->getTraceAsString ();
				$message .= "\nThrown in '".$exception->getFile ()."' on line ".$exception->getLine ();

				error_log ($message);

				echo $code == 404 ? "<h1>Page not found</h1>" : "<h1>An error occurred</h1>";
			}
		} catch (Exception $e) {
			echo self::HandleErrorMessage ($e);
		}
	}

	/**
	 * Sends an Email to the ADMINISTRATOR and returns back the error based on the current SYSTEMTYPE
	 * @param Exception $e
	 * @return string
	 */
	public static function HandleErrorMessage (Exception $e): string {

		$message = $e->getMessage ();
		$trace   = $e->getTraceAsString ();

		if ($e instanceof ValidationException) {
			$subject = Mailer::EMAIL_SUBJECT_VALIDATION_EXCEPTION;
		} else if ($e instanceof PDOException) {
			$subject = Mailer::EMAIL_SUBJECT_DB_EXCEPTION;
			if (str_contains ($message, 'Duplicate')) {
				$message = "Es existiert schon ein Eintrag mit denselben Daten";
			}
		} else {
			$subject = Mailer::EMAIL_SUBJECT_EXCEPTION;
		}

		Mailer::SendAdminMail ($message, $subject);

		$message = SYSTEMTYPE === SystemType::TEST || SystemType::DEV || $e instanceof ValidationException ? $message."\r\n".$trace : "Noch unbekannter Fehler. Der Administrator wurde informiert";

		return str_replace ("\n", "<br>", $message);

	}

}
