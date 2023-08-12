<?php

namespace Winata\Core\Response\Concerns;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait ValidatesInputs
{
    /**
     * Validates inputs.
     *
     * @param array $inputs
     * @param array $rules
     * @param array $messages
     * @param array $attributes
     *
     * @return array
     *
     * @throws ValidationException
     */
    public function validate(array $inputs, array $rules, array $messages = [], array $attributes = []): array
    {
        return Validator::make($inputs, $rules, $messages, $attributes)->validate();
    }
}
