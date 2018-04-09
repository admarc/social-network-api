<?php

namespace spec\App\Validator;

use App\Validator\EntityValidator;
use App\Interactor\Exception\ValidationException;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EntityValidatorSpec extends ObjectBehavior
{
    public function let(ValidatorInterface $validator)
    {
        $this->beConstructedWith($validator);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(EntityValidator::class);
    }

    public function it_should_return_formatted_errors_when_entity_is_invalid(
        ValidatorInterface $validator,
        ConstraintViolationInterface $constraintViolation
    ) {
        $object = new \stdClass();

        $validator->validate($object)->shouldBeCalled()->willReturn([$constraintViolation]);
        $constraintViolation->getPropertyPath()->shouldBeCalled()->willReturn('name');
        $constraintViolation->getMessage()->shouldBeCalled()->willReturn('not_blank');

        $this->validate($object)->shouldReturn(['name' => ['not_blank']]);
    }

    public function it_should_return_no_errors_when_entity_is_valid(
        ValidationException $validator
    ) {
        $object = new \stdClass();

        $validator->validate($object)->shouldBeCalled()->willReturn([]);
        $this->validate($object)->shouldReturn([]);
    }
}
