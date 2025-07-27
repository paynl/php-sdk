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
    public const PENDING = 20;
    public const PAID = 100;
    public const AUTHORIZE = 95;
    public const CANCEL = -1;
    public const VOID = -61;
    public const DENIED = -63;
    public const REFUND = -81;
    public const VERIFY = 85;
    public const PARTIAL_PAYMENT = 80;
    public const CHARGEBACK = -71;
    public const PARTIAL_REFUND = -82;
    public const PARTLY_CAPTURED = 97;
    public const CONFIRMED = 75;

    public const EVENT_PAID = 'new_ppt';
    public const EVENT_PENDING = 'pending';
    public const EVENT_CHARGEBACK = 'chargeback';
    public const EVENT_REFUND = 'refund';
    public const EVENT_CAPTURE = 'capture';

    /**
     * @param integer $stateId
     *
     * Source:
     * https://developer.pay.nl/docs/transaction-statuses#processing-statusses
     *
     * @return integer|mixed
     * @throws Exception
     */
    public function get(int $stateId)
    {
        $mapper[-70] = self::CHARGEBACK;
        $mapper[-71] = self::CHARGEBACK;
        $mapper[-72] = self::REFUND;
        $mapper[-81] = self::REFUND;
        $mapper[-82] = self::PARTIAL_REFUND;
        $mapper[-61] = self::VOID;
        $mapper[-63] = self::DENIED;
        $mapper[-64] = self::DENIED;
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
