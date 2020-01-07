<?php

namespace Magestat\SigninPhoneNumber\Model\Handler;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Config\Share as ConfigShare;
use Magestat\SigninPhoneNumber\Api\SigninInterface;
use Magestat\SigninPhoneNumber\Helper\Data as HelperData;

/**
 * Class Signin
 * Handle login using the phone number instead of the email as default.
 */
class Signin implements SigninInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param StoreManagerInterface $storeManager
     * @param HelperData $helperData
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        StoreManagerInterface $storeManager,
        HelperData $helperData
    ) {
        $this->customerRepository = $customerRepository;
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
        $websiteIdFilter[] = $this->filterWebsiteShare();

        // Add customer attribute filter
        $customerFilter[] = $this->filterBuilder
            ->setField(\Magestat\SigninPhoneNumber\Setup\InstallData::PHONE_NUMBER)
            ->setConditionType('eq')
            ->setValue($phone)
            ->create();

        // Build search criteria
        $searchCriteriaBuilder = $this->searchCriteriaBuilder->addFilters($customerFilter);
        if (!empty($websiteIdFilter)) {
            $searchCriteriaBuilder->addFilters($websiteIdFilter);
        }
        $searchCriteria = $searchCriteriaBuilder->create();

        // Retrieve customer collection.
        $collection = $this->customerRepository->getList($searchCriteria);
        if ($collection->getTotalCount() == 1) {
            // Return first occurrence.
            $accounts = $collection->getItems();
            return reset($accounts);
        }
        return false;
    }

    /**
     * Add website filter if customer accounts are shared per website.
     *
     * @return FilterBuilder|boolean
     */
    private function filterWebsiteShare()
    {
        if ($this->helperData->getCustomerShareScope() == ConfigShare::SHARE_WEBSITE) {
            return $this->filterBuilder
                ->setField('website_id')
                ->setConditionType('eq')
                ->setValue($this->storeManager->getStore()->getWebsiteId())
                ->create();
        }
        return false;
    }
}
