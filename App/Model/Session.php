<?php

namespace App\Model;

use DateTime;

class Session {

	#region LOGIN

	public static function IsGlobalAdmin (): bool {

		return !empty($_SESSION['login']['admin']);
	}

	public static function SetGlobalAdmin (bool $value): void {

		$_SESSION['login']['admin'] = $value === true ? true : null;
	}

	public static function IsLoggedIn (): bool {

		return !empty($_SESSION['login']['id']);
	}

	//Getter and Setter

	public static function Set (string $fieldName, mixed $value): void {

		$_SESSION['login'][$fieldName] = $value;
	}

	public static function Get (string $fieldName): mixed {

		return $_SESSION['login'][$fieldName] ?? null;
	}

	#endregion

	/**
	 * @return bool
	 */
	public static function IsLocalAdmin (): bool {

		return !empty($_SESSION['login']['localAdmin']) || !empty($_SESSION['login']['globalAdmin']);

	}

	/**
	 * @return int
	 */
	public static function VabsUserId (): int {

		return !empty($_SESSION['login']['id']) ? (int)$_SESSION['login']['id'] : 0;

	}

	/**
	 * @return float
	 */
	public static function GetStandardTaxRate (): float {

		return !empty($_SESSION['login']['standard_tax_rate']) ? floatval ($_SESSION['login']['standard_tax_rate']) : 19;

	}

	/**
	 *
	 */
	public static function DestroyLogin (): void {

		if (isset($_SESSION['login'])) {
			$_SESSION['login'] = false;
			$_SESSION['login'] = [];
		}

	}

	/**
	 * @return string
	 */
	public static function VabsUserFirstName (): string {

		return !empty($_SESSION['login']['firstName']) ? $_SESSION['login']['firstName'] : '';

	}

	/**
	 * @return string
	 */
	public static function VabsUserLastName (): string {

		return !empty($_SESSION['login']['lastName']) ? $_SESSION['login']['lastName'] : '';

	}

	/**
	 * @return int
	 */
	public static function TraininerId (): int {

		return !empty($_SESSION['login']['teacherId']) ? (int)$_SESSION['login']['teacherId'] : 0;

	}

	/**
	 * @return bool
	 */
	public static function HasSpoofing (): bool {

		return !empty($_SESSION['login']['spoofing']);

	}

	/**
	 * @return int
	 */
	public static function GetSpoofingValue (): int {

		return !empty($_SESSION['login']['spoofing']) ? (int)$_SESSION['login']['spoofing'] : 0;

	}

	/**
	 * @return bool
	 */
	public static function IsSpoofed (): bool {

		return !empty($_SESSION['login']['gespooft']);

	}

	/**
	 * @return int
	 */
	public static function OriginalUserId (): int {

		return !empty($_SESSION['login']['originalUserId']) ? $_SESSION['login']['originalUserId'] : 0;

	}

	/**
	 * @param int $id
	 */
	public static function SetBackendAreaId (int $id): void {

		$_SESSION['areaId'] = $id;

	}

	/**
	 * @return int
	 */
	public static function GetBackendAreaId (): int {

		return !empty($_SESSION['areaId']) ? (int)$_SESSION['areaId'] : 0;

	}

	/**
	 * @return int
	 */
	public static function VabsClientId (): int {

		return !empty($_SESSION['login']['vabsClientId']) ? (int)$_SESSION['login']['vabsClientId'] : 0;

	}

	/**
	 * @return string
	 */
	public static function VabsUserEmail (): string {

		return !empty($_SESSION['login']['email']) ? $_SESSION['login']['email'] : '';

	}

	public static function IsPublic () {

		return $_SESSION['public'] ?? false;

	}

	public static function SetPublic (): void {

		$_SESSION['public'] = true;

	}

	public static function SetSelectedTabNameForDashBoard (string $value): void {

		$_SESSION['DASHBAORD']['activeTab'] = $value;

	}

	public static function GetSelectedTabNameForDashBoard () {

		return $_SESSION['DASHBAORD']['activeTab'] ?? '';

	}

