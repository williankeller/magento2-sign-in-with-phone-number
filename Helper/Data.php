<?php

/**
 * A Magento 2 module named Magestat/SigninPhoneNumber
 * Copyright (C) 2019 Magestat
 *
 * This file included in Magestat/SigninWithPhoneNumber is licensed under OSL 3.0
 *
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */

namespace Magestat\SigninPhoneNumber\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Configuration class.
 */
class Data extends AbstractHelper
{
    /**
     * Is module active
     *
     * @param string|null $scopeCode
     * @return bool
     */
    public function isActive($scopeCode = null)
    {
        return (bool) $this->scopeConfig->isSetFlag(
            'magestat_signin_phone_number/module/enabled',
            ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }

    /**
     * Sign in mode.
     *
     * @param string|null $scopeCode
     * @return string
     */
    public function getSigninMode($scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            'magestat_signin_phone_number/options/mode',
            ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }

    /**
     * Get if customer accounts are shared per website.
     *
     * @see \Magento\Customer\Model\Config\Share
     * @param string|null $scopeCode
     * @return string
     */
    public function getCustomerShareScope($scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            \Magento\Customer\Model\Config\Share::XML_PATH_CUSTOMER_ACCOUNT_SHARE,
            ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }
}
