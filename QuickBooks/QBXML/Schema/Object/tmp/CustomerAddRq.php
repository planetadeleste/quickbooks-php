<?php

/**
 * Schema object for: CustomerAddRq
 *
 * @author "Keith Palmer Jr." <Keith@ConsoliByte.com>
 * @license LICENSE.txt
 *
 * @package QuickBooks
 * @subpackage QBXML
 */

/**
 *
 */
require_once 'QuickBooks.php';

/**
 *
 */
require_once 'QuickBooks/QBXML/Schema/Object.php';

/**
 *
 */
class QuickBooks_QBXML_Schema_Object_CustomerAddRq extends QuickBooks_QBXML_Schema_Object
{
	protected function &_qbxmlWrapper()
	{
		static $wrapper = 'CustomerAdd';

		return $wrapper;
	}

	protected function &_dataTypePaths()
	{
		static $paths = array (
  'Name' => 'STRTYPE',
  'IsActive' => 'BOOLTYPE',
  'ParentRef ListID' => 'IDTYPE',
  'ParentRef FullName' => 'STRTYPE',
  'CompanyName' => 'STRTYPE',
  'Salutation' => 'STRTYPE',
  'FirstName' => 'STRTYPE',
  'MiddleName' => 'STRTYPE',
  'LastName' => 'STRTYPE',
  'Suffix' => 'STRTYPE',
  'BillAddress Addr1' => 'STRTYPE',
  'BillAddress Addr2' => 'STRTYPE',
  'BillAddress Addr3' => 'STRTYPE',
  'BillAddress Addr4' => 'STRTYPE',
  'BillAddress Addr5' => 'STRTYPE',
  'BillAddress City' => 'STRTYPE',
  'BillAddress State' => 'STRTYPE',
  'BillAddress PostalCode' => 'STRTYPE',
  'BillAddress Country' => 'STRTYPE',
  'BillAddress Note' => 'STRTYPE',
  'ShipAddress Addr1' => 'STRTYPE',
  'ShipAddress Addr2' => 'STRTYPE',
  'ShipAddress Addr3' => 'STRTYPE',
  'ShipAddress Addr4' => 'STRTYPE',
  'ShipAddress Addr5' => 'STRTYPE',
  'ShipAddress City' => 'STRTYPE',
  'ShipAddress State' => 'STRTYPE',
  'ShipAddress PostalCode' => 'STRTYPE',
  'ShipAddress Country' => 'STRTYPE',
  'ShipAddress Note' => 'STRTYPE',
  'PrintAs' => 'STRTYPE',
  'Phone' => 'STRTYPE',
  'Mobile' => 'STRTYPE',
  'Pager' => 'STRTYPE',
  'AltPhone' => 'STRTYPE',
  'Fax' => 'STRTYPE',
  'Email' => 'STRTYPE',
  'Contact' => 'STRTYPE',
  'AltContact' => 'STRTYPE',
  'CustomerTypeRef ListID' => 'IDTYPE',
  'CustomerTypeRef FullName' => 'STRTYPE',
  'TermsRef ListID' => 'IDTYPE',
  'TermsRef FullName' => 'STRTYPE',
  'SalesRepRef ListID' => 'IDTYPE',
  'SalesRepRef FullName' => 'STRTYPE',
  'OpenBalance' => 'AMTTYPE',
  'OpenBalanceDate' => 'DATETYPE',
  'SalesTaxCodeRef ListID' => 'IDTYPE',
  'SalesTaxCodeRef FullName' => 'STRTYPE',
  'ItemSalesTaxRef ListID' => 'IDTYPE',
  'ItemSalesTaxRef FullName' => 'STRTYPE',
  'SalesTaxCountry' => 'STRTYPE',
  'ResaleNumber' => 'STRTYPE',
  'AccountNumber' => 'STRTYPE',
  'CreditLimit' => 'AMTTYPE',
  'PreferredPaymentMethodRef ListID' => 'IDTYPE',
  'PreferredPaymentMethodRef FullName' => 'STRTYPE',
  'CreditCardInfo CreditCardNumber' => 'STRTYPE',
  'CreditCardInfo ExpirationMonth' => 'INTTYPE',
  'CreditCardInfo ExpirationYear' => 'INTTYPE',
  'CreditCardInfo NameOnCard' => 'STRTYPE',
  'CreditCardInfo CreditCardAddress' => 'STRTYPE',
  'CreditCardInfo CreditCardPostalCode' => 'STRTYPE',
  'JobStatus' => 'ENUMTYPE',
  'JobStartDate' => 'DATETYPE',
  'JobProjectedEndDate' => 'DATETYPE',
  'JobEndDate' => 'DATETYPE',
  'JobDesc' => 'STRTYPE',
  'JobTypeRef ListID' => 'IDTYPE',
  'JobTypeRef FullName' => 'STRTYPE',
  'Notes' => 'STRTYPE',
  'IsStatementWithParent' => 'BOOLTYPE',
  'DeliveryMethod' => 'ENUMTYPE',
  'PriceLevelRef ListID' => 'IDTYPE',
  'PriceLevelRef FullName' => 'STRTYPE',
  'IncludeRetElement' => 'STRTYPE',
);

		return $paths;
	}

