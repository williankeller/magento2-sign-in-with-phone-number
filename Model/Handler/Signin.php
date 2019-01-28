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

namespace Magestat\SigninPhoneNumber\Model\Handler;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Config\Share as ConfigShare;
use Magestat\SigninPhoneNumber\Api\SigninInterface;
use Magestat\SigninPhoneNumber\Helper\Data as HelperData;

/**
 * @package \Magestat\SigninPhoneNumber\Model\Handler
 */
class Signin implements SigninInterface
{
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magestat\SigninPhoneNumber\Helper\Data
     */
    private $helperData;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        StoreManagerInterface $storeManager,
        HelperData $helperData
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->storeManager = $storeManager;
        $this->helperData = $helperData;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled($scopeCode = null)
    {
        return (bool) $this->helperData->isActive($scopeCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getSigninMode($scopeCode = null)
    {
        return $this->helperData->getSigninMode($scopeCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getByPhoneNumber(string $phone)
    {
        // Add website filter if customer accounts are shared per website
        $websiteIdFilter = false;
        if ($this->helperData->getCustomerShareScope() == ConfigShare::SHARE_WEBSITE) {
            $websiteIdFilter[] = $this->filterBuilder
                ->setField('website_id')
                ->setConditionType('eq')
                ->setValue($this->storeManager->getStore()->getWebsiteId())
                ->create();
        }

        // Add customer attribute filter
        $customerNumberFilter[] = $this->filterBuilder
            ->setField(\Magestat\SigninPhoneNumber\Setup\InstallData::PHONE_NUMBER)
            ->setConditionType('eq')
            ->setValue($phone)
            ->create();

        // Build search criteria
        $searchCriteriaBuilder = $this->searchCriteriaBuilder->addFilters($customerNumberFilter);
        if ($websiteIdFilter) {
            $searchCriteriaBuilder->addFilters($websiteIdFilter);
        }

        $searchCriteria = $searchCriteriaBuilder->create();

        // Retrieve the customer collection and return customer if there was exactly one customer found
        $collection = $this->customerRepository->getList($searchCriteria);
        if ($collection->getTotalCount() == 1) {
            return $collection->getItems()[0];
        }
        return false;
    }
}
