<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Pay;

use PayNL\Sdk\Util\Exchange;
use Exception;

/**
 * Class PayStatus
 *
 * @package PayNL\Sdk\Util
 */
class PayStatus
{
    const PENDING = 20;
    const PAID = 100;
    const AUTHORIZE = 95;
    const CANCEL = -1;
    const REFUND = -81;
    const VERIFY = 85;
    const PARTIAL_PAYMENT = 80;
    const CHARGEBACK = -71;
    const PARTIAL_REFUND = -82;
    const PARTLY_CAPTURED = 97;
    const CONFIRMED = 75;

    const EVENT_PAID = 'new_ppt';
    const EVENT_PENDING = 'pending';

    /**
     * @param int $stateId
     *
     * Source:
     * https://developer.pay.nl/docs/transaction-statuses#processing-statusses
     *
     * @return int|mixed
     * @throws Exception
     */
    public function get(int $stateId)
    {
        $mapper[-70] = self::CHARGEBACK;
        $mapper[-71] = self::CHARGEBACK;
        $mapper[-72] = self::REFUND;
        $mapper[-81] = self::REFUND;
        $mapper[-82] = self::PARTIAL_REFUND;
        $mapper[20] = self::PENDING;
        $mapper[25] = self::PENDING;
        $mapper[50] = self::PENDING;
        $mapper[90] = self::PENDING;
        $mapper[75] = self::CONFIRMED;
        $mapper[76] = self::CONFIRMED;
        $mapper[80] = self::PARTIAL_PAYMENT;
        $mapper[85] = self::VERIFY;
        $mapper[95] = self::AUTHORIZE;
        $mapper[97] = self::PARTLY_CAPTURED;
        $mapper[98] = self::PENDING;
        $mapper[100] = self::PAID;

        if (isset($mapper[$stateId])) {
            return $mapper[$stateId];
        } else {
            if ($stateId < 0) {
                return self::CANCEL;
            } else {
                throw new Exception('Unexpected status: ' . $stateId);
            }
        }
    }

}