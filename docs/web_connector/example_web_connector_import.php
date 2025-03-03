<?php

/**
 * Example QuickBooks SOAP Server / Web Service
 *
 * This is an example Web Service which imports Invoices currently stored
 * within QuickBooks desktop editions and then stores those invoices in a MySQL
 * database. It communicates with QuickBooks via the QuickBooks Web Connector.
 *
 * If you have not already looked at the more basic docs/example_server.php,
 * you may want to consider looking at that example before you dive into this
 * example, as the requests and processing are a bit simpler and the
 * documentation a bit more verbose.
 *
 *
 * @author Keith Palmer <keith@consolibyte.com>
 *
 * @package QuickBooks
 * @subpackage Documentation
 */

// I always program in E_STRICT error mode...
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

const REQ_DATE_NONE = 0;
const REQ_DATE_SIMPLE = 1;
const REQ_DATE_WRAPPED = 2;

// Support URL
if (!empty($_GET['support']))
{
	header('Location: http://www.consolibyte.com/');
	exit;
}

// We need to make sure the correct timezone is set, or some PHP installations will complain
if (function_exists('date_default_timezone_set'))
{
	// * MAKE SURE YOU SET THIS TO THE CORRECT TIMEZONE! *
	// List of valid timezones is here: http://us3.php.net/manual/en/timezones.php
	date_default_timezone_set('America/New_York');
}

// Require the framework
require_once '../QuickBooks.php';

// A username and password you'll use in:
//	a) Your .QWC file
//	b) The Web Connector
//	c) The QuickBooks framework
//
// 	NOTE: This has *no relationship* with QuickBooks usernames, Windows usernames, etc.
// 		It is *only* used for the Web Connector and SOAP server!
//
// If you wanted to allow others to log in, you'd create a .QWC file for each
//	individual user, and add each individual user to the auth database with the
//	QuickBooks_Utilities::createUser($dsn, $username, $password); static method.
$user = 'quickbooks';
$pass = 'password';

/**
 * Configuration parameter for the quickbooks_config table, used to keep track of the last time the QuickBooks sync ran
 */
define('QB_QUICKBOOKS_CONFIG_LAST', 'last');

/**
 * Configuration parameter for the quickbooks_config table, used to keep track of the timestamp for the current iterator
 */
define('QB_QUICKBOOKS_CONFIG_CURR', 'curr');

/**
 * Maximum number of customers/invoices returned at a time when doing the import
 */
define('QB_QUICKBOOKS_MAX_RETURNED', 10);

/**
 *
 */
define('QB_PRIORITY_PURCHASEORDER', 4);

/**
 * Request priorities, items sync first
 */
define('QB_PRIORITY_ITEM', 3);

/**
 * Request priorities, customers
 */
define('QB_PRIORITY_CUSTOMER', 2);

/**
 * Request priorities, salesorders
 */
define('QB_PRIORITY_SALESORDER', 1);

/**
 * Request priorities, invoices last...
 */
define('QB_PRIORITY_INVOICE', 0);

/**
 * Send error notices to this e-mail address
 */
define('QB_QUICKBOOKS_MAILTO', 'keith@consolibyte.com');

// The next three parameters, $map, $errmap, and $hooks, are callbacks which
//	will be called when certain actions/events/requests/responses occur within
//	the framework.

// Map QuickBooks actions to handler functions
$map = array(
	//QUICKBOOKS_IMPORT_SALESRECEIPT => array( '_quickbooks_salesreceipt_import_request', '_quickbooks_salesreceipt_import_response' ),
	QUICKBOOKS_IMPORT_PURCHASEORDER => array( '_quickbooks_purchaseorder_import_request', '_quickbooks_purchaseorder_import_response' ),
	QUICKBOOKS_IMPORT_INVOICE => array( '_quickbooks_invoice_import_request', '_quickbooks_invoice_import_response' ),
	QUICKBOOKS_IMPORT_CUSTOMER => array( '_quickbooks_customer_import_request', '_quickbooks_customer_import_response' ),
	QUICKBOOKS_IMPORT_SALESORDER => array( '_quickbooks_salesorder_import_request', '_quickbooks_salesorder_import_response' ),
	QUICKBOOKS_IMPORT_ITEM => array( '_quickbooks_item_import_request', '_quickbooks_item_import_response' ),
	);

