<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 * @author DNK Soft <i@dnk.software>
 * @copyright  2021-2022 DNK Soft
 * @license    Valid for 1 website (or project) for each purchase of license
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Dnkpdforderbarcode extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'dnkpdforderbarcode';
        $this->tab = 'market_place';
        $this->version = '1.0.0';
        $this->author = 'DNK Soft';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('DNK The order reference barcode in the delivery slips and invoice.');
        $this->description = $this->l('Show order reference by EAN 128 barcode in the delivery slips and invoice.');

        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        Configuration::updateValue('DNKPDFORDERBARCODE_DELIVERY', false);
        Configuration::updateValue('DNKPDFORDERBARCODE_INVOICE', false);

        return parent::install();
    }

    public function uninstall()
    {
        Configuration::deleteByName('DNKPDFORDERBARCODE_LIVE_MODE');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $conf = '';
        if (((bool) Tools::isSubmit('submitDnkpdforderbarcodeModule')) == true) {
            $this->postProcess();
            $conf = $this->displayConfirmation($this->l('Saved'));
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        return $conf . $this->renderForm() . $output;
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitDnkpdforderbarcodeModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'DNKPDFORDERBARCODE_DELIVERY',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'DNKPDFORDERBARCODE_INVOICE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return [
            'DNKPDFORDERBARCODE_DELIVERY' => Configuration::get('DNKPDFORDERBARCODE_DELIVERY', true),
            'DNKPDFORDERBARCODE_INVOICE' => Configuration::get('DNKPDFORDERBARCODE_INVOICE', 'contact@prestashop.com'),
        ];
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    public function getEanImageBase64($code)
    {

        $barcode = new TCPDFBarcode($code, 'C128A');

        $img = $barcode->getBarcodePngData();
        $file = Tools::hash($code) . '.png';
        file_put_contents($this->local_path. 'views/img/barcodes/' . $file, $img);

        return $this->_path. 'views/img/barcodes/' . $file;
    }

}
