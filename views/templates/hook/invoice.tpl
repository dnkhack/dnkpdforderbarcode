{*
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
*}

{$style_tab}

<div class="row">
	<img src="{$order_barcode}" style="width:150px; height:auto;" alt="EAN-128 Barcode">
</div>

<table width="100%" id="body" border="0" cellpadding="0" cellspacing="0" style="margin:0;">
	<!-- Invoicing -->
	<tr>
		<td colspan="12">

			{$addresses_tab}

		</td>
	</tr>

	<tr>
		<td colspan="12" height="30">&nbsp;</td>
	</tr>

	<!-- TVA Info -->
	<tr>
		<td colspan="12">

			{$summary_tab}

		</td>
	</tr>

	<tr>
		<td colspan="12" height="20">&nbsp;</td>
	</tr>

	<!-- Product -->
	<tr>
		<td colspan="12">

			{$product_tab}

		</td>
	</tr>

	<tr>
		<td colspan="12" height="10">&nbsp;</td>
	</tr>

	<!-- TVA -->
	<tr>
		<!-- Code TVA -->
		<td colspan="6" class="left">

			{$tax_tab}

		</td>
		<td colspan="1">&nbsp;</td>
		<!-- Calcule TVA -->
		<td colspan="5" rowspan="5" class="right">

			{$total_tab}

		</td>
	</tr>

	{$note_tab}

	<tr>
		<td colspan="12" height="10">&nbsp;</td>
	</tr>

	<tr>
		<td colspan="6" class="left">

			{$payment_tab}

		</td>
		<td colspan="1">&nbsp;</td>
	</tr>

	<tr>
		<td colspan="6" class="left">

			{$shipping_tab}

		</td>
		<td colspan="1">&nbsp;</td>
	</tr>

	<tr>
		<td colspan="12" height="10">&nbsp;</td>
	</tr>

	<tr>
		<td colspan="7" class="left small">

			<table>
				<tr>
					<td>
						<p>{$legal_free_text|escape:'html':'UTF-8'|nl2br}</p>
					</td>
				</tr>
			</table>

		</td>
	</tr>

	<!-- Hook -->
	{if isset($HOOK_DISPLAY_PDF)}
	<tr>
		<td colspan="12" height="30">&nbsp;</td>
	</tr>

	<tr>
		<td colspan="12">
			{$HOOK_DISPLAY_PDF}
		</td>
	</tr>
	{/if}

</table>
