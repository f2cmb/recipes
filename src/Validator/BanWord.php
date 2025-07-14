<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class BanWord extends Constraint
{
    public function __construct(
        public string $message = 'La valeur "{{ banWord }}" n\'est pas autorisée.',
        public array $banWords = [
        'spam',
        'viagra',
        'casino',
        'bitcoins',
        'xxx',
        ],
        ?array $groups = null,
        mixed $payload = null)
    {
        parent::__construct(null, $groups, $payload);
    }
    
}
