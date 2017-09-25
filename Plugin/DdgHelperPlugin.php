<?php

namespace Dotdigitalgroup\Enterprise\Plugin;

/**
 * Class DdgHelperPlugin
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class DdgHelperPlugin
{
    /**
     * @var \Dotdigitalgroup\Enterprise\Helper\Data
     */
    private $helper;

    /**
     * DdgHelperPlugin constructor.
     * @param \Dotdigitalgroup\Enterprise\Helper\Data $helper
     */
    public function __construct(\Dotdigitalgroup\Enterprise\Helper\Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param \Dotdigitalgroup\Email\Helper\Data $subject
     * @param $result
     * @param $website
     * @return array
     */
    public function afterGetWebsiteCustomerMappingDatafields(
        \Dotdigitalgroup\Email\Helper\Data $subject,
        $result,
        $website = 0
    ) {
        $enterpriseMapping = $this->helper->getEnterpriseAttributes($website);
        if ($enterpriseMapping) {
            //skip non mapped customer data fields
            foreach ($enterpriseMapping as $key => $value) {
                if (! $value) {
                    unset($enterpriseMapping[$key]);
                }
            }
            $result = array_merge($result, $enterpriseMapping);
        }

        return $result;
    }
}