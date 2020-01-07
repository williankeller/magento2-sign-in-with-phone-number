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

namespace Magestat\SigninPhoneNumber\Api;

/**
 * @api
 */
interface SigninInterface
{
    /**
     * Return if module is enabled.
     *
     * @see \Magestat\SigninPhoneNumber\Helper\Data\Helper
     * @param string|null $scopeCode
     * @return bool
     */
    public function isEnabled($scopeCode);
    
    /**
     * @see \Magestat\SigninPhoneNumber\Helper\Data\Helper
     * @param string|null $scopeCode
     * @return string
     */
    public function getSigninMode($scopeCode);

    /**
     * Load customer object by phone attribute.
     *
     * @param string $phone
     * @return boolean|object
     */
    public function getByPhoneNumber(string $phone);
}