// Error handlers
$errmap = array(
	500 => '_quickbooks_error_e500_notfound', 			// Catch errors caused by searching for things not present in QuickBooks
	1 => '_quickbooks_error_e500_notfound',
	'*' => '_quickbooks_error_catchall', 				// Catch any other errors that might occur
	);

// An array of callback hooks
$hooks = array(
	QuickBooks_WebConnector_Handlers::HOOK_LOGINSUCCESS => '_quickbooks_hook_loginsuccess', 	// call this whenever a successful login occurs
	);

// Logging level
//$log_level = QUICKBOOKS_LOG_NORMAL;
//$log_level = QUICKBOOKS_LOG_VERBOSE;
//$log_level = QUICKBOOKS_LOG_DEBUG;				// Use this level until you're sure everything works!!!
$log_level = QUICKBOOKS_LOG_DEVELOP;

// What SOAP server you're using
//$soapserver = QUICKBOOKS_SOAPSERVER_PHP;			// The PHP SOAP extension, see: www.php.net/soap
$soapserver = QUICKBOOKS_SOAPSERVER_BUILTIN;		// A pure-PHP SOAP server (no PHP ext/soap extension required, also makes debugging easier)

$soap_options = array(			// See http://www.php.net/soap
	);

$handler_options = array(		// See the comments in the QuickBooks/Server/Handlers.php file
	'deny_concurrent_logins' => false,
	'deny_reallyfast_logins' => false,
	);

$driver_options = array(		// See the comments in the QuickBooks/Driver/<YOUR DRIVER HERE>.php file ( i.e. 'Mysql.php', etc. )
	);

$callback_options = array(
	);

// * MAKE SURE YOU CHANGE THE DATABASE CONNECTION STRING BELOW TO A VALID MYSQL USERNAME/PASSWORD/HOSTNAME *
//
// This assumes that:
//	- You are connecting to MySQL with the username 'root'
//	- You are connecting to MySQL with an empty password
//	- Your MySQL server is located on the same machine as the script ( i.e.: 'localhost', if it were on another machine, you might use 'other-machines-hostname.com', or '192.168.1.5', or ... etc. )
//	- Your MySQL database name containing the QuickBooks tables is named 'quickbooks' (if the tables don't exist, they'll be created for you)
$dsn = 'mysql://root:root@localhost/quickbooks_import';
//$dsn = 'mysql://testuser:testpassword@localhost/testdatabase';

/**
 * Constant for the connection string (because we'll use it in other places in the script)
 */
define('QB_QUICKBOOKS_DSN', $dsn);

// If we haven't done our one-time initialization yet, do it now!
if (!QuickBooks_Utilities::initialized($dsn))
{
	// Create the example tables
	$file = dirname(__FILE__) . '/example.sql';
	if (file_exists($file))
	{
		$contents = file_get_contents($file);
		foreach (explode(';', $contents) as $sql)
		{
			if (!trim($sql))
			{
				continue;
			}

			$Driver = QuickBooks_Utilities::driverFactory(QB_QUICKBOOKS_DSN);
			$Driver->query($sql,$errnum,$errmsg) or die(trigger_error($errmsg));
		}
	}
	else
	{
		die('Could not locate "./example.sql" to create the demo SQL schema!');
	}

	// Create the database tables
	QuickBooks_Utilities::initialize($dsn);

	// Add the default authentication username/password
	QuickBooks_Utilities::createUser($dsn, $user, $pass);
}

// Initialize the queue
QuickBooks_WebConnector_Queue_Singleton::initialize($dsn);

// Create a new server and tell it to handle the requests
// __construct($dsn_or_conn, $map, $errmap = array(), $hooks = array(), $log_level = QUICKBOOKS_LOG_NORMAL, $soap = QUICKBOOKS_SOAPSERVER_PHP, $wsdl = QUICKBOOKS_WSDL, $soap_options = array(), $handler_options = array(), $driver_options = array(), $callback_options = array()
$Server = new QuickBooks_WebConnector_Server($dsn, $map, $errmap, $hooks, $log_level, $soapserver, QUICKBOOKS_WSDL, $soap_options, $handler_options, $driver_options, $callback_options);
$response = $Server->handle(true, true);

if ($log_level >= QUICKBOOKS_LOG_DEVELOP) {
	$marker = '===========';
	$log = "$marker RESPONSE $marker\n".html_entity_decode($response)."\n$marker END $marker\n";
	file_put_contents('/var/www/qbwc/log/traffic.log',$log,FILE_APPEND);	// for debugging
}

exit;

// ==== CALLBACK FUNCTIONS ====

