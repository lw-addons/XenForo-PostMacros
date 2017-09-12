<?php

class LiamW_Shared_Licensing
{
	/**
	 * @var string
	 */
	protected $_addonId;
	/**
	 * @var int
	 */
	protected $_productId;
	/**
	 * @var string
	 */
	protected $_callbackUrl;

	/**
	 * @param $addonId
	 * @param $productId
	 * @param $callbackUrl
	 *
	 * @throws XenForo_Exception
	 * @return LiamW_Shared_Licensing|string
	 */
	public static function getInstance($addonId, $productId, $callbackUrl = "https://xf-liam.com/products/license/callback")
	{
		if (!$addonId || !$productId)
		{
			throw new XenForo_Exception('Invalid parameters for class ' . __CLASS__);
		}

		$class = new LiamW_Shared_Licensing();
		$class->_addonId = strval($addonId);
		$class->_productId = intval($productId);
		$class->_callbackUrl = strval($callbackUrl);

		return $class;
	}

	public function checkLicense($strict = false)
	{
		$domain = $_SERVER['HTTP_HOST'];

		if (!$strict)
		{
			if (strstr($domain, '.') === false || substr($domain, -5) === '.local')
			{
				// If a domain doesn't have a . then it's clearly a local domain and shouldn't
				// be checked if strict mode is disabled.
				return true;
			}
		}

		$callback = XenForo_Helper_Http::getClient($this->_callbackUrl);
		$callback->setParameterPost('domain', $domain);
		$callback->setParameterPost('product_id', $this->_productId);

		$response = $callback->request('POST');

		if ($response->isError())
		{
			return false; // Can be returned for multiple reasons, main one being not found.
		}

		$json = $response->getBody();
		$array = json_decode($json, true);
		$array['license']['license_optional_extras'] = unserialize($array['license']['license_optional_extras']);

		return $array['license'];
	}
}