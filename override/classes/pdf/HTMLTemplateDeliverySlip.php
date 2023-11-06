<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
use PrestaShop\PrestaShop\Core\Util\Sorter;
/**
 * @since 1.5
 */
class HTMLTemplateDeliverySlip extends HTMLTemplateDeliverySlipCore
{
    /*
    * module: dnkpdforderbarcode
    * date: 2023-11-06 00:42:20
    * version: 1.0.0
    */
    public function getContent()
    {
        $delivery_address = new Address((int) $this->order->id_address_delivery);
        $formatted_delivery_address = AddressFormat::generateAddress($delivery_address, [], '<br />', ' ');
        $formatted_invoice_address = '';
        if ($this->order->id_address_delivery != $this->order->id_address_invoice) {
            $invoice_address = new Address((int) $this->order->id_address_invoice);
            $formatted_invoice_address = AddressFormat::generateAddress($invoice_address, [], '<br />', ' ');
        }
        $carrier = new Carrier($this->order->id_carrier);
        $carrier->name = ($carrier->name == '0' ? Configuration::get('PS_SHOP_NAME') : $carrier->name);
        $order_details = $this->order_invoice->getProducts();
        if (Configuration::get('PS_PDF_IMG_DELIVERY')) {
            foreach ($order_details as &$order_detail) {
                if ($order_detail['image'] != null) {
                    $name = 'product_mini_' . (int) $order_detail['product_id'] . (isset($order_detail['product_attribute_id']) ? '_' . (int) $order_detail['product_attribute_id'] : '') . '.jpg';
                    $path = _PS_PRODUCT_IMG_DIR_ . $order_detail['image']->getExistingImgPath() . '.jpg';
                    $order_detail['image_tag'] = preg_replace(
                        '/\.*' . preg_quote(__PS_BASE_URI__, '/') . '/',
                        _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR,
                        ImageManager::thumbnail($path, $name, 45, 'jpg', false),
                        1
                    );
                    if (file_exists(_PS_TMP_IMG_DIR_ . $name)) {
                        $order_detail['image_size'] = getimagesize(_PS_TMP_IMG_DIR_ . $name);
                    } else {
                        $order_detail['image_size'] = false;
                    }
                }
            }
        }
        $sorter = new Sorter();
        $order_details = $sorter->natural($order_details, Sorter::ORDER_DESC, 'product_reference', 'product_supplier_reference');
        $order_code = '';
        if (Configuration::get('DNKPDFORDERBARCODE_DELIVERY') && ($dnkpdforderbarcode = Module::getInstanceByName('dnkpdforderbarcode')) && Validate::isLoadedObject($dnkpdforderbarcode)) {
            $order_code = $dnkpdforderbarcode->getEanImageBase64($this->order->reference);
        }
        $this->smarty->assign([
            'order' => $this->order,
            'order_details' => $order_details,
            'delivery_address' => $formatted_delivery_address,
            'invoice_address' => $formatted_invoice_address,
            'order_invoice' => $this->order_invoice,
            'carrier' => $carrier,
            'display_product_images' => Configuration::get('PS_PDF_IMG_DELIVERY'),
            'order_barcode' => $order_code,
        ]);
        $tpls = [
            'style_tab' => $this->smarty->fetch($this->getTemplate('delivery-slip.style-tab')),
            'addresses_tab' => $this->smarty->fetch($this->getTemplate('delivery-slip.addresses-tab')),
            'summary_tab' => $this->smarty->fetch($this->getTemplate('delivery-slip.summary-tab')),
            'product_tab' => $this->smarty->fetch($this->getTemplate('delivery-slip.product-tab')),
            'payment_tab' => $this->smarty->fetch($this->getTemplate('delivery-slip.payment-tab')),
        ];
        $this->smarty->assign($tpls);
        return $this->smarty->fetch('module:dnkpdforderbarcode/views/templates/hook/delivery-slip.tpl');
    }
}
