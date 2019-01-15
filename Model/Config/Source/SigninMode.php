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

namespace Magestat\SigninPhoneNumber\Model\Config\Source;

/**
 * Class SigninMode
 *
 * @package Magestat\SigninPhoneNumber\Model\Config\Source
 */
class SigninMode
{
    /**
     * @var int Value using phone to sign in.
     */
    const TYPE_PHONE = 1;

    /**
     * @var int Value using phone or email to sign in.
     */
    const TYPE_BOTH_OR = 2;

    /**
     * Retrieve possible sign in options.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            self::TYPE_PHONE => __('Sign in Only With Phone and Password'),
            self::TYPE_BOTH_OR => __('Sign in With Phone/Email and Password'),
        ];
    }
}
