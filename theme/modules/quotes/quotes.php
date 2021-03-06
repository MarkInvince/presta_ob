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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2014 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_'))
	exit;

class Quotes extends Module
{
	protected $config_form = false;

	public function __construct()
	{
		$this->name = 'quotes';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'RCS';
		$this->need_instance = 1;
		$this->controllers = array('QuotesCart', 'SubmitedQuotes');
		/**
		 * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
		 */
		$this->bootstrap = true;
		parent::__construct();
		$this->displayName = $this->l('Ask for quotes');
		$this->description = $this->l('Ask for quotes module');
	}


	/**
	 * Don't forget to create update methods if needed:
	 *
	 */
	public function install()
	{
		include (dirname(__file__).'/sql/install.php');

		/* create new tab on backoffice Customers - Quotes */
		$tab = new Tab();
		foreach (Language::getLanguages() as $language)
			$tab->name[$language['id_lang']] = 'Quotes';

		$tab->id_parent = 10;
		$tab->class_name = 'AdminQuotes'; // Current admin quotes controller
		$tab->module = $this->name; // module name and folder
		$tab->position = Tab::getNewLastPosition($tab->id_parent);

		/* parent tab id */
		$tab->save(); // saving your tab
		Configuration::updateValue('MODULE_TAB_ID', $tab->id); // saving tab ID to remove it when uninstall
		Configuration::updateValue('MAIN_STATE', '1'); // Main module status
		Configuration::updateValue('MAIN_QUANTITY_FIELDS', '0'); // Quantity fields trigger
		Configuration::updateValue('MAIN_ANIMATE', '1'); // Quantity fields trigger
		Configuration::updateValue('MAIN_TERMS_AND_COND', '0'); // Quantity fields trigger
		Configuration::updateValue('MAIN_CMS_PAGE', '0'); // Quantity fields trigger
		Configuration::updateValue('PS_GUEST_QUOTES_ENABLED', '0');
		Configuration::updateValue('ADDRESS_ENABLED', '0');
		Configuration::updateValue('MESSAGING_ENABLED', '1');
		Configuration::updateValue('MAIN_PRODUCT_STATUS', '0');
		Configuration::updateValue('MAIN_PRODUCT_PAGE', '1');
		Configuration::updateValue('MAIN_PRODUCT_LIST', '1');
		Configuration::updateValue('MAIN_MAILS', Configuration::get('PS_SHOP_EMAIL'));
		Configuration::updateValue('CATEGORY_BOX', '');


		return parent::install() && $this->registerHook('header')
		&& $this->registerHook('extraRight')
		&& $this->registerHook('extraLeft')
		&& $this->registerHook('myAccountBlock')
		&& $this->registerHook('CustomerAccount')
		&& $this->registerHook('top')
		&& $this->registerHook('Header')
		&& $this->registerHook('displayProductButtons')
		&& $this->registerHook('displayProductListFunctionalButtons')
		&& $this->registerHook('displayMyAccountBlockfooter')
		&& $this->registerHook('displayBackOfficeHeader');
	}

	public function uninstall()
	{
		$this->deleteTables();

		$tab = new Tab(Configuration::get('MODULE_TAB_ID'));
		$tab->delete();

		return parent::uninstall() and Configuration::deleteByName('MAIN_STATE') and
		Configuration::deleteByName('MODULE_TAB_ID') and Configuration::deleteByName('MAIN_QUANTITY_FIELDS') and
		Configuration::deleteByName('MAIN_ANIMATE') and Configuration::deleteByName('MAIN_TERMS_AND_COND') and
		Configuration::deleteByName('MAIN_CMS_PAGE') and Configuration::deleteByName('PS_GUEST_QUOTES_ENABLED') and
		Configuration::deleteByName('ADDRESS_ENABLED') and Configuration::deleteByName('MESSAGING_ENABLED') and
		Configuration::deleteByName('MAIN_PRODUCT_STATUS') and Configuration::
			deleteByName('MAIN_PRODUCT_PAGE') and Configuration::deleteByName('MAIN_PRODUCT_LIST') and
		Configuration::deleteByName('MAIN_MAILS') and Configuration::deleteByName('MESSAGING_ENABLED') and
		Configuration::deleteByName('CATEGORY_BOX');
	}

