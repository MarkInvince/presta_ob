<?php
include_once(_PS_MODULE_DIR_.'quotes/classes/QuotesSubmit.php');
include_once(_PS_MODULE_DIR_.'quotes/classes/QuotesObj.php');
class AdminQuotesController extends ModuleAdminController
{
	public function __construct()
	{
	    $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->_defaultorderWay = 'DESC';

        $this->squotes = new QuotesSubmitCore;

        $this->bargains = new QuotesObj;

		$this->display = 'view';
        
		parent::__construct();
		if (!$this->module->active)
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        
        parent::init();
	    parent::setMedia();
    	// fix for 1.6.0.8
    	if(file_exists(_PS_THEME_DIR_.'global.tpl'))
    	{
            $this->context->smarty->fetch(_PS_THEME_DIR_.'global.tpl');
            $this->context->smarty->assign('js_defer' , (bool)Configuration::get('PS_JS_DEFER'));
    	}	
    	// End
    	parent::displayHeader();

	}
    public function initContent()
    {
        // default template
        $this->context->smarty->assign(array('index' => $this->context->link->getAdminLink('AdminQuotes')));
        $this->content = $this->context->smarty->fetch($this->getTemplatePath(). 'quotes_assign_global.tpl');
        $this->content.= $this->assign();
        parent::initContent();
    }
	public function postProcess()
	{
        if(Tools::getIsset('action')) {
            if(Tools::getValue('action') == 'delete') {
                die(Tools::jsonEncode(array('data' => $this->processDelete(Tools::getValue('item')))));
            }
        }
        if (Tools::isSubmit('addClientBargain'))
            $this->addAdminBargain(Tools::getValue('id_quote'));

        if (Tools::getValue('actionBargainDelete'))
            $this->bargainDelete(Tools::getValue('id_bargain'));
	}

    public function processDelete($item_customer_id) {
        $items = explode('_', $item_customer_id);
        if(!Validate::isInt($items[0]) OR !Validate::isInt($items[1]))
            return array('hasError' => true, 'message' => $this->l('There was some error!Please try again later'));

        if(!$this->squotes->deleteQuoteById($items[0], $items[1]))
            return array('hasError' => true, 'message' => $this->l('There was some problem while deleting quote ID:'.$items[0]));

        $this->context->smarty->assign(array(
            'quotes' => $this->squotes->getAllQuotes(),
            'totalQuotes' => count($this->squotes->getAllQuotes())
        ));
        return array('hasError' => false, 'quotes' => $this->context->smarty->fetch($this->getTemplatePath(). 'quotes_ajax_list_item.tpl'));
    }
    
    protected function assign() {
        global $currentIndex;
        if(!Tools::getValue('id_customer') AND !Tools::getValue('id_quote')) {
            $this->context->smarty->assign(array(
                'index' => $currentIndex.'&token='.Tools::getAdminTokenLite('AdminQuotes'),
                'quotes' => $this->squotes->getAllQuotes(),
                'totalQuotes' => count($this->squotes->getAllQuotes())
            ));
            return $this->context->smarty->fetch($this->getTemplatePath(). 'quotes_list.tpl');
        }
        else {
            $this->context->smarty->assign(array(
                'index' => $currentIndex.'&token='.Tools::getAdminTokenLite('AdminQuotes'),
                'quote' => $this->squotes->getQuoteById(pSQL(Tools::getValue('id_quote')), pSQL(Tools::getValue('id_customer'))),
                'id_quote' => Tools::getValue('id_quote'),
                'id_customer' => Tools::getValue('id_customer'),
                'currency' => $this->context->currency->sign
            ));

            $bargains = $this->bargains->getBargains(Tools::getValue('id_quote'));

            foreach ($bargains as $key=>$bargain){
                $bargains[$key]['bargain_price_display'] = Tools::displayPrice(Tools::ps_round($bargain['bargain_price'],2), $this->context->currency);
            }

            $this->context->smarty->assign('bargains', $bargains);

            return $this->context->smarty->fetch($this->getTemplatePath(). 'quotes_view.tpl');
        }
    }

    /**
     * Add admin bargain to quote by quote id
     */
    protected function addAdminBargain($id_quote = false) {
        if(!$id_quote)
            $this->errors[] = Tools::displayError('You can not add bargain without quote_id.');

        if(!Tools::getValue('bargain_text'))
            $this->errors[] = Tools::displayError('You can not add empty message.');

        if(Tools::getValue('bargain_price')){
            $price = Tools::getValue('bargain_price');
            if(!Validate::isPrice($price))
                $this->errors[] = Tools::displayError('Wrong price format.');
        }
        else
            $price = 0;

        $bargain_price_text = Tools::getValue('bargain_price_text') ? Tools::getValue('bargain_price_text') : '';

        if (!count($this->errors)) {
            if (!$this->bargains->addQuoteBargain(pSQL($id_quote), pSQL(Tools::getValue('bargain_text')), 'admin', pSQL($price), pSQL($bargain_price_text))) {
                $this->errors[] = Tools::displayError('Something wrong! Can not add bargain!.');
                $this->context->smarty->assign('bargain_errors', $this->errors);
            } else
                return true;
        }
        else
            $this->context->smarty->assign('bargain_errors', $this->errors);
    }

    /**
     * Add admin bargain to quote by quote id
     */
    protected function bargainDelete($id_bargain = false) {
        if(!$id_bargain)
            die(Tools::jsonEncode(array('hasError' => true)));

        if($this->bargains->deleteBargain($id_bargain)) {
            die(Tools::jsonEncode(array('deleted' => true, 'message' => $this->getMessage($this->l('Deleted')))));
        }else
            die(Tools::jsonEncode(array('hasError' => true)));
    }

    private function getMessage($message, $type = 'success') {
        $output = '<div class="alert alert-'.$type.'">';
            $output.= $message;
        $output.= '</div>';
        return $output;
    }

}