/**
 * Login success hook - perform an action when a user logs in via the Web Connector
 *
 *
 */
function _quickbooks_hook_loginsuccess($requestID, $user, $hook, &$err, $hook_data, $callback_config)
{
	// For new users, we need to set up a few things

	// Fetch the queue instance
	$Queue = QuickBooks_WebConnector_Queue_Singleton::getInstance();
	$date = '1983-01-02 12:01:01';

	// Set up the invoice imports
	if (!_quickbooks_get_last_run($user, QUICKBOOKS_IMPORT_INVOICE))
	{
		// And write the initial sync time
		_quickbooks_set_last_run($user, QUICKBOOKS_IMPORT_INVOICE, $date);
	}

	// Do the same for customers
	if (!_quickbooks_get_last_run($user, QUICKBOOKS_IMPORT_CUSTOMER))
	{
		_quickbooks_set_last_run($user, QUICKBOOKS_IMPORT_CUSTOMER, $date);
	}

	// ... and for sales orders
	if (!_quickbooks_get_last_run($user, QUICKBOOKS_IMPORT_SALESORDER))
	{
		_quickbooks_set_last_run($user, QUICKBOOKS_IMPORT_SALESORDER, $date);
	}

	// ... and for items
	if (!_quickbooks_get_last_run($user, QUICKBOOKS_IMPORT_ITEM))
	{
		_quickbooks_set_last_run($user, QUICKBOOKS_IMPORT_ITEM, $date);
	}

	// Make sure the requests get queued up
	//$Queue->enqueue(QUICKBOOKS_IMPORT_SALESORDER, 1, QB_PRIORITY_SALESORDER, null, $user);
	//$Queue->enqueue(QUICKBOOKS_IMPORT_INVOICE, 1, QB_PRIORITY_INVOICE, null, $user);
	$Queue->enqueue(QUICKBOOKS_IMPORT_PURCHASEORDER, 1, QB_PRIORITY_PURCHASEORDER, null, $user);
	$Queue->enqueue(QUICKBOOKS_IMPORT_CUSTOMER, 1, QB_PRIORITY_CUSTOMER, null, $user);
	//$Queue->enqueue(QUICKBOOKS_IMPORT_ITEM, 1, QB_PRIORITY_ITEM, null, $user);
}

/**
 * Get the last date/time the QuickBooks sync ran
 *
 * @param string $user		The web connector username
 * @return string			A date/time in this format: "yyyy-mm-dd hh:ii:ss"
 */
function _quickbooks_get_last_run($user, $action)
{
	$type = null;
	$opts = null;
	return QuickBooks_Utilities::configRead(QB_QUICKBOOKS_DSN, $user, md5(__FILE__), QB_QUICKBOOKS_CONFIG_LAST . '-' . $action, $type, $opts);
}

/**
 * Set the last date/time the QuickBooks sync ran to NOW
 *
 * @param string $user
 * @return boolean
 */
function _quickbooks_set_last_run($user, $action, $force = null)
{
	$value = date('Y-m-d') . 'T' . date('H:i:s');

	if ($force)
	{
		$value = date('Y-m-d', strtotime($force)) . 'T' . date('H:i:s', strtotime($force));
	}

	return QuickBooks_Utilities::configWrite(QB_QUICKBOOKS_DSN, $user, md5(__FILE__), QB_QUICKBOOKS_CONFIG_LAST . '-' . $action, $value);
}

/**
 *
 *
 */
function _quickbooks_get_current_run($user, $action)
{
	$type = null;
	$opts = null;
	return QuickBooks_Utilities::configRead(QB_QUICKBOOKS_DSN, $user, md5(__FILE__), QB_QUICKBOOKS_CONFIG_CURR . '-' . $action, $type, $opts);
}

/**
 *
 *
 */
function _quickbooks_set_current_run($user, $action, $force = null)
{
	$value = date('Y-m-d') . 'T' . date('H:i:s');

	if ($force)
	{
		$value = date('Y-m-d', strtotime($force)) . 'T' . date('H:i:s', strtotime($force));
	}

	return QuickBooks_Utilities::configWrite(QB_QUICKBOOKS_DSN, $user, md5(__FILE__), QB_QUICKBOOKS_CONFIG_CURR . '-' . $action, $value);
}

