<?php

namespace App\Presenters;

use Carbon\Carbon;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use NumberFormatter;

class InvoicePresenter
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function amount()
    {
        $formatter = new IntlMoneyFormatter(
            new NumberFormatter(config('cashier.currency_locale'), NumberFormatter::CURRENCY),
            new ISOCurrencies
        );
        $money = new Money(
            $this->model->amount_due,
            new Currency(strtoupper(config('cashier.currency')))
        );

        return $formatter->format($money);
    }

    public function nextPaymentAttempt()
    {
        return (new Carbon($this->model->next_payment_attempt))->toDateString();
    }
}
