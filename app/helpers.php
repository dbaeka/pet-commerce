<?php

function get_bool(mixed $value): ?bool
{
    return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
}
