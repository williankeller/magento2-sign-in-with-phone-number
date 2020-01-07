<?php

namespace Magestat\SigninPhoneNumber\Model\Config\Source;

/**
 * Class SigninMode
 * Define options to the login process.
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