function getIterationInfo($user, $action, $extra): array {
	// Iterator support (break the result set into small chunks)
	$attrs = 'iterator="Start"';
	if (empty($extra['iteratorID'])) {
		// This is the first request in a new batch
		$last = _quickbooks_get_last_run($user, $action);
		_quickbooks_set_last_run($user, $action);			// Update the last run time to NOW()

		// Set the current run to $last
		_quickbooks_set_current_run($user, $action, $last);
	}
	else {
		// This is a continuation of a batch
		$attrs = 'iterator="Continue" iteratorID="' . $extra['iteratorID'] . '"';
		$last = _quickbooks_get_current_run($user, $action);
	}

	return [$attrs,$last];
}

function wrapRequestXML(string $type, string $customXML, int $dateType, $requestID, $user, $action, $extra, $version, $locale) {
	list($attrs,$last) = getIterationInfo($user,$action,$extra);
	if ($dateType) {
		$xml = "<FromModifiedDate>$last</FromModifiedDate>";
		if ($dateType == REQ_DATE_WRAPPED) {
			$xml = "<ModifiedDateRangeFilter>$xml</ModifiedDateRangeFilter>";
		}
		$customXML .= $xml;
	}

	// Build the request
	$tag = $type.'Rq';
	$xml = "<$tag $attrs requestID=\"$requestID\">".'
			<MaxReturned>' . QB_QUICKBOOKS_MAX_RETURNED . '</MaxReturned>
			'.$customXML.'
			<OwnerID>0</OwnerID>
		</'.$tag.'>';

	return QuickBooks_Callbacks_SQL_Callbacks::xml($version,$locale,$xml,'stopOnError');
}

// Queue a request to QuickBooks (which is a response, from this script's perspective)
function queueRequest($action, $priority, $user, $idents): void {
	if (!empty($idents['iteratorRemainingCount']))
	{
		// Queue up another request
		$Queue = QuickBooks_WebConnector_Queue_Singleton::getInstance();
		$Queue->enqueue($action, null, $priority, ['iteratorID' => $idents['iteratorID']], $user);
	}
}

function processQuickBooksData($xml, string $type, callable $buildArrayFn, callable $buildLineItemArrayFn=null, bool $useDataExt=false) {
	// This piece of the response from QuickBooks is now stored in $xml. You
	//	can process the qbXML response in $xml in any way you like. Save it to
	//	a file, stuff it in a database, parse it and stuff the records in a
	//	database, etc. etc. etc.
	//
	// The following example shows how to use the built-in XML parser to parse
	//	the response and stuff it into a database.

	// Import all of the records
	$errnum = 0;
	$errmsg = '';
	$Parser = new QuickBooks_XML_Parser($xml);
	if ($Doc = $Parser->parse($errnum, $errmsg)) {
		$Root = $Doc->getRoot();
		$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/'.$type.'QueryRs');

		$Driver = QuickBooks_Utilities::driverFactory(QB_QUICKBOOKS_DSN);
		$table = 'qb_example_'.strtolower($type);

		foreach ($List->children() as $child) {
			$arr = $buildArrayFn($child);
			queryWithArray($Driver,'REPLACE',$table,$arr);

			if ($buildLineItemArrayFn) {
				$li_table = $table.'_lineitem';
				$txnId = $arr['TxnID'];

				// Remove any old line items
				dbDeleteTxn($Driver,$li_table,$txnId);

				// Process the line items
				$search = $type.'LineRet';
				foreach ($child->children() as $grandchild) {
					if ($grandchild->name() == $search) {
						$lineitem = $buildLineItemArrayFn($grandchild,$txnId);
						queryWithArray($Driver,'INSERT',$li_table, $lineitem);
					}
					elseif ($useDataExt && $grandchild->name() == 'DataExtRet') {
						// custom fields
						$dataext = [
							'DataExtName' => $grandchild->getChildDataAt('DataExtRet DataExtName'),
							'DataExtValue' => $grandchild->getChildDataAt('DataExtRet DataExtValue'),
						];
						QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, ' - custom field "' . $dataext['DataExtName'] . '": ' . $dataext['DataExtValue']);
					}
				}
			}
		}
	}
}

/**
 * Build a request to import invoices already in QuickBooks into our application
 */
function _quickbooks_invoice_import_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{
	$type = 'InvoiceQuery';
	$xml = '<IncludeLineItems>true</IncludeLineItems>';
	return wrapRequestXML($type,$xml,REQ_DATE_WRAPPED, $requestID,$user,$action,$extra,$version,$locale);
}

/**
 * Handle a response from QuickBooks
 */
