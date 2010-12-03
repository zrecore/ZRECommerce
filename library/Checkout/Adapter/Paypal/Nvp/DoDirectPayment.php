<?php

class Checkout_Adapter_Paypal_Nvp_DoDirectPayment {

    // ------ DoDirectPayment Request Message BOF ------

    /**
     * Required. The PayPal method to call. Do NOT edit this value.
     * @var string
     */
    protected final $method = 'DoDirectPayment';
    /**
     * Optional. Can be 'Autorization' or 'Sale'. Default is 'Sale'
     * @var string
     */
    protected $paymentAction = 'Sale';
    
    /**
     * Required. IP address of the payer's browser.
     * @var string
     */
    protected $ipAddress = '';
    
    /**
     * Optional. Flag to indicate whether or not to return Fraud Managment
     * filter data. Default is 0 (No).
     * @var int
     */
    protected $returnMfmDetail = 0;

    // ------ DoDirectPayment Request Message EOF ------

    // ------ Credit Card Detail fields BOF ------

    /**
     * Required. The credit card type. Allowable values: Visa, MasterCard, Discover,
     * Amex, Maestro: See note, Solo: See note.
     *
     * For UK, only Maestro, Solo, MasterCard, Discover, and Visa are allowable.
     * For Canada, only MasterCard and Visa are allowable; Interac debit cards
     * are not supported.
     *
     * NOTE:If the credit card type is Maestro or Solo, the CURRENCYCODE must
     * be GBP. In addition, either STARTDATE or ISSUENUMBER must be specified.
     *
     * @var string
     */
    protected $creditCardType = '';

    /**
     * Required. The credit card number. Numerical characters only.
     * @var string
     */
    protected $account = '';

    /**
     * Required if using recurring payments with this method. The credit card
     * expiration Date, formatted as MMYYYY
     * 
     * @var string
     */
    protected $expDate = '';

    /**
     * Merchant Account settings dependent. Card Verification Value, version 2.
     * @var string
     */
    protected $cvv2 = '';

    /**
     * Optional. Date the Maestro or Solo card was issued, formatted as MMYYYY
     * @var string|null
     */
    protected $startDate = null;

    /**
     * Optional. Two digit issue number of the Maestro or Solo card.
     * @var string|null
     */
    protected $issueNumber = null;

    // ------ Credit Card Detail fields EOF ------

    // ------ Payer Information fields BOF ------

    /**
     * Optional. Payer's e-mail address.
     * @var string|null
     */
    protected $email = null;

    /**
     * Required. Payer's first name.
     * @var string
     */
    protected $firstName = '';

    /**
     * Required. Payer's last name.
     * @var string
     */
    protected $lastName = '';

    // ------ Payer Information fields EOF ------

    // ------ Address fields BOF ------

    /**
     * Required. First line of the street address.
     * @var string
     */
    protected $street = '';

    /**
     * Optional. Second line of the street address.
     * @var string
     */
    protected $street2 = null;

    /**
     * Required. City name.
     * @var string
     */
    protected $city = '';

    /**
     * Required. State or province.
     * @var string
     */
    protected $state = '';

    /**
     * Required. The country code.
     * @var string
     */
    protected $countryCode = '';

    /**
     * Required. U.S. Zip code or other country-specific postal code.
     * @var string
     */
    protected $zip = '';

    /**
     * Optional. Phone number.
     * @var string
     */
    protected $shipToPhoneNum = null;

    // ------ Payment Details fields EOF ------

    /**
     * Required. The total cost of the transaction to the customer. If shipping cost and tax charges are known, include them in this value; if not, this value should be the current sub-total of the order.
     * If the transaction includes one or more one-time purchases, this field must be equal to the sum of the purchases.
     * Set this field to 0 if the transaction does not include a one-time purchase; for example, when you set up a billing agreement for a recurring payment that is not immediately charged. Purchase-specific fields will be ignored.
     * Limitations: Must not exceed $10,000 USD in any currency. No currency symbol. Must have two decimal places, decimal separator must be a period (.), and the optional thousands separator must be a comma (,).
     * 
     * @var string
     */
    protected $amt = '';
    
    /**
     * Three-character currency code.
     * @var string
     */
    protected $currencyCode = 'USD';
    /**
     * Optional. Sum of cost of all items in this order.
     * Limitations: Must not exceed $10,000 USD in any currency. No currency symbol. Must have two decimal places, decimal separator must be a period (.), and the optional thousands separator must be a comma (,).
     * @var string
     */
    protected $itemAmt = '';

    //@todo finish adding optional fields.

    // ------ Payment Details fields BOF ------

}