	protected function &_maxLengthPaths()
	{
		static $paths = array (
  'Name' => 41,
  'IsActive' => 0,
  'ParentRef ListID' => 0,
  'ParentRef FullName' => 0,
  'CompanyName' => 41,
  'Salutation' => 15,
  'FirstName' => 25,
  'MiddleName' => 5,
  'LastName' => 25,
  'Suffix' => 10,
  'BillAddress Addr1' => 41,
  'BillAddress Addr2' => 41,
  'BillAddress Addr3' => 41,
  'BillAddress Addr4' => 41,
  'BillAddress Addr5' => 41,
  'BillAddress City' => 31,
  'BillAddress State' => 21,
  'BillAddress PostalCode' => 13,
  'BillAddress Country' => 31,
  'BillAddress Note' => 41,
  'ShipAddress Addr1' => 41,
  'ShipAddress Addr2' => 41,
  'ShipAddress Addr3' => 41,
  'ShipAddress Addr4' => 41,
  'ShipAddress Addr5' => 41,
  'ShipAddress City' => 31,
  'ShipAddress State' => 21,
  'ShipAddress PostalCode' => 13,
  'ShipAddress Country' => 31,
  'ShipAddress Note' => 41,
  'PrintAs' => 110,
  'Phone' => 21,
  'Mobile' => 21,
  'Pager' => 21,
  'AltPhone' => 21,
  'Fax' => 21,
  'Email' => 1023,
  'Contact' => 41,
  'AltContact' => 41,
  'CustomerTypeRef ListID' => 0,
  'CustomerTypeRef FullName' => 0,
  'TermsRef ListID' => 0,
  'TermsRef FullName' => 0,
  'SalesRepRef ListID' => 0,
  'SalesRepRef FullName' => 0,
  'OpenBalance' => 0,
  'OpenBalanceDate' => 0,
  'SalesTaxCodeRef ListID' => 0,
  'SalesTaxCodeRef FullName' => 0,
  'ItemSalesTaxRef ListID' => 0,
  'ItemSalesTaxRef FullName' => 0,
  'SalesTaxCountry' => 31,
  'ResaleNumber' => 15,
  'AccountNumber' => 99,
  'CreditLimit' => 0,
  'PreferredPaymentMethodRef ListID' => 0,
  'PreferredPaymentMethodRef FullName' => 0,
  'CreditCardInfo CreditCardNumber' => 25,
  'CreditCardInfo ExpirationMonth' => 0,
  'CreditCardInfo ExpirationYear' => 0,
  'CreditCardInfo NameOnCard' => 41,
  'CreditCardInfo CreditCardAddress' => 41,
  'CreditCardInfo CreditCardPostalCode' => 41,
  'JobStatus' => 0,
  'JobStartDate' => 0,
  'JobProjectedEndDate' => 0,
  'JobEndDate' => 0,
  'JobDesc' => 99,
  'JobTypeRef ListID' => 0,
  'JobTypeRef FullName' => 0,
  'Notes' => 4095,
  'IsStatementWithParent' => 0,
  'DeliveryMethod' => 0,
  'PriceLevelRef ListID' => 0,
  'PriceLevelRef FullName' => 0,
  'IncludeRetElement' => 50,
);

		return $paths;
	}