	private function deleteTables()
	{
		return Db::getInstance()->execute('DROP TABLE IF EXISTS
			`'._DB_PREFIX_.'quotes_product`,
			`'._DB_PREFIX_.'quotes_bargains`,
			`'._DB_PREFIX_.'quotes`');
	}

	/**
	 * Load the configuration form
	 */
	public function getContent()
	{
		$output = '';
		/**
		 * If values have been submitted in the form, process.
		 */
		if (Tools::getValue('submitMainSettings')) {
			Configuration::updateValue('MAIN_STATE', Tools::getValue('MAIN_STATE'));
			Configuration::updateValue('MAIN_QUANTITY_FIELDS', Tools::getValue('MAIN_QUANTITY_FIELDS'));
			Configuration::updateValue('MAIN_ANIMATE', Tools::getValue('MAIN_ANIMATE'));
			Configuration::updateValue('MAIN_TERMS_AND_COND', Tools::getValue('MAIN_TERMS_AND_COND'));
			Configuration::updateValue('MAIN_CMS_PAGE', Tools::getValue('MAIN_CMS_PAGE'));
			Configuration::updateValue('PS_GUEST_QUOTES_ENABLED', Tools::getValue('PS_GUEST_QUOTES_ENABLED'));
			Configuration::updateValue('ADDRESS_ENABLED', Tools::getValue('ADDRESS_ENABLED'));
			Configuration::updateValue('MESSAGING_ENABLED', Tools::getValue('MESSAGING_ENABLED'));
			Configuration::updateValue('MAIN_PRODUCT_STATUS', Tools::getValue('MAIN_PRODUCT_STATUS'));
			Configuration::updateValue('MAIN_PRODUCT_PAGE', Tools::getValue('MAIN_PRODUCT_PAGE'));
			Configuration::updateValue('MAIN_PRODUCT_LIST', Tools::getValue('MAIN_PRODUCT_LIST'));
			Configuration::updateValue('CATEGORY_BOX', implode(',', Tools::getValue('CATEGORY_BOX')));

			if(Validate::isEmail(Tools::getValue('MAIN_MAILS')))
				Configuration::updateValue('MAIN_MAILS', Tools::getValue('MAIN_MAILS'));
			else
				$output .= $this->displayError($this->l('Wrong email format. Please try again'));
			$output .= $this->displayConfirmation($this->l('Settings updated'));
		}
		$this->context->smarty->assign('module_dir', $this->_path);

		return $output.$this->renderForm();
	}

	/**
	 * Create the form that will be displayed in the configuration of your module.
	 */
	protected function renderForm()
	{
		$options = $this->_generateCMS();
		$fields_form = array('form' => array(
			'legend' => array('title' => $this->l('Settings'), 'icon' => 'icon-cogs'),
			'input' => array(
				array(
					'type' => 'switch',
					'label' => $this->l('Turn bargain'),
					'name' => 'MAIN_STATE',
					'values' => array(
						array(
							'id' => 'on',
							'value' => 1,
							'label' => $this->l('Enabled')),
						array(
							'id' => 'off',
							'value' => 0,
							'label' => $this->l('Disabled')),
					),
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Quantity field'),
					'name' => 'MAIN_QUANTITY_FIELDS',
					'values' => array(
						array(
							'id' => 'on',
							'value' => 1,
							'label' => $this->l('Show')),
						array(
							'id' => 'off',
							'value' => 0,
							'label' => $this->l('Hide')),
					),
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Animate product to fly to cart (else popup option)'),
					'name' => 'MAIN_ANIMATE',
					'values' => array(
						array(
							'id' => 'on',
							'value' => 1,
							'label' => $this->l('Yes')),
						array(
							'id' => 'off',
							'value' => 0,
							'label' => $this->l('No')),
					),
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Enable Guest checkout'),
					'name' => 'PS_GUEST_QUOTES_ENABLED',
					'values' => array(
						array(
							'id' => 'on',
							'value' => 1,
							'label' => $this->l('Yes')),
						array(
							'id' => 'off',
							'value' => 0,
							'label' => $this->l('No')),
					)),
				array(
					'type' => 'switch',
					'label' => $this->l('Required terms and conditions'),
					'name' => 'MAIN_TERMS_AND_COND',
					'values' => array(
						array(
							'id' => 'on',
							'value' => 1,
							'label' => $this->l('Yes')),
						array(
							'id' => 'off',
							'value' => 0,
							'label' => $this->l('No')),
					)),
				array(
					'title' => $this->l('Please select CMS Page with Terms and Conditions'),
					'label' => $this->l('Select CMS Page with Terms and Rules'),
					'type' => 'select',
					'id' => 'cms_page_select',
					'name' => 'MAIN_CMS_PAGE',
					'options' => array(
						'query' => $options,
						'id' => 'id',
						'name' => 'name'),
					'identifier' => 'id',
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Delivery address option'),
					'name' => 'ADDRESS_ENABLED',
					'values' => array(
						array(
							'id' => 'on',
							'value' => 1,
							'label' => $this->l('Yes')),
						array(
							'id' => 'off',
							'value' => 0,
							'label' => $this->l('No')),
					)),
				array(
					'type' => 'switch',
					'label' => $this->l('User messaging'),
					'name' => 'MESSAGING_ENABLED',
					'desc' => 'Allows the user to send bargain messages to admin',
					'values' => array(
						array(
							'id' => 'on',
							'value' => 1,
							'label' => $this->l('Yes')),
						array(
							'id' => 'off',
							'value' => 0,
							'label' => $this->l('No')),
					)),
				array(
					'type' => 'switch',
					'label' => $this->l('Filtered on product status'),
					'name' => 'MAIN_PRODUCT_STATUS',
					'desc' => 'Present only if product not available for order',
					'values' => array(
						array(
							'id' => 'on',
							'value' => 1,
							'label' => $this->l('Yes')),
						array(
							'id' => 'off',
							'value' => 0,
							'label' => $this->l('No')),
					)),
				array(
					'type' => 'switch',
					'label' => $this->l('Button on product page'),
					'name' => 'MAIN_PRODUCT_PAGE',
					'values' => array(
						array(
							'id' => 'on',
							'value' => 1,
							'label' => $this->l('Yes')),
						array(
							'id' => 'off',
							'value' => 0,
							'label' => $this->l('No')),
					)),
				array(
					'type' => 'switch',
					'label' => $this->l('Button on product list'),
					'name' => 'MAIN_PRODUCT_LIST',
					'values' => array(
						array(
							'id' => 'on',
							'value' => 1,
							'label' => $this->l('Yes')),
						array(
							'id' => 'off',
							'value' => 0,
							'label' => $this->l('No')),
					)),
				array(
					'type' => 'text',
					'label' => $this->l('Email addresses:'),
					'name' => 'MAIN_MAILS',
					'desc' => 'Enter the email addresses separated by a comma (",") where you need submitted quotes to be sent'),
				array(
					'type' => 'categories',
					'name' => 'CATEGORY_BOX',
					'tree' => array(
						'id' => 'associated-categories-tree',
						'title' => $this->l('Filter on category base'),
						'use_search' => 1,
						'use_checkbox' => 1,
						'selected_categories' => explode(',', Configuration::get('CATEGORY_BOX'))))),
			'bottom' => '<script type="text/javascript">showBlock(element);hideBlock(element);</script>',
			'submit' => array('title' => $this->l('Save'), )), );

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitMainSettings';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) .
			'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->
				name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFormValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id);

		return $helper->generateForm(array($fields_form));
	}

