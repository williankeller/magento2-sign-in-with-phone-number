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

namespace Magestat\SigninPhoneNumber\Plugin\Model\ResourceModel\Customer;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Customer as ResourceModel;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magestat\SigninPhoneNumber\Helper\Data as Config;
use Magestat\SigninPhoneNumber\Setup\InstallData;

/**
 * Class ValidateUniquePhonenumber
 * Validates if the customer's phone number is unique
 */
class ValidateUniquePhonenumber
{

    /**
     * @var CustomerCollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param CustomerCollectionFactory $customerCollectionFactory
     * @param Config $config
     */
    public function __construct(
        CustomerCollectionFactory $customerCollectionFactory,
        Config $config
    ) {
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->config = $config;
    }

    /**
     * Validates if the customer phone number is unique
     *
     * @param ResourceModel $subject
     * @param Customer $customer
     *
     * @throws LocalizedException
     */
    public function beforeSave(ResourceModel $subject, Customer $customer)
    {
        if (!$this->config->isActive()) {
            return;
        }

        $collection = $this->customerCollectionFactory
            ->create()
            ->addAttributeToFilter(InstallData::PHONE_NUMBER, $customer->getData(InstallData::PHONE_NUMBER));

        // If the customer already exists, exclude them from the query
        if ($customer->getId()) {
            $collection->addAttribuTeToFilter(
                'entity_id',
                [
                    'neq' => (int) $customer->getId(),
                ]
            );
        }

        if ($collection->getSize() > 0) {
            throw new LocalizedException(
                __('A customer with the same phone number already exists in an associated website.')
            );
        }
    }
}
