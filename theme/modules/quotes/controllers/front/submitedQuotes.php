<?php
/**
 * 2007-2014 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2014 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

include_once(_PS_MODULE_DIR_ . 'quotes/classes/QuotesObj.php');
include_once(_PS_MODULE_DIR_ . 'quotes/classes/QuotesTools.php');

class quotesSubmitedQuotesModuleFrontController extends ModuleFrontController
{

	public $ssl = true;
	public $display_column_left = false;
	public $quote_product;
	public $id_quote;
	public $id_customer;

	public function __construct()
	{
		parent::__construct();

		$this->context = Context::getContext();
		$this->quote = new QuotesObj;
		$this->id_quote = 0;
		$this->id_customer = (int)$this->context->cookie->id_customer;

		if (!$this->context->customer->isLogged()) {
			Tools::redirect('authentication.php');
		}
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addJS(array());
	}

	public function postProcess()
	{

		if (Tools::getValue('action') == 'showQuoteDetails') {
			$this->showQuoteDetails();
		}

		if (Tools::getValue('action') == 'addClientBargain') {
			$this->addClientBargain(Tools::getValue('id_quote'));
		}

		if (Tools::getValue('actionSubmitBargain')) {
			$this->bargainCustomerSubmit();
		}
		if (Tools::getValue('quoteRename')) {
			$this->quoteRename(Tools::getValue('id_quote'));
		}
	}

	public function initContent()
	{
		// Send noindex to avoid ghost carts by bots
		header("X-Robots-Tag: noindex, nofollow", true);

		parent::initContent();

		// default template
		$this->assign();

	}

	public function assign()
	{
		$quotes = $this->quote->getQuotesByCustomer($this->id_customer);
		$quotes = $this->foreachQuotes($quotes);

		$this->context->smarty->assign(array(
			'quotes'	  => $quotes,
			'id_customer' => $this->id_customer
		));

		$this->setTemplate('submited_quotes.tpl');
	}

	/**
	 * Show bargains and quote details
	 */
	protected function showQuoteDetails()
	{
		$this->id_quote = Tools::getValue('id_quote');

		$quoteInfo = $this->quote->getQuoteInfo($this->id_quote);

		$quoteInfo = $this->foreachQuotes($quoteInfo);
		foreach ($quoteInfo as $quoteInf) {
			$quoteInfo = $quoteInf;
		}
		//$this->context->smarty->assign('quote', $quoteInfo);

		$bargains = $this->quote->getBargains($this->id_quote);
		foreach ($bargains as $key => $bargain) {
			$bargains[$key]['bargain_price_display'] = Tools::displayPrice(Tools::ps_round($bargain['bargain_price'], 2), $this->context->currency);
		}

		$this->context->smarty->assign(array(
			'id_quote'		  => $this->id_quote,
			'bargains'		  => $bargains,
			'quote'			 => $quoteInfo,
			'MESSAGING_ENABLED' => Configuration::get('MESSAGING_ENABLED')
		));

		die(Tools::jsonEncode(array(
			'details' => $this->context->smarty->fetch(_PS_MODULE_DIR_ . "quotes/views/templates/front/quote_view.tpl")
		)));
	}

	/**
	 * Parcing quotes products from json To Array And Get Total Price of Quote
	 */
	protected function foreachQuotes($quotes)
	{
		foreach ($quotes as $firstkey => $quoteInfo) {
			$quote_total_price = 0;
			$quoteIn = array();
			foreach ($quoteInfo as $key => $field) {
				//$currency = new Currency($quoteInfo['id_currency'], null, $this->context->shop->id);
				if ($key == 'products') {
					$quoteIn[$key] = Tools::unSerialize($field);
					foreach ($quoteIn[$key] as $k => $product) {
						$productObj = new Product($product['id'], true, $this->context->language->id);

						$quoteIn[$key][$k]['name'] = $productObj->name;

						$prod_price = Product::getPriceStatic($product['id'], true, null, 6);
						$quoteIn[$key][$k]['price_total'] = Tools::displayPrice(Tools::ps_round($prod_price * $product['quantity'], 2), $this->context->currency);
						$quoteIn[$key][$k]['price'] = Tools::displayPrice(Tools::ps_round($prod_price, 2), $this->context->currency);
						$quoteIn[$key][$k]['link_rewrite'] = $productObj->link_rewrite;
						$quoteIn[$key][$k]['link'] = $this->context->link->getProductLink($productObj, $productObj->link_rewrite, $productObj->category, null, null);

						if ($product['id_attribute'] != 0) {
							$id_image = getProductAttributeImage($productObj->id, $product['id_attribute'], $this->context->language->id);
							if ($id_image)
								$quoteIn[$key][$k]['id_image'] = $id_image;
							else
								$quoteIn[$key][$k]['id_image'] = $productObj->getCover($productObj->id)['id_image'];
						}
						else {
							$quoteIn[$key][$k]['id_image'] = $productObj->getCover($productObj->id)['id_image'];
						}

						$quote_total_price = $quote_total_price + $prod_price * $product['quantity'];
					}
					$quoteIn['price'] = Tools::displayPrice(Tools::ps_round($quote_total_price, 2), $this->context->currency);
				}
				elseif ($key == 'burgain_price') {
					$quoteIn['bargain_price'] = Tools::displayPrice(Tools::ps_round($field, 2), $this->context->currency);
				}
				else
					$quoteIn[$key] = $field;
			}
			$quotes[$firstkey] = $quoteIn;
		}

		return $quotes;
	}

	/**
	 * Add client bargain to quote by quote id
	 */
	protected function addClientBargain($id_quote = false)
	{
		if (!Configuration::get('MESSAGING_ENABLED') || !$id_quote)
			$this->errors[] = Tools::displayError('You can not add bargain without quote_id.');

		if (!Tools::getValue('bargain_text'))
			$this->errors[] = Tools::displayError('You can not add empty message.');

		if (!count($this->errors)) {
			if ($this->quote->addQuoteBargain(pSQL($id_quote), pSQL(Tools::getValue('bargain_text'))))
				die(Tools::jsonEncode(array('errors' => false)));
		}
		else
			die(Tools::jsonEncode(array('errors' => $this->errors)));
	}

	/**
	 * Customer bargin submit
	 */
	protected function bargainCustomerSubmit()
	{
		$action = Tools::getValue('actionSubmitBargain');

		if ($this->quote->submitBargain(Tools::getValue('id_bargain'), $action, Tools::getValue('id_quote'))) {
			if ($action == 'accept') {
				$quoteInfo = $this->quote->getQuoteInfo(Tools::getValue('id_quote'));
				if ($quoteInfo) {

					foreach ($quoteInfo as $quoteInf) {
						$quote = $quoteInf;
					}

					$customer = new Customer($quote['id_customer']);

					$subject = $this->module->l("Accepted offer by customer");
					$message = '
							<html>
							<head>
							  <title>' . $this->module->l("Accepted offer") . '</title>
							</head>
							<body>
								<h2>' . $this->module->l("Accepted quote information") . '</h2>
								  <table>
									<tr>
									  <th>' . $this->module->l("Quote ID") . '</th>
									  <th>' . $this->module->l("Quote name") . '</th>
									  <th>' . $this->module->l("Reference") . '</th>
									  <th>' . $this->module->l("Burgain offer") . '</th>
									  <th>' . $this->module->l("Date add") . '</th>
									</tr>
									<tr>
									  <td>' . $quote['id_quote'] . '</td>
									  <td>' . $quote['quote_name'] . '</td>
									  <td>' . $quote['reference'] . '</td>
									  <td>' . $quote['burgain_price'] . '</td>
									  <td>' . $quote['date_add'] . '</td>
									</tr>
								  </table>
								<h2>' . $this->module->l("User information") . '</h2>
								  <table>
									<tr>
									  <th>' . $this->module->l("user ID") . '</th><th>' . $this->module->l("firstname") . '</th><th>' . $this->module->l("lastname") . '</th><th>' . $this->module->l("Email") . '</th>
									</tr>
									<tr>
									  <td>' . $customer->id . '</td>
									  <td>' . $customer->firstname . '</td>
									  <td>' . $customer->lastname . '</td>
									  <td>' . $customer->email . '</td>
									</tr>
								  </table>
							</body>
							</html>
					';
					// Send e-mail to admin
					$to = Configuration::get('PS_SHOP_EMAIL');
					if (Configuration::get('MAIN_MAILS'))
						$to .= ', ' . Configuration::get('MAIN_MAILS');
					quotesMailConfirm($to, $message, $subject);
				}

			}
			die(Tools::jsonEncode(array('submited' => $action)));
		}
		else
			die(Tools::jsonEncode(array('hasError' => true)));
	}

	/**
	 * Rename quote
	 */
	protected function quoteRename($id_quote = false)
	{
		if (Tools::getValue('quoteName')) {
			$quoteName = Tools::getValue('quoteName');
			if (!Validate::isString($quoteName))
				die(Tools::jsonEncode(array('hasError' => true, 'message' => $this->module->l('Wrong quote name'))));
		}
		else
			die(Tools::jsonEncode(array('hasError' => true, 'message' => $this->module->l('Name is empty'))));

		if ($this->quote->renameQuote(pSQL($id_quote), pSQL($quoteName))) {
			die(Tools::jsonEncode(array('renamed' => $quoteName)));
		}
		else
			die(Tools::jsonEncode(array('hasError' => true, 'message' => $this->module->l('Cannot rename quote'))));
	}


}