function _quickbooks_invoice_import_response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
{
	queueRequest($action,QB_PRIORITY_INVOICE, $user,$idents);

	processQuickBooksData($xml, 'Invoice', function($Invoice){
		$arr = [
			'TxnID' => $Invoice->getChildDataAt('InvoiceRet TxnID'),
			'TimeCreated' => $Invoice->getChildDataAt('InvoiceRet TimeCreated'),
			'TimeModified' => $Invoice->getChildDataAt('InvoiceRet TimeModified'),
			'RefNumber' => $Invoice->getChildDataAt('InvoiceRet RefNumber'),
			'Customer_ListID' => $Invoice->getChildDataAt('InvoiceRet CustomerRef ListID'),
			'Customer_FullName' => $Invoice->getChildDataAt('InvoiceRet CustomerRef FullName'),
			'ShipAddress_Addr1' => $Invoice->getChildDataAt('InvoiceRet ShipAddress Addr1'),
			'ShipAddress_Addr2' => $Invoice->getChildDataAt('InvoiceRet ShipAddress Addr2'),
			'ShipAddress_City' => $Invoice->getChildDataAt('InvoiceRet ShipAddress City'),
			'ShipAddress_State' => $Invoice->getChildDataAt('InvoiceRet ShipAddress State'),
			'ShipAddress_PostalCode' => $Invoice->getChildDataAt('InvoiceRet ShipAddress PostalCode'),
			'BalanceRemaining' => $Invoice->getChildDataAt('InvoiceRet BalanceRemaining'),
		];
		QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'Importing invoice #' . $arr['RefNumber'] . ': ' . print_r($arr, true));
		return $arr;
	}, function($InvoiceLine,$txnId) {
		return [
			'TxnID' => $txnId,
			'TxnLineID' => $InvoiceLine->getChildDataAt('InvoiceLineRet TxnLineID'),
			'Item_ListID' => $InvoiceLine->getChildDataAt('InvoiceLineRet ItemRef ListID'),
			'Item_FullName' => $InvoiceLine->getChildDataAt('InvoiceLineRet ItemRef FullName'),
			'Descrip' => $InvoiceLine->getChildDataAt('InvoiceLineRet Desc'),
			'Quantity' => $InvoiceLine->getChildDataAt('InvoiceLineRet Quantity'),
			'Rate' => $InvoiceLine->getChildDataAt('InvoiceLineRet Rate'),
		];
	});

	return true;
}

/**
 * Build a request to import customers already in QuickBooks into our application
 */
function _quickbooks_customer_import_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{
	$type = 'CustomerQuery';
	$xml = '';
	return wrapRequestXML($type,$xml,REQ_DATE_SIMPLE, $requestID,$user,$action,$extra,$version,$locale);
}

/**
 * Handle a response from QuickBooks
 */
function _quickbooks_customer_import_response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
{
	queueRequest($action,QB_PRIORITY_CUSTOMER, $user,$idents);

	processQuickBooksData($xml, 'Customer', function($Customer){
		$arr = [
			'ListID' => $Customer->getChildDataAt('CustomerRet ListID'),
			'TimeCreated' => $Customer->getChildDataAt('CustomerRet TimeCreated'),
			'TimeModified' => $Customer->getChildDataAt('CustomerRet TimeModified'),
			'Name' => $Customer->getChildDataAt('CustomerRet Name'),
			'FullName' => $Customer->getChildDataAt('CustomerRet FullName'),
			'FirstName' => $Customer->getChildDataAt('CustomerRet FirstName'),
			'MiddleName' => $Customer->getChildDataAt('CustomerRet MiddleName'),
			'LastName' => $Customer->getChildDataAt('CustomerRet LastName'),
			'Contact' => $Customer->getChildDataAt('CustomerRet Contact'),
			'ShipAddress_Addr1' => $Customer->getChildDataAt('CustomerRet ShipAddress Addr1'),
			'ShipAddress_Addr2' => $Customer->getChildDataAt('CustomerRet ShipAddress Addr2'),
			'ShipAddress_City' => $Customer->getChildDataAt('CustomerRet ShipAddress City'),
			'ShipAddress_State' => $Customer->getChildDataAt('CustomerRet ShipAddress State'),
			'ShipAddress_PostalCode' => $Customer->getChildDataAt('CustomerRet ShipAddress PostalCode')
		];
		QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, "Importing customer " . $arr['FullName'] . ': ' . print_r($arr, true));
		return $arr;
	});

	return true;
}

/**
 * Build a request to import sales orders already in QuickBooks into our application
 */
