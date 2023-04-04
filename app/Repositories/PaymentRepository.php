<?php

namespace App\Repositories;

use App\Enums\PaymentStatus;
use App\Repositories\Interfaces\PaymentRepositoryContract;
use Dbaeka\StripePayment\Contracts\StripeUpdatable;
use Dbaeka\StripePayment\DataObjects\Payment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\LaravelData\Data;

class PaymentRepository extends BaseCrudRepository implements PaymentRepositoryContract, StripeUpdatable
{
    public function getListForUserUuid(string $uuid): LengthAwarePaginator
    {
        $query = $this->model::query()->whereRelation('user', 'users.uuid', $uuid);
        $query = $this->withRelations($query);

        return $this->withPaginate($query);
    }

    public function updatePayment(string $payment_uuid, Payment $data): ?Data
    {
        return $this->updateByUuid($payment_uuid, $data);
    }

    public function updateSuccess(string $payment_uuid, Payment $data): ?Data
    {
        return $this->updateByUuid($payment_uuid, $data->additional(['status' => PaymentStatus::SUCCESS]));
    }

    public function updateFailure(string $payment_uuid, Payment $data): ?Data
    {
        return $this->updateByUuid($payment_uuid, $data->additional(['status' => PaymentStatus::FAILED]));
    }
}