	/**
	 * Set values for the inputs.
	 */
	protected function getConfigFormValues()
	{
		return array(
			'MAIN_STATE' => Configuration::get('MAIN_STATE'),
			'MAIN_QUANTITY_FIELDS' => Configuration::get('MAIN_QUANTITY_FIELDS'),
			'MAIN_ANIMATE' => Configuration::get('MAIN_ANIMATE'),
			'MAIN_TERMS_AND_COND' => Configuration::get('MAIN_TERMS_AND_COND'),
			'MAIN_CMS_PAGE' => Configuration::get('MAIN_CMS_PAGE'),
			'PS_GUEST_QUOTES_ENABLED' => Configuration::get('PS_GUEST_QUOTES_ENABLED'),
			'ADDRESS_ENABLED' => Configuration::get('ADDRESS_ENABLED'),
			'MESSAGING_ENABLED' => Configuration::get('MESSAGING_ENABLED'),
			'CATEGORY_BOX' => explode(',', Configuration::get('CATEGORY_BOX')),
			'MAIN_PRODUCT_STATUS' => Configuration::get('MAIN_PRODUCT_STATUS'),
			'MAIN_PRODUCT_PAGE' => Configuration::get('MAIN_PRODUCT_PAGE'),
			'MAIN_PRODUCT_LIST' => Configuration::get('MAIN_PRODUCT_LIST'),
			'MAIN_MAILS' => Configuration::get('MAIN_MAILS'));
	}

	/**
	 * Add the CSS & JavaScript files you want to be loaded in the BO.
	 */
	public function hookdisplayBackOfficeHeader()
	{
		$this->context->controller->addJS($this->_path.'js/back.js');
		$this->context->controller->addCSS($this->_path.'css/back.css');
	}

	/**
	 * Add the CSS & JavaScript files you want to be added on the FO.
	 */
	public function hookHeader()
	{
		$this->context->controller->addJS($this->_path.'/js/front.js');
		$this->context->controller->addCSS($this->_path.'/css/front.css');
	}

