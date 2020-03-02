<?php

namespace Dotdigitalgroup\Enterprise\Plugin;

use Magento\CustomerSegment\Model\Segment;
use Magento\CustomerSegment\Model\ResourceModel\Segment as SegmentResource;
use Magento\CustomerSegment\Model\ResourceModel\SegmentFactory as SegmentResourceFactory;

class SegmentPlugin
{
    /**
     * @var SegmentResourceFactory
     */
    private $segmentResourceFactory;

    /**
     * SegmentPlugin constructor.
     *
     * @param SegmentResourceFactory $segmentResourceFactory
     */
    public function __construct(
        SegmentResourceFactory $segmentResourceFactory
    ) {
        $this->segmentResourceFactory = $segmentResourceFactory;
    }

    /**
     * Reimport customers from the segment before it is changed
     *
     * @param SegmentResource $segmentResource
     * @param Segment $segment
     */
    public function beforeDeleteSegmentCustomers(SegmentResource $segmentResource, Segment $segment)
    {
        $this->reimportSegmentCustomers($segment);
    }

    /**
     * Reimport customers following any change in conditions
     *
     * @param SegmentResource $segmentResource
     * @param $result
     * @param Segment $segment
     * @return SegmentResource
     */
    public function afterAggregateMatchedCustomers(
        SegmentResource $segmentResource,
        $result,
        Segment $segment
    ) {
        $this->reimportSegmentCustomers($segment);
        return $result;
    }

    /**
     * @param Segment $segment
     */
    private function reimportSegmentCustomers(Segment $segment)
    {
        $segmentResource = $this->segmentResourceFactory->create();

        $customerSegmentQuery = $segmentResource->getConnection()
            ->select()
            ->from($segmentResource->getTable('magento_customersegment_customer'), ['customer_id'])
            ->where('segment_id = ?', $segment->getId())
            ->assemble();

        $segmentResource->getConnection()->update(
            $segmentResource->getTable('email_contact'),
            ['email_imported' => 0],
            ['customer_id IN (?)' => new \Zend_Db_Expr($customerSegmentQuery)]
        );
    }
}
