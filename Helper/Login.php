<?php

/**
 * Copyright © 2016 Rouven Alexander Rieker
 * See LICENSE.md bundled with this module for license details.
 */

namespace Magestat\SigninPhoneNumber\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magestat\SigninPhoneNumber\Model\ConfigProvider as SigninPhoneNumberConfigProvider;
use Magestat\SigninPhoneNumber\Model\Config\Source\LoginMode;

/**
 * Class Login
 *
 * @package Magestat\SigninPhoneNumber\Helper
 */
class Login extends AbstractHelper
{
    /**
     * @var SigninPhoneNumberConfigProvider
     */
    private $advancedLoginConfigProvider;

    /**
     * Login constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param SigninPhoneNumberConfigProvider           $advancedLoginConfigProvider
     */
    public function __construct(
    \Magento\Framework\App\Helper\Context $context, SigninPhoneNumberConfigProvider $advancedLoginConfigProvider
    )
    {
        parent::__construct($context);
        $this->advancedLoginConfigProvider = $advancedLoginConfigProvider;
    }

    /**
     * Retrieve the email field config
     *
     * @return array
     */
    public function getEmailFieldConfig()
    {
        switch ($this->advancedLoginConfigProvider->getLoginMode()) {
            case LoginMode::LOGIN_TYPE_ONLY_ATTRIBUTE:
                $label        = $this->advancedLoginConfigProvider->getLoginAttributeLabel();
                $type         = 'text';
                $dataValidate = "{required:true}";
                break;
            case LoginMode::LOGIN_TYPE_BOTH:
                $label        = 'Email / ' . $this->advancedLoginConfigProvider->getLoginAttributeLabel();
                $type         = 'text';
                $dataValidate = "{required:true}";
                break;
            default:
                $label        = 'Email';
                $type         = 'email';
                $dataValidate = "{required:true, 'validate-email':true}";
                break;
        }

        return [
            'label' => $label,
            'type' => $type,
            'data_validate' => $dataValidate
        ];
    }
}
