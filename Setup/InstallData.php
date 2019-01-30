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

namespace Magestat\SigninPhoneNumber\Setup;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;

/**
 * Install data
 *
 * @package \Magestat\SigninPhoneNumber\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var string Customer Phone Number attribute.
     */
    const PHONE_NUMBER = 'phone_number';

    /**
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @param CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    // @codingStandardsIgnoreStart
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        // codingStandardsIgnoreEnd
        $setup->startSetup();

        /** @var \Magento\Customer\Setup\CustomerSetupFactory $customerSetup **/
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $customerSetup->addAttribute(
            Customer::ENTITY,
            self::PHONE_NUMBER,
            [
                'label' => 'Phone Number',
                'input' => 'text',
                'required' => false,
                'sort_order' => 900,
                'visible' => true,
                'system' => false,
                'unique' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true,
                'is_searchable_in_grid' => true
            ]
        );
        /** @var $attribute */
        $attribute = $customerSetup->getEavConfig()->getAttribute(
            Customer::ENTITY,
            self::PHONE_NUMBER
        );
        $usedInForms = [
            'adminhtml_customer',
            'checkout_register',
            'customer_account_create',
            'customer_account_edit',
            'adminhtml_checkout'
        ];
        $attribute->setData('used_in_forms', $usedInForms)
            ->setData('is_used_for_customer_segment', true)
            ->setData('is_system', 0)
            ->setData('is_user_defined', 1);

        $attribute->save();

        $setup->endSetup();
    }
}