	protected function &_isOptionalPaths()
	{
		static $paths = array (
  'Name' => false,
  'IsActive' => true,
  'ParentRef ListID' => true,
  'ParentRef FullName' => true,
  'CompanyName' => true,
  'Salutation' => true,
  'FirstName' => true,
  'MiddleName' => true,
  'LastName' => true,
  'Suffix' => true,
  'BillAddress Addr1' => true,
  'BillAddress Addr2' => true,
  'BillAddress Addr3' => true,
  'BillAddress Addr4' => true,
  'BillAddress Addr5' => true,
  'BillAddress City' => true,
  'BillAddress State' => true,
  'BillAddress PostalCode' => true,
  'BillAddress Country' => true,
  'BillAddress Note' => true,
  'ShipAddress Addr1' => true,
  'ShipAddress Addr2' => true,
  'ShipAddress Addr3' => true,
  'ShipAddress Addr4' => true,
  'ShipAddress Addr5' => true,
  'ShipAddress City' => true,
  'ShipAddress State' => true,
  'ShipAddress PostalCode' => true,
  'ShipAddress Country' => true,
  'ShipAddress Note' => true,
  'PrintAs' => true,
  'Phone' => true,
  'Mobile' => true,
  'Pager' => true,
  'AltPhone' => true,
  'Fax' => true,
  'Email' => true,
  'Contact' => true,
  'AltContact' => true,
  'CustomerTypeRef ListID' => true,
  'CustomerTypeRef FullName' => true,
  'TermsRef ListID' => true,
  'TermsRef FullName' => true,
  'SalesRepRef ListID' => true,
  'SalesRepRef FullName' => true,
  'OpenBalance' => true,
  'OpenBalanceDate' => true,
  'SalesTaxCodeRef ListID' => true,
  'SalesTaxCodeRef FullName' => true,
  'ItemSalesTaxRef ListID' => true,
  'ItemSalesTaxRef FullName' => true,
  'SalesTaxCountry' => true,
  'ResaleNumber' => true,
  'AccountNumber' => true,
  'CreditLimit' => true,
  'PreferredPaymentMethodRef ListID' => true,
  'PreferredPaymentMethodRef FullName' => true,
  'CreditCardInfo CreditCardNumber' => true,
  'CreditCardInfo ExpirationMonth' => true,
  'CreditCardInfo ExpirationYear' => true,
  'CreditCardInfo NameOnCard' => true,
  'CreditCardInfo CreditCardAddress' => true,
  'CreditCardInfo CreditCardPostalCode' => true,
  'JobStatus' => true,
  'JobStartDate' => true,
  'JobProjectedEndDate' => true,
  'JobEndDate' => true,
  'JobDesc' => true,
  'JobTypeRef ListID' => true,
  'JobTypeRef FullName' => true,
  'Notes' => true,
  'IsStatementWithParent' => true,
  'DeliveryMethod' => true,
  'PriceLevelRef ListID' => true,
  'PriceLevelRef FullName' => true,
  'IncludeRetElement' => true,
);
	}

