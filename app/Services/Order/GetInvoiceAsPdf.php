<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Repositories\Interfaces\OrderRepositoryContract;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

readonly class GetInvoiceAsPdf
{
    public function __construct(
        private OrderRepositoryContract $order_repository
    ) {
    }

    public function execute(string $uuid): \Barryvdh\DomPDF\PDF
    {
        /** @var Order|null $order */
        $order = $this->order_repository->findByUuid($uuid);
        if (empty($order)) {
            throw new UnprocessableEntityHttpException();
        }
        return Pdf::loadView('pdf.invoice.index', ['order' => $order]);
    }
}