function _quickbooks_salesorder_import_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{
	$type = 'SalesOrderQuery';
	$xml = '<IncludeLineItems>true</IncludeLineItems>';
	return wrapRequestXML($type,$xml,REQ_DATE_WRAPPED, $requestID,$user,$action,$extra,$version,$locale);
}

/**
 * Handle a response from QuickBooks
 */
function _quickbooks_salesorder_import_response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
{
	queueRequest($action,QB_PRIORITY_SALESORDER, $user,$idents);

	// This piece of the response from QuickBooks is now stored in $xml. You
	//	can process the qbXML response in $xml in any way you like. Save it to
	//	a file, stuff it in a database, parse it and stuff the records in a
	//	database, etc. etc. etc.
	//
	// The following example shows how to use the built-in XML parser to parse
	//	the response and stuff it into a database.

	// Import all of the records
	$errnum = 0;
	$errmsg = '';
	$Parser = new QuickBooks_XML_Parser($xml);
	if ($Doc = $Parser->parse($errnum, $errmsg))
	{
		$Root = $Doc->getRoot();
		$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/SalesOrderQueryRs');

		$Driver = QuickBooks_Utilities::driverFactory(QB_QUICKBOOKS_DSN);
		foreach ($List->children() as $SalesOrder)
		{
			$arr = array(
				'TxnID' => $SalesOrder->getChildDataAt('SalesOrderRet TxnID'),
				'TimeCreated' => $SalesOrder->getChildDataAt('SalesOrderRet TimeCreated'),
				'TimeModified' => $SalesOrder->getChildDataAt('SalesOrderRet TimeModified'),
				'RefNumber' => $SalesOrder->getChildDataAt('SalesOrderRet RefNumber'),
				'Customer_ListID' => $SalesOrder->getChildDataAt('SalesOrderRet CustomerRef ListID'),
				'Customer_FullName' => $SalesOrder->getChildDataAt('SalesOrderRet CustomerRef FullName'),
				'ShipAddress_Addr1' => $SalesOrder->getChildDataAt('SalesOrderRet ShipAddress Addr1'),
				'ShipAddress_Addr2' => $SalesOrder->getChildDataAt('SalesOrderRet ShipAddress Addr2'),
				'ShipAddress_City' => $SalesOrder->getChildDataAt('SalesOrderRet ShipAddress City'),
				'ShipAddress_State' => $SalesOrder->getChildDataAt('SalesOrderRet ShipAddress State'),
				'ShipAddress_PostalCode' => $SalesOrder->getChildDataAt('SalesOrderRet ShipAddress PostalCode'),
				'BalanceRemaining' => $SalesOrder->getChildDataAt('SalesOrderRet BalanceRemaining'),
				);

			QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'Importing sales order #' . $arr['RefNumber'] . ': ' . print_r($arr, true));

			// Store the invoices in MySQL
			queryWithArray($Driver,'REPLACE','qb_salesorder',$arr);

			// Remove any old line items
			dbDeleteTxn($Driver,'qb_salesorder_lineitem',$arr['TxnID']);

			// Process the line items
			foreach ($SalesOrder->children() as $Child)
			{
				if ($Child->name() == 'SalesOrderLineRet')
				{
					$SalesOrderLine = $Child;

					$lineitem = array(
						'TxnID' => $arr['TxnID'],
						'TxnLineID' => $SalesOrderLine->getChildDataAt('SalesOrderLineRet TxnLineID'),
						'Item_ListID' => $SalesOrderLine->getChildDataAt('SalesOrderLineRet ItemRef ListID'),
						'Item_FullName' => $SalesOrderLine->getChildDataAt('SalesOrderLineRet ItemRef FullName'),
						'Descrip' => $SalesOrderLine->getChildDataAt('SalesOrderLineRet Desc'),
						'Quantity' => $SalesOrderLine->getChildDataAt('SalesOrderLineRet Quantity'),
						'Rate' => $SalesOrderLine->getChildDataAt('SalesOrderLineRet Rate'),
						);

					// Store the lineitems in MySQL
					queryWithArray($Driver,'INSERT','qb_salesorder_lineitem',$lineitem);
				}
			}
		}
	}

	return true;
}

/**
 * Build a request to import customers already in QuickBooks into our application
 */
function _quickbooks_item_import_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{
	$type = 'ItemQuery';
	$xml = '';
	return wrapRequestXML($type,$xml,REQ_DATE_SIMPLE, $requestID,$user,$action,$extra,$version,$locale);
}