	public function hookTop()
	{
		//load model
		include_once (_PS_MODULE_DIR_.'quotes/classes/QuotesProduct.php');
		$quote_obj = new QuotesProductCart;

		$products = array();
		// check for user cart session. Defined in QuotesCart if user add product to quote box
		if ($this->context->cookie->__isset('request_id')) {
			$quote_obj->id_quote = $this->context->cookie->__get('request_id');
			list($products, $cart) = $quote_obj->getProducts();
		}
		$this->context->smarty->assign('session', $this->context->cookie->__get('request_id'));
		$this->context->smarty->assign('actionAddQuotes', $this->context->link->getModuleLink($this->name, 'QuotesCart', array(), true));
		$this->context->smarty->assign('products', $products);
		$this->context->smarty->assign('cart', $cart);
		$this->context->smarty->assign('active_overlay', 0);

		$customer = (($this->context->cookie->logged) ? (int)$this->context->cookie->
			id_customer : 0);

		$this->context->smarty->assign('isLogged', $customer);

		$product_count = 0;
		for($i = 0; $i < count($products); $i++) {
			$product_count = $product_count + (int)$products[$i]['quantity'];
		}
		$this->context->smarty->assign('cartTotalProducts', (int)$product_count);
		$this->context->smarty->assign('quotesCart', $this->context->link->
			getModuleLink($this->name, 'QuotesCart', array(), true));

		if (Configuration::get('MAIN_STATE'))
			return $this->display(__file__, 'quotesCart.tpl');
	}

	/**
	 * Add ask to quote button to product
	 */
	public function hookextraRight()
	{
		$product = new Product(Tools::getValue('id_product'), (int)$this->context->
			language->id, true);

		$customer = (($this->context->cookie->logged) ? (int)$this->context->cookie->
			id_customer : 0);

		$this->context->smarty->assign('isLogged', $customer);
		$this->context->smarty->assign('product', $product);
		$this->context->smarty->assign('enableAnimation', Configuration::get('MAIN_ANIMATE'));
		$this->context->smarty->assign('filtered_on_status', Configuration::get('MAIN_PRODUCT_STATUS'));
		$this->context->smarty->assign('present_on_product', Configuration::get('MAIN_PRODUCT_PAGE'));

		$link_core = new Link;
		$this->context->smarty->assign('plink', $link_core->getProductLink($product->id,
			$product->link_rewrite, $product->id_category_default));

		if (Configuration::get('MAIN_STATE'))
			return $this->display(__file__, 'extraRight.tpl');
	}
	/**
	 * Add ask to quote button to product
	 */
		/*public function hookdisplayProductButtons()
		{
			$product = new Product(Tools::getValue('id_product'), (int)$this->context->language->id, true);

			$customer = (($this->context->cookie->logged) ? (int)$this->context->cookie->id_customer : 0);

			$this->context->smarty->assign('isLogged', $customer);
			$this->context->smarty->assign('product', $product);
			$this->context->smarty->assign('enableAnimation',Configuration::get('MAIN_ANIMATE'));

			$linkCore = new Link;
			$this->context->smarty->assign('plink', $linkCore->getProductLink($product->id, $product->link_rewrite, $product->id_category_default));

			if (Configuration::get('MAIN_STATE'))
				return $this->display(__FILE__, 'extraRight.tpl');
		}*/

	/**
	 * Add ask to quote button to product list
	 */
	public function hookdisplayProductListFunctionalButtons($params)
	{
		$customer = (($this->context->cookie->logged) ? (int)$this->context->cookie->
			id_customer : 0);
		$this->context->smarty->assign('isLogged', $customer);
		$this->context->smarty->assign('enableAnimation', Configuration::get('MAIN_ANIMATE'));
		$category_box = Configuration::get('CATEGORY_BOX');
		$categories = !empty($category_box) ? explode(',', $category_box) : false;
		$this->context->smarty->assign('categories', $categories);
		$this->context->smarty->assign('present_on_product_list', Configuration::get('MAIN_PRODUCT_LIST'));
		$this->smarty->assign('product', $params['product']);
		if (Configuration::get('MAIN_STATE'))
			return $this->display(__file__, 'product-list.tpl');
	}

	/**
	 * Add quote link in my account
	 */
	public function hookCustomerAccount()
	{
		if (Configuration::get('MAIN_STATE'))
			return $this->display(__file__, 'my-account.tpl');
	}

	/**
	 * Add quote link in my account footer
	 */
	public function hookdisplayMyAccountBlockfooter()
	{
		if (Configuration::get('MAIN_STATE'))
			return $this->display(__file__, 'blockMyaccountFooter.tpl');
	}

	/**
	 * Add ask to quote button to product list
	 */
	private function _generateCMS()
	{
		$pages = CMS::getCMSPages((int)$this->context->language->id, null, true);
		$out = array();
		if (!empty($pages)) {
			foreach ($pages as $page) {
				$out[] = array(
					'id' => $page['id_cms'],
					'value' => $page['id_cms'],
					'name' => $page['meta_title']);
			}
		} else
			$out[] = array('name' => $this->l('No cms pages found'));

		return $out;
	}


}
