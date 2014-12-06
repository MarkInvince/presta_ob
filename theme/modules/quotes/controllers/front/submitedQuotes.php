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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2014 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

include_once(_PS_MODULE_DIR_.'quotes/classes/QuotesObj.php');
include_once(_PS_MODULE_DIR_.'quotes/classes/QuotesTools.php');

class QuotesSubmitedQuotesModuleFrontController extends ModuleFrontController
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

		if (!$this->context->customer->isLogged())
			Tools::redirect('authentication.php');
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addJS(array());
	}

	public function postProcess()
	{
		if (Tools::getValue('action') == 'showQuoteDetails')
			$this->showQuoteDetails();

		if (Tools::getValue('action') == 'addClientBargain')
			$this->addClientBargain(Tools::getValue('id_quote'));

		if (Tools::getValue('actionSubmitBargain'))
			$this->bargainCustomerSubmit();

		if (Tools::getValue('quoteRename'))
			$this->quoteRename(Tools::getValue('id_quote'));
	}

	public function initContent()
	{
		// Send noindex to avoid ghost carts by bots
		header('X-Robots-Tag: noindex, nofollow', true);

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

		$quote_info = $this->quote->getQuoteInfo($this->id_quote);

		$quote_info = $this->foreachQuotes($quote_info);
		foreach ($quote_info as $quote_inf)
			$quote_info = $quote_inf;
		//$this->context->smarty->assign('quote', $quoteInfo);

		$bargains = $this->quote->getBargains($this->id_quote);
		foreach ($bargains as $key => $bargain)
			$bargains[$key]['bargain_price_display'] = Tools::displayPrice(Tools::ps_round($bargain['bargain_price'], 2), $this->context->currency);

		$this->context->smarty->assign(array(
			'id_quote'		  => $this->id_quote,
			'bargains'		  => $bargains,
			'quote'			 => $quote_info,
			'MESSAGING_ENABLED' => Configuration::get('MESSAGING_ENABLED')
		));

		die(Tools::jsonEncode(array(
			'details' => $this->context->smarty->fetch(_PS_MODULE_DIR_.'quotes/views/templates/front/quote_view.tpl')
		)));
	}

	/**
	 * Parcing quotes products from json To Array And Get Total Price of Quote
	 */
	protected function foreachQuotes($quotes)
	{
		foreach ($quotes as $firstkey => $quote_info)
		{
			$quote_total_price = 0;
			$quote_in = array();
			foreach ($quote_info as $key => $field)
			{
				//$currency = new Currency($quoteInfo['id_currency'], null, $this->context->shop->id);
				if ($key == 'products')
				{
					$quote_in[$key] = Tools::unSerialize($field);
					foreach ($quote_in[$key] as $k => $product)
					{
						$product_obj = new Product($product['id'], true, $this->context->language->id);

						$quote_in[$key][$k]['name'] = $productObj->name;

						$prod_price = Product::getPriceStatic($product['id'], true, null, 6);
						$quote_in[$key][$k]['price_total'] = Tools::displayPrice(Tools::ps_round($prod_price * $product['quantity'], 2), $this->context->currency);
						$quote_in[$key][$k]['price'] = Tools::displayPrice(Tools::ps_round($prod_price, 2), $this->context->currency);
						$quote_in[$key][$k]['link_rewrite'] = $product_obj->link_rewrite;
						$quote_in[$key][$k]['link'] = $this->context->link->getProductLink($product_obj, $product_obj->link_rewrite, $product_obj->category, null, null);

						if ($product['id_attribute'] != 0)
						{
							$id_image = getProductAttributeImage($product_obj->id, $product['id_attribute'], $this->context->language->id);
							if ($id_image)
								$quote_in[$key][$k]['id_image'] = $id_image;
							else
								$quote_in[$key][$k]['id_image'] = $product_obj->getCover($product_obj->id)['id_image'];
						}
						else
							$quote_in[$key][$k]['id_image'] = $product_obj->getCover($product_obj->id)['id_image'];

						$quote_total_price = $quote_total_price + $prod_price * $product['quantity'];
					}
					$quote_in['price'] = Tools::displayPrice(Tools::ps_round($quote_total_price, 2), $this->context->currency);
				}
				elseif ($key == 'burgain_price')
					$quote_in['bargain_price'] = Tools::displayPrice(Tools::ps_round($field, 2), $this->context->currency);
				else
					$quote_in[$key] = $field;
			}
			$quotes[$firstkey] = $quote_in;
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

		if (!count($this->errors))
		{
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

		if ($this->quote->submitBargain(Tools::getValue('id_bargain'), $action, Tools::getValue('id_quote')))
		{
			if ($action == 'accept')
			{
				$quote_info = $this->quote->getQuoteInfo(Tools::getValue('id_quote'));
				if ($quote_info)
				{

					foreach ($quote_info as $quote_inf)
						$quote = $quote_inf;

					$customer = new Customer($quote['id_customer']);

					$tpl_message_vars = array(
						'{s_accept_title}' => $this->module->l('Accepted offer'),
						'{s_accepr_title_information}' => $this->module->l('Accepted quote information'),
						'{s_quote_id}' => $this->module->l('Quote ID'),
						'{s_quote_name}' => $this->module->l('Quote name'),
						'{s_quote_reference}' => $this->module->l('Reference'),
						'{s_quote_burgain_offer}' => $this->module->l('Burgain offer'),
						'{s_quote_date_add}' => $this->module->l('Date add'),
						'{quote_id}' => $quote['id_quote'],
						'{quote_name}' => $quote['quote_name'],
						'{quote_reference}' => $quote['reference'],
						'{quote_burgain}' => $quote['burgain_price'],
						'{quote_date_add}' => $quote['date_add'],
						'{s_user_info}' => $this->module->l('User information'),
						'{s_user_id}' => $this->module->l('user ID'),
						'{s_user_firstname}' => $this->module->l('firstname'),
						'{s_user_lastname}' => $this->module->l('lastname'),
						'{s_user_email}' => $this->module->l('Email'),
						'{user_id}' => $customer->id,
						'{user_firstname}' => $customer->firstname,
						'{user_lastname}' => $customer->lastname,
						'{user_email}' => $customer->email,
					);

					// Send e-mail to admin
					//$to = Configuration::get('PS_SHOP_EMAIL');
					if (Configuration::get('MAIN_MAILS'))
						$to = Configuration::get('MAIN_MAILS');
					//$to .= ', ' . Configuration::get('MAIN_MAILS');
					quotesMailConfirm ('quotes_notify', $to, $tpl_message_vars, $this->module->l('Accepted offer by customer'),
						$_SERVER['DOCUMENT_ROOT'].__PS_BASE_URI__.'modules/'.$this->module->name.'/mails/',
						$this->context->language->id, $this->context->shop->id);
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
		if (Tools::getValue('quoteName'))
		{
			$quote_name = Tools::getValue('quoteName');
			if (!Validate::isString($quote_name))
				die(Tools::jsonEncode(array('hasError' => true, 'message' => $this->module->l('Wrong quote name'))));
		}
		else
			die(Tools::jsonEncode(array('hasError' => true, 'message' => $this->module->l('Name is empty'))));

		if ($this->quote->renameQuote(pSQL($id_quote), pSQL($quote_name)))
			die(Tools::jsonEncode(array('renamed' => $quote_name)));
		else
			die(Tools::jsonEncode(array('hasError' => true, 'message' => $this->module->l('Cannot rename quote'))));
	}
}