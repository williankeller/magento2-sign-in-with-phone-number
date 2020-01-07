<?php

namespace Magestat\SigninPhoneNumber\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Customer\Model\Customer;
use Magestat\SigninPhoneNumber\Setup\InstallData;

/**
 * Uninstall
 * Delete "phone_number" attribute
 */
class Uninstall implements UninstallInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    // @codingStandardsIgnoreStart
    public function uninstall(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        // codingStandardsIgnoreEnd
        $setup->startSetup();

        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->removeAttribute(Customer::ENTITY, InstallData::PHONE_NUMBER);

        $setup->endSetup();
    }
}