	protected function &_sinceVersionPaths()
	{
		static $paths = array (
  'Name' => 999.99,
  'IsActive' => 999.99,
  'ParentRef ListID' => 999.99,
  'ParentRef FullName' => 999.99,
  'CompanyName' => 999.99,
  'Salutation' => 999.99,
  'FirstName' => 999.99,
  'MiddleName' => 999.99,
  'LastName' => 999.99,
  'Suffix' => 999.99,
  'BillAddress Addr1' => 999.99,
  'BillAddress Addr2' => 999.99,
  'BillAddress Addr3' => 999.99,
  'BillAddress Addr4' => 2,
  'BillAddress Addr5' => 6,
  'BillAddress City' => 999.99,
  'BillAddress State' => 999.99,
  'BillAddress PostalCode' => 999.99,
  'BillAddress Country' => 999.99,
  'BillAddress Note' => 6,
  'ShipAddress Addr1' => 999.99,
  'ShipAddress Addr2' => 999.99,
  'ShipAddress Addr3' => 999.99,
  'ShipAddress Addr4' => 2,
  'ShipAddress Addr5' => 6,
  'ShipAddress City' => 999.99,
  'ShipAddress State' => 999.99,
  'ShipAddress PostalCode' => 999.99,
  'ShipAddress Country' => 999.99,
  'ShipAddress Note' => 6,
  'PrintAs' => 999.99,
  'Phone' => 999.99,
  'Mobile' => 999.99,
  'Pager' => 999.99,
  'AltPhone' => 999.99,
  'Fax' => 999.99,
  'Email' => 999.99,
  'Contact' => 999.99,
  'AltContact' => 999.99,
  'CustomerTypeRef ListID' => 999.99,
  'CustomerTypeRef FullName' => 999.99,
  'TermsRef ListID' => 999.99,
  'TermsRef FullName' => 999.99,
  'SalesRepRef ListID' => 999.99,
  'SalesRepRef FullName' => 999.99,
  'OpenBalance' => 999.99,
  'OpenBalanceDate' => 999.99,
  'SalesTaxCodeRef ListID' => 999.99,
  'SalesTaxCodeRef FullName' => 999.99,
  'ItemSalesTaxRef ListID' => 999.99,
  'ItemSalesTaxRef FullName' => 999.99,
  'SalesTaxCountry' => 6,
  'ResaleNumber' => 999.99,
  'AccountNumber' => 999.99,
  'CreditLimit' => 999.99,
  'PreferredPaymentMethodRef ListID' => 999.99,
  'PreferredPaymentMethodRef FullName' => 999.99,
  'CreditCardInfo CreditCardNumber' => 999.99,
  'CreditCardInfo ExpirationMonth' => 0,
  'CreditCardInfo ExpirationYear' => 999.99,
  'CreditCardInfo NameOnCard' => 999.99,
  'CreditCardInfo CreditCardAddress' => 999.99,
  'CreditCardInfo CreditCardPostalCode' => 999.99,
  'JobStatus' => 999.99,
  'JobStartDate' => 999.99,
  'JobProjectedEndDate' => 999.99,
  'JobEndDate' => 999.99,
  'JobDesc' => 999.99,
  'JobTypeRef ListID' => 999.99,
  'JobTypeRef FullName' => 999.99,
  'Notes' => 3,
  'IsStatementWithParent' => 999.99,
  'DeliveryMethod' => 999.99,
  'PriceLevelRef ListID' => 999.99,
  'PriceLevelRef FullName' => 999.99,
  'IncludeRetElement' => 4,
);

		return $paths;
	}

	protected function &_isRepeatablePaths()
	{
		static $paths = array (
  'Name' => false,
  'IsActive' => false,
  'ParentRef ListID' => false,
  'ParentRef FullName' => false,
  'CompanyName' => false,
  'Salutation' => false,
  'FirstName' => false,
  'MiddleName' => false,
  'LastName' => false,
  'Suffix' => false,
  'BillAddress Addr1' => false,
  'BillAddress Addr2' => false,
  'BillAddress Addr3' => false,
  'BillAddress Addr4' => false,
  'BillAddress Addr5' => false,
  'BillAddress City' => false,
  'BillAddress State' => false,
  'BillAddress PostalCode' => false,
  'BillAddress Country' => false,
  'BillAddress Note' => false,
  'ShipAddress Addr1' => false,
  'ShipAddress Addr2' => false,
  'ShipAddress Addr3' => false,
  'ShipAddress Addr4' => false,
  'ShipAddress Addr5' => false,
  'ShipAddress City' => false,
  'ShipAddress State' => false,
  'ShipAddress PostalCode' => false,
  'ShipAddress Country' => false,
  'ShipAddress Note' => false,
  'PrintAs' => false,
  'Phone' => false,
  'Mobile' => false,
  'Pager' => false,
  'AltPhone' => false,
  'Fax' => false,
  'Email' => false,
  'Contact' => false,
  'AltContact' => false,
  'CustomerTypeRef ListID' => false,
  'CustomerTypeRef FullName' => false,
  'TermsRef ListID' => false,
  'TermsRef FullName' => false,
  'SalesRepRef ListID' => false,
  'SalesRepRef FullName' => false,
  'OpenBalance' => false,
  'OpenBalanceDate' => false,
  'SalesTaxCodeRef ListID' => false,
  'SalesTaxCodeRef FullName' => false,
  'ItemSalesTaxRef ListID' => false,
  'ItemSalesTaxRef FullName' => false,
  'SalesTaxCountry' => false,
  'ResaleNumber' => false,
  'AccountNumber' => false,
  'CreditLimit' => false,
  'PreferredPaymentMethodRef ListID' => false,
  'PreferredPaymentMethodRef FullName' => false,
  'CreditCardInfo CreditCardNumber' => false,
  'CreditCardInfo ExpirationMonth' => false,
  'CreditCardInfo ExpirationYear' => false,
  'CreditCardInfo NameOnCard' => false,
  'CreditCardInfo CreditCardAddress' => false,
  'CreditCardInfo CreditCardPostalCode' => false,
  'JobStatus' => false,
  'JobStartDate' => false,
  'JobProjectedEndDate' => false,
  'JobEndDate' => false,
  'JobDesc' => false,
  'JobTypeRef ListID' => false,
  'JobTypeRef FullName' => false,
  'Notes' => false,
  'IsStatementWithParent' => false,
  'DeliveryMethod' => false,
  'PriceLevelRef ListID' => false,
  'PriceLevelRef FullName' => false,
  'IncludeRetElement' => true,
);

		return $paths;
	}