/**
 * Handle a response from QuickBooks
 */
function _quickbooks_item_import_response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
{
	queueRequest($action,QB_PRIORITY_ITEM, $user,$idents);

	processQuickBooksData($xml, 'Item', function($Item){
		$type = substr(substr($Item->name(), 0, -3), 4);
		$ret = $Item->name();
		$arr = [
			'ListID' => $Item->getChildDataAt($ret . ' ListID'),
			'TimeCreated' => $Item->getChildDataAt($ret . ' TimeCreated'),
			'TimeModified' => $Item->getChildDataAt($ret . ' TimeModified'),
			'Name' => $Item->getChildDataAt($ret . ' Name'),
			'FullName' => $Item->getChildDataAt($ret . ' FullName'),
			'Type' => $type,
			'Parent_ListID' => $Item->getChildDataAt($ret . ' ParentRef ListID'),
			'Parent_FullName' => $Item->getChildDataAt($ret . ' ParentRef FullName'),
			'ManufacturerPartNumber' => $Item->getChildDataAt($ret . ' ManufacturerPartNumber'),
			'SalesTaxCode_ListID' => $Item->getChildDataAt($ret . ' SalesTaxCodeRef ListID'),
			'SalesTaxCode_FullName' => $Item->getChildDataAt($ret . ' SalesTaxCodeRef FullName'),
			'BuildPoint' => $Item->getChildDataAt($ret . ' BuildPoint'),
			'ReorderPoint' => $Item->getChildDataAt($ret . ' ReorderPoint'),
			'QuantityOnHand' => $Item->getChildDataAt($ret . ' QuantityOnHand'),
			'AverageCost' => $Item->getChildDataAt($ret . ' AverageCost'),
			'QuantityOnOrder' => $Item->getChildDataAt($ret . ' QuantityOnOrder'),
			'QuantityOnSalesOrder' => $Item->getChildDataAt($ret . ' QuantityOnSalesOrder'),
			'TaxRate' => $Item->getChildDataAt($ret . ' TaxRate'),
		];
		$look_for = array(
				'SalesPrice' => array( 'SalesOrPurchase Price', 'SalesAndPurchase SalesPrice', 'SalesPrice' ),
				'SalesDesc' => array( 'SalesOrPurchase Desc', 'SalesAndPurchase SalesDesc', 'SalesDesc' ),
				'PurchaseCost' => array( 'SalesOrPurchase Price', 'SalesAndPurchase PurchaseCost', 'PurchaseCost' ),
				'PurchaseDesc' => array( 'SalesOrPurchase Desc', 'SalesAndPurchase PurchaseDesc', 'PurchaseDesc' ),
				'PrefVendor_ListID' => array( 'SalesAndPurchase PrefVendorRef ListID', 'PrefVendorRef ListID' ),
				'PrefVendor_FullName' => array( 'SalesAndPurchase PrefVendorRef FullName', 'PrefVendorRef FullName' ),
				);

		foreach ($look_for as $field => $look_here) {
			if (!empty($arr[$field])) break;
			foreach ($look_here as $look) {
				$arr[$field] = $Item->getChildDataAt($ret . ' ' . $look);
			}
		}
		QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'Importing ' . $type . ' Item ' . $arr['FullName'] . ': ' . print_r($arr, true));
		return $arr;
	});

	return true;
}

/**
 * Build a request to import invoices already in QuickBooks into our application
 */
function _quickbooks_purchaseorder_import_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{
	$type = 'PurchaseOrderQuery';
	$xml = '<IncludeLineItems>true</IncludeLineItems>';
	return wrapRequestXML($type,$xml,REQ_DATE_NONE, $requestID,$user,$action,$extra,$version,$locale);
}

/**
 * Handle a response from QuickBooks
 */