	public static function GetUserTelephone () {

		return !empty($_SESSION['login']['telephone']) ? $_SESSION['login']['telephone'] : '';

	}

	public static function SetCurrentSessionTime (DateTime $datetime): void {

		$_SESSION['currentSessionTime']      = $datetime->format ('Y-m-d H:i:s');
		$_SESSION['currentSessionTimeStamp'] = strtotime ('now');

	}

	public static function GetCurrentSessionTime () {

		return $_SESSION['currentSessionTime'] ?? strtotime ('now');

	}

	public static function GetCurrentSessionTimeStamp () {

		return $_SESSION['currentSessionTimeStamp'] ?? strtotime ('now');

	}

	public static function GetLoginName () {

		return $_SESSION['login']['login'] ?? '';
	}

	public static function GetVabsClientName () {

		return $_SESSION['login']['vabsClientName'] ?? '';

	}

	public static function IsUsingOwnSmtpServer (): string {

		return empty($_SESSION['smtp']['emailUseVabsServer']);

	}

	public static function GetSmtpServer (): string {

		return $_SESSION['smtp']['emailServer'] ?? '';

	}

	public static function GetSmtpPort (): string {

		return $_SESSION['smtp']['emailPort'] ?? '';

	}

	public static function GetSmtpUser (): string {

		return $_SESSION['smtp']['emailUser'] ?? '';

	}

	public static function GetSmtpPass (): string {

		return $_SESSION['smtp']['emailPass'] ?? '';

	}

	public static function GetSmtpUseSmtpAuth (): string {

		return $_SESSION['smtp']['emailUseSmtpAuth'] ?? '';

	}

	public static function GetSmtpUseTls (): string {

		return $_SESSION['smtp']['emailUseTls'] ?? '';

	}

	#region CUTSOMER AREA

	public static function GetCustomerId () {

		return $_SESSION['frontend']['login']['id'] ?? 0;

	}

	public static function GetCustomerUserFirstName () {

		return $_SESSION['frontend']['login']['customerUserFirstName'] ?? '';

	}

	public static function GetCustomerUserLastName () {

		return $_SESSION['frontend']['login']['customerUserLastName'] ?? '';

	}

	public static function GetCustomerUserEmail () {

		return $_SESSION['frontend']['login']['customerUserEmail'] ?? '';

	}

	public static function GetCustomerVabsClientId () {

		return $_SESSION['frontend']['login']['vabsClientId'] ?? 0;

	}

	public static function CustomerVabsClientName () {

		return $_SESSION['frontend']['login']['vabsClientName'] ?? '';

	}

	public static function IsCustomerLoggedIn (): bool {

		return !empty($_SESSION['frontend']['login']['id']);

	}

	public static function SetCustomerVabsClientId (int $vabsClientId): void {

		$_SESSION['frontend']['login']['vabsClientId'] = $vabsClientId;
	}

	public static function SetCustomerId (int $id): void {

		$_SESSION['frontend']['login']['id'] = $id;
	}

	public static function SetCustomerFirstName (string $firstName): void {

		$_SESSION['frontend']['login']['firstName'] = $firstName;
	}

	public static function SetCustomerLastName (string $lastName): void {

		$_SESSION['frontend']['login']['lastName'] = $lastName;
	}

	public static function SetCustomerEmail (string $email): void {

		$_SESSION['frontend']['login']['email'] = $email;
	}

	public static function SetCustomerLogin (string $login): void {

		$_SESSION['frontend']['login']['login'] = $login;
	}

	/**
	 *
	 */
	public static function destroyCustomerLogin (): void {
		if (!empty($_SESSION['frontend']['login'])) {
			unset($_SESSION['frontend']['login']);
		}
	}

	#endregion

	public static function GetMaxSessionTime (): float|int {

		if (defined ('MAX_SESSION_TIME')) {
			return 20 * 60 * 1000;
		}

		return 20 * 60 * 1000;

	}

	public static function GetWarningSessionTime (): float|int {

		if (defined ('WARNING_SESSION_TIME')) {
			return 18 * 60 * 1000;
		}

		return 18 * 60 * 1000;

	}
}