	/*
	abstract protected function &_inLocalePaths()
	{
		static $paths = array(
			'FirstName' => array( 'QBD', 'QBCA', 'QBUK', 'QBAU' ),
			'LastName' => array( 'QBD', 'QBCA', 'QBUK', 'QBAU' ),
			);

		return $paths;
	}
	*/

	protected function &_reorderPathsPaths()
	{
		static $paths = array (
  0 => 'Name',
  1 => 'IsActive',
  2 => 'ParentRef ListID',
  3 => 'ParentRef FullName',
  4 => 'CompanyName',
  5 => 'Salutation',
  6 => 'FirstName',
  7 => 'MiddleName',
  8 => 'LastName',
  9 => 'Suffix',
  10 => 'BillAddress Addr1',
  11 => 'BillAddress Addr2',
  12 => 'BillAddress Addr3',
  13 => 'BillAddress Addr4',
  14 => 'BillAddress Addr5',
  15 => 'BillAddress City',
  16 => 'BillAddress State',
  17 => 'BillAddress PostalCode',
  18 => 'BillAddress Country',
  19 => 'BillAddress Note',
  20 => 'ShipAddress Addr1',
  21 => 'ShipAddress Addr2',
  22 => 'ShipAddress Addr3',
  23 => 'ShipAddress Addr4',
  24 => 'ShipAddress Addr5',
  25 => 'ShipAddress City',
  26 => 'ShipAddress State',
  27 => 'ShipAddress PostalCode',
  28 => 'ShipAddress Country',
  29 => 'ShipAddress Note',
  30 => 'PrintAs',
  31 => 'Phone',
  32 => 'Mobile',
  33 => 'Pager',
  34 => 'AltPhone',
  35 => 'Fax',
  36 => 'Email',
  37 => 'Contact',
  38 => 'AltContact',
  39 => 'CustomerTypeRef ListID',
  40 => 'CustomerTypeRef FullName',
  41 => 'TermsRef ListID',
  42 => 'TermsRef FullName',
  43 => 'SalesRepRef ListID',
  44 => 'SalesRepRef FullName',
  45 => 'OpenBalance',
  46 => 'OpenBalanceDate',
  47 => 'SalesTaxCodeRef ListID',
  48 => 'SalesTaxCodeRef FullName',
  49 => 'ItemSalesTaxRef ListID',
  50 => 'ItemSalesTaxRef FullName',
  51 => 'SalesTaxCountry',
  52 => 'ResaleNumber',
  53 => 'AccountNumber',
  54 => 'CreditLimit',
  55 => 'PreferredPaymentMethodRef ListID',
  56 => 'PreferredPaymentMethodRef FullName',
  57 => 'CreditCardInfo CreditCardNumber',
  58 => 'CreditCardInfo ExpirationMonth',
  59 => 'CreditCardInfo ExpirationYear',
  60 => 'CreditCardInfo NameOnCard',
  61 => 'CreditCardInfo CreditCardAddress',
  62 => 'CreditCardInfo CreditCardPostalCode',
  63 => 'JobStatus',
  64 => 'JobStartDate',
  65 => 'JobProjectedEndDate',
  66 => 'JobEndDate',
  67 => 'JobDesc',
  68 => 'JobTypeRef ListID',
  69 => 'JobTypeRef FullName',
  70 => 'Notes',
  71 => 'IsStatementWithParent',
  72 => 'DeliveryMethod',
  73 => 'PriceLevelRef ListID',
  74 => 'PriceLevelRef FullName',
  75 => 'IncludeRetElement',
);

		return $paths;
	}
}

?>