function _quickbooks_purchaseorder_import_response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
{
	queueRequest($action,QB_PRIORITY_PURCHASEORDER, $user,$idents);

	processQuickBooksData($xml, 'PurchaseOrder', function($PurchaseOrder){
		$arr = [
			'TxnID' => $PurchaseOrder->getChildDataAt('PurchaseOrderRet TxnID'),
			'TimeCreated' => $PurchaseOrder->getChildDataAt('PurchaseOrderRet TimeCreated'),
			'TimeModified' => $PurchaseOrder->getChildDataAt('PurchaseOrderRet TimeModified'),
			'RefNumber' => $PurchaseOrder->getChildDataAt('PurchaseOrderRet RefNumber'),
			'Customer_ListID' => $PurchaseOrder->getChildDataAt('PurchaseOrderRet CustomerRef ListID'),
			'Customer_FullName' => $PurchaseOrder->getChildDataAt('PurchaseOrderRet CustomerRef FullName'),
		];
		QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, 'Importing purchase order #' . $arr['RefNumber'] . ': ' . print_r($arr, true));
		return $arr;
	}, function($PurchaseOrderLine,$txnId){
		$lineitem = [
			'TxnID' => $txnId,
			'TxnLineID' => $PurchaseOrderLine->getChildDataAt('PurchaseOrderLineRet TxnLineID'),
			'Item_ListID' => $PurchaseOrderLine->getChildDataAt('PurchaseOrderLineRet ItemRef ListID'),
			'Item_FullName' => $PurchaseOrderLine->getChildDataAt('PurchaseOrderLineRet ItemRef FullName'),
			'Descrip' => $PurchaseOrderLine->getChildDataAt('PurchaseOrderLineRet Desc'),
			'Quantity' => 0+$PurchaseOrderLine->getChildDataAt('PurchaseOrderLineRet Quantity'),
			'Rate' => 0+$PurchaseOrderLine->getChildDataAt('PurchaseOrderLineRet Rate'),
		];
		QuickBooks_Utilities::log(QB_QUICKBOOKS_DSN, ' - line item #' . $lineitem['TxnLineID'] . ': ' . print_r($lineitem, true));
		return $lineitem;
	}, true);

	return true;
}

function queryWithArray($driver, $command, $table, $fields) {
	foreach ($fields as $key => $value) {
		if (substr($key,0,4) == 'Time') {
			$fields[$key] = 'FROM_UNIXTIME('.strtotime($value).')';
		}
		elseif (is_numeric($value)) $fields[$key] = $value;
		else $fields[$key] = "'".$driver->escape($value)."'";
	}

	$driver->query(
		"$command INTO $table (".implode(',',array_keys($fields)).") VALUES (".implode(",",$fields).")",
		$errnum, $errmsg
	) or die(trigger_error($errmsg));
}
function dbDeleteTxn($driver, string $table, string $id) {
	$driver->query(
		"DELETE FROM $table WHERE TxnID='".$driver->escape($id)."'",
		$errnum, $errmsg
	) or die(trigger_error($errmsg));
}

/**
 * Handle a 500 not found error from QuickBooks
 *
 * Instead of returning empty result sets for queries that don't find any
 * records, QuickBooks returns an error message. This handles those error
 * messages, and acts on them by adding the missing item to QuickBooks.
 */
function _quickbooks_error_e500_notfound($requestID, $user, $action, $ID, $extra, &$err, $xml, $errnum, $errmsg)
{
	$Queue = QuickBooks_WebConnector_Queue_Singleton::getInstance();

	if ($action == QUICKBOOKS_IMPORT_INVOICE)
	{
		return true;
	}
	else if ($action == QUICKBOOKS_IMPORT_CUSTOMER)
	{
		return true;
	}
	else if ($action == QUICKBOOKS_IMPORT_SALESORDER)
	{
		return true;
	}
	else if ($action == QUICKBOOKS_IMPORT_ITEM)
	{
		return true;
	}
	else if ($action == QUICKBOOKS_IMPORT_PURCHASEORDER)
	{
		return true;
	}

	return false;
}


/**
 * Catch any errors that occur
 *
 * @param string $requestID
 * @param string $action
 * @param mixed $ID
 * @param mixed $extra
 * @param string $err
 * @param string $xml
 * @param mixed $errnum
 * @param string $errmsg
 * @return void
 */
function _quickbooks_error_catchall($requestID, $user, $action, $ID, $extra, &$err, $xml, $errnum, $errmsg)
{
	$message = '';
	$message .= 'Request ID: ' . $requestID . "\r\n";
	$message .= 'User: ' . $user . "\r\n";
	$message .= 'Action: ' . $action . "\r\n";
	$message .= 'ID: ' . $ID . "\r\n";
	$message .= 'Extra: ' . print_r($extra, true) . "\r\n";
	//$message .= 'Error: ' . $err . "\r\n";
	$message .= 'Error number: ' . $errnum . "\r\n";
	$message .= 'Error message: ' . $errmsg . "\r\n";

	mail(QB_QUICKBOOKS_MAILTO,
		'QuickBooks error occured!',
		$message);
}
