<?php

namespace Magestat\SigninPhoneNumber\Model;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\ValidationResultsInterfaceFactory;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Customer\Model\Config\Share as ConfigShare;
use Magento\Customer\Model\Customer as CustomerModel;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\Metadata\Validator;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObjectFactory as ObjectFactory;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Math\Random;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\StringUtils as StringHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Psr\Log\LoggerInterface as PsrLogger;
use Magestat\SigninPhoneNumber\Api\SigninInterface as HandlerSignin;
use Magestat\SigninPhoneNumber\Model\Config\Source\SigninMode;

/**
 * Override Magento's default AccountManagement class.
 * @see \Magento\Customer\Model\AccountManagement
 */
class AccountManagement extends \Magento\Customer\Model\AccountManagement
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var SigninInterface
     */
    private $handlerSignin;

    /**
     * @param CustomerFactory $customerFactory
     * @param ManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param Random $mathRandom
     * @param Validator $validator
     * @param ValidationResultsInterfaceFactory $validationResultsDataFactory
     * @param AddressRepositoryInterface $addressRepository
     * @param CustomerMetadataInterface $customerMetadataService
     * @param CustomerRegistry $customerRegistry
     * @param PsrLogger $logger
     * @param Encryptor $encryptor
     * @param ConfigShare $configShare
     * @param StringHelper $stringHelper
     * @param CustomerRepositoryInterface $customerRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param TransportBuilder $transportBuilder
     * @param DataObjectProcessor $dataProcessor
     * @param Registry $registry
     * @param CustomerViewHelper $customerViewHelper
     * @param DateTime $dateTime
     * @param CustomerModel $customerModel
     * @param ObjectFactory $objectFactory
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param HandlerSignin $handlerSignin
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        CustomerFactory $customerFactory,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        Random $mathRandom,
        Validator $validator,
        ValidationResultsInterfaceFactory $validationResultsDataFactory,
        AddressRepositoryInterface $addressRepository,
        CustomerMetadataInterface $customerMetadataService,
        CustomerRegistry $customerRegistry,
        PsrLogger $logger,
        Encryptor $encryptor,
        ConfigShare $configShare,
        StringHelper $stringHelper,
        CustomerRepositoryInterface $customerRepository,
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        DataObjectProcessor $dataProcessor,
        Registry $registry,
        CustomerViewHelper $customerViewHelper,
        DateTime $dateTime,
        CustomerModel $customerModel,
        ObjectFactory $objectFactory,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        HandlerSignin $handlerSignin
    ) {
        parent::__construct(
            $customerFactory,
            $eventManager,
            $storeManager,
            $mathRandom,
            $validator,
            $validationResultsDataFactory,
            $addressRepository,
            $customerMetadataService,
            $customerRegistry,
            $logger,
            $encryptor,
            $configShare,
            $stringHelper,
            $customerRepository,
            $scopeConfig,
            $transportBuilder,
            $dataProcessor,
            $registry,
            $customerViewHelper,
            $dateTime,
            $customerModel,
            $objectFactory,
            $extensibleDataObjectConverter
        );
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->eventManager = $eventManager;
        $this->handlerSignin = $handlerSignin;
    }

    /**
     * @inheritdoc
     */
    public function authenticate($username, $password)
    {
        try {
            $customer = $this->handleSignin($username);
        } catch (NoSuchEntityException $e) {
            throw new InvalidEmailOrPasswordException(__('Invalid login or password.'));
        }

        $customerId = $customer->getId();
        if ($this->getAuthentication()->isLocked($customerId)) {
            throw new UserLockedException(__('The account is locked.'));
        }
        try {
            $this->getAuthentication()->authenticate($customerId, $password);
            // phpcs:disable Magento2.Exceptions.ThrowCatch
        } catch (InvalidEmailOrPasswordException $e) {
            throw new InvalidEmailOrPasswordException(__('Invalid login or password.'));
        }
        if ($customer->getConfirmation() && $this->isConfirmationRequired($customer)) {
            throw new EmailNotConfirmedException(__("This account isn't confirmed. Verify and try again."));
        }

        $this->dispatchEvents($customer, $password);

        return $customer;
    }

    /**
     * Handle login mode.
     *
     * @param string $username Customer email or phone number
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    private function handleSignin(string $username)
    {
        if (!$this->handlerSignin->isEnabled()) {
            return $this->customerRepository->get($username);
        }

        switch ($this->handlerSignin->getSigninMode()) {
            case SigninMode::TYPE_PHONE:
                return $this->withPhoneNumber($username);
            case SigninMode::TYPE_BOTH_OR:
                return $this->withPhoneNumberOrEmail($username);
            default:
                return $this->customerRepository->get($username);
        }
    }

    /**
     * Action to login with Phone Number only.
     *
     * @param string $username
     * @return CustomerInterface
     * @throws NoSuchEntityException
     */
    private function withPhoneNumber(string $username)
    {
        $customer = $this->handlerSignin->getByPhoneNumber($username);
        if (false == $customer) {
            throw new NoSuchEntityException();
        }
        return $customer;
    }

    /**
     * Action to login with Phone Number or Email.
     *
     * @param string $username
     * @return CustomerInterface
     */
    private function withPhoneNumberOrEmail(string $username)
    {
        $customer = $this->handlerSignin->getByPhoneNumber($username);
        if (false == $customer) {
            return $this->customerRepository->get($username);
        }
        return $customer;
    }

    /**
     * {@inheritdoc}
     */
    private function getAuthentication()
    {
        if (!($this->authentication instanceof AuthenticationInterface)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Customer\Model\AuthenticationInterface::class
            );
        }
        return $this->authentication;
    }

    /**
     * @param CustomerInterface $customer
     * @param string $password Customer password.
     * @return AccountManagement
     */
    private function dispatchEvents($customer, $password)
    {
        $customerModel = $this->customerFactory->create()->updateData($customer);
        $this->eventManager->dispatch(
            'customer_customer_authenticated',
            ['model' => $customerModel, 'password' => $password]
        );

        $this->eventManager->dispatch(
            'customer_data_object_login',
            ['customer' => $customer]
        );
        return $this;
    }
}
