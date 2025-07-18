<?php

namespace App\Constants;

class Status
{

    const ENABLE = 1;
    const DISABLE = 0;

    const UNLIMITED = -1;

    const YES = 1;
    const NO = 0;

    const CONNECTED = 1;
    const DISCONNECTED = 0;

    const VERIFIED = 1;
    const UNVERIFIED = 0;

    const SMS_INITIAL    = 0;
    const SMS_DELIVERED  = 1;
    const SMS_PENDING    = 2;
    const SMS_SCHEDULED  = 3;
    const SMS_PROCESSING = 4;
    const SMS_FAILED     = 9;

    const CAMPAIGN_INITIAL = 0;
    const CAMPAIGN_RUNNING = 1;
    const CAMPAIGN_PENDING = 2;
    const CAMPAIGN_FINISHED = 3;

    const SMS_TYPE_SEND     = 1;
    const SMS_TYPE_RECEIVED = 2;

    const PAYMENT_INITIATE = 0;
    const PAYMENT_SUCCESS = 1;
    const PAYMENT_PENDING = 2;
    const PAYMENT_REJECT = 3;

    const WALLET_PAYMENT = 1;
    const GATEWAY_PAYMENT = 2;

    const TICKET_OPEN = 0;
    const TICKET_ANSWER = 1;
    const TICKET_REPLY = 2;
    const TICKET_CLOSE = 3;

    const PRIORITY_LOW = 1;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_HIGH = 3;

    const USER_ACTIVE = 1;
    const USER_BAN = 0;

    const GOOGLE_PAY = 5001;

    const CUR_BOTH = 1;
    const CUR_TEXT = 2;
    const CUR_SYM = 3;

    const MONTHLY_PLAN = 1;
    const YEARLY_PLAN = 2;

    const CONTACT = 1;
    const GROUP = 2;
    const DIRECT_INPUT = 3;
    const DIRECT_INPUT_FROM_FILE = 4;
}
